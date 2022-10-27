<!--
Questo script è quello che mostra il catalogo dei giochi e permette di interagire con essi (in base al tipo di utente).
Se l'utente non è loggato, può vedere i prodotti ma non può interagirci.
Se un cliente arriva al catalogo, può vedere i prodotti ed aggiungerli al suo attuale carrello; può aggiungerli eventualmente con uno sconto a
sua scelta (rispettando i requisiti di crediti o reputazione).
Se un gestore arriva al catalogo, può vedere i prodotti ed eliminarli dal catalogo.
Inoltre a prescindere dal tipo di utente, la visualizzazione dei prodotti può essere filtrata:
In particolare si filtra il catalogo mediante le select presenti nella cornice e usati come filtri, o tramite la casella per la ricerca dei giochi. Il nome 
dei giochi è valutato tramite la funzione strcasecomp, che fa si che non sia importante il modo in cui venga inserito
il nome del gioco all'interno della casella di testo, poichè viene gestita in modo case insensitive. 
Gli eventuali avvisi vengono "calcolati" nella gestione della form, e poi mostrati dopo di essa, così da non bloccare il caricamento di quest' ultima.
 -->

<?php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ini_set('display_errors', 1);
    error_reporting(E_ALL &~E_NOTICE);
    session_start();
    require("cornice.php");
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Visualizza Catalogo</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>

<?php
    if (isset($_POST['cancella']))
    {   
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
            foreach($_POST['prodottiSelez'] as $c=>$sel)
            {   
                $arraySelezionati[$i]=$listaProdotti->item($sel-1);
                $i++;
                
            }
            

            for($i=0; $i < count($arraySelezionati); $i++)
                $rootProdotti->removeChild($arraySelezionati[$i]);
    
            $listaProdotti = $rootProdotti->childNodes;
            for ($k=0; $k<$listaProdotti->length; $k++) 
            {
                $prodotto = $listaProdotti->item($k);
                $idProdotto = $prodotto->firstChild;
                $nuovoID = $docProdotti->createElement("idProd", $k+1);
                $prodotto->replaceChild($nuovoID,$idProdotto);

            }
            
        
        $percorso = "../XML/prodotti.xml";
        $docProdotti->save($percorso);

        $cancellato = true;
        
    }

    
    if (isset($_POST['aggiungiACarrello']))
    {
        
        if(isset($_POST['sceltaSconto']))
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
                if(($sconto->getAttribute('stato')) == "attivo")
                {
                    $idScontoS = $sconto->firstChild;
                    $idScontoSVal = $idScontoS->textContent;

                    if($idScontoSVal == $_POST['sceltaSconto'])
                    {
                        $percent = $idScontoS->nextSibling;   
                                    
                        $repNec = $percent->nextSibling;
                        $repNecVal = $repNec->textContent;

                        $credNec = $repNec->nextSibling;
                        $credNecVal = $credNec->textContent;

                        if($reputazioneValue >= $repNecVal)
                        {
                            $_SESSION['carrelloTipoSconti'][] = 0;
                            $_SESSION['carrelloSconti'][] = $_POST['sceltaSconto'];
                            $_SESSION['carrello'][] = $_POST['aggiungiACarrello'];
                            $conScontoRep = true;                            
                            
                        }
                        else
                        {
                            if($creditiValue >= $credNecVal)
                            {
                                $_SESSION['carrelloTipoSconti'][] = 1;
                                $_SESSION['carrelloSconti'][] = $_POST['sceltaSconto'];
                                $_SESSION['carrello'][] = $_POST['aggiungiACarrello'];
                                $conScontoCred = true;
                            }
                            else
                            {
                                $scontoInsuff = true;
                                
                            }
                        }
                        
                    }
                }
            }
           
            
            
            

        }
        else{

            $_SESSION['carrello'][] = $_POST['aggiungiACarrello'];
            $_SESSION['carrelloSconti'][] = 0;
            $_SESSION['carrelloTipoSconti'][] = 0;
            
            $aggiunto = true;
            
            
        }
    }
?>

