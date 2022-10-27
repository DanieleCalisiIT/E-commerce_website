<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    require("./cornice.php");
    if (!isset($_SESSION['success']) || ($_SESSION['type'] == "amministratore") || ($_SESSION['type'] == "gestore")) {
        header('Location: home.php');  
    }
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Carrello</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<?php
    if (isset($_POST['rimuoviDaCarrello']))
    {
        $i=0;
        $flag=0;
        while(($i < count($_SESSION['carrello'])) && ($flag==0))
        {
            if($_SESSION['carrello'][$i] == $_POST['rimuoviDaCarrello'])    
            {
                $_SESSION['carrello'][$i] = 0;
                $flag=1;
                $rimozioneEffettuata = true;    
                
            }
            $i++;
        }
       
    }

    if(isset($_POST['acquistaCarrello']))
    {
        if($_SESSION['crediti'] >= $_SESSION['totaleCarrello'])
        {
            
            $_SESSION['crediti'] = $_SESSION['crediti'] - $_SESSION['totaleCarrello'];
            $query = "UPDATE users 
                    SET crediti = '".$_SESSION['crediti']."'
                    WHERE id = '".$_SESSION['id']."'; ";

            if (!$result = mysqli_query($con, $query)) {
                printf("errore nella query di aggiornamento crediti \n");
                exit();
            }
            $dataAttuale = date('Y-m-d');
            $query = "INSERT INTO ordini (id_utente, data_creazione, totale) VALUES
            ('".$_SESSION['id']."', '$dataAttuale', '".$_SESSION['totaleCarrello']."');";

            if (!$result = mysqli_query($con, $query)) {
                printf("errore nella query di inserimento ordine \n");
                exit();
            }

            for($i=0; $i < count($_SESSION['carrello']); $i++)
                {
                    
                    if($_SESSION['carrello'][$i] != 0)
                    {
                        $idProdotto = $_SESSION['carrello'][$i];
                        $quantita=0;
                        for($k=0; $k < count($_SESSION['carrello']); $k++)
                        {
                            if($idProdotto == $_SESSION['carrello'][$k])
                            {
                                $quantita++;
                                $_SESSION['carrello'][$k] = 0;
                            }
                        }

                        $query = "SELECT id FROM ordini ORDER BY id desc limit 1"; //opp con max(id)
                        if (!$result = mysqli_query($con, $query)) 
                        {
                            printf("errore nella query di ricerca IDordine \n");
                            exit();
                        }
                
                        $row = mysqli_fetch_array($result);
        
                        if ($row) {  

                            $idOrdine = $row['id'];               
                
                        }

                        $query = "INSERT INTO ordini_prodotti (id_ordine, id_prodotto, quantita) VALUES
                        ('$idOrdine', '$idProdotto', '$quantita');";

                        if (!$result = mysqli_query($con, $query)) {
                            printf("errore nella query di inserimento ordine_prodotto \n");
                            exit();
                        }

                        
                    }
                }

                $_SESSION['carrello']=array();
                $_SESSION['totaleCarrello'] = null;


                
                $acquistoEffettuato = true;
                header("refresh: 0"); 
            
        }
        else
            $soldiMancanti = true;
        
        
    }
?>

<body>
    <div class="body">
        <div class="header">
  	        <h2>Il tuo carrello</h2>
        </div>

        <?php
            $i=0;
            $flag=0;
            while(($i < count($_SESSION['carrello'])) && ($flag==0))
            {
                if($_SESSION['carrello'][$i] != 0)
                    $flag=1;

                $i++;
            }

            if($flag == 0)
            {
                echo "<p class=\"error\">Non ci sono prodotti nel carrello</p>";
            }
            else
            {
                echo "
                <form class=\"formCarrello\" method=\"post\" action=\"mostraCarrello.php\">
                <table id=\"carrello\">
                <tr>
                    <th></th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Casa Editrice</th>
                    <th>Lingua</th>
                    <th>Prezzo</th>
                </tr>";
                $totale = 0;
                for($i=0; $i < count($_SESSION['carrello']); $i++)
                {
                    if($_SESSION['carrello'][$i] != 0)
                    {

                        $query = "SELECT * FROM prodotti WHERE id = '".$_SESSION['carrello'][$i]."';"; 
                        if (!$result = mysqli_query($con, $query)) {
                            echo "errore query ricerca prodotto";
                            exit();
                        }

                        $row = mysqli_fetch_array($result);
            
                        if($row){
                            $img = $row['img'];
                            $nome = $row['nome'];
                            $categoria = $row['categoria'];
                            $casaEditrice = $row['casaEditrice'];
                            $lingua = $row['lingua'];
                            $prezzo = $row['prezzo'];
                        }    

                        $totale = $totale + $prezzo;

                        echo "
                            <tr id=\"trcarrello\">
                                <td class=\"tdcarrello\"><img src=$img height=\"100\" width=\"100\"></td>
                                <td>$nome</td>
                                <td>$categoria</td>
                                <td>$casaEditrice</td> 
                                <td>$lingua</td>
                                <td>$prezzo</td>
                                <td><button type=\"submit\" class=\"btn\" name=\"rimuoviDaCarrello\" value=".$_SESSION['carrello'][$i].">Rimuovi</button></td>
                            </tr>
                        "; 
                            


                    }

                }

                $_SESSION['totaleCarrello'] = $totale;

                echo "
                <tr>
                    <td id=\"totale\" colspan=\"6\">Totale = &euro;$totale</td>
                </tr>
                    
                </table>";

                echo "<button type=\"submit\" class=\"btn-carrello\" name=\"acquistaCarrello\" >Acquista prodotti</button>
                </form>";

            }

            if($rimozioneEffettuata == true)
            {
                $rimozioneEffettuata = false;
                echo "<script type=\"text/javascript\">
                alert(\"Prodotto rimosso dal carrello\");
                </script>";
                
            }

            if($acquistoEffettuato == true){
                $acquistoEffettuato = false;
                echo "<script type=\"text/javascript\">alert(\"Acquisto effettuato! Ordine inserito nello storico\");</script>";                
            }
            
            if($soldiMancanti == true){
                $soldiMancanti = false;
                echo "<script type=\"text/javascript\">alert(\"Non hai abbastanza soldi per effettuare questo acquisto. Ricarica il portafoglio prima di acquistare!\");</script>";
            }
            
                
        ?>
    </div>
</body>
</html>
