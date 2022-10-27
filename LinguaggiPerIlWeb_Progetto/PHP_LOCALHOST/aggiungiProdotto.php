<!-- 
In questo script si gestisce l' aggiunta di un prodotto al catalogo, da parte di un gestore. 
Riassumendo, la struttura di questo script è: 
    1: Javascript
    2: Gestione form 
    3: Form
    4: Avvisi

    Il javascript è stato usato solamente per far apparire o scomparire dei pezzi di form a seconda dei bottoni cliccati dall'utente.
    (ad esempio far apparire la descrizione di una categoria nel momento in cui la si seleziona)

    La gestione della form è stata fatta con un if(isset()) in cui gestire l'aggiunta.
    Prima di effettuare l'aggiunta si verifica se tutti i campi sono stati inseriti correttamente.
    Poi si verifica se non esiste già un gioco con il nome, anno e lingua di quello che si vuole aggiungere.
    Poi si procede a creare il nuovo prodotto, eventualmente creando anche una nuova categoria/casaEditrice.

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
    <title>Aggiungi prodotto</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <script>
        function funzioneCategoria(that) 
        {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() 
            {
                if (this.readyState == 4 && this.status == 200) {
                    myFunction(this);
                }
            };
            xhttp.open("GET", "../XML/categorie.xml", true);
            xhttp.send();

            function myFunction(xml) 
            {
                var xmlDoc = xml.responseXML;
                var i=0;
                for(i;i< xmlDoc.getElementsByTagName("nome").length ;i++)
                {   
                    
                    if (that.value == xmlDoc.getElementsByTagName("nome")[i].childNodes[0].nodeValue) 
                    {
                        
                        document.getElementById(i).style.display = "block";
                    }
                    else 
                        document.getElementById(i).style.display = "none";
                }
                if (that.value == "AltraCategoria")
                {
                    document.getElementById("seAltraCategoria").style.display = "block";
                    document.getElementById("seAltraCategoriaDescr").style.display = "block";
                }
                else
                {
                    document.getElementById("seAltraCategoria").style.display = "none";
                    document.getElementById("seAltraCategoriaDescr").style.display = "none";
                }
            }
        }

        function funzioneCasaEditrice(that)
        {
            if (that.value == "AltraCasa")
                document.getElementById("seAltraCasa").style.display = "block";
            else
                document.getElementById("seAltraCasa").style.display = "none";
        }
    </script>
</head>

<?php
            $stringaXML = "";
            foreach ( file("../XML/caseEditrici.xml") as $nodo )   
            {
                $stringaXML .= trim($nodo);
            }
    
            $docCaseEditrici = new DOMDocument();
            $docCaseEditrici->loadXML($stringaXML);
                  
            $rootCaseEditrici = $docCaseEditrici->documentElement;
            $listaCaseEditrici = $rootCaseEditrici->childNodes;

            for ($i=0; $i<$listaCaseEditrici->length; $i++) 
            {
                $casaEditrice = $listaCaseEditrici->item($i);

                $idCasa = $casaEditrice->firstChild;
                $idCasaValue = $idCasa->textContent;

                $nomeCasa = $idCasa->nextSibling;
                $nomeCasaValue = $nomeCasa->textContent;
                
                $arrayIDCaseEditrici[$i] = $idCasaValue;
                $arrayCaseEditrici[$i] = $nomeCasaValue;
            }

            $stringaXML = "";
            foreach ( file("../XML/categorie.xml") as $nodo )   
            {
                $stringaXML .= trim($nodo);
            }
    
            $docCategorie = new DOMDocument();
            $docCategorie->loadXML($stringaXML);
                  
            $rootCategorie = $docCategorie->documentElement;
            $listaCategorie = $rootCategorie->childNodes;

            for ($i=0; $i<$listaCategorie->length; $i++) 
            {
                $categoria = $listaCategorie->item($i);

                $IDCategoria = $categoria->firstChild;
                $IDCategoriaValue = $IDCategoria->textContent;
                
                $nomeCategoria = $IDCategoria->nextSibling;
                $nomeCategoriaValue = $nomeCategoria->textContent;

                $descrCategoria = $nomeCategoria->nextSibling;
                $descrCategoriaValue = $descrCategoria->textContent;

                $arrayIDCategorie[$i] = $IDCategoriaValue;
                $arrayCategorie[$i] = $nomeCategoriaValue;
                $arrayDescrCategorie[$i] = $descrCategoriaValue;

                
            }
        ?>

