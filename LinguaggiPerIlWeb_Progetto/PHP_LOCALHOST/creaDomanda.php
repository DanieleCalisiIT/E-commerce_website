<!-- 
Questo script, molto simile a quello riguardante le recensioni, permette di creare una nuova
domanda all'interno del forum da parte di un cliente, tramite una form. Tutti i campi della form
devono essere riempiti. Se è settata la session per la creazione della domanda, viene creato un nuovo
elemento domanda nell'XML relativo alle domande, in cui, tramite gli appendChild, verranno inseriti
i valori presi dalla form. Lo stato della domanda appena creata sarà inoltre impostato come aperto.
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
    <title>Crea domanda</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<?php
            if (isset($_POST['pubblica'])) {
                
                if (!empty($_POST['testo']))
                {
                    $stringaXML = "";
                    foreach ( file("../XML/domande.xml") as $nodo ) 
                    {
                        $stringaXML .= trim($nodo);
                    }
    
                    $docDomande = new DOMDocument();
                    $docDomande->loadXML($stringaXML);
                  
                    $rootDomande = $docDomande->documentElement;
                    $listaDomande = $rootDomande->childNodes;
                    $IDDomanda = (($listaDomande->length) + 1);

                    $nuovaDomanda = $docDomande->createElement("domanda");
                    $rootDomande->insertBefore($nuovaDomanda, $rootDomande->firstChild);

                    $nuovaDomanda->setAttribute("stato", "aperta");
                    $nuovoIDDomanda = $docDomande->createElement("idDom", $IDDomanda);
                    $nuovaDomanda->appendChild($nuovoIDDomanda);
                    $nuovaDataDomanda = $docDomande->createElement("dataCreaz", date('Y-m-d'));
                    $nuovaDomanda->appendChild($nuovaDataDomanda);
                    $nuovoCreatoreDomanda = $docDomande->createElement("creatore", $username);
                    $nuovaDomanda->appendChild($nuovoCreatoreDomanda);
                    $nuovoTestoDomanda = $docDomande->createElement("testo", $_POST['testo']);
                    $nuovaDomanda->appendChild($nuovoTestoDomanda);

                    $percorso = "../XML/domande.xml";
                    $docDomande->save($percorso);

                    $domandaCreata = true;
                }
            }
        ?>
<body>
    <div class="body">
        <div class="header">
  	        <h2>Crea una nuova domanda</h2>
        </div>

        <form method="post" action="creaDomanda.php">
            <div class="input-group">
            <?php 
                    if(isset($_POST['pubblica']))
                    { 
                        if (empty($_POST['testo'])) 
                            echo "<p id= \"error\">Testo domanda richiesta</p>";
                    } 
                ?>
                <label>Testo nuova domanda</label>
                <input type="text" name="testo">
            </div>
            <div class="input-group">
  	            <button type="submit" class="btn" name="pubblica">Pubblica domanda</button>
  	        </div>
        </form>
    </div>
</body>
<?php
            if($domandaCreata == true){
                $domandaCreata = false;
                echo "<script type=\"text/javascript\">alert(\"Domanda creata con successo!\"); location.replace(\"domande.php\");</script>";
            }
        ?>
</html>
