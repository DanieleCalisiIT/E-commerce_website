<!-- 
Questo è un semplice script di connessione al DB, in cui è presente il nome del DB MySql al quale 
connettersi e il nome della tabella che verrà poi usata all'interno degli altri script, come l'install    
-->

<?php

    $nomeDB = "tesina";
    $tabellaUtenti = "users";

    $con = new mysqli("localhost", "root", "", $nomeDB); //da modificare volendo id e pw

    if (mysqli_connect_errno($con)) 
    {
        printf("errore di connessione al DB: %s \n", mysqli_connect_error($con));
        exit();
    }

?>