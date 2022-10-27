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
    <title>Aggiungi Crediti</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <div class="body">
        <div class="header">
  	        <h2>Aggiungi Crediti</h2>
        </div>


        <form method="post" action="richiediSoldi.php">
            <div class="input-group">
                <label>Importo da aggiungere</label>
                <input type="number" step="0.01" name="importo">
            </div>
            <div class="input-group">
  	            <button type="submit" class="btn" name="invio">Aggiungi importo</button>
  	        </div>
        </form>

        <?php

            $idUtente = $_SESSION['id'];
            if (isset($_POST['invio'])) {
                
                if (empty($_POST['importo']))
                    echo "<p id= \"error\" style=\"font-size:160%;\">Importo richiesto</p>
                    <br />";
                else
                {
                    $vecchioCredito = $_SESSION['crediti'];
                    $importo = $_POST['importo'];
                    $query = "UPDATE users 
                              SET crediti = ($vecchioCredito + $importo)
                              WHERE id = $idUtente;";

                    if (!$result = mysqli_query($con, $query)) {
                        printf("errore nella query di ricarica \n");
                        exit();
                    }
                    $_SESSION['crediti'] = $vecchioCredito + $importo;
                    //avviso di richiesta inoltrata
                    echo "<script type=\"text/javascript\">alert(\"Hai ricaricato il tuo portafoglio.\"); location.replace(\"richiediSoldi.php\");</script>";
                }

            }
        ?>

    </div>
</body>
</html>