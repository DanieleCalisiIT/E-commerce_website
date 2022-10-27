<?php
    ini_set('display_errors', 1);
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
        if (isset($_POST['reg_user'])) { 
    
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password_1 = $_POST['password_1'];
            $password_2 = $_POST['password_2'];
            $nome = $_POST['nome'];
            $cognome = $_POST['cognome'];

            
            if ((!empty($username)) && (!empty($email)) && (!empty($password_1)) && ($password_1 == $password_2) && (!empty($_POST['nome'])) && (!empty($_POST['cognome'])) && (!empty($_POST['citta'])) && (!empty($_POST['CAP'])) &&
            (!empty($_POST['provincia'])) && (!empty($_POST['regione'])) && (!empty($_POST['nazione'])) && (!empty($_POST['via'])) && (!empty($_POST['civico']))){
            

                //prima di creare un utente controlla che non esista già
                $query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
            
                if (!$result = mysqli_query($con, $query)) 
                    {
                        printf("errore nella query di ricerca utenti esistenti \n");
                        exit();
                    }
            
                $row = mysqli_fetch_array($result);
    
                if ($row) {   
                
                    if ($row['username'] == $username) 
                        echo "<p id= \"error\" style=\"font-size:160%;\">Username gi&agrave; in uso</p>";
        
                    if ($row['email'] == $email) 
                        echo "<p id= \"error\" style=\"font-size:160%;\">E-mail gi&agrave; in uso</p>";
            
                }
                else{
    
                    //esegue la registrazione nel caso in cui l'utente non esista già
    
                    $queryIndirizzo = "INSERT INTO indirizzi (citta, cap, provincia, regione, nazione, via, civico)
                    VALUES ('".$_POST['citta']."', '".$_POST['CAP']."', '".$_POST['provincia']."', '".$_POST['regione']."', '".$_POST['nazione']."', '".$_POST['via']."', '".$_POST['civico']."');";
                    if (!$resultAdd = mysqli_query($con, $queryIndirizzo)) 
                    {
                        printf("errore nella query di inserimento indirizzo \n");
                        exit();
                    }

                    $queryIDAdd = "SELECT id FROM indirizzi ORDER BY id desc limit 1"; //opp con max(id)
                    if (!$resultIDAdd = mysqli_query($con, $queryIDAdd)) 
                    {
                        printf("errore nella query di ricerca IDindirizzo \n");
                        exit();
                    }
            
                    $row = mysqli_fetch_array($resultIDAdd);
    
                    if ($row) {  

                        $IDindirizzo = $row['id'];               
            
                    }

                    $query = "INSERT INTO users (username, password, email, nome, cognome, type, crediti, id_indirizzo) 
                    VALUES('$username', '$password_1', '$email', '$nome', '$cognome', 'cliente', '0.0', '$IDindirizzo');";
        
                    if (!$result = mysqli_query($con, $query)) {
                        printf("errore nella query di inserimento nuovo cliente \n");
                        exit();
                    }

                    echo "<script type=\"text/javascript\">alert(\"Registrazione effettuata, puoi procedere con il login\");</script>";
                    echo "<script>location.replace(\"logout.php\");</script>";
                    
                } 
            }
            
        }  
    ?>

    <body>
        <div class="body">
            <div class="header">
                <h2>Registrazione</h2>
            </div>
            
            <form  method="post" action="registration.php">
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
                    <input type="number" name="civico" value="<?php if(isset($_POST['civico'])){echo $_POST['civico'];}?>">
                </div>
                <div class="input-group">
                    <button type="submit" class="btn" name="reg_user">Registrati</button>
                </div>
            </form>
        </div>
    </body>
</html>