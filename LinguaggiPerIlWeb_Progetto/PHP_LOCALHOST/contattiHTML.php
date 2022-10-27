<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    require("./cornice.php");   
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Contatti</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <link href='https://fonts.googleapis.com/css?family=Fredericka the Great' rel='stylesheet'>
</head>

<body>
    <div class="body">
    <div class="header">
  	        <h2>CONTATTI</h2>
        </div>
        <p id="phome">Se vuoi ricevere ulteriori informazioni riguardo il nostro sito o desideri contattarci per un qualsiasi motivo
            non esitare a farlo. Puoi chiamare al numero <strong style="color: crimson;">0773-000000</strong> dal luned&igrave; al venerd&igrave;. Puoi inoltre venirci a trovare presso l'<strong style="color: crimson;">Universit&agrave;
            degli studi di Roma "La Sapienza", Sede di Latina</strong>, in Via Andrea Doria, Latina, 04100. Puoi inoltre raggiungerci ai seguenti recapiti: <ul style="color: crimson; text-align: left;" id="phome">
                <li style="font-weight: bold;">Gianmarco Porcile: email@gmail.com</li>
                <li style="font-weight: bold;">Daniele Calisi: email@gmail.com</li>
            </ul>
           
        </p>
        </div>
    </div>
</body>
</html>