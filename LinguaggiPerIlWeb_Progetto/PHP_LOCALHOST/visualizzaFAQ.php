<!-- 
In questo script vengono semplicemente recuperate, tramite DOM, le informazioni
presenti all'interno del file faq.xml, che contiene le domande e le risposte fatte
più frequentemente. Quindi viene parsato il file per recuperare il testo delle domande 
e delle risposte, viene creato una variabile index che contiene semplicemente il valore
all'interno del ciclo di quella domanda, in modo che nella visualizzazione vengano stampati
i numeri delle domande, per una questione di ordine. Il tutto (index, domandaFAQValue, rispostaFAQValue
che contengono rispettivamente l'indice, il testo della domanda e il testo della risposta), viene stampato
all'interno di una tabella.    
-->

<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    require("./cornice.php");
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>FAQ</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <div class="body">
        <div class="header">
  	        <h2>FAQ</h2>
        </div>

        <?php

            $stringaXML = "";
            foreach ( file("../XML/faq.xml") as $nodo )   
            {
                $stringaXML .= trim($nodo);
            }
        
            $docFAQ = new DOMDocument();
            $docFAQ->loadXML($stringaXML);

            $rootFAQ = $docFAQ->documentElement;
            $listaFAQ = $rootFAQ->childNodes;
            echo "<table class=\"FAQ\">";

            for ($i=0; $i<$listaFAQ->length; $i++) 
            {
                $FAQ = $listaFAQ->item($i);

                $idFAQ = $FAQ->firstChild;
                $idFAQValue = $idFAQ->textContent;

                $domandaFAQ = $idFAQ->nextSibling;
                $domandaFAQValue = $domandaFAQ->textContent;
        
                $rispostaFAQ = $domandaFAQ->nextSibling;             
                $rispostaFAQValue = $rispostaFAQ->textContent;

                $index = $i + 1;

                echo "
                <tr>
                    <td class=\"domFAQ\">$index)$domandaFAQValue</td>
                </tr>
                <tr>
                    <td class=\"rispFAQ\">$rispostaFAQValue</td>
                </tr>";
            }

        ?>
        </table>
    </div>
</body>
</html>