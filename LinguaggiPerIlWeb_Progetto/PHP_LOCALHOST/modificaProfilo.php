<!--
In questo script è presente una form che permette la modifica delle informazioni del profilo. Vengono recuperati, tramite
DOM, le informazioni del cliente dai file XML relativi all'account e agli indirizzi. Dopodichè viene semplicemente fatto
il replace degli elementi del'account e degli indirizzi tramite il metodo replaceChild del DOM, che sostituisce il vecchio valore
con quello preso dalla form appena compilata. Tutti i campi della form sono precompilati con le informazioni attuali del profilo
e devono essere tutti compilati, tranne le password, per far si che nel caso in cui l'utente volesse modificare solo un'informazione
non debba reinserire tutte le informazioni che sono ancora buone. 
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
    <title>Modifica Profilo</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <div class="body">
        <div class="header">
  	        <h2>Modifica il tuo profilo</h2>
        </div>
	
        

        <?php

            $password = $_SESSION['password'];
            $dbID = $_SESSION['id'];
            
        
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
                    $mioProfilo = $listaAccount->item($i);

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
                            $mioIndirizzo = $listaIndirizzi->item($k);
                            $mioIDIndirizzo = $idIndValue;

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


        ?>

    <?php
            if (isset($_POST['modifica'])) {

                $password_1 =  $_POST['nuovaPassword'];
                $password_2 =  $_POST['ripetiPassword'];

                //validazione form 
                if ((!empty($password_1)) && ($password_1 == $password_2) && (!empty($_POST['nome'])) && (!empty($_POST['cognome'])) && (!empty($_POST['citta'])) && (!empty($_POST['CAP'])) &&
                (!empty($_POST['provincia'])) && (!empty($_POST['regione'])) && (!empty($_POST['nazione'])) && (!empty($_POST['via'])) && (!empty($_POST['civico']))){       
                
                    if($password_1 != $password){  //vuole modificare la password

                        $query = "UPDATE users SET password = '$password_1' WHERE id=$dbID; ";

                        if (!$result = mysqli_query($con, $query)) {
                            printf("errore nella query di aggiornamento password \n");
                            exit();
                        }
                    }
                    
                    $_SESSION['password']=$password_1;
                        

                    $nuovoAccount = $docAccount->createElement("profilo");
                    $rootAccount->replaceChild($nuovoAccount,$mioProfilo);

                    $nuovoID = $docAccount->createElement("idAcc", $dbID);
                    $nuovoAccount->appendChild($nuovoID);
                    $nuovoNome = $docAccount->createElement("nome", $_POST['nome']);
                    $nuovoAccount->appendChild($nuovoNome);
                    $nuovoCognome = $docAccount->createElement("cognome", $_POST['cognome']);
                    $nuovoAccount->appendChild($nuovoCognome);
                    $nuoviSoldi = $docAccount->createElement("soldi", $soldiValue);
                    $nuovoAccount->appendChild($nuoviSoldi);
                    $nuoviCrediti = $docAccount->createElement("crediti", $creditiValue);
                    $nuovoAccount->appendChild($nuoviCrediti);
                    $nuovaRep = $docAccount->createElement("reputazione", $reputazioneValue);
                    $nuovoAccount->appendChild($nuovaRep); 

                    $nuovoIndirizzo = $docIndirizzi->createElement("indirizzo");
                    $rootIndirizzi->replaceChild($nuovoIndirizzo,$mioIndirizzo);

                    $nuovoIDInd = $docIndirizzi->createElement("id", $mioIDIndirizzo);
                    $nuovoIndirizzo->appendChild($nuovoIDInd);
                    $nuovaCitta = $docIndirizzi->createElement("citta", $_POST['citta']);
                    $nuovoIndirizzo->appendChild($nuovaCitta);
                    $nuovoCAP = $docIndirizzi->createElement("cap", $_POST['CAP']);
                    $nuovoIndirizzo->appendChild($nuovoCAP);
                    $nuovaProvincia = $docIndirizzi->createElement("provincia", $_POST['provincia']);
                    $nuovoIndirizzo->appendChild($nuovaProvincia);
                    $nuovaRegione = $docIndirizzi->createElement("regione", $_POST['regione']);
                    $nuovoIndirizzo->appendChild($nuovaRegione);
                    $nuovaNazione = $docIndirizzi->createElement("nazione", $_POST['nazione']);
                    $nuovoIndirizzo->appendChild($nuovaNazione);
                    $nuovaVia = $docIndirizzi->createElement("via", $_POST['via']);
                    $nuovoIndirizzo->appendChild($nuovaVia);
                    $nuovoCivico = $docIndirizzi->createElement("civico", $_POST['civico']);
                    $nuovoIndirizzo->appendChild($nuovoCivico);

                    $nuovoIDAddr = $docAccount->createElement("idAddr", $mioIDIndirizzo);
                    $nuovoAccount->appendChild($nuovoIDAddr);

                    $percorso = "../XML/account.xml";
                    $docAccount->save($percorso);

                    $percorsoInd = "../XML/indirizzi.xml";
                    $docIndirizzi->save($percorsoInd);
               
                    $modifica = true;
                   
                    
                }
            }
        ?>

