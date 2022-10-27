<?php   echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

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
        $primaConnessione = new mysqli("localhost", "root", ""); //da modificare volendo id e pw
    
        if (mysqli_connect_errno($primaConnessione)) 
        {             
            printf("errore con la prima connessione al DB: %s \n", mysqli_connect_error($primaConnessione));  
            exit();
        }

        $queryCreazioneDB = "CREATE DATABASE tesina";
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
        
        $query = "CREATE TABLE if not exists users (
            id int NOT NULL auto_increment, primary key (id), 
            username varchar (40) NOT NULL, 
            password varchar (30) NOT NULL,
            email varchar (50) NOT NULL,
            type varchar (20) NOT NULL
            );";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("tabella Utenti creata con successo \n");
            echo "<br />";
        }
        else 
        {
            printf("errore con la query di creazione della tabella users \n");
            exit();
        }

        $query = "INSERT INTO users (username, password, email, type)
              VALUES ('admin1', 'admin1', 'admin1@gmail.com', 'amministratore')";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("admin1 aggiunto alla tabella \n");
            echo "<br />";
        }
        else 
        {
            printf("errore di aggiunta di admin1 in tabella \n");
            exit();
        }
        
        $query = "INSERT INTO users (username, password, email, type)
              VALUES ('admin2', 'admin2', 'admin2@gmail.com', 'amministratore')";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("admin2 aggiunto alla tabella \n");
            echo "<br />";
        }
        else 
        {
            printf("errore di aggiunta di admin2 in tabella \n");
            exit();
        }
        
        $query = "INSERT INTO users (username, password, email, type)
              VALUES ('gestore1', 'gestore1', 'gestore1@gmail.com', 'gestore')";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("gestore1 aggiunto alla tabella \n");
            echo "<br />";
        }
        else 
        {
            printf("errore di aggiunta di gestore1 in tabella \n");
            exit();
        }
        
        $query = "INSERT INTO users (username, password, email, type)
              VALUES ('gestore2', 'gestore2', 'gestore2@gmail.com', 'gestore')";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("gestore2 aggiunto alla tabella \n");
            echo "<br />";
        }
        else 
        {
           printf("errore di aggiunta di gestore2 in tabella \n");
            exit();
        }
    
        $query = "INSERT INTO users (username, password, email, type)
              VALUES ('cliente1', 'cliente1', 'cliente1@gmail.com', 'cliente')";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("cliente1 aggiunto alla tabella \n");
            echo "<br />";
        }
        else 
        {
            printf("errore di aggiunta di cliente1 in tabella \n");
            exit();
        }

        $queryID = "SELECT id FROM users WHERE username = 'cliente1'";
        if (!$resultID = mysqli_query($con, $queryID)) 
        {
            printf("errore nella query di ricerca ID \n");
            exit();
        }
        
        $row = mysqli_fetch_array($resultID);
  
        if ($row) {  
            $dbID = $row['id']; 
        }
    
        $stringaXML = "";
        foreach ( file("./XML/account.xml") as $nodo )  
        {
            $stringaXML .= trim($nodo);
        }
        
        $docAccount = new DOMDocument();
        $docAccount->loadXML($stringaXML);
                  
        $rootAccount = $docAccount->documentElement;
        $listaAccount = $rootAccount->childNodes;

        $stringaXML = "";
        foreach ( file("./XML/indirizzi.xml") as $nodo ) 
        {
            $stringaXML .= trim($nodo);
        }
    
        $docIndirizzi = new DOMDocument();
        $docIndirizzi->loadXML($stringaXML);
                  
        $rootIndirizzi = $docIndirizzi->documentElement;
        $listaIndirizzi = $rootIndirizzi->childNodes;

        $nuovoAccount = $docAccount->createElement("profilo");
        $rootAccount->appendChild($nuovoAccount);
        $nuovoAccount->setAttribute("stato", "attivo");
        $nuovoID = $docAccount->createElement("idAcc", $dbID);
        $nuovoAccount->appendChild($nuovoID);
        $nuovoNome = $docAccount->createElement("nome", "cliente");
        $nuovoAccount->appendChild($nuovoNome);
        $nuovoCognome = $docAccount->createElement("cognome", "uno");
        $nuovoAccount->appendChild($nuovoCognome);
        $nuoviSoldi = $docAccount->createElement("soldi", 0);
        $nuovoAccount->appendChild($nuoviSoldi);
        $nuoviCrediti = $docAccount->createElement("crediti", 0);
        $nuovoAccount->appendChild($nuoviCrediti);
        $nuovaRep = $docAccount->createElement("reputazione", 0);
        $nuovoAccount->appendChild($nuovaRep);

        $IDInd = ($listaIndirizzi->length) + 1; 

        $nuovoIndirizzo = $docIndirizzi->createElement("indirizzo");
        $rootIndirizzi->appendChild($nuovoIndirizzo);

        $nuovoIDInd = $docIndirizzi->createElement("id", $IDInd);
        $nuovoIndirizzo->appendChild($nuovoIDInd);
        $nuovaCitta = $docIndirizzi->createElement("citta", "Milano");
        $nuovoIndirizzo->appendChild($nuovaCitta);
        $nuovoCAP = $docIndirizzi->createElement("cap", 20019);
        $nuovoIndirizzo->appendChild($nuovoCAP);
        $nuovaProvincia = $docIndirizzi->createElement("provincia","Milano");
        $nuovoIndirizzo->appendChild($nuovaProvincia);
        $nuovaRegione = $docIndirizzi->createElement("regione", "Lombardia");
        $nuovoIndirizzo->appendChild($nuovaRegione);
        $nuovaNazione = $docIndirizzi->createElement("nazione", "Italia");
        $nuovoIndirizzo->appendChild($nuovaNazione);
        $nuovaVia = $docIndirizzi->createElement("via", "Garibaldi");
        $nuovoIndirizzo->appendChild($nuovaVia);
        $nuovoCivico = $docIndirizzi->createElement("civico", 2);
        $nuovoIndirizzo->appendChild($nuovoCivico);

        $nuovoIDAddr = $docAccount->createElement("idAddr", $IDInd);
        $nuovoAccount->appendChild($nuovoIDAddr);

        $nuovoCount = $docAccount->createElement("countBan", 0);
        $nuovoAccount->appendChild($nuovoCount);

        $nuovaData = $docAccount->createElement("dataBan", "2000-01-01");
        $nuovoAccount->appendChild($nuovaData);

        $percorso = "./XML/account.xml";
        $docAccount->save($percorso);

        printf("cliente1 aggiunto ad account.xml \n");
        echo "<br />";

        $percorsoInd = "./XML/indirizzi.xml";
        $docIndirizzi->save($percorsoInd);

        printf("indirizzo di cliente1 aggiunto ad indirizzi.xml \n");
        echo "<br />";

        
    
        $query = "INSERT INTO users (username, password, email, type)
              VALUES ('cliente2', 'cliente2', 'cliente2@gmail.com', 'cliente')";

        if ($resultQ = mysqli_query($con, $query))
        {   
            printf("cliente2 aggiunto alla tabella \n");
            echo "<br />";
        }
        else 
        {
            printf("errore di aggiunta di cliente2 in tabella \n");
            exit();
        }

        $queryID = "SELECT id FROM users WHERE username = 'cliente2'";
        if (!$resultID = mysqli_query($con, $queryID)) 
        {
            printf("errore nella query di ricerca ID \n");
            exit();
        }
        
        $row = mysqli_fetch_array($resultID);
  
        if ($row) {  
            $dbID = $row['id'];                
        }

        $nuovoAccount = $docAccount->createElement("profilo");
        $rootAccount->appendChild($nuovoAccount);
        $nuovoAccount->setAttribute("stato", "attivo");
        $nuovoID = $docAccount->createElement("idAcc", $dbID);
        $nuovoAccount->appendChild($nuovoID);
        $nuovoNome = $docAccount->createElement("nome", "cliente");
        $nuovoAccount->appendChild($nuovoNome);
        $nuovoCognome = $docAccount->createElement("cognome", "due");
        $nuovoAccount->appendChild($nuovoCognome);
        $nuoviSoldi = $docAccount->createElement("soldi", 0);
        $nuovoAccount->appendChild($nuoviSoldi);
        $nuoviCrediti = $docAccount->createElement("crediti", 0);
        $nuovoAccount->appendChild($nuoviCrediti);
        $nuovaRep = $docAccount->createElement("reputazione", 0);
        $nuovoAccount->appendChild($nuovaRep);

        $listaIndirizzi = $rootIndirizzi->childNodes;
        $IDInd = ($listaIndirizzi->length) + 1; 

        $nuovoIndirizzo = $docIndirizzi->createElement("indirizzo");
        $rootIndirizzi->appendChild($nuovoIndirizzo);

        $nuovoIDInd = $docIndirizzi->createElement("id", $IDInd);
        $nuovoIndirizzo->appendChild($nuovoIDInd);
        $nuovaCitta = $docIndirizzi->createElement("citta", "Roma");
        $nuovoIndirizzo->appendChild($nuovaCitta);
        $nuovoCAP = $docIndirizzi->createElement("cap", 00100);
        $nuovoIndirizzo->appendChild($nuovoCAP);
        $nuovaProvincia = $docIndirizzi->createElement("provincia","Roma");
        $nuovoIndirizzo->appendChild($nuovaProvincia);
        $nuovaRegione = $docIndirizzi->createElement("regione", "Lazio");
        $nuovoIndirizzo->appendChild($nuovaRegione);
        $nuovaNazione = $docIndirizzi->createElement("nazione", "Italia");
        $nuovoIndirizzo->appendChild($nuovaNazione);
        $nuovaVia = $docIndirizzi->createElement("via", "del Corso");
        $nuovoIndirizzo->appendChild($nuovaVia);
        $nuovoCivico = $docIndirizzi->createElement("civico", 5);
        $nuovoIndirizzo->appendChild($nuovoCivico);

        $nuovoIDAddr = $docAccount->createElement("idAddr", $IDInd);
        $nuovoAccount->appendChild($nuovoIDAddr);

        $nuovoCount = $docAccount->createElement("countBan", 0);
        $nuovoAccount->appendChild($nuovoCount);

        $nuovaData = $docAccount->createElement("dataBan", "2000-01-01");
        $nuovoAccount->appendChild($nuovaData);

        $percorso = "./XML/account.xml";
        $docAccount->save($percorso);

        printf("cliente2 aggiunto ad account.xml \n");
        echo "<br />";

        $percorsoInd = "./XML/indirizzi.xml";
        $docIndirizzi->save($percorsoInd);

        printf("indirizzo di cliente2 aggiunto ad indirizzi.xml \n");
        echo "<br />";

        

        mysqli_close($con);
    ?>
</body>
</html>