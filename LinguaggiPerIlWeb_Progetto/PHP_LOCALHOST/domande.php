<!--
In questo script si gestisce gran parte di quello che riguarda il forum delle domande. 
    Riassumendo, la struttura di questo script è: 
    1: Javascript
    2: Gestione form (funzionalità)
    3: Form
    4: Avvisi
    Il javascript è stato usato solamente per far apparire o scomparire dei pezzi di form a seconda dei bottoni cliccati dall'utente. 
    La gestione della form è stata fatta con una serie di if(isset()) così da isolare le varie funzionalità del forum;  Le funzionalità sono: 
    --elevare una domanda a FAQ, se sei amministratore (rimanda allo script creaFAQ.php). 
    --mettere like o dislike, a domande e risposte, che incidono sulla reputazione di chi li riceve (non puoi mettere like/dislike ai tuoi post;
    mettere like/dislike dove lo hai già messo lo elimina; mettere like/dislike dove hai gia messo dislike/like lo sostituisce; i like/dislike dei gestori incidono maggiormente sulla reputazione).
    --rispondere ad una domanda aperta
    --eliminare una domanda (se sei gestore): questo comporta anche l'eliminazione di tutte le sue risposte; 
    tutti i like/dislike assegnati alla domanda e alle sue risposte vengono quindi eliminati (incidendo sulla reputazione di chi li aveva ricevuti).
    --chiudere una domanda (se sei gestore): impedendo la creazione di altre sue risposte.
    La form mostra tutte le domande e le risposte, permettendo di interagire con esse. 
    In base alla tipologia di utente che arriva in questa pagina, la form avrà una struttura diversa, permettendo o non permettendo di effettuare determinate operazioni (elencate sopra). 
    Se chi arriva su questa pagina è un Cliente, avrà la possibilità di cliccare sul link che porta alla creazione di una nuova domanda (creaDomanda.php). 
    Gli eventuali avvisi vengono "calcolati" nella gestione della form, e poi mostrati dopo di essa, così da non bloccare il caricamento di quest' ultima.

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
    <title>Domande del forum</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <script>
        function funzioneMostraRisposte(that) 
        {
            var idDomanda = new String(that.id);
            var idUL = "ul" + idDomanda;

            document.getElementById(idDomanda).style.display = "none";
            document.getElementById(idUL).style.display = "block";
            
        }

        function funzioneNascondiRisposte(that) 
        {
            var idDomanda = new String(that.id);
            var idUL = "ul" + idDomanda;

            var idDIV = "divRisp" + idDomanda;

            document.getElementById(idDomanda).style.display = "block";
            document.getElementById(idUL).style.display = "none";
            document.getElementById(idDIV).style.display = "none";
        }

        function funzioneRispondi(that)
        {
            var idRispDomanda = new String(that.id);
            var idDIV = "div" + idRispDomanda;

            document.getElementById(idDIV).style.display = "block";
            document.getElementById(idRispDomanda).style.display = "none";
        }

        function funzioneAnnullaRispondi(that)
        {
            var idRispDomanda = new String(that.id);
            var idDIV = "div" + idRispDomanda;

            document.getElementById(idDIV).style.display = "none";
            document.getElementById(idRispDomanda).style.display = "block";
        }
        
    </script>
</head>
<?php
    $stringaXML = "";
    foreach ( file("../XML/domande.xml") as $nodo )   
    {
        $stringaXML .= trim($nodo);
    }

    $docDomande = new DOMDocument();
    $docDomande->loadXML($stringaXML);

    $rootDomande = $docDomande->documentElement;
    $listaDomande = $rootDomande->childNodes;
?>

