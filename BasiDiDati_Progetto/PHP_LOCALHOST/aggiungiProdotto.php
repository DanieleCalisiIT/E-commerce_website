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
</head>

<?php
    if (isset($_POST['aggiungi'])) 
    { 
        $query = "SELECT id FROM prodotti ORDER BY id desc limit 1"; //opp con max(id)
        if (!$result = mysqli_query($con, $query)) 
        {
            printf("errore nella query di ricerca idProdottoMax \n");
            exit();
        }
            
        $row = mysqli_fetch_array($result);
    
        if ($row) {  

            $idProdottoMax = $row['id'];               
            
        }

        $IDProd = $idProdottoMax + 1;
        $nomeDaAgg = $_POST['nome'];
        $categoriaDaAgg = $_POST['categoria'];
        $casaDaAgg = $_POST['casaEditrice'];
        $linguaDaAgg = $_POST['lingua'];
        $prezzoDaAgg = $_POST['prezzo'];
        $location = '../images/' . $_POST['nome'] . $IDProd . '.png'; 
        move_uploaded_file($_FILES['img']['tmp_name'], $location);
                
        //validazione form
        if ((!empty($nomeDaAgg)) && ($linguaDaAgg != "")  && (!empty($prezzoDaAgg)) &&
        ($_FILES['img']['name'] != "") && ($categoriaDaAgg != "") && ($casaDaAgg != "")){                   

            $query = "INSERT INTO prodotti (img, nome, categoria, casaEditrice, lingua, prezzo) VALUES
            ('$location', '$nomeDaAgg', '$categoriaDaAgg', '$casaDaAgg', '$linguaDaAgg', $prezzoDaAgg);";

            if (!$result = mysqli_query($con, $query)) {
                echo "errore query inserimento prodotto";
                exit();
            }
                        
            $aggiunto = true;
        }   
    }


?>



<body>
    <div class="body">
        <div class="header">
  	        <h2>Aggiungi un gioco al catalogo</h2>
        </div>

        

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
                <input type="text" name="categoria" value="<?php if(isset($_POST['categoria'])){ echo $_POST['categoria'];}?>">
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
                <input type="text" name="casaEditrice" value="<?php if(isset($_POST['casaEditrice'])){ echo $_POST['casaEditrice'];}?>">        
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
  	        </div>
        </form>  

        <?php 
            if($aggiunto == true){
                $aggiunto = false;
                echo "<script type=\"text/javascript\">alert(\"Gioco aggiunto con successo\"); location.replace(\"aggiungiProdotto.php\");</script>";
            }
        ?>            
    </div>
</body>
</html>