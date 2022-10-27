<!--
In questo script si gestiscono le interazioni di un cliente con il suo attuale carrello. 
    Riassumendo, la struttura di questo script è: 
    1: Gestione form (funzionalità)
    2: Form
    3: Avvisi
    La gestione della form è stata fatta con una serie di if(isset()) così da isolare le varie funzionalità;  Le funzionalità sono: 
    --rimuovere un prodotto dal carrello
    --acquistare i prodotti nel carrello
    Il carrello è gestito principalmente con 3 array SESSION: 
    L'array "carrello" contiene gli ID dei prodotti aggiunti al carrello.
    L'array "carrelloSconti" contiene gli ID degli sconti associati ai prodotti nel carrello (se l'elemento con indice i vale 0, significa che il prodotto con indice i non ha sconto )
    L'array "carrelloTipoSconti" contiene elementi con valore 0 o 1 associati ai prodotti nel carrello (se l'elemento con indice i vale 0 significa che il prodotto con indice i ha uno
    sconto ottenuto grazie alla reputazione, quindi non avrà un costo in crediti; se vale 1 significa che il cliente non aveva abbastanza reputazione
    ,quindi dovrà pagare dei crediti per ottenere quello sconto)
    Dopo l'acquisto il cliente ottiene il 10% dei soldi spesi, in crediti.
    La form mostra i prodotti nel carrello, permettendo di interagire con essi. 
    Gli eventuali avvisi vengono "calcolati" nella gestione della form, e poi mostrati dopo di essa, così da non bloccare il caricamento di quest' ultima.

 -->
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
        if($soldiValue >= $_SESSION['totaleCarrello'])
        {
            if($creditiValue >= $_SESSION['totaleCreditiCarrello'])
            {

            
                $nuoviSoldi = $docAccount->createElement("soldi", $soldiValue - $_SESSION['totaleCarrello']);
                $mioProfilo->replaceChild($nuoviSoldi,$soldi);

                $creditiGuadagnati = ($_SESSION['totaleCarrello'] / 100) * 10;  //guadagni il 10% di quanto speso in crediti

                $nuoviCrediti = $docAccount->createElement("crediti", $creditiValue + $creditiGuadagnati - $_SESSION['totaleCreditiCarrello']);
                $mioProfilo->replaceChild($nuoviCrediti,$crediti);

                $percorso = "../XML/account.xml";
                $docAccount->save($percorso);

                $xmlOrdini = "";
                foreach(file("../XML/ordini.xml") as $nodo){
                $xmlOrdini .= trim($nodo);
                }

                $docOrdini = new DOMDocument();
                $docOrdini->loadXML($xmlOrdini);

                $rootOrdini = $docOrdini->documentElement;
                $listaOrdini = $rootOrdini->childNodes;
                $numOrdiniTotali = $rootOrdini->getElementsByTagName('ordine')->length;


                $nuovoOrdine = $docOrdini->createElement("ordine");
                $rootOrdini->appendChild($nuovoOrdine);

                $nuovoIDOrdine = $docOrdini->createElement("idOrd", $numOrdiniTotali + 1);
                $nuovoOrdine->appendChild($nuovoIDOrdine);

                $nuovoIDCli = $docOrdini->createElement("idCli", $_SESSION['id']);
                $nuovoOrdine->appendChild($nuovoIDCli);

                $nuovaDataOrdine = $docOrdini->createElement("dataAcquisto", date('Y-m-d'));
                $nuovoOrdine->appendChild($nuovaDataOrdine);

                $stringaXML = "";
                foreach ( file("../XML/prodotti.xml") as $nodo )   
                {
                    $stringaXML .= trim($nodo);
                }
            
                $docProdotti = new DOMDocument();
                $docProdotti->loadXML($stringaXML);

                $rootProdotti = $docProdotti->documentElement;
                $listaProdotti = $rootProdotti->childNodes;

                $totale = 0;
                for($i=0; $i < count($_SESSION['carrello']); $i++)
                {
                    if($_SESSION['carrello'][$i] != 0)
                    {
                        $nuovoGioco = $docOrdini->createElement("gioco");
                        $nuovoOrdine->appendChild($nuovoGioco);

                        for ($m=0; $m<$listaProdotti->length; $m++) 
                        {
                            $prodotto = $listaProdotti->item($m);
                            $idProd = $prodotto->firstChild;
                            $idProdValue = $idProd->textContent;

                            if($_SESSION['carrello'][$i] == $idProdValue )
                            {
                                $nomeProd = $idProd->nextSibling;
                                $nomeProdValue = $nomeProd->textContent;

                                $nuovoNomeGioco = $docOrdini->createElement("nomeGioco", $nomeProdValue);
                                $nuovoGioco->appendChild($nuovoNomeGioco);

                                $idCateg = $nomeProd->nextSibling;
                                $idCasa = $idCateg->nextSibling;

                                $lingua = $idCasa->nextSibling;  
                                $linguaValue = $lingua->textContent;

                                $annoProd = $lingua->nextSibling;
                                $annoProdValue = $annoProd->textContent;

                                $prezzo = $annoProd->nextSibling;
                                $prezzoVal = $prezzo->textContent;

                                $img = $prezzo->nextSibling;
                                $imgValue = $img->textContent;

                                $nuovaImgGioco = $docOrdini->createElement("immagineGioco", $imgValue);
                                $nuovoGioco->appendChild($nuovaImgGioco);

                                $nuovaLinguaGioco = $docOrdini->createElement("linguaGioco", $linguaValue);
                                $nuovoGioco->appendChild($nuovaLinguaGioco);

                                $nuovoAnnoGioco = $docOrdini->createElement("annoGioco", $annoProdValue);
                                $nuovoGioco->appendChild($nuovoAnnoGioco);

                                if($_SESSION['carrelloSconti'][$i] != 0)
                                {
                                    $stringaXML = "";
                                    foreach ( file("../XML/sconti.xml") as $nodo ) 
                                        {
                                            $stringaXML .= trim($nodo);
                                        }

                                    $docSconti = new DOMDocument();
                                    $docSconti->loadXML($stringaXML);

                                    $rootSconti = $docSconti->documentElement;
                                    $listaSconti = $rootSconti->childNodes;

                                    for ($n=0; $n<$listaSconti->length; $n++)
                                    {
                                        $sconto = $listaSconti->item($n);
                                                                        
                                        $idScontoS = $sconto->firstChild;
                                        $idScontoSVal = $idScontoS->textContent;

                                        if($idScontoSVal == $_SESSION['carrelloSconti'][$i])
                                        {
                                            $percent = $idScontoS->nextSibling;
                                            $percentValue = $percent->textContent; 

                                            $scontoEffettivo = ($prezzoVal / 100) * $percentValue; 
                                            $prezzoVal = $prezzoVal - $scontoEffettivo;
                                        }
                                    }
                                }

                                $nuovoPrezzoAcquistoGioco = $docOrdini->createElement("prezzoAcquisto", $prezzoVal);
                                $nuovoGioco->appendChild($nuovoPrezzoAcquistoGioco);
                            }
                        }
                    }
                }

                $nuovoTotale = $docOrdini->createElement("totale", $_SESSION['totaleCarrello']);
                $nuovoOrdine->appendChild($nuovoTotale);

                $nuovoTotaleCrediti = $docOrdini->createElement("totaleCrediti", $_SESSION['totaleCreditiCarrello']);
                $nuovoOrdine->appendChild($nuovoTotaleCrediti);
                

                $percorso = "../XML/ordini.xml";
                $docOrdini->save($percorso);
                    
                $_SESSION['carrello']=array();
                $_SESSION['carrelloSconti']=array();
                $_SESSION['carrelloTipoSconti']=array();
                $_SESSION['totaleCarrello'] = null;
                $_SESSION['totaleCreditiCarrello'] = null;

                
                $acquistoEffettuato = true;
                header("refresh: 0"); 
            }
            else
                $creditiMancanti = true;
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
  
                //controllo scadenza sconti 
                $xmlSconti = "";
                foreach(file("../XML/sconti.xml") as $nodo){
                $xmlSconti .= trim($nodo);
                }

                $docSconti = new DOMDocument();
                $docSconti->loadXML($xmlSconti);

                $rootSconti = $docSconti->documentElement;
                $listaSconti = $rootSconti->childNodes;

                for ($i=0; $i<$listaSconti->length; $i++) 
                {   
                    $sconto = $listaSconti->item($i);

                    $stato = $sconto->getAttribute('stato');

                    if($stato = "attivo")
                    {
                        $scadenza = $sconto->lastChild;
                        $scadenzaValue = $scadenza->textContent;

                        if(date('Y-m-d') > $scadenzaValue)
                        {
                            $sconto->setAttribute("stato", "scaduto");
                        }
                    }
                }

                $percorso = "../XML/sconti.xml";
                $docSconti->save($percorso);

                $stringaXML = "";
                foreach ( file("../XML/prodotti.xml") as $nodo )   
                {
                    $stringaXML .= trim($nodo);
                }
        
                $docProdotti = new DOMDocument();
                $docProdotti->loadXML($stringaXML);

                $rootProdotti = $docProdotti->documentElement;
                $listaProdotti = $rootProdotti->childNodes;
                echo "
                <form class=\"formCarrello\" method=\"post\" action=\"mostraCarrello.php\">
                <table id=\"carrello\">
                <tr>
                    <th></th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Casa Editrice</th>
                    <th>Lingua</th>
                    <th>Anno</th>
                    <th>Prezzo intero</th>
                    <th>Percentuale sconto</th>
                    <th>Scadenza sconto</th>
                    <th>Prezzo scontato</th>
                    <th>Costo in crediti</th>
                </tr>";
                $totale = 0;
                $totaleCrediti = 0;
                for($i=0; $i < count($_SESSION['carrello']); $i++)
                {
                    if($_SESSION['carrello'][$i] != 0)
                    {
                        for ($m=0; $m<$listaProdotti->length; $m++) 
                        {
                            $prodotto = $listaProdotti->item($m);
                            $idProd = $prodotto->firstChild;
                            $idProdValue = $idProd->textContent;

                            if($_SESSION['carrello'][$i] == $idProdValue )
                            {
                                $nomeProd = $idProd->nextSibling;
                                $nomeValue = $nomeProd->textContent;
                    
                                $idCateg = $nomeProd->nextSibling;             
                                $idCategVal = $idCateg->textContent;

                                $stringaXML = "";
                                foreach ( file("../XML/categorie.xml") as $nodo ) 
                                {
                                    $stringaXML .= trim($nodo);
                                }

                                $docCategorie = new DOMDocument();
                                $docCategorie->loadXML($stringaXML);

                                $rootCategorie = $docCategorie->documentElement;
                                $listaCategorie = $rootCategorie->childNodes;

                                for ($k=0; $k<$listaCategorie->length; $k++)
                                {
                                    $categoria = $listaCategorie->item($k);
                                    $idCategoria = $categoria->firstChild;
                                    $idCategoriaVal = $idCategoria->textContent;
                                    if($idCategoriaVal == $idCategVal)
                                    {
                                        $nomeCateg = $idCategoria->nextSibling;
                                        $nomeCategVal = $nomeCateg->textContent;   
                                            
                                        $descrizione = $nomeCateg->nextSibling;
                                        $descrizioneVal = $descrizione->textContent;

                                    }
                                }                

                                $idCasa = $idCateg->nextSibling;             
                                $idCasaVal = $idCasa->textContent;

                                $stringaXML = "";
                                foreach ( file("../XML/caseEditrici.xml") as $nodo ) 
                                {
                                    $stringaXML .= trim($nodo);
                                }

                                $docCase = new DOMDocument();
                                $docCase->loadXML($stringaXML);

                                $rootCase = $docCase->documentElement;
                                $listaCase = $rootCase->childNodes;

                                for ($j=0; $j<$listaCase->length; $j++)
                                {
                                    $casaEd = $listaCase->item($j);
                                    $idCasaEd = $casaEd->firstChild;
                                    $idCasaEdVal = $idCasaEd->textContent;
                                    if($idCasaEdVal == $idCasaVal)
                                    {
                                        $nome = $idCasaEd->nextSibling;
                                        $nomeCasaVal = $nome->textContent;   

                                    }
                                }                

                                $lingua = $idCasa->nextSibling;             
                                $linguaVal = $lingua->textContent;
                                    
                                $annoProd = $lingua->nextSibling;
                                $annoProdVal = $annoProd->textContent;

                                $prezzo = $annoProd->nextSibling;
                                $prezzoVal = $prezzo->textContent;

                                $img = $prezzo->nextSibling;
                                $imgVal = $img->textContent;

                                if($_SESSION['carrelloSconti'][$i] != 0)
                                {
                                    $stringaXML = "";
                                    foreach ( file("../XML/sconti.xml") as $nodo ) 
                                        {
                                            $stringaXML .= trim($nodo);
                                        }

                                    $docSconti = new DOMDocument();
                                    $docSconti->loadXML($stringaXML);

                                    $rootSconti = $docSconti->documentElement;
                                    $listaSconti = $rootSconti->childNodes;
                                        
                                    for ($n=0; $n<$listaSconti->length; $n++)
                                    {
                                        $sconto = $listaSconti->item($n);
                                                                    
                                        $idScontoS = $sconto->firstChild;
                                        $idScontoSVal = $idScontoS->textContent;

                                        if($idScontoSVal == $_SESSION['carrelloSconti'][$i])
                                        {
                                            if(($sconto->getAttribute('stato')) == "attivo")
                                            {
                                                $percent = $idScontoS->nextSibling;
                                                $percentValue = $percent->textContent;   
                                                            
                                                $repNec = $percent->nextSibling;
                                                
                                                $credNec = $repNec->nextSibling;

                                                if($_SESSION['carrelloTipoSconti'][$i] == 1)
                                                {
                                                    $costoCrediti = $credNec->textContent;
                                                    $totaleCrediti = $totaleCrediti + $costoCrediti;
                                                }
                                                else
                                                    $costoCrediti = 0;   
                                                        
                                                $scadenza = $credNec->nextSibling;
                                                $scadenzaValue = $scadenza->textContent;
                                                
                                                $scontoEffettivo = ($prezzoVal / 100) * $percentValue; 
                                                $prezzoScontato = $prezzoVal - $scontoEffettivo;
                                                $totale = $totale + $prezzoScontato;
                                            }
                                            else{
                                                $totale = $totale + $prezzoVal;
                                            }

                                        }   
                                    }
                                }
                                else{
                                    $totale = $totale + $prezzoVal;
                                    $costoCrediti=0;
                                }
                                    
                                echo "
                                <tr id=\"trcarrello\">
                                    <td class=\"tdcarrello\"><img src=$imgVal height=\"100\" width=\"100\"></td>
                                    <td>$nomeValue</td>
                                    <td>$nomeCategVal</td>
                                    <td>$nomeCasaVal </td> 
                                    <td>$linguaVal</td>
                                    <td>$annoProdVal</td>
                                    <td>$prezzoVal</td>";
                                
                                if($_SESSION['carrelloSconti'][$i] != 0)
                                {
                                    if(($sconto->getAttribute('stato')) == "attivo")
                                    {
                                        echo "
                                        <td>$percentValue</td>
                                        <td>$scadenzaValue</td>
                                        <td>$prezzoScontato</td> 
                                        ";
                                    }
                                    else
                                    {
                                        echo "
                                        <td>sconto scaduto</td>
                                        <td>sconto scaduto</td>
                                        <td>sconto scaduto</td> 
                                        ";
                                    }
                                }
                                else
                                {
                                    echo "
                                    <td>N/C</td>
                                    <td>N/C</td>
                                    <td>N/C</td> 
                                    "; 
                                }

                                echo "
                                <td>$costoCrediti</td>
                                <td><button type=\"submit\" class=\"btn\" name=\"rimuoviDaCarrello\" value=\"$idProdValue\">Rimuovi</button></td>
                                </tr>"; 

                            }
                        } 
                    }

                }
                $_SESSION['totaleCarrello'] = $totale;
                $_SESSION['totaleCreditiCarrello'] = $totaleCrediti;

                echo "
                <tr>
                    <td id=\"totale\" colspan=\"6\">Totale = &euro;$totale</td>
                    <td id=\"totale\" colspan=\"6\">Costo crediti totale = &euro;$totaleCrediti</td>
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
                echo "<script type=\"text/javascript\">alert(\"Acquisto effettuato! hai ricevuto il 10% dei soldi spesi in crediti\");</script>";                
            }
            
            if($soldiMancanti == true){
                $soldiMancanti = false;
                echo "<script type=\"text/javascript\">alert(\"Non hai abbastanza soldi per effettuare questo acquisto. Ricarica il portafoglio prima di acquistare!\");</script>";
            }

            if($creditiMancanti == true){
                $creditiMancanti = false;
                echo "<script type=\"text/javascript\">alert(\"Non hai abbastanza crediti per effettuare questo acquisto. Rimuovi dei giochi scontati che costano crediti\");</script>";
            }
            
                
        ?>
    </div>
</body>
</html>
