<!-- 
All'iterno di questo script si gestisce il sistema di visualizzazione dei clienti presenti all'interno del database
da parte di gestori e admin. Nel caso in cui la session sia di tipo gestore, potrà semplicemente essere visualizzata
la lista dei clienti presenti con alcune informazioni. Nel caso la session sia di tipo admin, oltre alle informazioni 
relative a quel cliente, sarà presente una form con un tasto per bannare il cliente. Nel caso in cui sia settata la $_POST
per il ban, semplicemente verrà settato l'attributo stato del cliente da attivo a bannato e sarà aumentato di uno il count ban.
Sarà inoltre settata la data di fine ban dell'utente, con un sistema che prende la data odierna e la incrementa di 7/14 giorni o 
30 anni in base al numero del ban di quell'utente. Tutte le informazioni vengono recuperate sempre tramite DOM dai relativi XML, e 
vengono inserite all'interno di array, sopra i quali si effettueranno dei cicli per stampare le informazioni di tutti i clienti.
-->

<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    require("cornice.php");
    if (!isset($_SESSION['success'])) {
        header('Location: home.php');  
    }
?>

<!DOCTYPE html>
    <html>
    <head>
        <title>Gestione Clienti</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>

    <?php
        $stringaXML = "";
        foreach(file("../XML/account.xml") as $nodo){
            $stringaXML .= trim($nodo);
        }
        
        $docClienti = new DOMDocument();
        $docClienti->loadXML($stringaXML);

        $rootClienti = $docClienti->documentElement;
        $listaClienti = $rootClienti->childNodes;


        
        for($i = 0; $i < $listaClienti->length; $i++){
            $cliente = $listaClienti->item($i);
            $idAcc = $cliente->firstChild;
            $idAccValue = $idAcc->textContent;

            $query = "SELECT username FROM users WHERE id = " . (int) $idAccValue; 
                        if (!$result = mysqli_query($con, $query))
                        {
                            printf("errore nella query di ricerca Username \n");
                            exit();
                        }
                        $row = mysqli_fetch_array($result);
                        if ($row) {
                            $dbUser = $row['username'];
                        }

            $nomeCliente = $idAcc->nextSibling;
            $nomeClienteValue = $nomeCliente->textContent; 

            $cognomeCliente = $nomeCliente->nextSibling;
            $cognomeClienteValue = $cognomeCliente->textContent;

            $soldiCliente = $cognomeCliente->nextSibling;
            
            $creditiCliente = $soldiCliente->nextSibling;
            $creditiClienteValue = $creditiCliente->textContent;

            $reputazioneCliente = $creditiCliente->nextSibling;
            $reputazioneClienteValue = $reputazioneCliente->textContent;

            $idAddr = $reputazioneCliente->nextSibling;

            $countBan = $idAddr->nextSibling;
            $countBanValue = $countBan->textContent;

            $dataBan = $countBan->nextSibling;
            $dataBanValue = $dataBan->textContent;

            $statoBan = $cliente->getAttribute('stato');

            $arrayClienti[$i] = $cliente;

            $arrayIDAcc[$i] = $idAccValue;
            $arrayUsername[$i] = $dbUser;
            $arrayNomi[$i] = $nomeClienteValue;
            $arrayCognomi[$i] = $cognomeClienteValue;
            $arrayCrediti[$i] = $creditiClienteValue;
            $arrayReputazioni[$i] = $reputazioneClienteValue;
            $arrayCountBan[$i] = $countBanValue;
            $arrayDateBan[$i] = $dataBanValue;
            $arrayStatoBan[$i] = $statoBan;

            $arrayCountBanReplace[$i] = $countBan;
            $arrayDateBanReplace[$i] = $dataBan;

        }
    ?>

    <?php 
        if(isset($_POST['banna'])){

            for($i = 0; $i < $listaClienti->length; $i++){

                if($_POST['banna'] == $arrayIDAcc[$i]){
                $nuovoBan = $docClienti->createElement("countBan", ($countBanValue + 1));
                $arrayClienti[$i]->replaceChild($nuovoBan, $arrayCountBanReplace[$i]);

                if($countBanValue == 0){  
                $nuovaData = $docClienti->createElement("dataBan", date('Y-m-d', strtotime($Date. ' + 7 days')));
                $arrayClienti[$i]->replaceChild($nuovaData, $arrayDateBanReplace[$i]);
                } else if ($countBanValue == 1){
                    $nuovaData = $docClienti->createElement("dataBan", date('Y-m-d', strtotime($Date. ' + 14 days')));
                    $arrayClienti[$i]->replaceChild($nuovaData, $arrayDateBanReplace[$i]);
                } else {
                    $nuovaData = $docClienti->createElement("dataBan", date('Y-m-d', strtotime($Date. ' + 11000 days')));
                    $arrayClienti[$i]->replaceChild($nuovaData, $arrayDateBanReplace[$i]);
                }

                $arrayClienti[$i]->setAttribute("stato", "bannato");  
                }
        }                             
        


    $percorso = "../XML/account.xml";
    $docClienti->save($percorso);

    $bannato = true;
}
    ?>

    <body>
    <div class="body">
    <div class="header">
  	        <h2>GESTIONE CLIENTI</h2>
        </div>
        <table>
            
            <th></th><th>Username</th><th>Nome</th><th>Cognome</th><th>Crediti</th><th>Reputazione</th><th>Stato</th><th>Data fine Ban</th>

        <?php 

        if($_SESSION['type'] == "gestore"){
            for($k=0; $k<$listaClienti->length; $k++)
                        {   
                            $indice = $k + 1;
                            echo '<tr>';
                            echo "<td>$indice)";
                            echo '    <td>'. $arrayUsername[$k] . '</td>';
                            echo '    <td>'.$arrayNomi[$k].'</td>';
                            echo '    <td>'.$arrayCognomi[$k].'</td>';
                            echo '    <td>'.$arrayCrediti[$k].'</td>';
                            echo '    <td>'.$arrayReputazioni[$k].'</td>';
                            echo '    <td>'.$arrayStatoBan[$k].'</td>';
                            echo '    <td>'.$arrayDateBan[$k].'</td>';
                            echo '</tr>';
                        }
        }

        if($_SESSION['type'] == "amministratore"){
            for($k=0; $k<$listaClienti->length; $k++)
                        { 
                            $indice = $k + 1;
                            echo "<form method=\"post\" action=\"storicoClienti.php\">";
                            echo '<tr>';
                            echo "<td>$indice)";
                            echo '    <td>'. $arrayUsername[$k] . '</td>';
                            echo '    <td>'.$arrayNomi[$k].'</td>';
                            echo '    <td>'.$arrayCognomi[$k].'</td>';
                            echo '    <td>'.$arrayCrediti[$k].'</td>';
                            echo '    <td>'.$arrayReputazioni[$k].'</td>';
                            echo '    <td>'.$arrayStatoBan[$k].'</td>';
                            echo '    <td>'.$arrayDateBan[$k].'</td>';
                            if($arrayStatoBan[$k] == "attivo"){
                            echo "    <td><button type=\"submit\" class=\"btn\" name=\"banna\" value=\"$arrayIDAcc[$k]\">BANNA</button></td>";
                            }
                            echo '</tr></form>';                          
                        
                        }
                        if($bannato == true){
                            $bannato = false;
                            echo "<script type=\"text/javascript\">alert(\"Utente bannato!\"); location.replace(\"storicoClienti.php\");</script>";
                            
                        }
                        
                    }
        
        ?>


        </table>
    </div>
    </body>
    </html>