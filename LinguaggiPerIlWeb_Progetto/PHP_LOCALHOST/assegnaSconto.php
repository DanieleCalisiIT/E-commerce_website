<!-- 
In questo script si gestisce la creazione di uno sconto, e la sua assegnazione ad uno o piu' giochi nel catalogo, da parte di un gestore. 
    Riassumendo, la struttura di questo script è: 
    1: Gestione form 
    2: Form
    3: Avvisi

    La gestione della form è stata fatta con un if(isset()) in cui gestire la creazione/assegnazione.
    Prima di effettuare la creazione si verifica se tutti i campi sono stati inseriti correttamente.
    Poi si procede a creare il nuovo sconto, e ad assegnarlo a tutti i giochi selezionati.

    La form mostra tutti i campi da riempire. 
    Se il gestore prova ad aggiungere un gioco, ma si verifica un errore, i campi da lui inseriti rimangono scritti cosi da facilitarne la modifica.

    Gli eventuali avvisi vengono "calcolati" nella gestione della form, e poi mostrati dopo di essa, così da non bloccare il caricamento di
    quest' ultima.
-->

<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    require("./cornice.php");
    if (!isset($_SESSION['success'])) {
        header('Location: home.php');  
    }
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Assegna sconto</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<?php
            $xmlProdotti = "";
            foreach(file("../XML/prodotti.xml") as $nodo){
                $xmlProdotti .= trim($nodo);
            }

            $docProdotti = new DOMDocument();
            $docProdotti->loadXML($xmlProdotti);

            $rootProdotti = $docProdotti->documentElement;
            $listaProdotti = $rootProdotti->childNodes;

            for ($i=0; $i<$listaProdotti->length; $i++) 
            {
                $prodotto = $listaProdotti->item($i);

                $IDProdotto = $prodotto->firstChild;
                $IDProdottoValue = $IDProdotto->textContent;

                $nomeProdotto = $IDProdotto->nextSibling;
                $nomeProdottoValue = $nomeProdotto->textContent;

                $arrayIDProdotti[$i] = $IDProdottoValue;
                $arrayProdotti[$i] = $nomeProdottoValue;
            }
        ?>

<?php
            if (isset($_POST['assegna'])) 
            { 
                
                if ((!empty($_POST['percentuale'])) && (!empty($_POST['repNecess'])) && (!empty($_POST['credNecess'])) && (!empty($_POST['scadenza'])) && (!empty($_POST['selezionati'])))
                {
                    $xmlSconti = "";
                    foreach(file("../XML/sconti.xml") as $nodo){
                        $xmlSconti .= trim($nodo);
                    }

                    $docSconti = new DOMDocument();
                    $docSconti->loadXML($xmlSconti);

                    $rootSconti = $docSconti->documentElement;
                    $listaSconti = $rootSconti->childNodes;

                    $IDSconto = (($listaSconti->length) + 1);

                    $nuovoSconto = $docSconti->createElement("sconto");
                    $rootSconti->appendChild($nuovoSconto);

                    $nuovoSconto->setAttribute("stato", "attivo");

                    $nuovoIDSconto = $docSconti->createElement("idSconto", $IDSconto);
                    $nuovoSconto->appendChild($nuovoIDSconto);
                    $nuovaPercentuale = $docSconti->createElement("percentuale", $_POST['percentuale']);
                    $nuovoSconto->appendChild($nuovaPercentuale);
                    $nuovaRepNecess = $docSconti->createElement("repNecess", $_POST['repNecess']);
                    $nuovoSconto->appendChild($nuovaRepNecess);
                    $nuoviCredNecess = $docSconti->createElement("credNecess", $_POST['credNecess']);
                    $nuovoSconto->appendChild($nuoviCredNecess);
                    $nuovaScadenza = $docSconti->createElement("scadenza", $_POST['scadenza']);
                    $nuovoSconto->appendChild($nuovaScadenza);

                    $percorso = "../XML/sconti.xml";
                    $docSconti->save($percorso);

                    for ($i=0; $i<$listaProdotti->length; $i++) 
                    {
                        $prodotto = $listaProdotti->item($i);

                        $IDProdotto = $prodotto->firstChild;
                        $IDProdottoValue = $IDProdotto->textContent;
                        
                        $k=0;
                        foreach($_POST['selezionati'] as $c=>$sel)
                        {   
                            if($IDProdottoValue == $sel)
                            {
                                $nuovoIDSconto = $docProdotti->createElement("idSconto", $IDSconto);
                                $prodotto->appendChild($nuovoIDSconto); 
                            }
                        }
                    }

                    $percorso = "../XML/prodotti.xml";
                    $docProdotti->save($percorso);

                    $scontoAssegnato = true;

                }
            }
        ?>

