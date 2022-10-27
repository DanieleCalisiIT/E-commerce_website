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
    <title>Visualizza Profilo</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <div class="body">
        <div class="header">
  	        <h2>Il tuo Profilo</h2>
        </div>

        <?php

            $username = $_SESSION['username'];
            $password = $_SESSION['password'];
            $email = $_SESSION['email'];
            $nome = $_SESSION['nome'];
            $cognome = $_SESSION['cognome'];
            $ID = $_SESSION['id'];
            $idAdd = $_SESSION['id_indirizzo'];

            $query = "SELECT * FROM indirizzi WHERE id = '$idAdd';"; 
            if (!$result = mysqli_query($con, $query)) {
                echo "errore query ricerca indirizzo";
                exit();
            }

            /*$query = "SELECT i.citta, i.cap, i.provincia, i.regione, i.nazione, i.via, i.civico
             FROM indirizzi i, users u
             WHERE i.id = u.id_indirizzo";*/

            $row = mysqli_fetch_array($result);
            
            if($row){
                $citta = $row['citta'];
                $CAP = $row['cap'];
                $provincia = $row['provincia'];
                $regione = $row['regione'];
                $nazione = $row['nazione'];
                $via = $row['via'];
                $civico = $row['civico'];
            }    
         
            echo "
            <ul class=\"modificaProfilo\"> 
                <li>Username: $username</li>
                <li>Password: $password</li>
                <li>E-mail: $email</li>    
                <li>Nome: $nome</li>
                <li>Cognome: $cognome</li>
            </ul>
            <h2 style=\"text-align: center; font-size: 200%; color: #DC143C;\">Il tuo indirizzo</h2>
            <ul class=\"modificaProfilo\">
                <li>Citt&agrave;: $citta</li>
                <li>CAP: $CAP</li>
                <li>Provincia: $provincia</li>
                <li>Regione: $regione</li>
                <li>Nazione: $nazione</li>
                <li>Via: $via</li>
                <li>Civico: $civico</li>
            </ul>
            <br />
            <br />
            <br />
            <a class=\"modifica\" href=\"modificaProfilo.php\">Modifica Profilo</a>";
    



        ?>
    
    
    </div>
</body>
</html>