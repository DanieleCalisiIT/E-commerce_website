<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    require("cornice.php");
    if ($_SESSION['type'] != "admin") {
        echo "<script>location.replace(\"home.php\");</script>";  
    }
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Storico Clienti</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>
    <?php 

        if(isset($_POST['filtra_crediti'])){
            $importi = $_POST['importo'];
            switch($importi){
                case 'valore1':
                    $query = "SELECT * FROM users WHERE crediti <= 100 AND type = 'cliente';";
                    break;
                case 'valore2':
                    $query = "SELECT * FROM users WHERE crediti > 100 AND crediti <= 300 AND type = 'cliente';";
                    break;
                case 'valore3':
                    $query = "SELECT * FROM users WHERE crediti > 300 AND crediti <= 500 AND type = 'cliente';";
                    break;
                case 'valore4':
                    $query = "SELECT * FROM users WHERE crediti > 500 AND type = 'cliente';";
                    break;
            }            
        } 
        else if(isset($_POST['filtra_nome'])) {
            $nome = $_POST['nome'];
            $query = "SELECT * FROM users WHERE username = '$nome' AND type = 'cliente';";
        } 
        else if(isset($_POST['filtra_prodotto'])) {
            $quantita = $_POST['quantita'];
            $gioco = $_POST['gioco'];
            $query = "SELECT C.* FROM users C WHERE type = 'cliente' AND EXISTS (
                        SELECT * FROM ordini O, ordini_prodotti OP, prodotti P 
                        WHERE C.id = O.id_utente
                        AND OP.id_ordine = O.id
                        AND OP.id_prodotto = P.id
                        AND OP.quantita >= '$quantita'
                        AND P.nome = '$gioco' )";
        
        }
        else if(isset($_POST['filtra_data'])) {
            $giorno1 = $_POST['giorno1'];
            $giorno2 = $_POST['giorno2'];
            $query = "SELECT C.* FROM users C WHERE type = 'cliente' AND EXISTS (
                        SELECT * FROM ordini O
                        WHERE C.id = O.id_utente
                        AND O.data_creazione BETWEEN '$giorno1' AND '$giorno2')";
                }
        else {
          $query = "SELECT * FROM users WHERE type = 'cliente';";
        }
          
        if (!$result = mysqli_query($con, $query)) {
            printf("errore nella query di filtraggio clienti \n");
            exit();
        }              
    ?>

    <body>
        <div class="body">
            <div class="header">
  	            <h2>Storico Clienti</h2>
            </div>
            <table>
            
                <tr><th>Username</th><th>Email</th><th>Nome</th><th>Cognome</th><th>Crediti</th></tr>
                <?php
                    $num_cli = 0;
                    while($row = mysqli_fetch_assoc($result)){
                        $num_cli ++;
                        echo "<tr><td>".$row['username']."</td><td>".$row['email']."</td><td>".$row['nome']."</td><td>".$row['cognome']."</td><td>".$row['crediti']."</td></tr>";
                    }     
                ?>
            </table>
            <?php
                if($num_cli == 0)
                    echo "<p class=\"error\" style=\"font-size:120%;\">Nessun cliente rispetta questi filtri</p>";
            ?>
        </div>
        <div class="body">
            <div class="header">
  	            <h2>Filtra Clienti</h2>
            </div>
            <form method="post" action="storicoClienti.php">
                <p style="color:green; font-size:150%;">Filtra per crediti</p>
                <div class="input-group">
                    <label>I clienti che hanno attualmente questi crediti:</label>
                    <select id="selezione" name="importo">
                        <option value="" selected disabled>Scegli range di crediti</option>
                        <option value="valore1">0-100</option>
                        <option value="valore2">100-300</option>
                        <option value="valore3">300-500</option>
                        <option value="valore4">500+</option>
                    </select>
                </div>
                <div class="input-group">
  	                <button type="submit" class="btn" name="filtra_crediti">Filtra per crediti</button>
  	            </div>
            </form>
            <form method="post" action="storicoClienti.php">
                <p style="color:green; font-size:150%;">Filtra per username</p>
                <div class="input-group">
                    <input type="text" name="nome" placeholder="Inserisci un username...">
                </div>
                <div class="input-group">
  	                <button type="submit" class="btn" name="filtra_nome">Filtra per username</button>
  	            </div>
            </form>
            <form method="post" action="storicoClienti.php">
                <p style="color:green; font-size:150%;">Filtra per prodotto</p>
                <div class="input-group">
                    <label>I clienti che hanno acquistato almeno:</label>
                    <input type="number" step="1" min="1" name="quantita" placeholder="Inserisci una quantità...">
                </div>
                <div class="input-group">
                    <label>copie del seguente gioco in un singolo ordine:</label>
                    <select id="selezione" name="gioco">
                        <option value="" selected disabled>Scegli un gioco</option>
                        <?php
                            $query1 = "SELECT nome FROM prodotti ;";
                            if (!$result1 = mysqli_query($con, $query1)) {
                                printf("errore query \n");
                                exit();
                            }    
                            while($row1 = mysqli_fetch_assoc($result1)){
                                $nomeGioco = $row1['nome'];
                                echo "<option value=\"$nomeGioco\">$nomeGioco</option>";

                            }

                        ?>
                    </select>
                </div>
                <div class="input-group">
  	                <button type="submit" class="btn" name="filtra_prodotto">Filtra per prodotto</button>
  	            </div>
            </form>
            <form method="post" action="storicoClienti.php">
                <p style="color:green; font-size:150%;">Filtra per data</p>
                <div class="input-group">
                    <label>I Clienti che hanno effettuato almeno un ordine tra il giorno:</label>
                    <input type="date" max="<?php echo date('Y-m-d');?>" name="giorno1" placeholder="Inserisci un giorno...">
                </div>
                <div class="input-group">
                    <label>e il giorno:  (estremi compresi)</label>
                    <input type="date" max="<?php echo date('Y-m-d');?>" name="giorno2" placeholder="Inserisci un giorno...">
                </div>
                <div class="input-group">
  	                <button type="submit" class="btn" name="filtra_data">Filtra per data</button>
  	            </div>
            </form>
        </div>
    </body>
</html>
