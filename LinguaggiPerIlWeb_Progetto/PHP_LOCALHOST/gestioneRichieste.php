<!--
All'interno di questo script viene gestito il sistema tramite il quale gli amministratori accettano le  richieste
di ricarica portafogli fatte dai clienti del sito. E' presente una semplice tabella che mostra le richieste in sospeso estratte da
richieste.xml . Con delle checkbox un admin può accettare una o molteplici richieste; così facendo le richieste vengono rimosse dall'XML 
e l'importo viene aggiunto nel portafoglio del cliente che ha fatto richiesta.
 -->
<?php

    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);

    session_start();

    require("cornice.php");

    if (!isset($_SESSION['success'])) {
        header('Location: home.php');
    }

    echo '<xml version="1.0" encoding="UTF-8">';
?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>provaGestione</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <div class="body">
    <div class="header">
  	        <h2>ACCETTA RICARICHE</h2>
        </div>
        

            <?php
            
                $stringaXML = "";
                $percorsoXML = "../XML/richieste.xml"; 
                foreach ( file($percorsoXML) as $nodo )
                {
                    $stringaXML .= trim($nodo);
                }
                $docRichiesta = new DOMDocument();
                $docRichiesta->loadXML($stringaXML);
                $rootRichieste = $docRichiesta->documentElement;
                $listaRichieste = $rootRichieste->childNodes;

                if ($rootRichieste->getElementsByTagName("richiesta")->length == 0){
                    echo "<p class=\"error\">Non ci sono richieste</p>";
                } else {

                    echo "<table>
            
                    <tr>
                        <th></th>
                        <th>Importo</th>
                        <th>Username</th>
                    </tr>";
                
                    for ($i=0; $i<$listaRichieste->length; $i++) 
                    {

                        $richiesta = $listaRichieste->item($i);

                        $idRichiesta = $richiesta->firstChild;
                        $idRichiestaVal = $idRichiesta->textContent;

                        $importo = $idRichiesta->nextSibling;
                        $importoVal = $importo->textContent;

                        $idAcc = $importo->nextSibling;
                        $idAccVal = $idAcc->textContent;

                        $query = "SELECT username FROM users WHERE id = " . (int) $idAccVal; 
                        if (!$result = mysqli_query($con, $query))
                        {
                            printf("errore nella query di ricerca Username \n");
                            exit();
                        }
                        $row = mysqli_fetch_array($result);
                        if ($row) {
                            $dbUser = $row['username'];
                        }
                        
                        $arrayIDRichieste[$i] = $idRichiestaVal;
                        $arrayImportoRichieste[$i] = $importoVal;
                        $arrayUsernameRichieste[$i] = $dbUser;

                    }
            
                    echo "<form method=\"post\" action=\"gestioneRichieste.php\">";
                        for($k=0; $k<$listaRichieste->length; $k++)
                        { 
                            $indice = $k + 1;
                            echo '<tr>';
                            echo "<td>$indice)</td>";
                            echo '    <td>&euro;'. $arrayImportoRichieste[$k] . '</td>';
                            echo '    <td>'.$arrayUsernameRichieste[$k].'</td>';
                            echo "    <td><input type=\"checkbox\" name=\"selezionati[]\" value=\"$arrayIDRichieste[$k]\"></td>";
                            echo '</tr>';
                        }
                    
                    echo "</table>";
                    echo "<button type=\"submit\" class=\"btn\" name=\"accetta\">Accetta selezionati</button>";
                    echo "</form>";
                }
            ?>
            
        
    </div>
    </body>
    </html>


<?php
    
    if (isset($_POST['accetta']))
    {   
        $xmlString = "";
        foreach(file("../XML/account.xml") as $nodo){
            $xmlString .= trim($nodo);
        }

        $docAccount = new DOMDocument();
        $docAccount->loadXML($xmlString);

        $rootAccount = $docAccount->documentElement;
        $listaAccount = $rootAccount->childNodes;

        if($_POST['selezionati'] == null){
            echo "<script type=\"text/javascript\">alert(\"Nessuna richiesta selezionata!!\");</script>";
        } else{

        
        foreach($_POST['selezionati'] as $c=>$sel)
        {
            $usernameCliente = $arrayUsernameRichieste[$sel-1];
            $importoDaAggiungere = $arrayImportoRichieste[$sel-1];

            $queryID = "SELECT id FROM users WHERE username = '$usernameCliente'";
                if (!$resultID = mysqli_query($con, $queryID)) 
                {
                    printf("errore nella query di ricerca ID \n");
                    exit();
                }
        
            $row = mysqli_fetch_array($resultID);
  
            if ($row) {  

                $IDCliente = $row['id'];               
            }

            for ($i=0; $i<$listaAccount->length; $i++) 
            {
                $profilo = $listaAccount->item($i);
                $idAcc = $profilo->firstChild;
                $idAccValue = $idAcc->textContent;
                if($idAccValue == $IDCliente)  
                {
                    $nome = $idAcc->nextSibling;
    
                    $cognome = $nome->nextSibling;             

                    $soldi = $cognome->nextSibling;             
                    $soldiValue = $soldi->textContent;

                    $nuoviSoldi = $docAccount->createElement("soldi", $soldiValue + $importoDaAggiungere);
                    $profilo->replaceChild($nuoviSoldi,$soldi);

                    $percorso = "../XML/account.xml";
                    $docAccount->save($percorso);
                }
            }


        }




        $k=0;
        foreach($_POST['selezionati'] as $c=>$sel)
        {   
            $arraySelezionati[$k]=$listaRichieste->item($sel-1);
            $k++;
                   
            }

        for($k=0; $k < count($arraySelezionati); $k++)
            $rootRichieste->removeChild($arraySelezionati[$k]);
       
        $listaRichieste = $rootRichieste->childNodes;
        for ($i=0; $i<$listaRichieste->length; $i++) 
        {
            $richiesta = $listaRichieste->item($i);
            $idRichiesta = $richiesta->firstChild;
            $nuovoID = $docRichiesta->createElement("idRich", $i+1);
            $richiesta->replaceChild($nuovoID,$idRichiesta);

        }

        $percorso = "../XML/richieste.xml";
        $docRichiesta->save($percorso);

        echo "<script type=\"text/javascript\">alert(\"Tutte le richieste selezionate sono state accettate.\"); location.replace(\"gestioneRichieste.php\");</script>";
    }
}
    ?>