<body>
    <div class="body">
        <div class="header">
  	        <h2>CATALOGO</h2>
        </div>

        <?php


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

            if ($rootProdotti->getElementsByTagName("gioco")->length == 0){
                echo "Non ci sono prodotti nel catalogo";
            } 
            else{
                echo "<table>
                      <tr>
                      <th></th>
                      <th>NOME</th>
                      <th>CATEGORIA</th>
                      <th>CASA EDITRICE</th>
                      <th>LINGUA</th>
                      <th>ANNO PRODUZIONE</th>
                      <th>PREZZO</th>
                      <th>PERCENTUALE SCONTO</th>
                      <th>REPUTAZIONE NECESSARIA SCONTO</th>
                      <th>CREDITI NECESSARI SCONTO</th>
                      <th>SCADENZA SCONTO</th>
                      </tr>";
                if (($_SESSION['type'] == "gestore") || ($_SESSION['type'] == "cliente")){
                    echo "<form method=\"post\" action=\"visualizzaCatalogo.php\">";
                }
           

                for ($i=0; $i<$listaProdotti->length; $i++) 
                {
                    $prodotto = $listaProdotti->item($i);
                    $idProd = $prodotto->firstChild;
                    $idProdValue = $idProd->textContent;

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

                    $sconti = $prodotto->getElementsByTagName('idSconto');
                    $numScontiAttivi=0;
                    if($sconti->length > 0)
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

                        
                        for($k=0; $k<$sconti->length; $k++)
                        {
                            $ElemSconto = $sconti->item($k);
                            $idScontoVal = $ElemSconto->textContent;
                            
                            for ($n=0; $n<$listaSconti->length; $n++)
                            {
                                $sconto = $listaSconti->item($n);
                                if(($sconto->getAttribute('stato')) == "attivo")
                                {
                                    $idScontoS = $sconto->firstChild;
                                    $idScontoSVal = $idScontoS->textContent;

                                    if($idScontoSVal == $idScontoVal)
                                    {
                                        $arrayIDScontoVal[$numScontiAttivi] = $idScontoSVal;

                                        $percent = $idScontoS->nextSibling;
                                        $arrayPercentVal[$numScontiAttivi] = $percent->textContent;   
                                            
                                        $repNec = $percent->nextSibling;
                                        $arrayRepNecVal[$numScontiAttivi] = $repNec->textContent;

                                        $credNec = $repNec->nextSibling;
                                        $arrayCredNecVal[$numScontiAttivi] = $credNec->textContent;

                                        $scadenza = $credNec->nextSibling;
                                        $arrayScadenzaVal[$numScontiAttivi] = $scadenza->textContent;

                                        $numScontiAttivi++;

                                    }   
                                }    
                            }
                        }
                    }                

                    if(!isset($_SESSION['success']) || ($_SESSION['type'] == "amministratore"))
                    {
                        if((strcasecmp($_POST['cerca'], $nomeValue) == 0 || $_POST['cerca'] == "") && ($_POST['categoria'] == $nomeCategVal || $_POST['categoria'] == "") && ($_POST['casaEditrice'] == $nomeCasaVal || $_POST['casaEditrice'] == "") && ($_POST['lingua'] == $linguaVal || $_POST['lingua'] == "")){
                        echo " 
                        <tr>
                        <td><img src=\"$imgVal\" height=\"100\" width=\"100\"> </td>
                        <td class=\"chiara\">$nomeValue</td>
                        <td class=\"scura\">$nomeCategVal</td>
                        <td class=\"chiara\">$nomeCasaVal </td> 
                        <td class=\"scura\">$linguaVal</td>
                        <td class=\"chiara\">$annoProdVal</td>
                        <td class=\"scura\">&euro;$prezzoVal</td>
                        <td class=\"chiara\">";
                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayPercentVal[$k]%<br /> <br />";
                        echo "</td><td class=\"scura\">";

                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayRepNecVal[$k]<br /> <br />";
                        echo "</td><td class=\"chiara\">";

                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayCredNecVal[$k]<br /> <br />";
                        echo "</td><td class=\"scura\">";

                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayScadenzaVal[$k]<br /> <br />";
                        }
                    }
        
        
                    if($_SESSION['type'] == "gestore"){

                        if((strcasecmp($_POST['cerca'], $nomeValue) == 0 || $_POST['cerca'] == "") && ($_POST['categoria'] == $nomeCategVal || $_POST['categoria'] == "") && ($_POST['casaEditrice'] == $nomeCasaVal || $_POST['casaEditrice'] == "") && ($_POST['lingua'] == $linguaVal || $_POST['lingua'] == "")){
                        echo " 
                        <tr>
                        <td><img src=\"$imgVal\" height=\"100\" width=\"100\"> </td>
                        <td class=\"chiara\">$nomeValue</td>
                        <td class=\"scura\">$nomeCategVal</td>
                        <td class=\"chiara\">$nomeCasaVal </td>
                        <td class=\"scura\">$linguaVal</td>
                        <td class=\"chiara\">$annoProdVal</td>
                        <td class=\"scura\">&euro;$prezzoVal</td>
                        <td class=\"chiara\">";
                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayPercentVal[$k]%<br /> <br />";
                        echo "</td><td class=\"scura\">";

                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayRepNecVal[$k]<br /> <br />";
                        echo "</td><td class=\"chiara\">";

                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayCredNecVal[$k]<br /> <br />";
                        echo "</td><td class=\"scura\">";

                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayScadenzaVal[$k]<br /> <br />";
                        echo "</td>
                        
                        <td><input type=\"checkbox\" name=\"prodottiSelez[]\" value=\"$idProdValue\"></td>
                        </tr>";
                    
                    }
                }

                    if($_SESSION['type'] == "cliente"){

                        if((strcasecmp($_POST['cerca'], $nomeValue) == 0 || $_POST['cerca'] == "") && ($_POST['categoria'] == $nomeCategVal || $_POST['categoria'] == "") && ($_POST['casaEditrice'] == $nomeCasaVal || $_POST['casaEditrice'] == "") && ($_POST['lingua'] == $linguaVal || $_POST['lingua'] == "")){
                        echo " 
                        <tr>
                        <td><img src=\"$imgVal\" height=\"100\" width=\"100\"> </td>
                        <td class=\"chiara\">$nomeValue</td>
                        <td class=\"scura\">$nomeCategVal</td>
                        <td class=\"chiara\">$nomeCasaVal </td> 
                        <td class=\"scura\">$linguaVal</td>
                        <td class=\"chiara\">$annoProdVal</td>
                        <td class=\"scura\">&euro;$prezzoVal</td>
                        <td class=\"chiara\">";
                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayPercentVal[$k]%<br /> <br />";
                        echo "</td><td class=\"scura\">";

                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayRepNecVal[$k]<br /> <br />";
                        echo "</td><td class=\"chiara\">";

                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayCredNecVal[$k]<br /> <br />";
                        echo "</td><td class=\"scura\">";

                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "$arrayScadenzaVal[$k]<br /> <br />";
                        echo "</td><td>";

                        for($k=0; $k < $numScontiAttivi; $k++)
                            echo "<input type=\"radio\" name=\"sceltaSconto\" value=\"$arrayIDScontoVal[$k]\"><br /> <br />";
                        echo "</td>";

                        echo "
                        <td><button type=\"submit\" class=\"btn\" name=\"aggiungiACarrello\" value=\"$idProdValue\">Aggiungi al carrello</button></td>
                        </tr>";

                    }
                }
                
            
                }    
                echo "</table>";

                if(($_SESSION['type'] == "cliente"))   
                    echo "</form>";
                
                if(($_SESSION['type'] == "gestore")){
                    echo "<button type=\"submit\" class=\"btn\" name=\"cancella\">Cancella selezionati</button>
                        </form>";
                }

            }

            if($conScontoRep == true){
                $conScontoRep = false;
                echo "<script type=\"text/javascript\">alert(\"Prodotto aggiunto al carrello con lo sconto da te selezionato (hai piu' della reputazione necessaria quindi NON ti togliera' crediti)\");</script>";
            }

            if($conScontoCred == true){
                $conScontoCred = false;
                echo "<script type=\"text/javascript\">alert(\"Prodotto aggiunto al carrello con lo sconto da te selezionato (hai meno della reputazione necessaria quindi ti togliera' crediti)\");</script>";
            }

            if($aggiunto == true){
                $aggiunto = false;
                echo "<script type=\"text/javascript\">alert(\"Prodotto aggiunto al carrello\");</script>";
            }

            if($scontoInsuff == true){
                $scontoInsuff = false;
                echo "<script type=\"text/javascript\">alert(\"Non hai abbastanza crediti o reputazione per questo sconto\");</script>";
            }
            
            if($cancellato == true){
                $cancellato = false;
                echo "<script type=\"text/javascript\">alert(\"Prodotto cancellato dal catalogo\"); location.replace(\"visualizzaCatalogo.php\");</script>";
            }
      
        ?>
    
    
    </div>
</body>
</html>
