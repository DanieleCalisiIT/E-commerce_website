<!-- 
In questo script vengono visualizzate le informazioni personali (del profilo, solo per i clienti), 
in particolare vengono visualizzate le seguenti informazioni:
    -Username
    -Password
    -Email
    -Cognome
    -Nome
    -Informazioni relative all'indirizzo, quali:
        -Città
        -CAP
        -Provincia
        -Regione
        -Nazione
        -Via
        -Civico.
Le prime tre informazioni recuperate vengono recuperate mediante la session che è attiva in quel momento,
che è proprio quella del cliente con quello Username (l'username è unico, non possono esserci duplicati),
Poichè sono recuperate dal database e non dall'XML. Le altre informazioni sono recuperate dai file XML
relativi a quelle info, in particolare il file "account.xml" e "indirizzi.xml". Poichè ogni account
ha al suo interno un Id relativo all'indirizzo ad esso associato, basterà eguagliare l'ID dell' indirizzo
all'interno dell'account con quello all'interno degli indirizzi.xml. In questo modo, tramite DOM, saranno
recuperate e stampate le informazioni riguardanti quel profilo. Nel caso in cui venga cliccato il bottone 
"Modifica Profilo" presente a fondo pagina, si viene mandati tramite un link ad un altro script, "modificaProfilo.php"
che permette di modificare le informazioni relative a quel profilo.
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
        
            $stringaXML = "";
            foreach ( file("../XML/account.xml") as $nodo )   
            {
                $stringaXML .= trim($nodo);
            }
    
            $docAccount = new DOMDocument();
            $docAccount->loadXML($stringaXML);
                  
            $rootAccount = $docAccount->documentElement;
            $listaAccount = $rootAccount->childNodes;

            for ($i=0; $i<$listaAccount->length; $i++) 
            {
                $profilo = $listaAccount->item($i);
                $idAcc = $profilo->firstChild;
                $idAccValue = $idAcc->textContent;
                if($idAccValue == $_SESSION['id'])  
                {
                    $nome = $idAcc->nextSibling;
                    $nomeValue = $nome->textContent;
    
                    $cognome = $nome->nextSibling;             
                    $cognomeValue = $cognome->textContent;

                    $soldi = $cognome->nextSibling;             
                    $soldiValue = $soldi->textContent;

                    $crediti = $soldi->nextSibling;             
                    $creditiValue = $crediti->textContent;
                    
                    $reputazione = $crediti->nextSibling;
                    $reputazioneValue = $reputazione->textContent;
                    
                    $idAddr = $reputazione->nextSibling;   
                    $idAddrValue = $idAddr->textContent;

                    $stringaXML = "";
                    foreach ( file("../XML/indirizzi.xml") as $nodo ) 
                    {
                        $stringaXML .= trim($nodo);
                    }
    
                    $docIndirizzi = new DOMDocument();
                    $docIndirizzi->loadXML($stringaXML);
    
                    $rootIndirizzi = $docIndirizzi->documentElement;
                    $listaIndirizzi = $rootIndirizzi->childNodes;

                    for ($k=0; $k<$listaIndirizzi->length; $k++)
                    {
                        $indirizzo = $listaIndirizzi->item($k);
                        $idInd = $indirizzo->firstChild;
                        $idIndValue = $idInd->textContent;
                        if($idIndValue == $idAddrValue)
                        {
                            $citta = $idInd->nextSibling;
                            $cittaValue = $citta->textContent;   
                                
                            $CAP = $citta->nextSibling;
                            $CAPValue = $CAP->textContent;

                            $provincia = $CAP->nextSibling;
                            $provinciaValue = $provincia->textContent;

                            $regione = $provincia->nextSibling;
                            $regioneValue = $regione->textContent;

                            $nazione = $regione->nextSibling;
                            $nazioneValue = $nazione->textContent;

                            $via = $nazione->nextSibling;
                            $viaValue = $via->textContent;

                            $civico = $via->nextSibling;
                            $civicoValue = $civico->textContent;
                        }
                    }
                    
                }
            }
             //per testare stampo tutto con lista
            echo "
            <ul class=\"modificaProfilo\"> 
                <li>Username: $username</li>
                <li>Password: $password</li>
                <li>E-mail: $email</li>    
                <li>Nome: $nomeValue</li>
                <li>Cognome: $cognomeValue</li>
            </ul>
            <h2 style=\"text-align: center; font-size: 200%; color: #DC143C;\">Il tuo indirizzo</h2>
            <ul class=\"modificaProfilo\">
                <li>Citt&agrave;: $cittaValue</li>
                <li>CAP: $CAPValue</li>
                <li>Provincia: $provinciaValue</li>
                <li>Regione: $regioneValue</li>
                <li>Nazione: $nazioneValue</li>
                <li>Via: $viaValue</li>
                <li>Civico: $civicoValue</li>
            </ul>
            <br />
            <br />
            <br />
            <a class=\"modifica\" href=\"modificaProfilo.php\">Modifica Profilo</a>";
    



        ?>
    
    
    </div>
</body>
</html>