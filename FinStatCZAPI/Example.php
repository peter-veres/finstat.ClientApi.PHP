<?php
require_once('FinstatApi/FinstatApi.php');
function echoBase($response)
{
    echo "<pre>";
    echo '<b>IČO: </b>'.                    $response->Ico.'<br />';
    echo '<b>Názov: </b>'.                  $response->Name.'<br />';
    echo '<b>Ulica: </b>'.                  $response->Street.'<br />';
    echo '<b>Číslo ulice: </b>'.            $response->StreetNumber.'<br />';
    echo '<b>PSČ: </b>'.                    $response->ZipCode.'<br />';
    echo '<b>Mesto: </b>'.                  $response->City.'<br />';
    if($response instanceof DetailResult)
    {
        echo '<b>Okres: </b>'.                  $response->District.'<br />';
        echo '<b>Kraj: </b>'.                   $response->Region.'<br />';
    }
    echo '<b>Odvetvie: </b>'.               $response->Activity.'<br />';
    if($response instanceof DetailResult)
    {
        echo '<b>CZ Nace Kod: </b>'.            $response->CzNaceCode.'<br />';
        echo '<b>CZ Nace Text: </b>'.           $response->CzNaceText.'<br />';
        echo '<b>CZ Nace Divizia: </b>'.        $response->CzNaceDivision.'<br />';
        echo '<b>CZ Nace Skupina: </b>'.        $response->CzNaceGroup.'<br />';
        echo '<b>Právna forma: </b>'.           $response->LegalForm.'<br />';
        echo '<b>Druh vlastníctva: </b>'.       $response->OwnershipType.'<br />';
        echo '<b>Počet zamestnancov: </b>'.     $response->EmployeeCount.'<br />';
    }
    echo '<b>Založená: </b>'.               (($response->Created) ? $response->Created->format('d.m.Y') : '').'<br />';
    echo '<b>Zrušená: </b>'.                (($response->Cancelled) ? $response->Cancelled->format('d.m.Y') : '') .'<br />';

    echo '<b>Url: </b>'.            $response->Url.'<br />';
    echo '<b>Príznak, či sa daná firma nachádza insolvenčnom registry: </b>';
    if($response->Warning) echo 'Áno (<a href="'.$response->WarningUrl.'">viac info</a>)<br />'; else echo 'Nie<br />';

    echo "</pre>";
}

function echoException($e)
{
    echo "<h1 style=\"color: red\">Exception</h1>";
    echo"<table>";
    echo"<tr><th>Code:</th><td>{$e->getCode()}</td></tr>";
    echo"<tr><th>Message:</th><td> {$e->getMessage()}</td></tr>";
    echo"<tr><th>Body:</th><td>{$e->getData()}</td></tr>";
    echo"</table>";
    die();
}

function echoAutoComplete($response)
{
    echo "<pre>";
    echo '<b>Výsledky: </b><br />';
    if (!empty($response->Results)) {
        echo "<table>";
        echo
            "<tr><th>ICO" .
            "</td><th>Nazov" .
            "</td><th>Mesto" .
            "</td><th>Zrusena" .
            "</th></tr>"
        ;
        foreach ($response->Results as $company) {
            echo
                "<tr><td>" . $company->Ico .
                "</td><td>" . $company->Name .
                "</td><td>" . $company->City .
                "</td><td>" . (($company->Cancelled) ? "true" : 'false') .
                "</td></tr>"
            ;
        }
        echo "</table>";
    }
    echo '<br /><b>Návrhy: </b>';
    if (!empty($response->Suggestions)) {
        echo implode(', ', $response->Suggestions);
    }
    echo '<br />';
    echo '<hr />';
    echo "</pre>";
}

// zakladne prihlasovacie udaje a nastavenia klienta
$apiUrl = 'http://cz.finstat.sk/api/';    // URL adresa Finstat API
$apiKey = 'PLEASE_FILL_IN_YOUR_API_KEY';// PLEASE_FILL_IN_YOUR_API_KEY je NEFUNKCNY API kluc. Pre plnu funkcnost API,
                                        // prosim poziadajte o svoj jedinecny kluc na info@finstat.sk.
$privateKey = 'PLEASE_FILL_IN_YOUR_PRIVATE_KEY';// PLEASE_FILL_IN_YOUR_PRIVATE_KEY je NEFUNKCNY API kluc. Pre plnu funkcnost API,
                                        // prosim poziadajte o svoj privatny kluc na info@finstat.sk.
$stationId = 'Api test';                // Identifikátor stanice, ktorá dopyt vygenerovala.
                                        // Môže byť ľubovolný reťazec.
$stationName = 'Api test';                // Názov alebo opis stanice, ktorá dopyt vygenerovala.
                                        // Môže byť ľubovolný reťazec.
$timeout = 10;                            // Dĺžka čakania na odozvu zo servera v sekundách.

// inicializacia klienta
$api = new FinstatApi($apiUrl, $apiKey, $privateKey, $stationId, $stationName, $timeout);

// priklad dopytu na detail firmy, ktora ma ICO 48207349
$ico = (isset($_GET['ico']) && !empty($_GET['ico'])) ? $_GET['ico'] : '48207349';
?>
<h1>Detail test:</h1>
<?php
try
{
    // funkcia $api->RequestDetail(string) vracia naplneny objekt typu BaseResultCZ s udajmi o dopytovanej firme
    if (!empty($ico)) {
        $response = $api->Request($ico);
    }
}
catch (Exception $e)
{
    echoException($e);
}

// priklad vypisu ziskanych udajov z Finstatu
header('Content-Type: text/html; charset=utf-8');
?>
<h1>Detail test:</h1>
<?php
echoBase($response);
echo '<hr />';
?>

<h1>AutoComplete test "volkswagen":</h1>
<?php
try
{
    $response2 = $api->RequestAutoComplete('volkswagen');
}
catch (Exception $e)
{
    // popis a kod chyby, ktora nastala
    echoException($e);
}
echoAutoComplete($response2);