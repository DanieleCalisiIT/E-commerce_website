<!--
All'interno di questo script è presente il sistema di gestione delle registrazioni dei clienti al sito.
La registrazione si sviluppa in due parti, la prima all'interno del database MySql, la seconda all'interno 
dei file XML relativi agli account dei clienti e degli indirizzi. Il cliente inserisce tutte le informazioni
all'interno di una form, che una volta submittata, come prima cosa verifica che tutti i campi siano riempiti.
Dopodichè verifica che non esistano già utenti con quello username o quella mail. Nel caso in cui questi valori
non siano presenti all'interno del DB mysql, verrà inserito il nuovo cliente all'interno della tabella nel DB.
Dopodichè, verranno inserite le informazioni anche all'interno dei file xml. Inoltre, all'interno del file XML
degli account, l'id del cliente sarà lo stesso dell'id all'interno del DB MySql, recuperato tramite la funzione
mysqli_fetch_array.
 -->

<?php
    ini_set('display_errors', "off");
    error_reporting(E_ALL);
    require("./cornice.php"); 
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Registrazione</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<?php
    if (isset($_POST['reg_user'])) { //riceve i valori in input dalla form
  
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password_1 = $_POST['password_1'];
        $password_2 = $_POST['password_2'];

        //validazione form 
        if ((!empty($username)) && (!empty($email)) && (!empty($password_1)) && ($password_1 == $password_2) && (!empty($_POST['nome'])) && (!empty($_POST['cognome'])) && (!empty($_POST['citta'])) && (!empty($_POST['CAP'])) &&
        (!empty($_POST['provincia'])) && (!empty($_POST['regione'])) && (!empty($_POST['nazione'])) && (!empty($_POST['via'])) && (!empty($_POST['civico']))){
        

            //prima di creare un utente controlla che non esista già
            $query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
        
            if (!$result = mysqli_query($con, $query)) 
                {
                    printf("errore nella query di ricerca utenti esistenti \n");
                    exit();
                }
        
            $row = mysqli_fetch_array($result);
  
            if ($row) {   
            
                if ($row['username'] == $username) 
                    echo "<p id= \"error\">Username gi&agrave; in uso</p>";
    
                if ($row['email'] == $email) 
                    echo "<p id= \"error\">E-mail gi&agrave; in uso</p>";
        
            }
            else{
  
                //esegue la registrazione nel caso in cui l'utente non esista già
   
  	             $query = "INSERT INTO users (username, password, email, type) 
                 VALUES('$username', '$password_1', '$email', 'cliente')";               //va aggiunto un nuovo utente anche nell xml, nell install anche
      
                if (!$result = mysqli_query($con, $query)) {
                    printf("errore nella query di inserimento nuovo cliente \n");
                    exit();
                }

                $queryID = "SELECT id FROM users WHERE username = '$username'";
                if (!$resultID = mysqli_query($con, $queryID)) 
                {
                    printf("errore nella query di ricerca ID \n");
                    exit();
                }
        
            $row = mysqli_fetch_array($resultID);
  
            if ($row) {  

                $dbID = $row['id'];               
        
            }

                $xmlString = "";
                foreach(file("../XML/account.xml") as $nodo){
                    $xmlString .= trim($nodo);
                }

                $docAccount = new DOMDocument();
                $docAccount->loadXML($xmlString);

                $rootAccount = $docAccount->documentElement;
                $listaAccount = $rootAccount->childNodes;

                $nuovoAccount = $docAccount->createElement("profilo");
                $rootAccount->appendChild($nuovoAccount);
                $nuovoAccount->setAttribute("stato", "attivo");
                $nuovoID = $docAccount->createElement("idAcc", $dbID);
                $nuovoAccount->appendChild($nuovoID);
                $nuovoNome = $docAccount->createElement("nome", $_POST['nome']);
                $nuovoAccount->appendChild($nuovoNome);
                $nuovoCognome = $docAccount->createElement("cognome", $_POST['cognome']);
                $nuovoAccount->appendChild($nuovoCognome);
                $nuoviSoldi = $docAccount->createElement("soldi", 0);
                $nuovoAccount->appendChild($nuoviSoldi);
                $nuoviCrediti = $docAccount->createElement("crediti", 0);
                $nuovoAccount->appendChild($nuoviCrediti);
                $nuovaRep = $docAccount->createElement("reputazione", 0);
                $nuovoAccount->appendChild($nuovaRep);

                $xmlInd = "";
                foreach(file("../XML/indirizzi.xml") as $nodoInd){
                    $xmlInd .= trim($nodoInd);
                }

                $docIndirizzi = new DOMDocument();
                $docIndirizzi->loadXML($xmlInd);

                $rootIndirizzi = $docIndirizzi->documentElement;
                $listaIndirizzi = $rootIndirizzi->childNodes;

                $IDInd = (($listaIndirizzi->length) + 1);

                $nuovoIndirizzo = $docIndirizzi->createElement("indirizzo");
                $rootIndirizzi->appendChild($nuovoIndirizzo);
                $nuovoIDInd = $docIndirizzi->createElement("id", $IDInd);
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

                $nuovoIDAddr = $docAccount->createElement("idAddr", $IDInd);
                $nuovoAccount->appendChild($nuovoIDAddr);

                $nuovoCount = $docAccount->createElement("countBan", 0);
                $nuovoAccount->appendChild($nuovoCount);

                $nuovaData = $docAccount->createElement("dataBan", "2000-01-01");
                $nuovoAccount->appendChild($nuovaData);

                $percorso = "../XML/account.xml";
                $docAccount->save($percorso);

                $percorsoInd = "../XML/indirizzi.xml";
                $docIndirizzi->save($percorsoInd);
                
                $registrato = true;
  	        } 
        }
        
    }  
