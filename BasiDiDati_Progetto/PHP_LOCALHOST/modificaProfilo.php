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
  	        <h2>Modifica il tuo profilo</h2>
        </div>
	
        <?php

            $nomeVecchio = $_SESSION['nome'];
            $cognomeVecchio = $_SESSION['cognome'];
            $password = $_SESSION['password'];
            $ID = $_SESSION['id'];
            $idAdd = $_SESSION['id_indirizzo'];

            $query = "SELECT * FROM indirizzi WHERE id = '$idAdd';";

            if (!$result = mysqli_query($con, $query)) {
                printf("errore nella query di selezione indirizzo \n");
                exit();
            }

            $row = mysqli_fetch_array($result);

            if ($row){   
                $cittaVecchia = $row['citta'];
                $capVecchio = $row['cap'];
                $provinciaVecchia = $row['provincia'];
                $regioneVecchia = $row['regione'];
                $nazioneVecchia = $row['nazione'];
                $viaVecchia = $row['via'];
                $civicoVecchio = $row['civico'];
            }  
        
            if (isset($_POST['modifica'])) {

                $password_1 =  $_POST['nuovaPassword'];
                $password_2 =  $_POST['ripetiPassword'];

                //validazione form 
                if ((!empty($password_1)) && ($password_1 == $password_2) && (!empty($_POST['nome'])) && (!empty($_POST['cognome'])) && (!empty($_POST['citta'])) && (!empty($_POST['CAP'])) &&
                (!empty($_POST['provincia'])) && (!empty($_POST['regione'])) && (!empty($_POST['nazione'])) && (!empty($_POST['via'])) && (!empty($_POST['civico']))){   
                    
                    $nome = $_POST['nome'];
                    $cognome = $_POST['cognome'];
                    $citta = $_POST['citta'];
                    $cap = $_POST['CAP'];
                    $provincia = $_POST['provincia'];
                    $regione = $_POST['regione'];
                    $nazione = $_POST['nazione'];
                    $via = $_POST['via'];
                    $civico = $_POST['civico'];                
                    
                    if($password_1 != $password || $nome != $nomeVecchio || $cognome != $cognomeVecchio || $citta != $cittaVecchia || $cap != $capVecchio || $provincia != $provinciaVecchia || $regione != $regioneVecchia || $nazione != $nazioneVecchia || $via != $viaVecchia || $civico != $civicoVecchio){

                        $query = "UPDATE users 
                        SET password = '$password_1', nome = '$nome', cognome = '$cognome' 
                        WHERE id = $ID; ";

                        if (!$result = mysqli_query($con, $query)) {
                            printf("errore nella query di aggiornamento utente \n");
                            exit();
                        }

                        $query = "UPDATE indirizzi 
                        SET citta = '$citta', cap = '$cap', provincia = '$provincia', regione = '$regione', nazione = '$nazione', via = '$via', civico = '$civico'
                        WHERE id = $idAdd; ";

                        if (!$result = mysqli_query($con, $query)) {
                            printf("errore nella query di aggiornamento indirizzo \n");
                            exit();
                        }
                    }
                    
                    $_SESSION['password'] = $password_1;
                    $_SESSION['cognome'] = $cognome;
                    $_SESSION['nome'] = $nome;               
                    
                    $modificato = true;
                }
            }
        ?>

        <form method="post" action="modificaProfilo.php">
  	        <div class="input-group">
                <?php if(isset($_POST['modifica'])){ if (empty($password_1)) {
                echo "<p id= \"error\">Password richiesta</p>";
                }} 
                ?>
  	            <label>Nuova Password</label>
  	            <input type="password" name="nuovaPassword">
  	        </div>
  	        <div class="input-group">
                <?php if(isset($_POST['modifica'])){ if (empty($password_2)) {
                echo "<p id= \"error\">Conferma password richiesta</p>";
                }} 
                ?>
  	            <label>Ripeti Password</label>
  	            <input type="password" name="ripetiPassword">             
  	        </div>
  	        <div class="input-group">
                <?php if(isset($_POST['modifica'])){ if (empty($_POST['nome'])) {
                echo "<p id= \"error\">Nome richiesto</p>";
                }} 
                ?>
  	            <label>Nome</label>
  	            <input type="text" name="nome" value="<?php if(isset($_POST['nome'])){
                      echo $_POST['nome'];
                  } else {
                      echo $nomeVecchio;
                  }?>">
  	        </div>
  	        <div class="input-group">
                <?php if(isset($_POST['modifica'])){ if (empty($_POST['cognome'])) {
                echo "<p id= \"error\">Cognome richiesto</p>";
                }} 
                ?>
  	            <label>Cognome</label>
  	            <input type="text" name="cognome" value="<?php if(isset($_POST['cognome'])){
                      echo $_POST['cognome'];
                  } else {
                      echo $cognomeVecchio;
                  }?>">
  	        </div>

  	        <p style="font-size: 120%; color: #DC143C; ">
  		        <b>Modifica indirizzo</b>
  	        </p>

            <div class="input-group">
                <?php if(isset($_POST['modifica'])){ if (empty($_POST['citta'])) {
                echo "<p id= \"error\">Citta richiesta</p>";
                }} 
                ?>
  	            <label>Citt&agrave;</label>
  	            <input type="text" name="citta" value="<?php if(isset($_POST['citta'])){
                      echo $_POST['citta'];
                  } else {
                      echo $cittaVecchia;
                  }?>">
  	        </div>
            <div class="input-group">
                <?php if(isset($_POST['modifica'])){ if (empty($_POST['CAP'])) {
                echo "<p id= \"error\">CAP richiesto</p>";
                }} 
                ?>
  	            <label>CAP</label>
  	            <input type="number" name="CAP" maxlenght=5 value="<?php if(isset($_POST['CAP'])){
                      echo $_POST['CAP'];
                  } else {
                      echo $capVecchio;
                  }?>">
  	        </div>
            <div class="input-group">
                <?php if(isset($_POST['modifica'])){ if (empty($_POST['provincia'])) {
                echo "<p id= \"error\">Provincia richiesta</p>";
                }} 
                ?>
  	            <label>Provincia</label>
  	            <input type="text" name="provincia" value="<?php if(isset($_POST['provincia'])){
                      echo $_POST['provincia'];
                  } else {
                      echo $provinciaVecchia;
                  }?>">
  	        </div>
            <div class="input-group">
                <?php if(isset($_POST['modifica'])){ if (empty($_POST['regione'])) {
                echo "<p id= \"error\">Regione richiesta</p>";
                }} 
                ?>
  	            <label>Regione</label>
  	            <input type="text" name="regione" value="<?php if(isset($_POST['regione'])){
                      echo $_POST['regione'];
                  } else {
                      echo $regioneVecchia;
                  }?>">
  	        </div>
            <div class="input-group">
                <?php if(isset($_POST['modifica'])){ if (empty($_POST['nazione'])) {
                echo "<p id= \"error\">Nazione richiesta</p>";
                }} 
                ?>
  	            <label>Nazione</label>
  	            <input type="text" name="nazione" value="<?php if(isset($_POST['nazione'])){
                      echo $_POST['nazione'];
                  } else {
                      echo $nazioneVecchia;
                  }?>">
  	        </div>
            <div class="input-group">
                <?php if(isset($_POST['modifica'])){ if (empty($_POST['via'])) {
                echo "<p id= \"error\">Via richiesta</p>";
                }} 
                ?>
  	            <label>Via</label>
  	            <input type="text" name="via" value="<?php if(isset($_POST['via'])){
                      echo $_POST['via'];
                  } else {
                      echo $viaVecchia;
                  }?>">
  	        </div>
            <div class="input-group">
                <?php if(isset($_POST['modifica'])){ if (empty($_POST['civico'])) {
                echo "<p id= \"error\">Civico richiesto</p>";
                }} 
                ?>
  	            <label>Civico</label>
  	            <input type="text" name="civico" value="<?php if(isset($_POST['civico'])){
                      echo $_POST['civico'];
                  } else {
                      echo $civicoVecchio;
                  }?>">
  	        </div>
            <div class="input-group">
  	            <button type="submit" class="btn" name="modifica">Conferma modifiche</button>
  	        </div>
        </form>
    
        
    </div>

        <?php 
            if($modificato == true){
                $modificato = false;
                echo "<script type=\"text/javascript\">alert(\"Modifiche effettuate con successo.\"); location.replace(\"visualizzaProfilo.php\");</script>";
            }
        ?>

</body>
</html>