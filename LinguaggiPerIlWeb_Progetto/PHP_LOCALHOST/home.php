<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    require("./cornice.php");
    if(isset($_SESSION['errore_forum'])){
        echo $_SESSION['errore_forum'];
        unset($_SESSION['errore_forum']);
    }
   
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Visualizza Profilo</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <link href='https://fonts.googleapis.com/css?family=Fredericka the Great' rel='stylesheet'>
</head>

<body>
    <div class="body">
    <div class="header">
  	        <h2>Home</h2>
        </div>
        <p id="phome">Questo sito nasce da un'idea di due studenti dell'Universit&agrave; degli Studi Di Roma "La Sapienza". Il sito in questione &egrave; un e-commerce che tratta 
            giochi da tavolo di diverse tipologie. Si pu&ograve; fare una selezione dei giochi per categoria o per prezzo, grazie alla colonna di sinistra, nella quale si possono 
            scegliere vari criteri di selezione dei prodotti presenti all'interno del sito. Nella colonna in alto a sinistra si pu&ograve; inoltre accedere alla pagina di registrazione
            dell'account o fare il login per gli utenti gi&agrave; registrati. Nella barra di navigazione in alto si possono scorrere le pagine principali del sito, dove si possono
            trovare informazioni importanti riguardo il sito, in generale, o pi&ugrave; in particolare, riguardo i contatti e le domande pi&ugrave; frequenti. All'interno del sito 
            &egrave; presente un forum all'interno del quale gli utenti possono scambiare informazioni e possono fare domande riguardanti i prodotti. Possono inoltre dare risposte a domande
            gi&agrave; presenti e rilasciare recensioni riguardo alcuni prodotti. Ogni domanda, risposta e recensione pu&ograve; essere votata da parte degli altri utenti del forum,
            facendo guadagnare reputazione all'utente che l'ha sottoposta. Reputazione che sar&agrave; utilizzata per poter accedere a determinati sconti su alcuni prodotti.
        </p>
        </div>
    </div>
</body>
</html>