<body>
    <div class="body">
        <div class="header">
  	        <h2>Assegna uno sconto ai giochi</h2>
        </div>

        

        <form method="post" action="assegnaSconto.php">
            <div class="input-group">
            <?php if(isset($_POST['assegna'])){ if (empty($_POST['percentuale'])) {
                echo "<p id= \"error\">Percentuale richiesta</p>";
            }} ?>
                <label>Percentuale</label>
                <input type="number" name="percentuale" min="5" max="90" value="<?php if(isset($_POST['assegna'])){ echo $_POST['percentuale'];}?>">
            </div>
            <div class="input-group">
            <?php if(isset($_POST['assegna'])){ if (empty($_POST['repNecess'])) {
                echo "<p id= \"error\">Reputazione necessaria richiesta</p>";
            }} ?>
                <label>Reputazione necessaria</label>
                <input type="number" name="repNecess" min="0" value="<?php if(isset($_POST['assegna'])){ echo $_POST['repNecess'];}?>">
            </div>
            <div class="input-group">
            <?php if(isset($_POST['assegna'])){ if (empty($_POST['credNecess'])) {
                echo "<p id= \"error\">Crediti necessari richiesti</p>";
            }} ?>
                <label>Crediti necessari</label>
                <input type="number" name="credNecess" min="0" value="<?php if(isset($_POST['assegna'])){ echo $_POST['credNecess'];}?>">
            </div>
            <div class="input-group"><?php if(isset($_POST['assegna'])){ if (empty($_POST['scadenza'])) {
                echo "<p id= \"error\">Scadenza richiesta</p>";
            }} ?>
                <label>Scadenza</label>
                <input type="date" name="scadenza" min= "<?php echo date('Y-m-d');?>" value="<?php if(isset($_POST['assegna'])){ echo $_POST['scadenza'];}?>">
            </div>
            <?php if(isset($_POST['assegna'])){ if (empty($_POST['selezionati'])) {
                echo "<p id= \"error\">Lo sconto deve essere assegnato ad almeno un gioco</p>";
            }} ?>
                <label>Scegli i giochi a cui assegnare lo sconto</label>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Assegna</th>
                    </tr>
                    <?php 
                        for($k=0; $k<$listaProdotti->length; $k++)
                            { 
                                echo '<tr>';
                                echo '    <td>'. $arrayIDProdotti[$k] . '</td>';
                                echo '    <td>'.$arrayProdotti[$k].'</td>';
                                echo "    <td><input type=\"checkbox\" name=\"selezionati[]\" value=\"$arrayIDProdotti[$k]\"></td>";
                                echo '</tr>';
                            }
                    ?>
                </table>
            <div class="input-group">
  	            <button type="submit" class="btn" name="assegna">Assegna</button>
  	        </div>
        </form>

        <?php
            if ($scontoAssegnato == true){
                $scontoAssegnato = false;
                echo "<script type=\"text/javascript\">alert(\"Sconto assegnato con successo!\"); location.replace(\"assegnaSconto.php\");</script>";
            }
        ?>

        
    </div>
</body>
</html>