<form method="post" action="modificaProfilo.php">
  	        <div class="input-group">
              <?php if(isset($_POST['modifica'])){ if (empty($password_1)) {
                echo "<p id= \"error\">Password richiesta</p>";
            }} ?>
  	            <label>Nuova Password</label>
  	            <input type="password" name="nuovaPassword">
  	        </div>
  	        <div class="input-group">
              <?php if(isset($_POST['modifica'])){ if (empty($password_2)) {
                echo "<p id= \"error\">Conferma password richiesta</p>";
            }} ?>
  	            <label>Ripeti Password</label>
  	            <input type="password" name="ripetiPassword">             
  	        </div>
  	        <div class="input-group">
              <?php if(isset($_POST['modifica'])){ if (empty($_POST['nome'])) {
                echo "<p id= \"error\">Nome richiesto</p>";
            }} ?>
  	            <label>Nome</label>
  	            <input type="text" name="nome" value="<?php if(isset($_POST['nome'])){
                      echo $_POST['nome'];
                  } else {
                      echo $nomeValue;
                  }?>">
  	        </div>
  	        <div class="input-group">
              <?php if(isset($_POST['modifica'])){ if (empty($_POST['cognome'])) {
                echo "<p id= \"error\">Cognome richiesto</p>";
            }} ?>
  	            <label>Cognome</label>
  	            <input type="text" name="cognome" value="<?php if(isset($_POST['cognome'])){
                      echo $_POST['cognome'];
                  } else {
                      echo $cognomeValue;
                  }?>">
  	        </div>

  	        <p style="font-size: 120%; color: #DC143C; ">
  		        <b>Modifica indirizzo</b>
  	        </p>

            <div class="input-group">
            <?php if(isset($_POST['modifica'])){ if (empty($_POST['citta'])) {
                echo "<p id= \"error\">Citta richiesta</p>";
            }} ?>
  	            <label>Citt&agrave;</label>
  	            <input type="text" name="citta" value="<?php if(isset($_POST['citta'])){
                      echo $_POST['citta'];
                  } else {
                      echo $cittaValue;
                  }?>">
  	        </div>
            <div class="input-group">
            <?php if(isset($_POST['modifica'])){ if (empty($_POST['CAP'])) {
                echo "<p id= \"error\">CAP richiesto</p>";
            }} ?>
  	            <label>CAP</label>
  	            <input type="number" name="CAP" maxlenght=5 value="<?php if(isset($_POST['CAP'])){
                      echo $_POST['CAP'];
                  } else {
                      echo $CAPValue;
                  }?>">
  	        </div>
            <div class="input-group">
            <?php if(isset($_POST['modifica'])){ if (empty($_POST['provincia'])) {
                echo "<p id= \"error\">Provincia richiesta</p>";
            }} ?>
  	            <label>Provincia</label>
  	            <input type="text" name="provincia" value="<?php if(isset($_POST['provincia'])){
                      echo $_POST['provincia'];
                  } else {
                      echo $provinciaValue;
                  }?>">
  	        </div>
            <div class="input-group">
            <?php if(isset($_POST['modifica'])){ if (empty($_POST['regione'])) {
                echo "<p id= \"error\">Regione richiesta</p>";
            }} ?>
  	            <label>Regione</label>
  	            <input type="text" name="regione" value="<?php if(isset($_POST['regione'])){
                      echo $_POST['regione'];
                  } else {
                      echo $regioneValue;
                  }?>">
  	        </div>
            <div class="input-group">
            <?php if(isset($_POST['modifica'])){ if (empty($_POST['nazione'])) {
                echo "<p id= \"error\">Nazione richiesta</p>";
            }} ?>
  	            <label>Nazione</label>
  	            <input type="text" name="nazione" value="<?php if(isset($_POST['nazione'])){
                      echo $_POST['nazione'];
                  } else {
                      echo $nazioneValue;
                  }?>">
  	        </div>
            <div class="input-group">
            <?php if(isset($_POST['modifica'])){ if (empty($_POST['via'])) {
                echo "<p id= \"error\">Via richiesta</p>";
            }} ?>
  	            <label>Via</label>
  	            <input type="text" name="via" value="<?php if(isset($_POST['via'])){
                      echo $_POST['via'];
                  } else {
                      echo $viaValue;
                  }?>">
  	        </div>
            <div class="input-group">
            <?php if(isset($_POST['modifica'])){ if (empty($_POST['civico'])) {
                echo "<p id= \"error\">Civico richiesto</p>";
            }} ?>
  	            <label>Civico</label>
  	            <input type="text" name="civico" value="<?php if(isset($_POST['civico'])){
                      echo $_POST['civico'];
                  } else {
                      echo $civicoValue;
                  }?>">
  	        </div>
            <div class="input-group">
  	            <button type="submit" class="btn" name="modifica">Conferma modifiche</button>
  	        </div>
        </form>
                  <?php
                    if($modifica == true){
                        echo "<script type=\"text/javascript\">alert(\"Modifiche effettuate con successo.\"); location.replace(\"visualizzaProfilo.php\");</script>";
                        $modifica = false;
                    }
                  ?>
        
    </div>
</body>
</html>