<?php
    if (isset($_POST['elevaDomanda']))
    {
        $_SESSION['testoDomandaElevata'] = $_POST['elevaDomanda'];
        $creaFAQ = true;
    }

    if (isset($_POST['likeDomanda']))
                {
                    for ($i=0; $i<$listaDomande->length; $i++) 
                    {
                        $domanda = $listaDomande->item($i);

                        $idDomanda = $domanda->firstChild;
                        $idDomandaValue = $idDomanda->textContent;

                        if($idDomandaValue == $_POST['likeDomanda'])
                        {
                            $dataCreazDomanda = $idDomanda->nextSibling;

                            $creatoreDomanda = $dataCreazDomanda->nextSibling;
                            $creatoreDomandaValue = $creatoreDomanda->textContent;

                            if($creatoreDomandaValue == $username)
                                $erroreLike = true;
                            else{

                                $xmlString = "";
                                foreach(file("../XML/account.xml") as $nodo){
                                    $xmlString .= trim($nodo);
                                }

                                $docAccount = new DOMDocument();
                                $docAccount->loadXML($xmlString);

                                $rootAccount = $docAccount->documentElement;
                                $listaAccount = $rootAccount->childNodes;

                                $queryID = "SELECT id FROM users WHERE username = '$creatoreDomandaValue'";
                                if (!$resultID = mysqli_query($con, $queryID)) 
                                {
                                    printf("errore nella query di ricerca ID \n");
                                    exit();
                                }
                        
                                $row = mysqli_fetch_array($resultID);
                    
                                if ($row) {  

                                    $IDCliente = $row['id'];               
                                }

                                $j=0;
                                $trovato=0;
                                while(($j<$listaAccount->length) && ($trovato==0))
                                {
                                    $profilo = $listaAccount->item($j);
                                    $idAcc = $profilo->firstChild;
                                    $idAccValue = $idAcc->textContent;
                                    if($idAccValue == $IDCliente)  
                                    {
                                        $trovato=1;
                                        $nome = $idAcc->nextSibling;
                                        $cognome = $nome->nextSibling;   
                                        $soldi = $cognome->nextSibling; 
                                        $crediti = $soldi->nextSibling;
                                        $reputazione = $crediti->nextSibling;
                                        $reputazioneValue = $reputazione->textContent;
                                    }          
                                    $j++;
                                }

                                $listaVotiDomanda = $domanda->getElementsByTagName('votoDomanda');

                                $k=0;
                                $trovato=0;
                                while(($k<$listaVotiDomanda->length) && ($trovato==0))
                                {
                                    $voto = $listaVotiDomanda->item($k);

                                    $creatoreVoto = $voto->firstChild;
                                    $creatoreVotoValue = $creatoreVoto->textContent;

                                    if($creatoreVotoValue == $username)
                                    {
                                        $trovato = 1;
                                        $valoreVoto = $creatoreVoto->nextSibling;
                                        if($valoreVoto->textContent == "true")
                                        {
                                            $domanda->removeChild($voto);

                                            if($_SESSION['type'] == "cliente")
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 10);
                                            else
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 15);
                                            
                                            $profilo->replaceChild($nuovaReputazione,$reputazione);
                                            //avviso che siccome gia aveva messo like, questo è stato rimosso
                                        }
                                        else
                                        {
                                            $nuovoValoreVoto = $docDomande->createElement("valoreVoto", "true");
                                            $voto->replaceChild($nuovoValoreVoto,$valoreVoto);

                                            if($_SESSION['type'] == "cliente")
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 20);
                                            else    
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 30);
                                            
                                            $profilo->replaceChild($nuovaReputazione,$reputazione);
                                            //avviso che siccome aveva gia messo dislike, è stato sostituito con un like
                                        }

                                    }
                                    $k++;
                                }

                                if($trovato == 0)
                                {
                                    $nuovoCreatoreVoto = $docDomande->createElement("creatore", $username);
                                    $nuovoValoreVoto = $docDomande->createElement("valoreVoto", "true");
                                    $nuovoVoto = $docDomande->createElement("votoDomanda");

                                    $domanda->appendChild($nuovoVoto);
                                    $nuovoVoto->appendChild($nuovoCreatoreVoto);
                                    $nuovoVoto->appendChild($nuovoValoreVoto);

                                    if($_SESSION['type'] == "cliente")
                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 10);
                                    else    
                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 15);

                                    $profilo->replaceChild($nuovaReputazione,$reputazione);
                                    //avviso di aggiunta nuovo like
                                }   
                            }
                        }
                    }
                    $percorso = "../XML/account.xml";
                    $docAccount->save($percorso);

                    $percorso = "../XML/domande.xml";
                    $docDomande->save($percorso);
                }


                if (isset($_POST['dislikeDomanda']))
                {
                    for ($i=0; $i<$listaDomande->length; $i++) 
                    {
                        $domanda = $listaDomande->item($i);

                        $idDomanda = $domanda->firstChild;
                        $idDomandaValue = $idDomanda->textContent;

                        if($idDomandaValue == $_POST['dislikeDomanda'])
                        {
                            $dataCreazDomanda = $idDomanda->nextSibling;

                            $creatoreDomanda = $dataCreazDomanda->nextSibling;
                            $creatoreDomandaValue = $creatoreDomanda->textContent;

                            if($creatoreDomandaValue == $username)
                                $erroreLike = true;
                            else{

                                $xmlString = "";
                                foreach(file("../XML/account.xml") as $nodo){
                                    $xmlString .= trim($nodo);
                                }

                                $docAccount = new DOMDocument();
                                $docAccount->loadXML($xmlString);

                                $rootAccount = $docAccount->documentElement;
                                $listaAccount = $rootAccount->childNodes;

                                $queryID = "SELECT id FROM users WHERE username = '$creatoreDomandaValue'";
                                if (!$resultID = mysqli_query($con, $queryID)) 
                                {
                                    printf("errore nella query di ricerca ID \n");
                                    exit();
                                }
                        
                                $row = mysqli_fetch_array($resultID);
                    
                                if ($row) {  

                                    $IDCliente = $row['id'];               
                                }

                                $j=0;
                                $trovato=0;
                                while(($j<$listaAccount->length) && ($trovato==0))
                                {
                                    $profilo = $listaAccount->item($j);
                                    $idAcc = $profilo->firstChild;
                                    $idAccValue = $idAcc->textContent;
                                    if($idAccValue == $IDCliente)  
                                    {
                                        $trovato=1;
                                        $nome = $idAcc->nextSibling;
                                        $cognome = $nome->nextSibling;   
                                        $soldi = $cognome->nextSibling; 
                                        $crediti = $soldi->nextSibling;
                                        $reputazione = $crediti->nextSibling;
                                        $reputazioneValue = $reputazione->textContent;
                                    }          
                                    $j++;
                                }

                                $listaVotiDomanda = $domanda->getElementsByTagName('votoDomanda');

                                $k=0;
                                $trovato=0;
                                while(($k<$listaVotiDomanda->length) && ($trovato==0))
                                {
                                    $voto = $listaVotiDomanda->item($k);

                                    $creatoreVoto = $voto->firstChild;
                                    $creatoreVotoValue = $creatoreVoto->textContent;

                                    if($creatoreVotoValue == $username)
                                    {
                                        $trovato = 1;
                                        $valoreVoto = $creatoreVoto->nextSibling;
                                        if($valoreVoto->textContent == "false")
                                        {
                                            $domanda->removeChild($voto);

                                            if($_SESSION['type'] == "cliente")
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 10);
                                            else
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 15);
                                            
                                            $profilo->replaceChild($nuovaReputazione,$reputazione);
                                            //avviso che siccome gia aveva messo dislike, questo è stato rimosso
                                        }
                                        else
                                        {
                                            $nuovoValoreVoto = $docDomande->createElement("valoreVoto", "false");
                                            $voto->replaceChild($nuovoValoreVoto,$valoreVoto);

                                            if($_SESSION['type'] == "cliente")
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 20);
                                            else
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 30);
                                            
                                            $profilo->replaceChild($nuovaReputazione,$reputazione);
                                            //avviso che siccome aveva gia messo like, è stato sostituito con un dislike
                                        }

                                    }
                                    $k++;
                                }

                                if($trovato == 0)
                                {
                                    $nuovoCreatoreVoto = $docDomande->createElement("creatore", $username);
                                    $nuovoValoreVoto = $docDomande->createElement("valoreVoto", "false");
                                    $nuovoVoto = $docDomande->createElement("votoDomanda");

                                    $domanda->appendChild($nuovoVoto);
                                    $nuovoVoto->appendChild($nuovoCreatoreVoto);
                                    $nuovoVoto->appendChild($nuovoValoreVoto);

                                    if($_SESSION['type'] == "cliente")
                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 10);
                                    else
                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 15);
                                            
                                    $profilo->replaceChild($nuovaReputazione,$reputazione);

                                    //avviso di aggiunta nuovo dislike
                                }
                            }
                        }
                    }
                    $percorso = "../XML/account.xml";
                    $docAccount->save($percorso);

                    $percorso = "../XML/domande.xml";
                    $docDomande->save($percorso);
                }


                if (isset($_POST['likeRisposta']))
                {
                    $i=0;
                    $rispostaTrovata=0;
                    while (($i<$listaDomande->length) && ($rispostaTrovata == 0)) 
                    {
                        $domanda = $listaDomande->item($i);

                        $listaRisposte = $domanda->getElementsByTagName('risposta');

                        for($k=0; $k<$listaRisposte->length; $k++)
                        {
                            $risposta = $listaRisposte->item($k);
                        
                            $idRisposta = $risposta->firstChild;
                            $idRispostaValue = $idRisposta->textContent;

                            if($idRispostaValue == $_POST['likeRisposta'])
                            {
                                $rispostaTrovata=1;

                                $dataCreazRisposta = $idRisposta->nextSibling;

                                $creatoreRisposta = $dataCreazRisposta->nextSibling;
                                $creatoreRispostaValue = $creatoreRisposta->textContent;

                                if($creatoreRispostaValue == $username)
                                    $erroreLike = true;
                                else{
                                
                                    $query = "SELECT id, type FROM users WHERE username = '$creatoreRispostaValue'";
                                    if (!$result = mysqli_query($con, $query)) 
                                    {
                                        printf("errore nella query di ricerca ID e tipo \n");
                                        exit();
                                    }
                            
                                    $row = mysqli_fetch_array($result);
                        
                                    if ($row) {  

                                        $IDUtente = $row['id'];
                                        $TipoUtente = $row['type'];

                                    }

                                    if($TipoUtente == "cliente")  //se sei gestore, i voti non avranno impatto sulla tua reputazione (non ce l hai)
                                    {
                                        $xmlString = "";
                                        foreach(file("../XML/account.xml") as $nodo){
                                            $xmlString .= trim($nodo);
                                        }

                                        $docAccount = new DOMDocument();
                                        $docAccount->loadXML($xmlString);

                                        $rootAccount = $docAccount->documentElement;
                                        $listaAccount = $rootAccount->childNodes;

                                        $j=0;
                                        $trovato=0;
                                        while(($j<$listaAccount->length) && ($trovato==0))
                                        {
                                            $profilo = $listaAccount->item($j);
                                            $idAcc = $profilo->firstChild;
                                            $idAccValue = $idAcc->textContent;
                                            if($idAccValue == $IDUtente)  
                                            {
                                                $trovato=1;
                                                $nome = $idAcc->nextSibling;
                                                $cognome = $nome->nextSibling;   
                                                $soldi = $cognome->nextSibling; 
                                                $crediti = $soldi->nextSibling;
                                                $reputazione = $crediti->nextSibling;
                                                $reputazioneValue = $reputazione->textContent;
                                            }          
                                            $j++;
                                        }
                                    }

                                    $listaVotiRisposta = $risposta->getElementsByTagName('votoRisposta');

                                    $j=0;
                                    $trovato=0;
                                    while(($j<$listaVotiRisposta->length) && ($trovato==0))
                                    {
                                        $voto = $listaVotiRisposta->item($j);

                                        $creatoreVoto = $voto->firstChild;
                                        $creatoreVotoValue = $creatoreVoto->textContent;

                                        if($creatoreVotoValue == $username)
                                        {
                                            $trovato = 1;
                                            $valoreVoto = $creatoreVoto->nextSibling;
                                            if($valoreVoto->textContent == "true")
                                            {
                                                $risposta->removeChild($voto);
                                                
                                                if($TipoUtente == "cliente")
                                                {
                                                    if($_SESSION['type'] == "cliente")
                                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 10);
                                                    else
                                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 15);
                                                
                                                    $profilo->replaceChild($nuovaReputazione,$reputazione);
                                                }
                                                //avviso che siccome gia aveva messo like, questo è stato rimosso
                                            }
                                            else
                                            {
                                                $nuovoValoreVoto = $docDomande->createElement("valoreVoto", "true");
                                                $voto->replaceChild($nuovoValoreVoto,$valoreVoto);

                                                if($TipoUtente == "cliente")
                                                {
                                                    if($_SESSION['type'] == "cliente")
                                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 20);
                                                    else
                                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 30);
                                                
                                                    $profilo->replaceChild($nuovaReputazione,$reputazione);
                                                }
                                                
                                                //avviso che siccome aveva gia messo dislike, è stato sostituito con un like
                                            }

                                        }
                                        $j++;
                                    }

                                    if($trovato == 0)
                                    {
                                        $nuovoCreatoreVoto = $docDomande->createElement("creatore", $username);
                                        $nuovoValoreVoto = $docDomande->createElement("valoreVoto", "true");
                                        $nuovoVoto = $docDomande->createElement("votoRisposta");

                                        $risposta->appendChild($nuovoVoto);
                                        $nuovoVoto->appendChild($nuovoCreatoreVoto);
                                        $nuovoVoto->appendChild($nuovoValoreVoto);

                                        if($TipoUtente == "cliente")
                                        {
                                            if($_SESSION['type'] == "cliente")
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 10);
                                            else
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 15);
                                            
                                            $profilo->replaceChild($nuovaReputazione,$reputazione);
                                        }
                                        //avviso di aggiunta nuovo like
                                    }
                                }
                            }
                        }
                        $i++;
                    }

                    if($TipoUtente == "cliente")
                    {
                        $percorso = "../XML/account.xml";
                        $docAccount->save($percorso);
                    }
                    

                    $percorso = "../XML/domande.xml";
                    $docDomande->save($percorso);
                }


                if (isset($_POST['dislikeRisposta']))
                {
                    $i=0;
                    $rispostaTrovata=0;
                    while (($i<$listaDomande->length) && ($rispostaTrovata == 0)) 
                    {
                        $domanda = $listaDomande->item($i);

                        $listaRisposte = $domanda->getElementsByTagName('risposta');

                        for($k=0; $k<$listaRisposte->length; $k++)
                        {
                            $risposta = $listaRisposte->item($k);
                        
                            $idRisposta = $risposta->firstChild;
                            $idRispostaValue = $idRisposta->textContent;

                            if($idRispostaValue == $_POST['dislikeRisposta'])
                            {
                                $rispostaTrovata=1;

                                $dataCreazRisposta = $idRisposta->nextSibling;

                                $creatoreRisposta = $dataCreazRisposta->nextSibling;
                                $creatoreRispostaValue = $creatoreRisposta->textContent;

                                if($creatoreRispostaValue == $username)
                                    $erroreLike = true;
                                else{

                                    $query = "SELECT id, type FROM users WHERE username = '$creatoreRispostaValue'";
                                    if (!$result = mysqli_query($con, $query)) 
                                    {
                                        printf("errore nella query di ricerca ID e tipo\n");
                                        exit();
                                    }
                            
                                    $row = mysqli_fetch_array($result);
                        
                                    if ($row) {  

                                        $IDUtente = $row['id'];
                                        $TipoUtente = $row['type'];              
                                    }

                                    if($TipoUtente == "cliente") //se sei gestore, i voti non avranno impatto sulla tua reputazione (non ce l hai)
                                    {
                                        $xmlString = "";
                                        foreach(file("../XML/account.xml") as $nodo){
                                            $xmlString .= trim($nodo);
                                        }

                                        $docAccount = new DOMDocument();
                                        $docAccount->loadXML($xmlString);

                                        $rootAccount = $docAccount->documentElement;
                                        $listaAccount = $rootAccount->childNodes;

                                        $j=0;
                                        $trovato=0;
                                        while(($j<$listaAccount->length) && ($trovato==0))
                                        {
                                            $profilo = $listaAccount->item($j);
                                            $idAcc = $profilo->firstChild;
                                            $idAccValue = $idAcc->textContent;
                                            if($idAccValue == $IDUtente)  
                                            {
                                                $trovato=1;
                                                $nome = $idAcc->nextSibling;
                                                $cognome = $nome->nextSibling;   
                                                $soldi = $cognome->nextSibling; 
                                                $crediti = $soldi->nextSibling;
                                                $reputazione = $crediti->nextSibling;
                                                $reputazioneValue = $reputazione->textContent;
                                            }          
                                            $j++;
                                        }
                                    }

                                    $listaVotiRisposta = $risposta->getElementsByTagName('votoRisposta');

                                    $j=0;
                                    $trovato=0;
                                    while(($j<$listaVotiRisposta->length) && ($trovato==0))
                                    {
                                        $voto = $listaVotiRisposta->item($j);

                                        $creatoreVoto = $voto->firstChild;
                                        $creatoreVotoValue = $creatoreVoto->textContent;

                                        if($creatoreVotoValue == $username)
                                        {
                                            $trovato = 1;
                                            $valoreVoto = $creatoreVoto->nextSibling;
                                            if($valoreVoto->textContent == "false")
                                            {
                                                $risposta->removeChild($voto);
                                                if($TipoUtente == "cliente")
                                                {
                                                    if($_SESSION['type'] == "cliente")
                                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 10);
                                                    else
                                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 15);
                                                
                                                    $profilo->replaceChild($nuovaReputazione,$reputazione);
                                                }
                                                //avviso che siccome gia aveva messo dislike, questo è stato rimosso
                                            }
                                            else
                                            {
                                                $nuovoValoreVoto = $docDomande->createElement("valoreVoto", "false");
                                                $voto->replaceChild($nuovoValoreVoto,$valoreVoto);

                                                if($TipoUtente == "cliente")
                                                {
                                                    if($_SESSION['type'] == "cliente")
                                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 20);
                                                    else
                                                        $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 30);
                                                
                                                    $profilo->replaceChild($nuovaReputazione,$reputazione);
                                                }
                                                //avviso che siccome aveva gia messo like, è stato sostituito con un dislike
                                            }

                                        }
                                        $j++;
                                    }

                                    if($trovato == 0)
                                    {
                                        $nuovoCreatoreVoto = $docDomande->createElement("creatore", $username);
                                        $nuovoValoreVoto = $docDomande->createElement("valoreVoto", "false");
                                        $nuovoVoto = $docDomande->createElement("votoRisposta");

                                        $risposta->appendChild($nuovoVoto);
                                        $nuovoVoto->appendChild($nuovoCreatoreVoto);
                                        $nuovoVoto->appendChild($nuovoValoreVoto);

                                        if($TipoUtente == "cliente")
                                        {
                                            if($_SESSION['type'] == "cliente")
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 10);
                                            else
                                                $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 15);
                                                
                                            $profilo->replaceChild($nuovaReputazione,$reputazione);
                                        }

                                        //avviso di aggiunta nuovo dislike 
                                    }
                                }
                            }
                        }
                        $i++;
                    }

                    if($TipoUtente == "cliente")
                    {
                        $percorso = "../XML/account.xml";
                        $docAccount->save($percorso);
                    }

                    $percorso = "../XML/domande.xml";
                    $docDomande->save($percorso);
                }

                if (isset($_POST['rispondi']))
                {
                    $idDomandaPOST = $_POST['rispondi'];
                    if($_POST["testoNuovaRisposta$idDomandaPOST"] != "")
                    {
                        $nuovaRisposta = $docDomande->createElement("risposta");

                        $numRisposteTotali = $rootDomande->getElementsByTagName('risposta')->length;
                        $nuovoIDRisp = $docDomande->createElement("idRisp", $numRisposteTotali + 1);
                        $nuovaRisposta->appendChild($nuovoIDRisp);

                        $nuovaDataRisp = $docDomande->createElement("dataCreaz", date('Y-m-d'));
                        $nuovaRisposta->appendChild($nuovaDataRisp);

                        $nuovoCreatoreRisp = $docDomande->createElement("creatore", $username);
                        $nuovaRisposta->appendChild($nuovoCreatoreRisp);

                        for ($i=0; $i<$listaDomande->length; $i++) 
                        {
                            $domanda = $listaDomande->item($i);
                        
                            $idDomanda = $domanda->firstChild;
                            $idDomandaValue = $idDomanda->textContent;

                            if($idDomandaValue == $idDomandaPOST)
                            {
                                $nuovoTestoRisp = $docDomande->createElement("testo", $_POST["testoNuovaRisposta$idDomandaValue"]);
                                $nuovaRisposta->appendChild($nuovoTestoRisp);
                                
                                $dataCreazDomanda = $idDomanda->nextSibling;

                                $creatoreDomanda = $dataCreazDomanda->nextSibling;

                                $testoDomanda = $creatoreDomanda->nextSibling;

                                $listaRisposte = $domanda->getElementsByTagName('risposta');

                                if($listaRisposte->length == 0)
                                    $domanda->insertBefore($nuovaRisposta,$testoDomanda->nextSibling); //inserisce la nuova risposta prima degli eventuali votiDomanda
                                else
                                {
                                    $arrayRisposte[0] = $testoDomanda->nextSibling;
                                    for($k=1; $k<$listaRisposte->length; $k++)
                                    {
                                        $arrayRisposte[$k]= $arrayRisposte[$k-1]->nextSibling;
                                    }
                                    $domanda->insertBefore($nuovaRisposta,$arrayRisposte[$k-1]->nextSibling);

                                }
                                
                                $percorso = "../XML/domande.xml";
                                $docDomande->save($percorso);

                            }
                        }
                    }
                }


                if (isset($_POST['rimuoviDomanda']))
                {
                    for ($i=0; $i<$listaDomande->length; $i++) 
                    {
                        $domanda = $listaDomande->item($i);
                        
                        $idDomanda = $domanda->firstChild;
                        $idDomandaValue = $idDomanda->textContent;

                        if($idDomandaValue == $_POST['rimuoviDomanda'])
                        {
                            $listaRisposte = $domanda->getElementsByTagName('risposta');

                            for($k=0; $k<$listaRisposte->length; $k++)
                            {
                                $risposta = $listaRisposte->item($k);

                                $idRisposta = $risposta->firstChild;
                                $idRispostaValue = $idRisposta->textContent;

                                $dataCreazRisposta = $idRisposta->nextSibling;

                                $creatoreRisposta = $dataCreazRisposta->nextSibling;
                                $creatoreRispostaValue = $creatoreRisposta->textContent;

                                $query = "SELECT id, type FROM users WHERE username = '$creatoreRispostaValue'";
                                if (!$result = mysqli_query($con, $query)) 
                                {
                                    printf("errore nella query di ricerca di ID e tipo \n");
                                    exit();
                                }
                            
                                $row = mysqli_fetch_array($result);
                        
                                if ($row) {  

                                    $IDUtente = $row['id']; 
                                    $TipoUtente = $row['type'];               
                                }

                                if($TipoUtente == "cliente")
                                {
                                    $xmlString = "";
                                    foreach(file("../XML/account.xml") as $nodo){
                                        $xmlString .= trim($nodo);
                                    }

                                    $docAccount = new DOMDocument();
                                    $docAccount->loadXML($xmlString);

                                    $rootAccount = $docAccount->documentElement;
                                    $listaAccount = $rootAccount->childNodes;

                                    $a=0;
                                    $trovato=0;
                                    while(($a<$listaAccount->length) && ($trovato==0))
                                    {
                                        $profilo = $listaAccount->item($a);
                                        $idAcc = $profilo->firstChild;
                                        $idAccValue = $idAcc->textContent;
                                        if($idAccValue == $IDUtente)  
                                        {
                                            $trovato=1;
                                            $nome = $idAcc->nextSibling;
                                            $cognome = $nome->nextSibling;   
                                            $soldi = $cognome->nextSibling; 
                                            $crediti = $soldi->nextSibling;
                                            $reputazione = $crediti->nextSibling;
                                            $reputazioneValue = $reputazione->textContent;
                                        }          
                                        $a++;
                                    }

                                    $listaVotiRisposta = $risposta->getElementsByTagName('votoRisposta');

                                    $valoreNuovaReputazione = $reputazioneValue;

                                    for($j=0; $j<$listaVotiRisposta->length; $j++) 
                                    {
                                        $voto = $listaVotiRisposta->item($j);

                                        $creatoreVoto = $voto->firstChild;
                                        $creatoreVotoValue = $creatoreVoto->textContent;

                                        $queryTipo = "SELECT type FROM users WHERE username = '$creatoreVotoValue'";
                                        if (!$resultTipo = mysqli_query($con, $queryTipo)) 
                                        {
                                            printf("errore nella query di ricerca del tipo di utente \n");
                                            exit();
                                        }
                                
                                        $row = mysqli_fetch_array($resultTipo);
                            
                                        if ($row) {  
                                            $TipoUtenteVoto = $row['type'];               
                                        }

                                        $valoreVoto = $creatoreVoto->nextSibling;
                                        if($valoreVoto->textContent == "true")
                                        {
                                            if($TipoUtenteVoto == "cliente")
                                                $valoreNuovaReputazione = $valoreNuovaReputazione - 10;
                                            else
                                                $valoreNuovaReputazione = $valoreNuovaReputazione - 15;
                                        }
                                        else
                                        {
                                            if($TipoUtenteVoto == "cliente")
                                                $valoreNuovaReputazione = $valoreNuovaReputazione + 10;
                                            else
                                                $valoreNuovaReputazione = $valoreNuovaReputazione + 15;
                                        }
                                    }

                                    $nuovaReputazione = $docAccount->createElement("reputazione", $valoreNuovaReputazione);
                                    $profilo->replaceChild($nuovaReputazione,$reputazione);

                                    $percorso = "../XML/account.xml";
                                    $docAccount->save($percorso);
                                }

                                $domanda->removeChild($risposta);
                            }

                            $listaRisposteTotali = $rootDomande->getElementsByTagName('risposta');
                            for ($j=0; $j<$listaRisposteTotali->length; $j++) 
                            {
                                $risposta = $listaRisposteTotali->item($j);
                                $idRisposta = $risposta->firstChild;
                                $nuovoID = $docDomande->createElement("idRisp", $j+1);
                                $risposta->replaceChild($nuovoID,$idRisposta);
                            }

                            $listaVotiDomanda = $domanda->getElementsByTagName('votoDomanda');
                            if($listaVotiDomanda->length != 0)
                            {
                                $dataCreazDomanda = $idDomanda->nextSibling;

                                $creatoreDomanda = $dataCreazDomanda->nextSibling;
                                $creatoreDomandaValue = $creatoreDomanda->textContent;

                                $query = "SELECT id FROM users WHERE username = '$creatoreDomandaValue'";
                                if (!$result = mysqli_query($con, $query)) 
                                {
                                    printf("errore nella query di ricerca di ID \n");
                                    exit();
                                }
                            
                                $row = mysqli_fetch_array($result);
                        
                                if ($row) {  

                                    $IDCliente = $row['id'];              
                                }

                                $xmlString = "";
                                foreach(file("../XML/account.xml") as $nodo){
                                    $xmlString .= trim($nodo);
                                }

                                $docAccount = new DOMDocument();
                                $docAccount->loadXML($xmlString);

                                $rootAccount = $docAccount->documentElement;
                                $listaAccount = $rootAccount->childNodes;

                                $a=0;
                                $trovato=0;
                                while(($a<$listaAccount->length) && ($trovato==0))
                                {
                                    $profilo = $listaAccount->item($a);
                                    $idAcc = $profilo->firstChild;
                                    $idAccValue = $idAcc->textContent;
                                    if($idAccValue == $IDCliente)  
                                    {
                                        $trovato=1;
                                        $nome = $idAcc->nextSibling;
                                        $cognome = $nome->nextSibling;   
                                        $soldi = $cognome->nextSibling; 
                                        $crediti = $soldi->nextSibling;
                                        $reputazione = $crediti->nextSibling;
                                        $reputazioneValue = $reputazione->textContent;
                                    }          
                                    $a++;
                                }

                                $valoreNuovaReputazione = $reputazioneValue;
                                for($j=0; $j<$listaVotiDomanda->length; $j++)
                                {
                                    $voto = $listaVotiDomanda->item($j);

                                    $creatoreVoto = $voto->firstChild;
                                    $creatoreVotoValue = $creatoreVoto->textContent;

                                    $queryTipo = "SELECT type FROM users WHERE username = '$creatoreVotoValue'";
                                    if (!$resultTipo = mysqli_query($con, $queryTipo)) 
                                    {
                                        printf("errore nella query di ricerca del tipo di utente \n");
                                        exit();
                                    }
                                
                                    $row = mysqli_fetch_array($resultTipo);
                            
                                    if ($row) {  
                                        $TipoUtenteVoto = $row['type'];               
                                    }

                                    $valoreVoto = $creatoreVoto->nextSibling;
                                    if($valoreVoto->textContent == "true")
                                    {
                                        if($TipoUtenteVoto == "cliente")
                                            $valoreNuovaReputazione = $valoreNuovaReputazione - 10;
                                        else
                                            $valoreNuovaReputazione = $valoreNuovaReputazione - 15;
                                    }
                                    else
                                    {
                                        if($TipoUtenteVoto == "cliente")
                                            $valoreNuovaReputazione = $valoreNuovaReputazione + 10;
                                        else
                                            $valoreNuovaReputazione = $valoreNuovaReputazione + 15;
                                    }
                                }

                                $nuovaReputazione = $docAccount->createElement("reputazione", $valoreNuovaReputazione);
                                $profilo->replaceChild($nuovaReputazione,$reputazione);

                                $percorso = "../XML/account.xml";
                                $docAccount->save($percorso);

                            }

                            $rootDomande->removeChild($domanda);

                        }
                    }

                    $listaDomande = $rootDomande->getElementsByTagName('domanda');
                    for ($i=0; $i<$listaDomande->length; $i++) 
                    {
                        $domanda = $listaDomande->item($i);
                        $idDomanda = $domanda->firstChild;
                        $nuovoID = $docDomande->createElement("idDom", $i+1);
                        $domanda->replaceChild($nuovoID,$idDomanda);
                    }

                    $percorso = "../XML/domande.xml";
                    $docDomande->save($percorso);
                    //avviso domanda eliminata

                }


                if (isset($_POST['rimuoviRisposta']))
                {
                    $listaRisposteTotali = $rootDomande->getElementsByTagName('risposta');
                    $i=0;
                    $trovato=0;
                    while(($i<$listaRisposteTotali->length) && ($trovato==0))
                    {
                        $risposta = $listaRisposteTotali->item($i);

                        $idRisposta = $risposta->firstChild;
                        $idRispostaValue = $idRisposta->textContent;
                        if($idRispostaValue == $_POST['rimuoviRisposta'])
                        {
                            $trovato=1;

                            $listaVotiRisposta = $risposta->getElementsByTagName('votoRisposta');
                            if($listaVotiRisposta->length != 0)
                            {
                                $dataCreazRisposta = $idRisposta->nextSibling;

                                $creatoreRisposta = $dataCreazRisposta->nextSibling;
                                $creatoreRispostaValue = $creatoreRisposta->textContent;

                                $query = "SELECT id, type FROM users WHERE username = '$creatoreRispostaValue'";
                                if (!$result = mysqli_query($con, $query)) 
                                {
                                    printf("errore nella query di ricerca di ID e tipo \n");
                                    exit();
                                }
                                
                                $row = mysqli_fetch_array($result);
                            
                                if ($row) {  

                                    $IDUtente = $row['id']; 
                                    $TipoUtente = $row['type'];               
                                }

                                if($TipoUtente == "cliente")
                                {
                                    
                                    $xmlString = "";
                                    foreach(file("../XML/account.xml") as $nodo){
                                        $xmlString .= trim($nodo);
                                    }

                                    $docAccount = new DOMDocument();
                                    $docAccount->loadXML($xmlString);

                                    $rootAccount = $docAccount->documentElement;
                                    $listaAccount = $rootAccount->childNodes;

                                    $a=0;
                                    $accountTrovato=0;
                                    while(($a<$listaAccount->length) && ($accountTrovato==0))
                                    {
                                        $profilo = $listaAccount->item($a);
                                        $idAcc = $profilo->firstChild;
                                        $idAccValue = $idAcc->textContent;
                                        if($idAccValue == $IDUtente)  
                                        {
                                            $accountTrovato=1;
                                            $nome = $idAcc->nextSibling;
                                            $cognome = $nome->nextSibling;   
                                            $soldi = $cognome->nextSibling; 
                                            $crediti = $soldi->nextSibling;
                                            $reputazione = $crediti->nextSibling;
                                            $reputazioneValue = $reputazione->textContent;
                                        }          
                                        $a++;
                                    }

                                    $valoreNuovaReputazione = $reputazioneValue;

                                    for($k=0; $k<$listaVotiRisposta->length; $k++) 
                                    {
                                        $voto = $listaVotiRisposta->item($k);

                                        $creatoreVoto = $voto->firstChild;
                                        $creatoreVotoValue = $creatoreVoto->textContent;

                                        $queryTipo = "SELECT type FROM users WHERE username = '$creatoreVotoValue'";
                                        if (!$resultTipo = mysqli_query($con, $queryTipo)) 
                                        {
                                            printf("errore nella query di ricerca del tipo di utente \n");
                                            exit();
                                        }
                                
                                        $row = mysqli_fetch_array($resultTipo);
                            
                                        if ($row) {  
                                            $TipoUtenteVoto = $row['type'];               
                                        }

                                        $valoreVoto = $creatoreVoto->nextSibling;
                                        if($valoreVoto->textContent == "true")
                                        {
                                            if($TipoUtenteVoto == "cliente")
                                                $valoreNuovaReputazione = $valoreNuovaReputazione - 10;
                                            else
                                                $valoreNuovaReputazione = $valoreNuovaReputazione - 15;
                                        }
                                        else
                                        {
                                            if($TipoUtenteVoto == "cliente")
                                                $valoreNuovaReputazione = $valoreNuovaReputazione + 10;
                                            else
                                                $valoreNuovaReputazione = $valoreNuovaReputazione + 15;
                                        }
                                    }

                                    $nuovaReputazione = $docAccount->createElement("reputazione", $valoreNuovaReputazione);
                                    $profilo->replaceChild($nuovaReputazione,$reputazione);

                                    $percorso = "../XML/account.xml";
                                    $docAccount->save($percorso);
                                }
                            }

                            $domandaPadre=$risposta->parentNode;
                            $domandaPadre->removeChild($risposta);

                            $listaRisposteTotali = $rootDomande->getElementsByTagName('risposta');
                            for ($j=0; $j<$listaRisposteTotali->length; $j++) 
                            {
                                $risposta = $listaRisposteTotali->item($j);
                                $idRisposta = $risposta->firstChild;
                                $nuovoID = $docDomande->createElement("idRisp", $j+1);
                                $risposta->replaceChild($nuovoID,$idRisposta);
                            }
                        }
                        $i++;
                    }

                    $percorso = "../XML/domande.xml";
                    $docDomande->save($percorso);
                    //avviso risposta eliminata
                }


                if (isset($_POST['chiudiDomanda']))
                {
                    $i=0;
                    $trovata=0;
                    while (($i<$listaDomande->length) && ($trovata==0)) 
                    {
                        $domanda = $listaDomande->item($i);
                        
                        $idDomanda = $domanda->firstChild;
                        $idDomandaValue = $idDomanda->textContent;

                        if($idDomandaValue == $_POST['chiudiDomanda'])
                        {
                            $trovata=1;

                            $domanda->setAttribute("stato", "chiusa");

                            $percorso = "../XML/domande.xml";
                            $docDomande->save($percorso);

                            //avviso domanda chiusa con successo
                        }
                        $i++;
                    }
                }
