<?php

    $nomeDB = "progettoDB";

    $con = new mysqli("localhost", "root", "", $nomeDB); 

    if (mysqli_connect_errno($con)) 
    {
        printf("errore di connessione al DB: %s \n", mysqli_connect_error($con));
        exit();
    }

?>