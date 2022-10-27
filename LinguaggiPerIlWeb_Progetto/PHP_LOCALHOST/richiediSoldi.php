<!--
All'interno di questo script viene gestito il sistema tramite il quale i clienti fanno richieste
di ricarica portafogli agli admin del sito. E' presente una semplice form che una volta riempita e 
premuto il tasto di submit, fa si che venga gestita la $_POST di invio richiesta di ricarica, all'interno 
della quale si gestisce la creazione, all'interno del file xml richieste, di un nuovo elemento, quindi
vengono settati i campi dell'elemento tramite i valori ricevuti dalla form. Il tutto è gestito sempre
tamite il metodo DOM.
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
    <title>Modifica Profilo</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <div class="body">
        <div class="header">
  	        <h2>Ricarica Portafoglio</h2>
        </div>


        <form method="post" action="richiediSoldi.php">
            <div class="input-group">
                <label>Importo da aggiungere</label>
                <input type="number" step="0.01" name="importo">
            </div>
            <div class="input-group">
  	            <button type="submit" class="btn" name="invio">Invia Richiesta</button>
  	        </div>
        </form>

        <?php
            if (isset($_POST['invio'])) {
                
                if (empty($_POST['importo']))
                    echo "<p id= \"error\">Importo richiesto</p>";
                else
                {
                    $stringaXML = "";
                    foreach ( file("../XML/richieste.xml") as $nodo ) 
                    {
                        $stringaXML .= trim($nodo);
                    }
    
                    $docRichieste = new DOMDocument();
                    $docRichieste->loadXML($stringaXML);
                  
                    $rootRichieste = $docRichieste->documentElement;
                    $listaRichieste = $rootRichieste->childNodes;
                    $IDRich = (($listaRichieste->length) + 1);
                    $idAcc = $_SESSION['id'];

                    $nuovaRichiesta = $docRichieste->createElement("richiesta");
                    $rootRichieste->appendChild($nuovaRichiesta);

                    $nuovoID = $docRichieste->createElement("idRich", $IDRich);
                    $nuovaRichiesta->appendChild($nuovoID);
                    $nuovoImporto = $docRichieste->createElement("importo", $_POST['importo']);
                    $nuovaRichiesta->appendChild($nuovoImporto);
                    $nuovoIDAcc = $docRichieste->createElement("idAcc", $idAcc);
                    $nuovaRichiesta->appendChild($nuovoIDAcc);

                    $percorso = "../XML/richieste.xml";
                    $docRichieste->save($percorso);

                    //avviso di richiesta inoltrata
                    echo "<script type=\"text/javascript\">alert(\"La tua richiesta è stata inoltrata agli amministratori del sito.\");</script>";
                }

            }
        ?>

    </div>
</body>
</html>