<?php
            if (isset($_POST['aggiungi'])) 
            { 
                
                //validazione form
                if ((!empty($_POST['nome'])) && ($_POST['lingua'] != "") && (!empty($_POST['annoProd'])) && (!empty($_POST['prezzo'])) &&
                ($_FILES['img']['name'] != "") && ($_POST['categoria'] != "") && ($_POST['casaEditrice'] != "") && ((($_POST['categoria'] == "AltraCategoria") && (!empty($_POST['nomeCategoria']))) || ($_POST['categoria'] != "AltraCategoria")) && 
                ((($_POST['categoria'] == "AltraCategoria") && (!empty($_POST['descrizioneCategoria']))) || ($_POST['categoria'] != "AltraCategoria")) && 
                ((($_POST['casaEditrice'] == "AltraCasa") && (!empty($_POST['nomeCasaEditrice']))) || ($_POST['casaEditrice'] != "AltraCasa")))
                {
                    $xmlProdotti = "";
                    foreach(file("../XML/prodotti.xml") as $nodo){
                        $xmlProdotti .= trim($nodo);
                    }

                    $docProdotti = new DOMDocument();
                    $docProdotti->loadXML($xmlProdotti);

                    $rootProdotti = $docProdotti->documentElement;
                    $listaProdotti = $rootProdotti->childNodes;
                    

                    //controllo se non ci sia già un gioco con stesso nome,anno e lingua
                    $i=0;
                    $flagProdotto=0;
                    while(($i<$listaProdotti->length) && ($flagProdotto == 0))
                    {
                        $Prodotto = $listaProdotti->item($i);

                        $idProdotto = $Prodotto->firstChild;

                        $nomeProdotto = $idProdotto->nextSibling;
                        $nomeProdottoValue = $nomeProdotto->textContent;

                        $idCategProd = $nomeProdotto->nextSibling;
                        
                        $idCasaProd = $idCategProd->nextSibling;

                        $linguaProdotto = $idCasaProd->nextSibling;
                        $linguaProdottoValue = $linguaProdotto->textContent;

                        $annoProdotto = $linguaProdotto->nextSibling;
                        $annoProdottoValue = $annoProdotto->textContent;

                        if(($nomeProdottoValue == $_POST['nome']) && ($linguaProdottoValue == $_POST['lingua']) && ($annoProdottoValue == $_POST['annoProd']))
                            $flagProdotto=1;
                        
                        $i++;
                    }

                    if($flagProdotto != 1){
                        $IDProd = (($listaProdotti->length) + 1);

                        $nuovoProdotto = $docProdotti->createElement("gioco");
                        $rootProdotti->appendChild($nuovoProdotto);

                        $nuovoID = $docProdotti->createElement("idProd", $IDProd);
                        $nuovoProdotto->appendChild($nuovoID);
                        $nuovoNome = $docProdotti->createElement("nome", $_POST['nome']);
                        $nuovoProdotto->appendChild($nuovoNome);

                        if($_POST['categoria'] == "AltraCategoria")
                        {
                            $IDCat = (($listaCategorie->length) + 1);

                            $nuovaCategoria = $docCategorie->createElement("categoria");
                            $rootCategorie->appendChild($nuovaCategoria);
                            $nuovoIDCat = $docCategorie->createElement("idCateg", $IDCat);
                            $nuovaCategoria->appendChild($nuovoIDCat);
                            $nuovoNomeCat = $docCategorie->createElement("nome", $_POST['nomeCategoria']);
                            $nuovaCategoria->appendChild($nuovoNomeCat);
                            $nuovaDescrCat = $docCategorie->createElement("descrizione", $_POST['descrizioneCategoria']);
                            $nuovaCategoria->appendChild($nuovaDescrCat);

                            $nuovoIDCategoria = $docProdotti->createElement("idCateg", $IDCat);
                            $nuovoProdotto->appendChild($nuovoIDCategoria);

                            $percorso = "../XML/categorie.xml";
                            $docCategorie->save($percorso);

                        }
                        else
                        {
                            for($j=0; $j < count($arrayCategorie); $j++)
                            {
                                if($arrayCategorie[$j] == $_POST['categoria'])
                                    $IDCat = $arrayIDCategorie[$j];
                            }

                            $nuovoIDCategoria = $docProdotti->createElement("idCateg", $IDCat);
                            $nuovoProdotto->appendChild($nuovoIDCategoria);

                        }

                        if($_POST['casaEditrice'] == "AltraCasa")
                        {
                            $IDCas = (($listaCaseEditrici->length) + 1);

                            $nuovaCasaEditrice = $docCaseEditrici->createElement("casaEditrice");
                            $rootCaseEditrici->appendChild($nuovaCasaEditrice);
                            $nuovoIDCas = $docCaseEditrici->createElement("idCasa", $IDCas);
                            $nuovaCasaEditrice->appendChild($nuovoIDCat);
                            $nuovoNomeCat = $docCaseEditrici->createElement("nome", $_POST['nomeCasaEditrice']);
                            $nuovaCasaEditrice->appendChild($nuovoNomeCat);

                            $nuovoIDCasa = $docProdotti->createElement("idCasa", $IDCas);
                            $nuovoProdotto->appendChild($nuovoIDCasa);

                            $percorso = "../XML/caseEditrici.xml";
                            $docCaseEditrici->save($percorso);

                        }
                        else
                        {
                            for($j=0; $j < count($arrayCaseEditrici); $j++)
                            {
                                if($arrayCaseEditrici[$j] == $_POST['casaEditrice'])
                                    $IDCas = $arrayIDCaseEditrici[$j];
                            }

                            $nuovoIDCasa = $docProdotti->createElement("idCasa", $IDCas);
                            $nuovoProdotto->appendChild($nuovoIDCasa);

                        }

                        $nuovaLingua = $docProdotti->createElement("lingua", $_POST['lingua']);
                        $nuovoProdotto->appendChild($nuovaLingua);
                        $nuovoAnnoProd = $docProdotti->createElement("annoProd", $_POST['annoProd']);
                        $nuovoProdotto->appendChild($nuovoAnnoProd);
                        $nuovoPrezzo = $docProdotti->createElement("prezzo", $_POST['prezzo']);
                        $nuovoProdotto->appendChild($nuovoPrezzo);

                        $location = '../images/' . $_POST['nome'] . $IDProd . '.png'; 
                        move_uploaded_file($_FILES['img']['tmp_name'], $location);

                        $nuovaImg = $docProdotti->createElement("img", $location);
                        $nuovoProdotto->appendChild($nuovaImg);

                        $percorso = "../XML/prodotti.xml";
                        $docProdotti->save($percorso);

                        $aggiunto = true;
                        
                    }   
                }
            }


        ?>



