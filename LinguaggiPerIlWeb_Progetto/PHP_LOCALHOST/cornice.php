<!-- 
Questo è lo script che viene incluso in tutti gli altri script del sito, come si può intuire dal nome è
appunto la cornice di ogni pagina, cioè contiene la topbar e le sidenav, tramite le quali si ci può spostare
all'interno del sito. Nella cornice è presente la form per il login, che una volta fatto il submit fa una select
dal database per il match di username e password per l'accesso e dopodichè fa partire una session all'interno della
quale sono presenti le informazioni di quell'utente, prese in parte dal db e in parte dall'XML, tramite il match
dell'ID all'interno del db e del file account.xml. Vengono quindi stampate alcune info relative a quel profilo. 
Nella colonna in basso c'è presente il link per il catalogo e la form per filtrare la ricerca nel catalogo, che manda in
$_POST i valori allo script catalogoCategorie.php in modo da filtrare la ricerca tramite quei parametri.
-->
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
        <a class=\"top\" href=\"forum.php\">FORUM</a>
        <a class=\"top\" href=\"visualizzaFAQ.php\">FAQ</a>
        <a class=\"top\" href=\"contattiHTML.php\">CONTATTI</a>
        <a class=\"top\" href=\"creditiHTML.php\">CREDITI</a>
        <a class=\"top\" href=\"reputazioneHTML.php\">REPUTAZIONE</a>
        </div>
        <div class=\"div-login-head\">
                <h4 class=\"login-head\">Accesso Utente</h4>
              </div>
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
  		Non sei ancora un membro? <br /><a href=\"registration.php\">Registrati</a>
  	</p>
  </form>";

  //login utente
