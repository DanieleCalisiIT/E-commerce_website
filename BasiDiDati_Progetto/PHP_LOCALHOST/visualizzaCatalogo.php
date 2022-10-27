<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    require("cornice.php");
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Visualizza Catalogo</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<?php
    if (isset($_POST['rimuoviDisponibilita']))
    {   
        $i=0;
        foreach($_POST['prodottiSelez'] as $c=>$sel){
            $query = "UPDATE prodotti SET disponibilita = 'No' WHERE id='$sel';";
            if (!$result = mysqli_query($con, $query)) {
                echo "errore query aggiornamento disponibilità";
                exit();
            }
        }      

        $rimuoviDisponibilita = true;
        
    }

    
    if (isset($_POST['aggiungiACarrello']))
    {

            $_SESSION['carrello'][] = $_POST['aggiungiACarrello'];   
            $aggiunto = true;
    }
?>

<body>
    <div class="body">
        <div class="header">
  	        <h2>CATALOGO</h2>
        </div>

        <?php
            $query = "SELECT * FROM prodotti
                WHERE (nome = '".$_POST['cerca']."' OR '".$_POST['cerca']."' = '')
                AND (categoria = '".$_POST['categoria']."' OR '".$_POST['categoria']."' = '')
                AND (casaEditrice = '".$_POST['casaEditrice']."' OR '".$_POST['casaEditrice']."' = '')
                AND (disponibilita = 'Si');
                ";

            if (!$result = mysqli_query($con, $query)) {
                echo "errore query";
                exit();
            }
            

            echo "<table>
                <tr>
                <th></th>
                <th>NOME</th>
                <th>CATEGORIA</th>
                <th>CASA EDITRICE</th>
                <th>LINGUA</th>
                <th>PREZZO</th>
                </tr>";

            if (($_SESSION['type'] == "admin") || ($_SESSION['type'] == "cliente")){
                echo "<form method=\"post\" action=\"visualizzaCatalogo.php\">";
            }
            $num_prod = 0;
            while($row = mysqli_fetch_assoc($result)){
                $num_prod++;
                echo " 
                <tr>
                <td><img src=".$row['img']." height=\"100\" width=\"100\"> </td>
                <td class=\"chiara\">".$row['nome']."</td>
                <td class=\"scura\">".$row['categoria']."</td>
                <td class=\"chiara\">".$row['casaEditrice']."</td> 
                <td class=\"scura\">".$row['lingua']."</td>
                <td class=\"chiara\">&euro;".$row['prezzo']."</td>";
                        

                            
                if($_SESSION['type'] == "admin"){
                    echo "<td><input type=\"checkbox\" name=\"prodottiSelez[]\" value=".$row['id']."></td></tr>";
                    
                }
                if($_SESSION['type'] == "cliente"){
                    echo "
                    <td><button type=\"submit\" class=\"btn\" name=\"aggiungiACarrello\" value=".$row['id'].">Aggiungi al carrello</button></td>
                    </tr>";
                }
                            
            }
            echo "</table>";

            
                            
            if(($_SESSION['type'] == "admin")){
                if ($num_prod != 0){
                    echo "<button type=\"submit\" class=\"btn\" name=\"rimuoviDisponibilita\">Rimuovi disponibilità dei selezionati</button>";
                }        
            }     
            
            echo "</form>
                <br /><br />";
            
            if($num_prod == 0)
                echo "<p class=\"error\">Non ci sono prodotti disponibili con questi filtri</p>";

            if($aggiunto == true){
                $aggiunto = false;
                echo "<script type=\"text/javascript\">alert(\"Prodotto aggiunto al carrello\");</script>";
            }
            
            if($rimuoviDisponibilita == true){
                $rimuoviDisponibilita = false;
                echo "<script type=\"text/javascript\">alert(\"Disponibilità rimossa/e\");</script>";
            }
      
        ?>
    
    
    </div>
</body>
</html>
