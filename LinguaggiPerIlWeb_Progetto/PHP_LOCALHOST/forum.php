<!--
Qui è presente la home page del forum del sito. Come prima cosa in questa pagina, viene controllato se il cliente
che sta tentado di effettuare l'accesso al forum sia bannato o no. Nel caso in cui l'utente non sia bannato, accede 
normalmente alla home del forum. Nel caso l'utente sia bannato ma la data attuale sia maggiore della data di scadenza
del ban, all'utente viene revocato il ban tramite il setAttribute dello stato del cliente, e viene permesso l'accesso al forum.
Nel caso in cui la data attuale sia minore della data di fine ban, all'utente viene impedito l'accesso al forum e viene reindirizzato
alla home page del sito. Tramite questa pagina si può accedere alla sezione di forum dedicata alle domande o alle recensioni.
 -->

<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    include("./cornice.php");
    
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Forum</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<?php
        if (!isset($_SESSION['success'])) {
            $nonSuccesso = true; 

        }
        if($_SESSION['type'] == "cliente"){
            if($statoBan == "bannato" && $dataBanValue >= date('Y-m-d')){
                $bannato = true;
                     
    }
            if($dataBanValue < date('Y-m-d')){
                $profilo->setAttribute("stato", "attivo");

                $percorso = "../XML/account.xml";
                $docAccount->save($percorso);
            }
}
        ?>
<body>
    <div class="body">
        <div class="header">
  	        <h2>Forum</h2>
        </div>
        
        <p id="forum"> All'interno di questo forum gli utenti possono discutere di tutto ci&ograve; che riguardi il sito, il catalogo dei prodotti, e in generale tutte le 
            sezioni che sono presenti all'interno del sito. Questa sezione di Forum del sito si divide in due ulteriori parti: una riguardante le <strong>domande</strong> e una riguardante 
            le <strong>recensioni</strong>. <br /> Nella sezione delle domande, gli utenti registrati potranno fare domande relative a qualsiasi questione riguardi il sito; potranno inoltre
            rispondere a domande gi&agrave; esistenti. Le domande e le risposte ritenute migliori possono essere votate dagli altri utenti e dai gestori in modo da far guadagnare
            punti reputazione agli utenti che le hanno sottoposte in modo da permettere loro di accedere, dopo una certa quantit&agrave; di punti, a degli sconti. Possono essere
            votate anche le recensioni ritenute migliori, allo stesso modo delle domande e con la stessa modalit&agrave; di guadagno di punti reputazione. <br /><strong>ATTENZIONE</strong>:
            Si prega di usare un linguaggio appropriato all'interno del forum per evitare di incorrere in <strong>ban</strong> da parte degli amministratori del sito. <br />
             Grazie.
        </p>
        <a class="forum-btn1" href="domande.php">Vai alle domande</a>
        <a class="forum-btn2" href="recensioni.php">Vai alle recensioni</a>
        
    </div>
</body>
    <?php
        if($nonSuccesso == true){
            $nonSuccesso = false;
            echo "<script type=\"text/javascript\">alert(\"Per favore effettua il login per accedere al forum o registrati!\"); window.location.replace(\"home.php\");</script>";
        }

        if($bannato == true){
            $bannato = false;
            echo "<script type=\"text/javascript\">alert(\"Non puoi accedere al forum, sei bannato!\"); window.location.replace(\"home.php\");</script>";
        }
    ?>
</html>