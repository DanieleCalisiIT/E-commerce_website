<!--
In questo script si gestisce gran parte di quello che riguarda il forum delle recensioni. 
    Riassumendo, la struttura di questo script è: 
    1: Gestione form (funzionalità)
    2: Form
    3: Avvisi
La gestione della form è stata fatta con una serie di if(isset()) così da isolare le varie funzionalità del forum; Le funzionalità sono: 
    --mettere like o dislike alle recensioni, che incidono sulla reputazione di chi li  riceve (non puoi mettere like/dislike ai tuoi post;
     mettere like/dislike dove lo hai già messo lo elimina; mettere like/dislike dove hai gia messo dislike/like lo sostituisce; 
     i like/dislike dei gestori incidono maggiormente sulla reputazione).
    --eliminare una recensione(se sei gestore): tutti i like/dislike assegnati alla recensione vengono quindi eliminati (incidendo sulla reputazione di chi li aveva ricevuti).
 La form mostra tutte recensioni, permettendo di interagire con esse. 
 In base alla tipologia di utente che arriva in questa pagina, la form avrà una struttura diversa, permettendo o non permettendo di effettuare determinate operazioni (elencate sopra). 
 Se chi arriva su questa pagina è un Cliente, avrà la possibilità di cliccare sul link che porta alla creazione di una nuova recensione (creaRecensione.php). 
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
    <title>Recensioni del forum</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<?php

    $stringaXML = "";
    foreach ( file("../XML/recensioni.xml") as $nodo )   
    {
        $stringaXML .= trim($nodo);
    }

    $docRecensioni = new DOMDocument();
    $docRecensioni->loadXML($stringaXML);

    $rootRecensioni = $docRecensioni->documentElement;
    $listaRecensioni = $rootRecensioni->childNodes;

    if (isset($_POST['like']))
    {
        for ($i=0; $i<$listaRecensioni->length; $i++) 
        {
            $recensione = $listaRecensioni->item($i);

            $idRecensione = $recensione->firstChild;
            $idRecensioneValue = $idRecensione->textContent;

            if($idRecensioneValue == $_POST['like'])
            {
                $dataCreazRecensione = $idRecensione->nextSibling;

                $creatoreRecensione = $dataCreazRecensione->nextSibling;
                $creatoreRecensioneValue = $creatoreRecensione->textContent;

                if($creatoreRecensioneValue == $username)
                    echo "<p id= \"error\">Non puoi mettere like/dislike ad una tua recensione </p>";
                else{

                    $xmlString = "";
                    foreach(file("../XML/account.xml") as $nodo){
                        $xmlString .= trim($nodo);
                    }

                    $docAccount = new DOMDocument();
                    $docAccount->loadXML($xmlString);

                    $rootAccount = $docAccount->documentElement;
                    $listaAccount = $rootAccount->childNodes;

                    $queryID = "SELECT id FROM users WHERE username = '$creatoreRecensioneValue'";
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

                    $listaVotiRecensione = $recensione->getElementsByTagName('voto');

                    $k=0;
                    $trovato=0;
                    while(($k<$listaVotiRecensione->length) && ($trovato==0))
                    {
                        $voto = $listaVotiRecensione->item($k);

                        $creatoreVoto = $voto->firstChild;
                        $creatoreVotoValue = $creatoreVoto->textContent;

                        if($creatoreVotoValue == $username)
                        {
                            $trovato = 1;
                            $valoreVoto = $creatoreVoto->nextSibling;
                            if($valoreVoto->textContent == "true")
                            {
                                $recensione->removeChild($voto);

                                if($_SESSION['type'] == "cliente")
                                    $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 10);
                                else
                                    $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue - 15);
                                
                                $profilo->replaceChild($nuovaReputazione,$reputazione);
                                //avviso che siccome gia aveva messo like, questo è stato rimosso
                            }
                            else
                            {
                                $nuovoValoreVoto = $docRecensioni->createElement("valoreVoto", "true");
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
                        $nuovoCreatoreVoto = $docRecensioni->createElement("creatore", $username);
                        $nuovoValoreVoto = $docRecensioni->createElement("valoreVoto", "true");
                        $nuovoVoto = $docRecensioni->createElement("voto");

                        $recensione->appendChild($nuovoVoto);
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

        $percorso = "../XML/recensioni.xml";
        $docRecensioni->save($percorso);
    }


    if (isset($_POST['dislike']))
    {
        for ($i=0; $i<$listaRecensioni->length; $i++) 
        {
            $recensione = $listaRecensioni->item($i);

            $idRecensione = $recensione->firstChild;
            $idRecensioneValue = $idRecensione->textContent;

            if($idRecensioneValue == $_POST['dislike'])
            {
                $dataCreazRecensione = $idRecensione->nextSibling;

                $creatoreRecensione = $dataCreazRecensione->nextSibling;
                $creatoreRecensioneValue = $creatoreRecensione->textContent;

                if($creatoreRecensioneValue == $username)
                    echo "<p id= \"error\">Non puoi mettere like/dislike ad una tua recensione </p>";
                else{

                    $xmlString = "";
                    foreach(file("../XML/account.xml") as $nodo){
                        $xmlString .= trim($nodo);
                    }

                    $docAccount = new DOMDocument();
                    $docAccount->loadXML($xmlString);

                    $rootAccount = $docAccount->documentElement;
                    $listaAccount = $rootAccount->childNodes;

                    $queryID = "SELECT id FROM users WHERE username = '$creatoreRecensioneValue'";
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

                    $listaVotiRecensione = $recensione->getElementsByTagName('voto');

                    $k=0;
                    $trovato=0;
                    while(($k<$listaVotiRecensione->length) && ($trovato==0))
                    {
                        $voto = $listaVotiRecensione->item($k);

                        $creatoreVoto = $voto->firstChild;
                        $creatoreVotoValue = $creatoreVoto->textContent;

                        if($creatoreVotoValue == $username)
                        {
                            $trovato = 1;
                            $valoreVoto = $creatoreVoto->nextSibling;
                            if($valoreVoto->textContent == "false")
                            {
                                $recensione->removeChild($voto);

                                if($_SESSION['type'] == "cliente")
                                    $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 10);
                                else
                                    $nuovaReputazione = $docAccount->createElement("reputazione", $reputazioneValue + 15);
                                
                                $profilo->replaceChild($nuovaReputazione,$reputazione);
                                //avviso che siccome gia aveva messo dislike, questo è stato rimosso
                            }
                            else
                            {
                                $nuovoValoreVoto = $docRecensioni->createElement("valoreVoto", "false");
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
                        $nuovoCreatoreVoto = $docRecensioni->createElement("creatore", $username);
                        $nuovoValoreVoto = $docRecensioni->createElement("valoreVoto", "false");
                        $nuovoVoto = $docRecensioni->createElement("voto");

                        $recensione->appendChild($nuovoVoto);
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

        $percorso = "../XML/recensioni.xml";
        $docRecensioni->save($percorso);
    }

    if (isset($_POST['rimuovi']))
    {
        for ($i=0; $i<$listaRecensioni->length; $i++) 
        {
            $recensione = $listaRecensioni->item($i);
                        
            $idRecensione = $recensione->firstChild;
            $idRecensioneValue = $idRecensione->textContent;

            if($idRecensioneValue == $_POST['rimuovi'])
            {
                $listaVotiRecensione = $recensione->getElementsByTagName('voto');
                if($listaVotiRecensione->length != 0)
                {
                    $dataCreazRecensione = $idRecensione->nextSibling;

                    $creatoreRecensione = $dataCreazRecensione->nextSibling;
                    $creatoreRecensioneValue = $creatoreRecensione->textContent;

                    $query = "SELECT id FROM users WHERE username = '$creatoreRecensioneValue'";
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
                    for($j=0; $j<$listaVotiRecensione->length; $j++)
                    {
                        $voto = $listaVotiRecensione->item($j);

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

                $rootRecensioni->removeChild($recensione);

            }
        }

        $listaRecensioni = $rootRecensioni->getElementsByTagName('recensione');
        for ($i=0; $i<$listaRecensioni->length; $i++) 
        {
            $recensione = $listaRecensioni->item($i);
            $idRecensione = $recensione->firstChild;
            $nuovoID = $docRecensioni->createElement("idRec", $i+1);
            $recensione->replaceChild($nuovoID,$idRecensione);
        }

        $percorso = "../XML/recensioni.xml";
        $docRecensioni->save($percorso);
        //avviso recensione eliminata

    }

?>
<body>
    <div class="body">
        <div class="header">
  	        <h2>Recensioni del forum</h2>
        </div>

        <?php
            if($_SESSION['type'] == "cliente")
                echo "<a id=\"creaRecens\" href=\"creaRecensione.php\">Crea una nuova recensione</a>";

            if ($rootRecensioni->getElementsByTagName("recensione")->length == 0){
                echo "Non ci sono recensioni nel forum";
            } 
            else
            {
                echo "<form method=\"post\" action=\"recensioni.php\">";
                echo"<ul id=\"recens\">";
                for ($i=0; $i<$listaRecensioni->length; $i++) 
                {
                    $recensione = $listaRecensioni->item($i);

                    $idRecensione = $recensione->firstChild;
                    $idRecensioneValue = $idRecensione->textContent;

                    $dataCreazRecensione = $idRecensione->nextSibling;
                    $dataCreazRecensioneValue = $dataCreazRecensione->textContent;

                    $creatoreRecensione = $dataCreazRecensione->nextSibling;
                    $creatoreRecensioneValue = $creatoreRecensione->textContent;

                    $testoRecensione = $creatoreRecensione->nextSibling;
                    $testoRecensioneValue = $testoRecensione->textContent;

                    $valutazioneRecensione = $testoRecensione->nextSibling;
                    $valutazioneRecensioneValue = $valutazioneRecensione->textContent;

                    $giocoRecensione = $valutazioneRecensione->nextSibling;
                    $giocoRecensioneValue = $giocoRecensione->textContent;

                    $listaVotiRecensione = $recensione->getElementsByTagName('voto');

                    $numLike=0;
                    $numDislike=0;
                    for($k=0; $k<$listaVotiRecensione->length; $k++)
                    {
                        $voto = $listaVotiRecensione->item($k);

                        $creatoreVoto = $voto->firstChild;

                        $valoreVoto = $creatoreVoto->nextSibling;
                        
                        if($valoreVoto->textContent == "true")
                            $numLike++;
                        else
                            $numDislike++;
                    }

                    if($_SESSION['type'] == "cliente")
                    {
                        echo "
                        <li><strong style=\"color: crimson;\">$testoRecensioneValue</strong> <br /> Creata da <strong>$creatoreRecensioneValue</strong> in data $dataCreazRecensioneValue per il gioco <strong style=\"color: crimson;\">$giocoRecensioneValue</strong> <br /> Valutazione: $valutazioneRecensioneValue <br /> <button type=\"submit\" name=\"like\" value=\"$idRecensioneValue\"><img src=\"../images/like.png\" style=\"width: 25px; height: auto;\"></button>$numLike <button type=\"submit\" name=\"dislike\" value=\"$idRecensioneValue\"><img src=\"../images/dislike.png\" style=\"width: 25px; height: auto;\"></button>$numDislike</li>";
                    }

                    if($_SESSION['type'] == "gestore")
                    {
                        echo "
                        <li><strong style=\"color: crimson;\">$testoRecensioneValue</strong> <br /> Creata da <strong>$creatoreRecensioneValue</strong> in data $dataCreazRecensioneValue per il gioco <strong style=\"color: crimson;\">$giocoRecensioneValue</strong> <br /> Valutazione: $valutazioneRecensioneValue <br /> <button type=\"submit\" name=\"like\" value=\"$idRecensioneValue\"> <img src=\"../images/like.png\" style=\"width: 25px; height: auto;\"></button>$numLike <button type=\"submit\" name=\"dislike\" value=\"$idRecensioneValue\"><img src=\"../images/dislike.png\" style=\"width: 25px; height: auto;\"></button>$numDislike</li>
                        <button type=\"submit\" class=\"btn\" name=\"rimuovi\" value=\"$idRecensioneValue\">Elimina recensione</button>";
                    }

                    if($_SESSION['type'] == "amministratore")
                    {
                        echo "
                        <li><strong style=\"color: crimson;\">$testoRecensioneValue</strong> <br /> Creata da <strong>$creatoreRecensioneValue</strong> in data $dataCreazRecensioneValue per il gioco <strong style=\"color: crimson;\">$giocoRecensioneValue</strong> <br /> Valutazione: $valutazioneRecensioneValue <br /> <img src=\"../images/like.png\" style=\"width: 25px; height: auto;\"></button>$numLike <img src=\"../images/dislike.png\" style=\"width: 25px; height: auto;\">$numDislike</li>";
                    }
                }

                echo "</ul>
                </form>";
            


                
            }
        ?>
    </div>
</body>
</html>