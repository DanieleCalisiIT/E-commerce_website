<!--
Questo script semplicemente rimanda alla home dopo aver distrutto la session presente, ad esempio
non appena venga effettuata la registrazione
 -->

<?php
    session_start();
    unset($_SESSION);
    session_destroy();
    header("Location: home.php");
?>