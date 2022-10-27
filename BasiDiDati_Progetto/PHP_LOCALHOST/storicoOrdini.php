<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    require("./cornice.php");
    if (!isset($_SESSION['success']) || ($_SESSION['type'] == "admin")) {
        header('Location: home.php');  
    }
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Storico Ordini</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <div class="body">
        <div class="header">
  	        <h2>Storico Ordini</h2>
        </div>

        <?php

            $query = "SELECT COUNT(*) AS num_ordini FROM ordini WHERE id_utente = '".$_SESSION['id']."';";

            if (!$result = mysqli_query($con, $query)) {
                printf("errore nella query di conta ordini \n");
                exit();
            }

            $row = mysqli_fetch_array($result);
        
            if($row['num_ordini'] == 0){
                echo "<p class=\"error\">Non hai mai effettuato ordini</p>";       
                
            }
            else{
                
                echo "<br />";
                echo "<ul id=\"ordini\">";
    
                $query = "SELECT id, data_creazione, totale  FROM ordini WHERE id_utente = '".$_SESSION['id']."';";

                if (!$result1 = mysqli_query($con, $query)) {
                    printf("errore nella query sugli ordini \n");
                    exit();
                }
                $numOrdine = 0;
                while($row1 = mysqli_fetch_assoc($result1)){

                    $idOrdine = $row1['id'];  
                    $dataCreazione = $row1['data_creazione'];  
                    $totale = $row1['totale'];  
                    $numOrdine++;

                    echo "<li style=\"font-size: 120%;\"><span style=\"font-size: 125%; color: #D2691E;\">Ordine N°$numOrdine</span> <strong>Data Acquisto</strong>: <span style=\"color: crimson;\">$dataCreazione</span> <strong>Totale</strong>: <span style=\"color: crimson;\">&euro;$totale</span></li>";
                        echo "<br /><br /><table>
                            <tr>
                                <th>Immagine</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Casa Editrice</th>
                                <th>Lingua</th>
                                <th>Prezzo (del singolo)</th>
                                <th>Quantità</th>
                            </tr>";

                    $query = "SELECT id_prodotto, quantita FROM ordini_prodotti WHERE id_ordine = '$idOrdine';";
                    if (!$result2 = mysqli_query($con, $query)) {
                        printf("errore nella query sugli ordini_prodotti \n");
                        exit();
                    }
                    while($row2 = mysqli_fetch_assoc($result2)){
                        $idProdotto = $row2['id_prodotto']; 
                        $quantita = $row2['quantita']; 
                        $query = "SELECT * FROM prodotti WHERE id = '$idProdotto';";
                        if (!$result3 = mysqli_query($con, $query)) {
                            printf("errore nella query sui prodotti \n");
                            exit();
                        }

                        $row3 = mysqli_fetch_array($result3);
            
                        if($row3){
                            $img = $row3['img'];
                            $nome = $row3['nome'];
                            $categoria = $row3['categoria'];
                            $casaEditrice = $row3['casaEditrice'];
                            $lingua = $row3['lingua'];
                            $prezzo = $row3['prezzo'];
                        }    

                        echo "<tr>
                            <td style=\"width: 100;\"><img src=$img height=\"100\" width=\"100\"></td><td class=\"chiara\">$nome</td><td class=\"scura\">$categoria</td><td class=\"chiara\">$casaEditrice</td><td class=\"scura\">$lingua</td><td class=\"chiara\">$prezzo</td><td class=\"scura\">$quantita</td>
                            </tr>";

                    }
                    echo "</table> <br /><br /><br /><br /><br /><br />";

                }
                echo "</ul>";

                

            }





            
        ?>
    
    
    </div>
</body>
</html>