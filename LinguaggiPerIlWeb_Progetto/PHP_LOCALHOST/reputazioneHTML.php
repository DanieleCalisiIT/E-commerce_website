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
    <title>Reputazione</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <link href='https://fonts.googleapis.com/css?family=Fredericka the Great' rel='stylesheet'>
</head>

<body>
    <div class="body">
    <div class="header">
  	        <h2>REPUTAZIONE</h2>
        </div>
        <p id="phome">Il metodo di reputazione in questo sito funziona in un modo molto semplice. La reputazione viene assegnata tramite i voti ricevuti ad una domanda, 
            risposta o recensione. Ogni utente del sito, che sia un cliente o un gestore, pu&ograve; votare i topic del forum e far quindi acquistare punti reputazione 
            al cliente che ha sottoposto quella domanda/risposta o recensione. Per ogni voto ricevuto, l'utente acquista 10 punti reputazione, se il voto &egrave; dato
            da un cliente, 15 punti reputazione se il voto viene dato da un gestore. Stessa cosa vale nel caso in cui il gestore o il cliente esprimano un voto negativo, 
            e cio&egrave;, 10 punti persi per ogni voto negativo dato da clienti e 15 punti persi per ogni voto negativo dato da gestori. Grazie a questi punti reputazione
            accumulati si pu&ograve; accedere ad alcuni sconti. Ogni sconto ha una sua percentuale di sconto e un numero di crediti o reputazione necessari per accedervi. 
            I crediti funzionano in modo simile alla reputazione, ma per ulteriori dettagli si rimanda alla pagina relativa <a href="creditiHTML.php">i crediti</a>.<br />
            Per ulteriori dettagli o spiegazioni prova a visitare la pagina delle <a href="faq.php">FAQ</a> o a visitare il <a href="forum.php">forum</a>. Se hai ancora dubbi <a href="">
                CONTATTACI</a>!
        </p>
        </div>
    </div>
</body>
</html>