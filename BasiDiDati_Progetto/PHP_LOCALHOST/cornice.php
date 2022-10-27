<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<?php
    require("./connection.php");

    echo "<div class=\"topbar\">
        <a class=\"top\" href=\"home.php\">HOME</a>
        <a class=\"top\" href=\"contattiHTML.php\">CONTATTI</a>
        </div>
        <div class=\"div-login-head\">";
            if(!isset($_SESSION['success'])){
                echo "<h4 class=\"login-head\">Accesso Utente</h4>";
            }
            else
                echo "<h4 class=\"login-head\">Pannello Utente</h4>"; 
                
    echo " </div>
    <div class=\"sidenavlogin\">";
     
    if (!isset($_SESSION['success']))
    {
        if(isset($_SESSION['errore'])){
            echo "<h4 class=\"errori\">";
            echo $_SESSION['errore'];
            unset($_SESSION['errore']);
            echo "</h4>";
        }        
        echo "<form class=\"login-form\" method=\"post\" action=\"cornice.php\">
  	      
  	<div class=\"input-login\">
  		<label>Username</label>
  		<input class=\"login-input\" type=\"text\" name=\"username\" >
      </div>
      <br />
  	<div class=\"input-login\">
  		<label>Password</label>
  		<input class=\"login-input\" type=\"password\" name=\"password\">
      </div>
      <br />
  	<div class=\"input-login\">
  		<button class=\"login-btn\" type=\"submit\" class=\"btn\" name=\"login_user\">Login</button>
  	</div>
  	<p class=\"input-login\">
  		Non sei ancora un membro? <a href=\"registration.php\">Registrati</a>
  	</p>
  </form>";

  //login utente
if (isset($_POST['login_user'])) {
   
    $query = "SELECT * FROM users WHERE username = \"{$_POST['username']}\" AND password = \"{$_POST['password']}\";";
    
    if (!$result = mysqli_query($con, $query)) {
        echo "errore query ";
        exit();
    }
        $row = mysqli_fetch_array($result);

        if ($row){   
                session_start();
                $_SESSION['id']=$row['id'];
                $_SESSION['username']= $_POST['username'];
                $_SESSION['type']=$row['type'];
                $_SESSION['email']=$row['email'];
                $_SESSION['nome']=$row['nome'];
                $_SESSION['cognome']=$row['cognome'];
                $_SESSION['crediti']=$row['crediti'];
                $_SESSION['id_indirizzo']=$row['id_indirizzo'];
                $_SESSION['password']= $_POST['password'];
                $_SESSION['success'] = 1000;
                $_SESSION['carrello']=array();
                header('Location: home.php');    
        }
        else 
            { 
                session_start();
                $_SESSION['errore'] = 'Lo username e la password non combaciano! Riprova';
                header('Location: home.php'); 
                               
            }
            
            
}
    }
    else{
        
        $username = $_SESSION['username'];
        if($_SESSION['type'] == "cliente")
        {
            $query = "SELECT * FROM users WHERE username = '$username';";
            
            if (!$result = mysqli_query($con, $query)) {
                echo "errore query login";
                exit();
            }
                $row = mysqli_fetch_array($result);
            
            if($row){
                $nome = $row['nome'];
                $cognome = $row['cognome'];
                $crediti = $row['crediti'];
            }    

            echo "
            <div class=\"pannelloUtente\">
            <p id=\"benvenuto\">Ciao,   $username </p> 
            <p id=\"crediti\">Crediti: $crediti</p>
            <a class=\"pannello\" href=\"visualizzaProfilo.php\">Il tuo Profilo</a><br />
            <a class=\"pannello\" href=\"storicoOrdini.php\">Storico Ordini</a><br />
            <a class=\"pannello\" href=\"richiediSoldi.php\">Aggiungi Crediti</a><br />
            <a class=\"pannello\" href=\"mostraCarrello.php\">Vai al Carrello</a><br /><br />
            <a class=\"btn\" href=\"logout.php\">Logout</a>
            </div>";
        }
    
        if($_SESSION['type'] == "admin"){
            echo "
            <div class=\"pannelloUtente\">
            <p id=\"benvenuto\">Ciao,   $username </p> 
            <a class=\"pannello\" href=\"aggiungiProdotto.php\">Aggiungi Gioco</a><br />
            <a class=\"pannello\" href=\"storicoClienti.php\">Storico Clienti</a><br /><br />
            <a class=\"btn\" href=\"logout.php\">Logout</a>
            </div>";
        }

    }

    echo "
        </div>
        <div class=\"div-catalogo-head\">
        <h4 class=\"catalogo-head\">Catalogo</h4>
        </div>
        <div class=\"sidenav\">
        <a href=\"visualizzaCatalogo.php\">CATALOGO</a>
        <form id=\"selezioneForm\" method=\"post\" action=\"visualizzaCatalogo.php\">";
        if (isset($_POST['filtra'])) {
            if($_POST['cerca'] != ""){
            echo "<input id=\"selezione\" type=\"text\" name=\"cerca\" value=";
            echo $_POST['cerca']; 
            echo">";
            }
            else{
            echo "<input id=\"selezione\" type=\"text\" name=\"cerca\" placeholder=\"Ricerca gioco...\"/>";
            }
        }
        else{
            echo "<input id=\"selezione\" type=\"text\" name=\"cerca\" placeholder=\"Ricerca gioco...\"/>";
            }

        echo "<label>FILTRA RICERCA</label>
        <select id=\"selezione\" name=\"categoria\">";
        if($_POST['categoria'] != ""){
            echo "<option value=";
            echo $_POST['categoria'];  
            echo ' selected>';
            echo $_POST['categoria'];
            echo"</option>";
        } else {
        echo "<option value=\"\" selected disabled>Scegli categoria</option>";
        
    }

        $query = "SELECT DISTINCT categoria FROM prodotti;";
            
            if (!$result = mysqli_query($con, $query)) {
                echo "errore query";
                exit();
            }

         while($row = mysqli_fetch_assoc($result)){
            echo "<option value=".$row['categoria'].">".$row['categoria']."</option>";
        }
        echo"</select>";
        echo "
        <select id=\"selezione\" name=\"casaEditrice\">";
        if($_POST['casaEditrice'] != ""){
            echo "<option value=";
            echo $_POST['casaEditrice'];  
            echo ' selected>';
            echo $_POST['casaEditrice'];
            echo"</option>";
        } else{
        echo "<option value=\"\" selected disabled>Scegli casa editrice</option>";
        }

        $query = "SELECT DISTINCT casaEditrice FROM prodotti;";
            
            if (!$result = mysqli_query($con, $query)) {
                echo "errore query";
                exit();
            }
                
            while($row = mysqli_fetch_assoc($result)){
                echo "<option value=".$row['casaEditrice'].">".$row['casaEditrice']."</option>";
            }
        echo "</select>
        
        <select id=\"selezione\" name=\"lingua\">";
        if($_POST['lingua'] != ""){
            echo "<option value=";
            echo $_POST['lingua'];  
            echo ' selected>';
            echo $_POST['lingua'];
            echo"</option>";
            
        } else{
        echo "<option value=\"\" selected disabled>Scegli lingua</option>";
    }

        echo "<option value=\"Italiano\">Italiano</option>
              <option value=\"Inglese\">Inglese</option>
              <option value=\"Spagnolo\">Spagnolo</option>
              <option value=\"Russo\">Russo</option>";
        echo "</select><button class=\"login-btn\" type=\"submit\" name=\"filtra\" class=\"btn\">Filtra</button></form>
    </div>";

?>
</body>
</html>