if (isset($_POST['login_user'])) {
   
    $query = "SELECT * FROM users WHERE username = \"{$_POST['username']}\" AND password = \"{$_POST['password']}\";";
    
    if (!$result = mysqli_query($con, $query)) {
        echo "errore query";
        exit();
    }
        $row = mysqli_fetch_array($result);

        if ($row){   
                session_start();
                $_SESSION['id']=$row['id'];
                $_SESSION['username']= $_POST['username'];
                $_SESSION['type']=$row['type'];
                $_SESSION['email']=$row['email'];
                $_SESSION['password']= $_POST['password'];
                $_SESSION['success'] = 1000;
                $_SESSION['carrello']=array();
                $_SESSION['carrelloSconti']=array();
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
                    $mioProfilo = $profilo;

                    $statoBan = $profilo->getAttribute('stato');
                    
                    $nome = $idAcc->nextSibling;
                    
                    $cognome = $nome->nextSibling;             

                    $soldi = $cognome->nextSibling;             
                    $soldiValue = $soldi->textContent;

                    $crediti = $soldi->nextSibling;             
                    $creditiValue = $crediti->textContent;
        
                    $reputazione = $crediti->nextSibling;
                    $reputazioneValue = $reputazione->textContent;

                    $idAddr = $reputazione->nextSibling;

                    $countBan = $idAddr->nextSibling;

                    $dataBan = $countBan->nextSibling;
                    $dataBanValue = $dataBan->textContent;
                }
            }
    
            echo "
            <div class=\"pannelloUtente\">
            <p id=\"benvenuto\">Ciao,   $username </p> 
            <p id=\"crediti\">Crediti: $creditiValue</p>
            <p id=\"portafoglio\">Portafoglio: $soldiValue</p>
            <p id=\"reputazione\">Reputazione: $reputazioneValue</p>
            <a class=\"pannello\" href=\"visualizzaProfilo.php\">Il tuo Profilo</a><br />
            <a class=\"pannello\" href=\"storicoOrdini.php\">Storico Ordini</a><br />
            <a class=\"pannello\" href=\"richiediSoldi.php\">Richiedi ricarica</a><br />
            <a class=\"pannello\" href=\"mostraCarrello.php\">Vai al Carrello</a><br /><br />
            <a class=\"btn\" href=\"logout.php\">Logout</a>
            </div>";
        }


        if($_SESSION['type'] == "gestore"){
            echo "
            <div class=\"pannelloUtente\">
            <p id=\"benvenuto\">Ciao,   $username </p> 
            <a class=\"pannello\" href=\"aggiungiProdotto.php\">Aggiungi gioco</a><br />
            <a class=\"pannello\" href=\"assegnaSconto.php\">Assegna Sconti</a><br />
            <a class=\"pannello\" href=\"storicoClienti.php\">Storico Clienti</a><br /><br />
            <a class=\"btn\" href=\"logout.php\">Logout</a>
            </div>";
        }

    
        if($_SESSION['type'] == "amministratore"){
            echo "
            <div class=\"pannelloUtente\">
            <p id=\"benvenuto\">Ciao,   $username </p> 
            <a class=\"pannello\" href=\"gestioneRichieste.php\">Accetta Ricariche</a><br />
            <a class=\"pannello\" href=\"storicoClienti.php\">Gestisci Clienti</a><br /><br />
            <a class=\"btn\" href=\"logout.php\">Logout</a>
            </div>";
        }

    }


                    $stringaXML = "";
                    foreach ( file("../XML/categorie.xml") as $nodo ) 
                    {
                        $stringaXML .= trim($nodo);
                    }

                    $docCategorie = new DOMDocument();
                    $docCategorie->loadXML($stringaXML);

                    $rootCategorie = $docCategorie->documentElement;
                    $listaCategorie = $rootCategorie->childNodes;

                    for ($k=0; $k<$listaCategorie->length; $k++)
                    {
                        $categoria = $listaCategorie->item($k);
                        $idCategoria = $categoria->firstChild;
                        $idCategoriaVal = $idCategoria->textContent;
                        
                        $nomeCateg = $idCategoria->nextSibling;
                        $nomeCategVal = $nomeCateg->textContent;   
                                
                        $descrizione = $nomeCateg->nextSibling;
                        $descrizioneVal = $descrizione->textContent;
                        
                        $arrayCateg[$k] = $nomeCategVal;
                    }                

                    $stringaXML = "";
                    foreach ( file("../XML/caseEditrici.xml") as $nodo ) 
                    {
                        $stringaXML .= trim($nodo);
                    }

                    $docCase = new DOMDocument();
                    $docCase->loadXML($stringaXML);

                    $rootCase = $docCase->documentElement;
                    $listaCase = $rootCase->childNodes;

                    for ($j=0; $j<$listaCase->length; $j++)
                    {
                        $casaEd = $listaCase->item($j);
                        $idCasaEd = $casaEd->firstChild;
                        $idCasaEdVal = $idCasaEd->textContent;
                        
                        $nome = $idCasaEd->nextSibling;
                        $nomeCasaVal = $nome->textContent;   
                       
                        $arrayCase[$j] = $nomeCasaVal;
                    }                

    echo "
        </div>
        <div class=\"div-catalogo-head\">
        <h4 class=\"catalogo-head\">Catalogo</h4>
        </div>
        <div class=\"sidenav\">
        <a href=\"visualizzaCatalogo.php\">CATALOGO</a>
        <form id=\"selezioneForm\" method=\"post\" action=\"visualizzaCatalogo.php\">";
        if($_POST['cerca'] != ""){
            echo "<input id=\"selezione\" type=\"text\" name=\"cerca\" value=";
            echo $_POST['cerca']; 
            echo">";
        } else{
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
    for ($i=0; $i<$listaCategorie->length; $i++){
        echo "<option value=\"$arrayCateg[$i]\">$arrayCateg[$i]</option>";
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
        for ($k=0; $k<$listaCase->length; $k++){
            echo "<option value=\"$arrayCase[$k]\">$arrayCase[$k]</option>";
        }
        echo "</select>";
    
        
        echo "<select id=\"selezione\" name=\"lingua\">";
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
        echo "</select><button class=\"login-btn\" type=\"submit\" class=\"btn\">Filtra</button></form>
    </div>";

?>
</body>
</html>