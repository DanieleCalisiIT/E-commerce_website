<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>installer del DB</title>
</head>

<body>
    <h2>Creazione DB</h2>

    <?php			
        error_reporting(E_ALL &~E_NOTICE);                      
        $primaConnessione = new mysqli("localhost", "root", ""); 
    
        if (mysqli_connect_errno($primaConnessione)) 
        {             
            printf("errore con la prima connessione al DB: %s \n", mysqli_connect_error($primaConnessione));  
            exit();
        }

        $queryCreazioneDB = "CREATE DATABASE progettoDB";
        if ($resultQ = mysqli_query($primaConnessione, $queryCreazioneDB)) 
        {
            printf("DB creato con successo \n");
            echo "<br />";
        }
        else 
        {
            printf("errore in creazione del DB (il database potrebbe essere già presente)\n"); 
            exit();
        }
    
        mysqli_close($primaConnessione);

        echo "<h2>Creazione tabella utenti e popolamento con qualche riga</h2>";  
    
        require_once("./PHP_LOCALHOST/connection.php");

        $query = "CREATE TABLE if not exists indirizzi (
            id int auto_increment PRIMARY KEY,
            citta varchar (50) NOT NULL,
            cap varchar (5) NOT NULL,
            provincia varchar (50) NOT NULL,
            regione varchar (50) NOT NULL,
            nazione varchar (50) NOT NULL,
            via varchar (50) NOT NULL,
            civico varchar (20) NOT NULL
        );";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("tabella indirizzi creata con successo \n");
            echo "<br />";
        }
        else 
        {
            printf("errore con la query di creazione della tabella indirizzi \n");
            exit();
        }
        
        $query = "INSERT INTO indirizzi (citta, cap, provincia, regione, nazione, via, civico) VALUES 
            ('Latina', '04100', 'Latina', 'Lazio', 'Italia', 'Togliatti', '2'),
            ('Terracina', '04019', 'Latina', 'Lazio', 'Italia','Garibaldi', '15');";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("tabella indirizzi popolata con successo \n");
            echo "<br />";
        }
        else 
        {
            printf("errore popolazione tabella indirizzi \n");
            exit();
        }

        $query = "CREATE TABLE if not exists users (
            id int auto_increment PRIMARY KEY,
            username varchar (50) NOT NULL UNIQUE,
            password varchar (50) NOT NULL,
            email varchar(50) NOT NULL UNIQUE,
            nome varchar (50) NOT NULL,
            cognome varchar (50) NOT NULL,
            type varchar (20) NOT NULL,
            crediti double,
            id_indirizzo int,
            FOREIGN KEY (id_indirizzo) REFERENCES indirizzi (id)
        );";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("tabella users creata con successo \n");
            echo "<br />";
        }
        else 
        {
            printf("errore con la query di creazione della tabella users \n");
            exit();
        }

        $query = "INSERT INTO users (username, password, email, nome, cognome, type, crediti, id_indirizzo) VALUES
        ('cliente1', 'cliente1', 'cliente1@gmail.com', 'cliente', 'uno', 'cliente', '0.00', '1'),
        ('cliente2', 'cliente2', 'cliente2@gmail.com', 'cliente', 'due', 'cliente', '0.00', '2'),
        ('admin1', 'admin1', 'admin1@gmail.com', 'admin', 'uno', 'admin', null, null);";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("tabella users popolata con successo \n");
            echo "<br />";
        }
        else 
        {
            printf("errore popolamento tabella users \n");
            exit();
        }
        
        $query = "CREATE TABLE if not exists prodotti (
            id int auto_increment PRIMARY KEY,
            img varchar (50) NOT NULL,
            nome varchar (50) NOT NULL,
            categoria varchar (50) NOT NULL,
            casaEditrice varchar (50) NOT NULL,
            lingua varchar (50) NOT NULL,
            prezzo float NOT NULL,
            disponibilita char (10) NOT NULL
        );";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("tabella prodotti creata con successo \n");
            echo "<br />";
        }
        else 
        {
            printf("errore creazione tabella prodotti \n");
            exit();
        }

        $query = "INSERT INTO prodotti (img, nome, categoria, casaEditrice, lingua, prezzo, disponibilita) VALUES
        ('../images/Risiko1.png', 'Risiko', 'Strategia', 'Pendragon', 'Russo', '29.99', 'Si'),
        ('../images/Monopoly2.png', 'Monopoly', 'Famiglia', 'Hasbro', 'Italiano', '35.99','Si');";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("tabella prodotti popolata con successo \n");
            echo "<br />";
        }
        else 
        {
            printf("errore popolazione tabella prodotti \n");
            exit();
        }

        $query = "CREATE TABLE if not exists ordini (
            id int auto_increment PRIMARY KEY,
            id_utente int NOT NULL,
            data_creazione date NOT NULL,
            totale double NOT NULL,
            FOREIGN KEY (id_utente) REFERENCES users (id)
        );";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("tabella ordini creata con successo \n");
            echo "<br />";
        }
        else 
        {
            printf("errore creazione tabella ordini \n");
            exit();
        }

        $query = "CREATE TABLE if not exists ordini_prodotti (
            id_ordine int NOT NULL,
            id_prodotto int NOT NULL,
            quantita int NOT NULL,
            PRIMARY KEY (id_ordine, id_prodotto),
            FOREIGN KEY (id_ordine) REFERENCES ordini (id),
            FOREIGN KEY (id_prodotto) REFERENCES prodotti (id)
        );";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("tabella ordini_prodotti creata con successo \n");
            echo "<br />";
        }
        else 
        {
            printf("errore creazione tabella ordini_prodotti \n");
            exit();
        }
        
        mysqli_close($con);
    ?>
</body>
</html>