?>

<body>
    <div class="body">
    <div class="header">
  	    <h2>Registrazione</h2>
    </div>
	
    <form onsubmit="alert('Complimenti, sei stato registrato!');" method="post" action="registration.php">
  	    <div class="input-group">
            <?php if(isset($_POST['reg_user'])){ if (empty($username)) {
                echo "<p id= \"error\">Username richiesto</p>";
            }} ?>
  	        <label>Username</label>
              <input type="text" name="username" value="<?php if(isset($_POST['username'])){ echo $username;}?>">
  	    </div>
  	    <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($email)) {
                echo "<p id= \"error\">Email richiesta</p>";
            }} ?>
  	        <label>Email</label>
  	        <input type="email" name="email" value="<?php if(isset($_POST['email'])){ echo $email;}?>"> 
  	    </div>
  	    <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($password_1)) {
                echo "<p id= \"error\">Password richiesta</p>";
            }} ?>
  	        <label>Password</label>
  	        <input type="password" name="password_1">
  	    </div>
  	    <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($password_2)) {
                echo "<p id= \"error\">Conferma password richiesta</p>";
            }} ?>
  	        <label>Ripeti password</label>
  	        <input type="password" name="password_2">
          </div>
          <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($_POST['nome'])) {
                echo "<p id= \"error\">Nome richiesto</p>";
            }} ?>
            <label>Nome</label>
            <input type="text" name="nome" value="<?php if(isset($_POST['nome'])){echo $_POST['nome'];}?>">
          </div>
          <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($_POST['cognome'])) {
                echo "<p id= \"error\">Cognome richiesto</p>";
            }} ?>
              <label>Cognome</label>
              <input type="text" name="cognome" value="<?php if(isset($_POST['cognome'])){echo $_POST['cognome'];}?>">
          </div>
          <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($_POST['citta'])) {
                echo "<p id= \"error\">Citta richiesta</p>";
            }} ?>
              <label>Citt&agrave;</label>
              <input type="text" name="citta" value="<?php if(isset($_POST['citta'])){echo $_POST['citta'];}?>">
          </div>
          <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($_POST['CAP'])) {
                echo "<p id= \"error\">CAP richiesto</p>";
            }} ?>
              <label>CAP</label>
              <input type="number" name="CAP" max="99999" value="<?php if(isset($_POST['CAP'])){echo $_POST['CAP'];}?>">
          </div>
          <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($_POST['provincia'])) {
                echo "<p id= \"error\">Provincia richiesta</p>";
            }} ?>
              <label>Provincia</label>
              <input type="text" name="provincia" value="<?php if(isset($_POST['provincia'])){echo $_POST['provincia'];}?>">
          </div>
          <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($_POST['regione'])) {
                echo "<p id= \"error\">Regione richiesta</p>";
            }} ?>
              <label>Regione</label>
              <input type="text" name="regione" value="<?php if(isset($_POST['regione'])){echo $_POST['regione'];}?>">
          </div>
          <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($_POST['nazione'])) {
                echo "<p id= \"error\">Nazione richiesta</p>";
            }} ?>
              <label>Nazione</label>
              <input type="text" name="nazione" value="<?php if(isset($_POST['nazione'])){echo $_POST['nazione'];}?>">
          </div>
          <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($_POST['via'])) {
                echo "<p id= \"error\">Via richiesta</p>";
            }} ?>
              <label>Via</label>
              <input type="text" name="via" value="<?php if(isset($_POST['via'])){echo $_POST['via'];}?>">
          </div>
          <div class="input-group">
          <?php if(isset($_POST['reg_user'])){ if (empty($_POST['civico'])) {
                echo "<p id= \"error\">Civico richiesto</p>";
            }} ?>
              <label>Civico</label>
              <input type="text" name="civico" value="<?php if(isset($_POST['civico'])){echo $_POST['civico'];}?>">
          </div>
  	    <div class="input-group">
  	        <button type="submit" class="btn" name="reg_user">Registrati</button>
  	    </div>
    </form>
    <?php
        if($registrato == true){
            echo "<script>location.replace(\"logout.php\");</script>";
        }
    ?>
</div>
</body>
</html>