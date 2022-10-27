<!--
Questo script permette semplicemente di creare una recensione per un dato gioco, tramite una form. 
E' presente una select che mostra i giochi presenti in catalogo, presi dal DOM, e all'interno della 
form può essere scritto del testo che verrà inserito all'interno dell XML delle recensioni tramite
il metodo appendChild. Tutti i campi della form devono essere presenti. La valutazione va da 1 a 5, 
con incrementi di 0.5 punti. 
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
    <title>Crea recensione</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<?php
    if (isset($_POST['pubblica'])) {
                
    if ((!empty($_POST['testo'])) && (!empty($_POST['valutazione'])) && ($_POST['gioco'] != ""))
    {
        $stringaXML = "";
        foreach ( file("../XML/recensioni.xml") as $nodo ) 
        {
            $stringaXML .= trim($nodo);
        }
    
        $docRecensioni = new DOMDocument();
        $docRecensioni->loadXML($stringaXML);
                  
        $rootRecensioni = $docRecensioni->documentElement;
        $listaRecensioni = $rootRecensioni->childNodes;
        $IDRecensione = (($listaRecensioni->length) + 1);

        $nuovaRecensione = $docRecensioni->createElement("recensione");
        $rootRecensioni->insertBefore($nuovaRecensione, $rootRecensioni->firstChild);

        $nuovoIDRecensione = $docRecensioni->createElement("idRec", $IDRecensione);
        $nuovaRecensione->appendChild($nuovoIDRecensione);
        $nuovaDataRecensione = $docRecensioni->createElement("dataCreaz", date('Y-m-d'));
        $nuovaRecensione->appendChild($nuovaDataRecensione);
        $nuovoCreatoreRecensione = $docRecensioni->createElement("creatore", $username);
        $nuovaRecensione->appendChild($nuovoCreatoreRecensione);
        $nuovoTestoRecensione = $docRecensioni->createElement("testo", $_POST['testo']);
        $nuovaRecensione->appendChild($nuovoTestoRecensione);
        $nuovaValutazioneRecensione = $docRecensioni->createElement("valutazione", $_POST['valutazione']);
        $nuovaRecensione->appendChild($nuovaValutazioneRecensione);
        $nuovoGiocoRecensione = $docRecensioni->createElement("gioco", $_POST['gioco']);
        $nuovaRecensione->appendChild($nuovoGiocoRecensione);

        $percorso = "../XML/recensioni.xml";
        $docRecensioni->save($percorso);

        $recensioneCreata = true;
        }
    }
?>

<body>
    <div class="body">
        <div class="header">
  	        <h2>Crea una nuova recensione</h2>
        </div>
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

                $nomeProdotto = $IDProdotto->nextSibling;
                $arrayProdotti[$i] = $nomeProdotto->textContent;
            }
        ?>
        <form method="post" action="creaRecensione.php">
            <div class="input-group">
            <?php 
                    if(isset($_POST['pubblica']))
                    { 
                        if ($_POST['gioco'] == "") 
                            echo "<p id= \"error\">Scelta del gioco richiesta</p>";
                    } 
                ?>
                <label>Gioco da recensire</label>
                <select name="gioco">
                    
                    <?php 
                        echo '<option value="';
                        if($_POST['gioco'] != ""){
                            echo $_POST['gioco'];
                        }
                        echo '" selected>';
                        if($_POST['gioco'] != ""){
                            echo $_POST['gioco'];
                        } else {
                            echo "Seleziona un gioco...";
                        }
                        echo '</option>';
                        for($k=0; $k<$listaProdotti->length; $k++)
                            echo"<option value=\"$arrayProdotti[$k]\">$arrayProdotti[$k]</option>";
                    ?>
                </select>
            </div>
            <div class="input-group">
            <?php 
                    if(isset($_POST['pubblica']))
                    { 
                        if (empty($_POST['testo'])) 
                            echo "<p id= \"error\">Testo recensione richiesto</p>";
                    } 
                ?>
                <label>Testo nuova recensione</label>
                <textarea name="testo" rows="20" cols="60" value="<?php if(isset($_POST['testo'])){ echo $_POST['testo'];}?>"><?php if(isset($_POST['testo'])){ echo $_POST['testo'];}?></textarea>
            </div>
            <div class="input-group">
            <?php 
                    if(isset($_POST['pubblica']))
                    { 
                        if (empty($_POST['valutazione'])) 
                            echo "<p id= \"error\">Valutazione richiesta</p>";
                    } 
                ?>
                <label>Valutazione (da 1 a 5)</label>
                <input type="number" step="0.5" name="valutazione" min="1" max="5" value="<?php if(isset($_POST['valutazione'])){ echo $_POST['valutazione'];}?>">
            </div>
            <div class="input-group">
  	            <button type="submit" class="btn" name="pubblica">Pubblica recensione</button>
  	        </div>
        </form>
    </div>
</body>
<?php
            if($recensioneCreata == true){
                $recensioneCreata = false;
                echo "<script type=\"text/javascript\">alert(\"Recensione creata con successo!\"); location.replace(\"recensioni.php\");</script>";
            }
        ?>
</html>