?>
<body>
    <div class="body" style="font-family: 'Times New Roman', Times, serif;">
        <div class="header" style="font-family: monospace;">
  	        <h2>Domande del forum</h2>
        </div>

        <?php
            if($_SESSION['type'] == "cliente")
                echo "<a id=\"creaDom\" href=\"creaDomanda.php\">Crea una nuova domanda</a>";

            if ($rootDomande->getElementsByTagName("domanda")->length == 0){
                echo "<p class=\"error\">Non ci sono domande nel forum</p>";
            } 
            else
            {
                echo "<form method=\"post\" action=\"domande.php\">";
                echo"<ul id=\"domande\">";
                for ($i=0; $i<$listaDomande->length; $i++) 
                {
                    $domanda = $listaDomande->item($i);
                    
                    $statoDomanda = $domanda->getAttribute('stato');

                    $idDomanda = $domanda->firstChild;
                    $idDomandaValue = $idDomanda->textContent;

                    $dataCreazDomanda = $idDomanda->nextSibling;
                    $dataCreazDomandaValue = $dataCreazDomanda->textContent;

                    $creatoreDomanda = $dataCreazDomanda->nextSibling;
                    $creatoreDomandaValue = $creatoreDomanda->textContent;

                    $testoDomanda = $creatoreDomanda->nextSibling;
                    $testoDomandaValue = $testoDomanda->textContent;

                    $listaRisposte = $domanda->getElementsByTagName('risposta');

                    for($k=0; $k<$listaRisposte->length; $k++)
                    {
                        $risposta = $listaRisposte->item($k);
                        
                        $idRisposta = $risposta->firstChild;
                        $arrayIDRispostaValue[$k] = $idRisposta->textContent;
                        
                        $dataCreazRisposta = $idRisposta->nextSibling;
                        $arrayDataCreazRispostaValue[$k] = $dataCreazRisposta->textContent;

                        $creatoreRisposta = $dataCreazRisposta->nextSibling;
                        $arrayCreatoreRispostaValue[$k] = $creatoreRisposta->textContent;

                        $testoRisposta = $creatoreRisposta->nextSibling;
                        $arrayTestoRispostaValue[$k] = $testoRisposta->textContent;

                        $listaVotiRisposta = $risposta->getElementsByTagName('votoRisposta');
                        
                        for($j=0; $j<$listaVotiRisposta->length; $j++)
                        {
                            $voto = $listaVotiRisposta->item($j);
                    
                            $creatoreVoto = $voto->firstChild;

                            $valoreVoto = $creatoreVoto->nextSibling;
                            $matriceValoreVotoRisp[$k][$j] = $valoreVoto->textContent;

                        }
                    }

                    $listaVotiDomanda = $domanda->getElementsByTagName('votoDomanda');
                    $numLikeDomanda=0;
                    $numDislikeDomanda=0;
                    for($k=0; $k<$listaVotiDomanda->length; $k++)
                    {
                        $voto = $listaVotiDomanda->item($k);

                        $creatoreVoto = $voto->firstChild;

                        $valoreVoto = $creatoreVoto->nextSibling;
                        
                        if($valoreVoto->textContent == "true")
                            $numLikeDomanda++;
                        else
                            $numDislikeDomanda++;
                    }
                

                    if($_SESSION['type'] == "cliente")
                    {
                        if($erroreLike == true){
                            echo "<p id= \"error\" style=\"text-align: left; font-size: 100%;\">Non puoi mettere like/dislike ad una tua domanda o risposta </p>";
                            $erroreLike = false;
                        }
                        echo "
                        <li><p style=\"color: #D2691E;\">$testoDomandaValue</p> <br /> Postata da <strong>$creatoreDomandaValue</strong> in data $dataCreazDomandaValue <br /> Stato: <strong>$statoDomanda</strong> <br /> <button type=\"submit\" name=\"likeDomanda\" value=\"$idDomandaValue\"><img src=\"../images/like.png\" style=\"width: 25px; height: auto;\"></button>$numLikeDomanda <button type=\"submit\" name=\"dislikeDomanda\" value=\"$idDomandaValue\"><img src=\"../images/dislike.png\" style=\"width: 25px; height: auto;\"></button>$numDislikeDomanda</li>
                        <button class=\"btn\" style=\"margin-top: 15px; background-color: crimson;\" type =\"button\" id=\"$idDomandaValue\" onclick=\"funzioneMostraRisposte(this);\">Mostra Risposte</button>
                        <ul style=\"display: none;\" id=\"ul$idDomandaValue\">";
                        if($listaRisposte->length == 0)
                            echo "<h3>Questa domanda non ha risposte<h3>";
                        else
                        {
                            for($k=0; $k<$listaRisposte->length; $k++)
                            {   
                                $risposta = $listaRisposte->item($k);

                                $numLikeRisposta=0;
                                $numDislikeRisposta=0;

                                $listaVotiRisposta = $risposta->getElementsByTagName('votoRisposta');
                                
                                for($j=0; $j<$listaVotiRisposta->length; $j++)
                                {   
                                    if(($matriceValoreVotoRisp[$k][$j]) == "true")
                                        $numLikeRisposta++;
                                    else
                                        $numDislikeRisposta++;  
                                }

                                echo "
                                <li><strong style=\"color: crimson;\">$arrayTestoRispostaValue[$k]</strong><br /> Risposta di <strong>$arrayCreatoreRispostaValue[$k]</strong> in data $arrayDataCreazRispostaValue[$k]<br /> <button type=\"submit\" name=\"likeRisposta\" value=\"$arrayIDRispostaValue[$k]\"><img src=\"../images/like.png\" style=\"width: 25px; height: auto;\"></button>$numLikeRisposta <button type=\"submit\" name=\"dislikeRisposta\" value=\"$arrayIDRispostaValue[$k]\"><img src=\"../images/dislike.png\" style=\"width: 25px; height: auto;\"></button>$numDislikeRisposta</li>";
                            }
                        }
                        echo "<br />";
                        if($statoDomanda == "aperta")
                        {
                            echo "
                            <button class=\"btn\" style=\"margin-top: 15px; background-color: crimson;\" type =\"button\" id=\"Risp$idDomandaValue\" onclick=\"funzioneRispondi(this);\">Rispondi</button>
                            <div style=\"display: none;\" id=\"divRisp$idDomandaValue\">
                                <div class=\"input-group\">";
                                if(isset($_POST['rispondi']))
                                { 
                                    if (isset($_POST['rispondi']) && empty($_POST["testoNuovaRisposta$idDomandaValue"])) 
                                        echo "<p id= \"error\">Testo risposta richiesto</p>";
                                } 
                                    echo "<input type=\"text\" name=\"testoNuovaRisposta$idDomandaValue\" placeholder=\"Rispondi ...\"/>
                                </div>
                                <button type=\"submit\" class=\"btn\" value=\"$idDomandaValue\" name=\"rispondi\">Pubblica risposta</button>
                                <button class=\"btn\" type =\"button\" id=\"Risp$idDomandaValue\" onclick=\"funzioneAnnullaRispondi(this);\">Annulla</button>
                                <br />
                            </div>";
                        }
                        echo "
                        <button class=\"btn\" style=\"margin-top: 15px; background-color: crimson;\" type =\"button\" id=\"$idDomandaValue\" onclick=\"funzioneNascondiRisposte(this);\">Nascondi Risposte</button>
                        </ul>";
                
                    }


                    if($_SESSION['type'] == "gestore")
                    {
                        echo "
                        <li><p style=\"color: #D2691E;\">$testoDomandaValue</p> <br /> Postata da <strong>$creatoreDomandaValue</strong> in data $dataCreazDomandaValue <br /> Stato: <strong>$statoDomanda</strong> <br /> <button type=\"submit\" name=\"likeDomanda\" value=\"$idDomandaValue\"><img src=\"../images/like.png\" style=\"width: 25px; height: auto;\"></button>$numLikeDomanda <button type=\"submit\" name=\"dislikeDomanda\" value=\"$idDomandaValue\"><img src=\"../images/dislike.png\" style=\"width: 25px; height: auto;\"></button>$numDislikeDomanda</li>
                        <br /><button class=\"btn\" style=\"margin-top: 15px; background-color: crimson;\" type =\"button\" id=\"$idDomandaValue\" onclick=\"funzioneMostraRisposte(this);\">Mostra Risposte</button><br /><br />
                        <button type=\"submit\" class=\"btn\" name=\"rimuoviDomanda\" value=\"$idDomandaValue\">Elimina domanda</button>";
                        if($statoDomanda == "aperta")
                            echo "&nbsp<button type=\"submit\" class=\"btn\" name=\"chiudiDomanda\" value=\"$idDomandaValue\">Chiudi domanda</button>";
                        echo "<ul style=\"display: none;\" id=\"ul$idDomandaValue\">";
                        if($listaRisposte->length == 0)
                            echo "<h3>Questa domanda non ha risposte</h3>";
                        else
                        {
                            for($k=0; $k<$listaRisposte->length; $k++)
                            {   
                                $risposta = $listaRisposte->item($k);

                                $numLikeRisposta=0;
                                $numDislikeRisposta=0;

                                $listaVotiRisposta = $risposta->getElementsByTagName('votoRisposta');
                                
                                for($j=0; $j<$listaVotiRisposta->length; $j++)
                                {   
                                    if(($matriceValoreVotoRisp[$k][$j]) == "true")
                                        $numLikeRisposta++;
                                    else
                                        $numDislikeRisposta++;  
                                }

                                echo "
                                <li style=\"margin-top: 15px;\"><strong style=\"color: crimson;\">$arrayTestoRispostaValue[$k]</strong><br /> Risposta di <strong>$arrayCreatoreRispostaValue[$k]</strong> in data $arrayDataCreazRispostaValue[$k]<br /> <button type=\"submit\" name=\"likeRisposta\" value=\"$arrayIDRispostaValue[$k]\"><img src=\"../images/like.png\" style=\"width: 25px; height: auto;\"></button>$numLikeRisposta <button type=\"submit\" name=\"dislikeRisposta\" value=\"$arrayIDRispostaValue[$k]\"><img src=\"../images/dislike.png\" style=\"width: 25px; height: auto;\"></button>$numDislikeRisposta</li>
                                <br /><button type=\"submit\" class=\"btn\" name=\"rimuoviRisposta\" value=\"$arrayIDRispostaValue[$k]\">Elimina risposta</button>";
                            }
                        }
                        echo "<br />";
                        if($statoDomanda == "aperta")
                        {
                            echo "
                            <button class=\"btn\" style=\"margin-top: 15px; background-color: crimson;\" type =\"button\" id=\"Risp$idDomandaValue\" onclick=\"funzioneRispondi(this);\">Rispondi</button>
                            <div style=\"display: none;\" id=\"divRisp$idDomandaValue\">
                                <div class=\"input-group\">";
                                { 
                                    if (isset($_POST['rispondi']) && empty($_POST["testoNuovaRisposta$idDomandaValue"])) 
                                        echo "<p id= \"error\">Testo risposta richiesto</p>";
                                }     
                                echo "<input type=\"text\" name=\"testoNuovaRisposta$idDomandaValue\" placeholder=\"Rispondi ...\"/>
                                </div>
                                <button type=\"submit\" class=\"btn\" value=\"$idDomandaValue\" name=\"rispondi\">Pubblica risposta</button>
                                <button class=\"btn\" type =\"button\" id=\"Risp$idDomandaValue\" onclick=\"funzioneAnnullaRispondi(this);\">Annulla</button>
                                <br />
                            </div>";
                        }
                        echo "
                        <button class=\"btn\" style=\"margin-top: 15px; background-color: crimson;\" type =\"button\" id=\"$idDomandaValue\" onclick=\"funzioneNascondiRisposte(this);\">Nascondi Risposte</button>
                        </ul>";
                
                    }

                    if($_SESSION['type'] == "amministratore")
                    {
                        echo "
                        <li><p style=\"color: #D2691E;\">$testoDomandaValue</p> <br /> Postata da <strong>$creatoreDomandaValue</strong> in data $dataCreazDomandaValue <br /> Stato: <strong>$statoDomanda</strong> <br /> <img src=\"../images/like.png\" style=\"width: 25px; height: auto;\"> $numLikeDomanda <img src=\"../images/dislike.png\" style=\"width: 25px; height: auto;\"> $numDislikeDomanda</li>
                        <br /><button class=\"btn\" style=\"margin-top: 15px; background-color: crimson;\" type =\"button\" id=\"$idDomandaValue\" onclick=\"funzioneMostraRisposte(this);\">Mostra Risposte</button><br /><br />
                        <button type=\"submit\" class=\"btn\" name=\"elevaDomanda\" value=\"$testoDomandaValue\">Eleva domanda a FAQ</button>
                        <ul style=\"display: none;\" id=\"ul$idDomandaValue\">";
                        if($listaRisposte->length == 0)
                            echo "<h3>Questa domanda non ha risposte</h3>";
                        else
                        {
                            for($k=0; $k<$listaRisposte->length; $k++)
                            {   
                                $risposta = $listaRisposte->item($k);

                                $numLikeRisposta=0;
                                $numDislikeRisposta=0;

                                $listaVotiRisposta = $risposta->getElementsByTagName('votoRisposta');
                                
                                for($j=0; $j<$listaVotiRisposta->length; $j++)
                                {   
                                    if(($matriceValoreVotoRisp[$k][$j]) == "true")
                                        $numLikeRisposta++;
                                    else
                                        $numDislikeRisposta++;  
                                }

                                echo "
                                <li style=\"margin-top: 15px;\"><strong style=\"color: crimson;\">$arrayTestoRispostaValue[$k]</strong><br /> Risposta di <strong>$arrayCreatoreRispostaValue[$k]</strong> in data $arrayDataCreazRispostaValue[$k]<br /> <img src=\"../images/like.png\" style=\"width: 25px; height: auto;\"> $numLikeRisposta <img src=\"../images/dislike.png\" style=\"width: 25px; height: auto;\"> $numDislikeRisposta</li>";
                            }
                        }
                        echo "<br />";
                        if($statoDomanda == "aperta")
                        {
                            echo "
                            <button class=\"btn\" style=\"margin-top: 15px; background-color: crimson;\" type =\"button\" id=\"Risp$idDomandaValue\" onclick=\"funzioneRispondi(this);\">Rispondi</button>
                            <div style=\"display: none;\" id=\"divRisp$idDomandaValue\">
                                <div class=\"input-group\">";
                                { 
                                    if (isset($_POST['rispondi']) && empty($_POST["testoNuovaRisposta$idDomandaValue"])) 
                                        echo "<p id= \"error\">Testo risposta richiesto</p>";
                                }     
                                echo "<input type=\"text\" name=\"testoNuovaRisposta$idDomandaValue\" placeholder=\"Rispondi ...\"/>
                                </div>
                                <button type=\"submit\" class=\"btn\" value=\"$idDomandaValue\" name=\"rispondi\">Pubblica risposta</button>
                                <button class=\"btn\" type =\"button\" id=\"Risp$idDomandaValue\" onclick=\"funzioneAnnullaRispondi(this);\">Annulla</button>
                                <br />
                            </div>";
                        }
                        echo "
                        <button class=\"btn\" style=\"margin-top: 15px; background-color: crimson;\" type =\"button\" id=\"$idDomandaValue\" onclick=\"funzioneNascondiRisposte(this);\">Nascondi Risposte</button>
                        </ul>";
                
                    }

                
                
                
                }
                echo "</ul>
                </form>";

            }

            if($creaFAQ == true){
                $creaFAQ = false;
                echo "<script type=\"text/javascript\">location.replace(\"creaFAQ.php\");</script>";
            }
        ?>
    </div>
</body>
</html>
