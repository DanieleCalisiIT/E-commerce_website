<!-- 
Si accede a questa pagina solo quando l’amministratore, all’interno della sezione domande del forum, clicca “Eleva domanda a FAQ”
 sotto una domanda da lui scelta. In questo modo viene mandata una richiesta all’interno di una SESSION che contiene, nell’altra pagina,
  relativa alle domande, la richiesta in POST con il testo della domanda che si vuole elevare. Sarà presente una form all’interno della
   quale l’admin dovrà inserire la risposta a quella domanda e cliccando sul bottone crea, la form sarà sottomessa e la FAQ sarà aggiunta
    al documento XML relativo alle FAQ. Dopodichè la SESSION ricevuta dalla pagina precedente sarà distrutta uguagliandola a NULL.
-->

<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    require("./cornice.php");
    if ((!isset($_SESSION['success'])) || (!isset($_SESSION['testoDomandaElevata']))) {
        header('Location: home.php');  
    }
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Crea FAQ</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<?php
            if (isset($_POST['crea'])) 
            {
                
                if (!empty($_POST['testo']))
                {
                    $stringaXML = "";
                    foreach ( file("../XML/faq.xml") as $nodo ) 
                    {
                        $stringaXML .= trim($nodo);
                    }
    
                    $docFAQ = new DOMDocument();
                    $docFAQ->loadXML($stringaXML);
                  
                    $rootFAQ = $docFAQ->documentElement;
                    $listaFAQ = $rootFAQ->childNodes;
                    $IDFAQ = (($listaFAQ->length) + 1);

                    $nuovaFAQ = $docFAQ->createElement("domanda");
                    $rootFAQ->appendChild($nuovaFAQ);

                    $nuovoIDFAQ = $docFAQ->createElement("idFaq", $IDFAQ);
                    $nuovaFAQ->appendChild($nuovoIDFAQ);
                    $nuovaDomandaFAQ = $docFAQ->createElement("domandaFAQ", $testoNuovaFAQ);
                    $nuovaFAQ->appendChild($nuovaDomandaFAQ);
                    $nuovaRispostaFAQ = $docFAQ->createElement("risposta", $_POST['testo']);
                    $nuovaFAQ->appendChild($nuovaRispostaFAQ);

                    $percorso = "../XML/faq.xml";
                    $docFAQ->save($percorso);

                    $_SESSION['testoDomandaElevata'] = NULL;

                    //avviso di creazione nuova FAQ e header altrove
                    $FAQCreata = true;
                }
            }
        ?>
<body>
    <div class="body">
        <div class="header">
  	        <h2>Crea una nuova FAQ</h2>
        </div>

        <form method="post" action="creaFAQ.php">
            <?php 
                $testoNuovaFAQ = $_SESSION['testoDomandaElevata'];
                echo "<h2 id=\"nuovaFAQ\"> <span id=\"newFAQ\">Nuova FAQ:</span> $testoNuovaFAQ</h2>"; 
            ?>
            <div class="input-group">
            <?php 
                    if(isset($_POST['crea']))
                    { 
                        if (empty($_POST['testo'])) 
                            echo "<p id= \"error\">Testo risposta richiesta</p>";
                    } 
                ?>
                <label>Risposta alla nuova FAQ</label>
                <input type="text" name="testo">
            </div>
            <div class="input-group">
  	            <button type="submit" class="btn" name="crea">Crea FAQ</button>
  	        </div>
        </form>
    </div>
</body>
            <?php
                if($FAQCreata == true){
                    $FAQCreata = false;
                    echo "<script type=\"text/javascript\">alert(\"La domanda è stata elevata a FAQ!\"); location.replace(\"visualizzaFAQ.php\");</script>";
                }
            ?>
</html>