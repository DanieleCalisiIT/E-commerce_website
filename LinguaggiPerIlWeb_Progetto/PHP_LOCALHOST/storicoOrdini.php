<!-- 
All'interno di questo script è gestito il sistema di visualizzazione dello storico degli ordini per i clienti.
Vengono recuperate, sempre tramite il DOM, le informazioni contenute all'interno del file XML relativo agli ordini.
Ogni ordine ha un'elemento che contiene l'ID del cliente che lo ha effettuato, che sarà fatto matchare con l'ID 
del cliente presente nella session attiva in quel momento. Verranno quindi stampate all'interno di una tabella
tutte le informazioni relative all'ordine in questione. Gli ordini sono posti all'interno di una lista. La variabile
$index indica il numero dell'ordine in questione.
-->

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
    <title>Storico Ordini</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <div class="body">
        <div class="header">
  	        <h2>Storico Ordini</h2>
        </div>

        <?php

            $stringaXML = "";
            foreach ( file("../XML/ordini.xml") as $nodo )   
            {
                $stringaXML .= trim($nodo);
            }

            $docOrdini = new DOMDocument();
            $docOrdini->loadXML($stringaXML);

            $rootOrdini = $docOrdini->documentElement;
            $listaOrdini = $rootOrdini->childNodes;

            $stringaXML = "";
            foreach ( file("../XML/prodotti.xml") as $nodo )   
            {
                $stringaXML .= trim($nodo);
            }
    
            $docProdotti = new DOMDocument();
            $docProdotti->loadXML($stringaXML);

            $rootProdotti = $docProdotti->documentElement;
            $listaProdotti = $rootProdotti->childNodes;

            $i=0;
            $ordineTrovato=0;
            while (($i<$listaOrdini->length) && ($ordineTrovato==0)) 
            {
                $ordine = $listaOrdini->item($i);
                $idOrd = $ordine->firstChild;

                $idCli = $idOrd->nextSibling;

                if(($idCli->textContent) == $_SESSION['id'])
                    $ordineTrovato=1;
                
                $i++;
            }

            if ($ordineTrovato == 0){ 
                echo "<p class=\"error\">Non hai mai effettuato ordini</p>";
            } 
            else
            {
                echo "<br />";
                echo "<ul id=\"ordini\">";
                for($i=0; $i<$listaOrdini->length; $i++) 
                {
                    $ordine = $listaOrdini->item($i);
                    $idOrd = $ordine->firstChild;

                    $idCli = $idOrd->nextSibling;

                    if(($idCli->textContent) == $_SESSION['id'])
                    {
                        $dataAquisto = $idCli->nextSibling;
                        $dataAquistoValue = $dataAquisto->textContent;

                        $listaGiochi = $ordine->getElementsByTagName("gioco");

                        for($j=0; $j<$listaGiochi->length; $j++)
                        {
                            $gioco = $listaGiochi->item($j);

                            $nomeGioco = $gioco->firstChild;
                            $arrayNomeGiocoValue[$j] = $nomeGioco->textContent;

                            $immagineGioco = $nomeGioco->nextSibling;
                            $arrayImmagineGiocoValue[$j] = $immagineGioco->textContent;

                            $linguaGioco = $immagineGioco->nextSibling;
                            $arrayLinguaGiocoValue[$j] = $linguaGioco->textContent;

                            $annoGioco = $linguaGioco->nextSibling;
                            $arrayAnnoGiocoValue[$j] = $annoGioco->textContent;

                            $prezzoAcquisto = $annoGioco->nextSibling;
                            $arrayPrezzoAcquistoValue[$j] = $prezzoAcquisto->textContent;

                        }

                        $totaleCrediti = $ordine->lastChild;
                        $totaleCreditiValue = $totaleCrediti->textContent;

                        $totale = $totaleCrediti->previousSibling;
                        $totaleValue = $totale->textContent;

                        $creditiRicevuti = ($totaleValue / 100)*10;

                        $index = $i + 1;

                        echo "<li style=\"font-size: 120%;\"><span style=\"font-size: 125%; color: #D2691E;\">Ordine N°$index</span> <strong>Data Acquisto</strong>: <span style=\"color: crimson;\">$dataAquistoValue</span> <strong>Totale</strong>: <span style=\"color: crimson;\">&euro;$totaleValue</span> <strong>Totale crediti spesi</strong>: <span style=\"color: crimson;\">&euro;$totaleCreditiValue</span> <strong>Crediti Ricevuti</strong>: <span style=\"color: crimson;\">$creditiRicevuti</span></li>";
                        echo "<br /><br /><table>
                            <tr>
                                <th>Immagine</th>
                                <th>Nome</th>
                                <th>Anno</th>
                                <th>Lingua</th>
                                <th>Prezzo d acquisto</th>
                            </tr>";

                        for($j=0; $j<$listaGiochi->length; $j++)
                            echo "<tr>
                            <td style=\"width: 100;\"><img src=$arrayImmagineGiocoValue[$j] height=\"100\" width=\"100\"></td><td class=\"chiara\">$arrayNomeGiocoValue[$j]</td><td class=\"scura\">$arrayAnnoGiocoValue[$j]</td><td class=\"chiara\">$arrayLinguaGiocoValue[$j]</td><td class=\"scura\">$arrayPrezzoAcquistoValue[$j]</td>
                            </tr>";
                        
                        echo "</table> <br /><br /><br /><br /><br /><br />";
                    }
                }
                echo "</ul>";
            }
        ?>
    
    
    </div>
</body>
</html>