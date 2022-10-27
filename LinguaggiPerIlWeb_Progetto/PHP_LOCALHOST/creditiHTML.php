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
    <title>Crediti</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <link href='https://fonts.googleapis.com/css?family=Fredericka the Great' rel='stylesheet'>
</head>

<body>
    <div class="body">
    <div class="header">
  	        <h2>CREDITI</h2>
        </div>
        <p id="phome">I crediti funzionano in un modo molto semplice, sono legati agli acquisti effettuati e vengono erogati come "premio" per gli acquisti. Ogni acquisto
            effettuato da parte di un cliente fornisce un numero di crediti pari al 10% della somma totale spesa per quell'acquisto. I crediti vengono assegnati quindi in
            automatico e servono per accedere agli sconti presenti sugli articoli. Ogni sconto ha una sua percentuale di sconto e un numero di crediti o reputazione necessari per accedervi. 
            La reputazione funziona in modo simile ai crediti, ma per ulteriori dettagli si rimanda alla pagina relativa <a href="reputazioneHTML.php">la reputazione</a>.<br />
            Per ulteriori dettagli o spiegazioni prova a visitare la pagina delle <a href="faq.php">FAQ</a> o a visitare il <a href="forum.php">forum</a>. Se hai ancora dubbi <a href="">
                CONTATTACI</a>!
        </p>
        </div>
    </div>
</body>
</html>