<body>
    <div class="body">
        <div class="header">
  	        <h2>Aggiungi un gioco al catalogo</h2>
        </div>

        
            <?php
            if($flagProdotto == 1){
                echo "<p id= \"error\" style=\"text-align: center; font-size: 100%;\">Esiste gi&agrave; un gioco con questo nome,lingua e anno </p>";
            }
            ?>
        <form method="post" action="aggiungiProdotto.php" enctype="multipart/form-data">
            <div class="input-group">
                <?php 
                    if(isset($_POST['aggiungi']))
                    { 
                        if (empty($_POST['nome'])) 
                            echo "<p id= \"error\">Nome richiesto</p>";
                    } 
                ?>
                <label>Nome</label>
                <input type="text" name="nome" value="<?php if(isset($_POST['nome'])){ echo $_POST['nome'];}?>">
            </div>
            <div class="input-group">
                <?php 
                    if(isset($_POST['aggiungi']))
                    { 
                        if ($_POST['categoria'] == "") 
                            echo "<p id= \"error\">Categoria richiesta</p>";
                    }
                ?>
                <label>Categoria</label>
                <select name="categoria" onchange="funzioneCategoria(this);" value="<?php if($_POST['categoria'] != ""){echo $_POST['categoria'];}?>">
                    <?php 
                        echo '<option value="';
                        if($_POST['categoria'] != ""){
                            echo $_POST['categoria'];
                        }
                        echo '" selected>';
                        if($_POST['categoria'] != ""){
                            echo $_POST['categoria'];
                        } else {
                            echo "Seleziona una categoria...";
                        }
                        echo '</option>';
                        for($k=0; $k<$listaCategorie->length; $k++)
                            echo"<option value=\"$arrayCategorie[$k]\">$arrayCategorie[$k]</option>";
                        echo "<option value=\"AltraCategoria\">Altra categoria</option>";
                    ?>
                </select>
            </div>
            <div class="input-group">
                <?php 
                    if(isset($_POST['aggiungi']))
                    { 
                        if(($_POST['categoria'] == "AltraCategoria") && (empty($_POST['nomeCategoria']))) 
                            echo "<p id= \"error\">Nome nuova Categoria richiesta</p>";
                    } 
                ?>
                <input id="seAltraCategoria" style="<?php if(empty($_POST['nomeCategoria'])) { echo "display: none;";} ?>" 
                type="text" name="nomeCategoria" placeholder="Nome nuova categoria..." value="<?php if(($_POST['categoria'] == "AltraCategoria") && (!empty($_POST['nomeCategoria']))){echo $_POST['nomeCategoria'];}?>"/>
            </div>
            <div class="input-group">
                <?php 
                    if(isset($_POST['aggiungi']))
                    { 
                        if(($_POST['categoria'] == "AltraCategoria") && (empty($_POST['descrizioneCategoria']))) 
                            echo "<p id= \"error\">Descrizione nuova Categoria richiesta</p>";
                    } 
                ?>
                <textarea id="seAltraCategoriaDescr" name="descrizioneCategoria" rows="10" cols="50" style="<?php if(empty($_POST['nomeCategoria'])) { echo "display: none;";} ?>"
                value="<?php if(($_POST['categoria'] == "AltraCategoria") && (!empty($_POST['descrizioneCategoria']))){echo $_POST['descrizioneCategoria'];}?>">
                <?php if(($_POST['categoria'] == "AltraCategoria") && (!empty($_POST['descrizioneCategoria']))){echo $_POST['descrizioneCategoria'];} ?></textarea>
            </div>
            <div id="descrizioneCategoria">
                <?php
                    for($k=0; $k<$listaCategorie->length; $k++)
                        echo"<p style=\"display: none;\" class=\"descrizione\" id=\"$k\">$arrayDescrCategorie[$k]</p>";
                ?>
            </div>
            <div class="input-group">
                <?php 
                    if(isset($_POST['aggiungi']))
                    { 
                        if ($_POST['casaEditrice'] == "") 
                            echo "<p id= \"error\">Casa Editrice richiesta</p>";
                    } 
                ?>
                <label>Casa Editrice</label>
                <select name="casaEditrice"  onchange="funzioneCasaEditrice(this);">
                    <?php 
                        echo '<option value="';
                        if($_POST['casaEditrice'] != ""){
                            echo $_POST['casaEditrice'];
                        }
                        echo '" selected>';
                        if($_POST['casaEditrice'] != ""){
                            echo $_POST['casaEditrice'];
                        } else {
                            echo "Seleziona una casa editrice...";
                        }
                        echo '</option>';
                        for($k=0; $k<$listaCaseEditrici->length; $k++)
                            echo"<option value=\"$arrayCaseEditrici[$k]\">$arrayCaseEditrici[$k]</option>";
                        echo "<option value=\"AltraCasa\">Altra casa editrice</option>"
                    ?>
                </select>
            </div>
            <div class="input-group">
                <?php 
                    if(isset($_POST['aggiungi']))
                    { 
                        if(($_POST['casaEditrice'] == "AltraCasa") && (empty($_POST['nomeCasaEditrice']))) 
                            echo "<p id= \"error\">Nome nuova Casa Editrice richiesta</p>";
                    } 
                ?>
                <input id="seAltraCasa" style="display: none;" type="text" name="nomeCasaEditrice" placeholder="Nome nuova casa editrice..." value="<?php if(($_POST['casaEditrice'] == "AltraCasa") && (!empty($_POST['nomeCasaEditrice']))){echo $_POST['nomeCasaEditrice'];}?>"/>
            </div>
            <div class="input-group">
                <?php 
                    if(isset($_POST['aggiungi']))
                    { 
                        if ($_POST['lingua'] == "") 
                            echo "<p id= \"error\">Lingua richiesta</p>";
                    } 
                ?>
                <label>Lingua</label>
                <select name="lingua">
                    <option value="<?php if(isset($_POST['lingua'])){echo $_POST['lingua'];}?>" selected><?php if(isset($_POST['lingua'])){echo $_POST['lingua'];} else { echo "Scegli una lingua...";}?></option>
                    <option value="Italiano">Italiano</option>
                    <option value="Inglese">Inglese</option>
                    <option value="Spagnolo">Spagnolo</option>
                    <option value="Russo">Russo</option>
                </select>
            </div>
            <div class="input-group">
                <?php 
                    if(isset($_POST['aggiungi']))
                    { 
                        if (empty($_POST['annoProd'])) 
                            echo "<p id= \"error\">Anno Produzione richiesto</p>";
                    } 
                ?>
                <label>Anno Produzione</label>
                <input type="number" name="annoProd" max= "2019" value="<?php if(isset($_POST['annoProd'])){ echo $_POST['annoProd'];}?>">
            </div>
            <div class="input-group">
                <?php 
                    if(isset($_POST['aggiungi']))
                    { 
                        if (empty($_POST['prezzo'])) 
                            echo "<p id= \"error\">Prezzo richiesto</p>";
                    } 
                ?>
                <label>Prezzo</label>
                <input type="number" step="0.01" name="prezzo" value="<?php if(isset($_POST['prezzo'])){ echo $_POST['prezzo'];}?>">
            </div>
            <div class="input-group">
                <?php 
                    if(isset($_POST['aggiungi']))
                    { 
                        if ($_FILES['img']['name'] == "") 
                            echo "<p id= \"error\">Immagine richiesta</p>";
                    } 
                ?>
                <label>File Immagine</label>
                <input type="file" name="img">
            </div>
            <div class="input-group">
  	            <button type="submit" class="btn" name="aggiungi">Aggiungi</button>
                  <?php 
                    if($aggiunto == true){
                        $aggiunto = false;
                        echo "<script type=\"text/javascript\">alert(\"Prodotto aggiunto con successo!\"); location.replace(\"aggiungiProdotto.php\");</script>";
                    }
                  ?>
  	        </div>
        </form>              
    </div>
</body>
</html>