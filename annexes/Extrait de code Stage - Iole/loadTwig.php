<?php

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . "/PDF/L3_clsPrint.php";
require_once './arrayFinal.php';
/**
 * This file allow to use the Twig template engine.
 * 
 * To be able to use Twig as a standalone (without composer)
 * - Download Twig from https://github.com/twigphp/Twig/archive/2.x.zip
 * - Extract it to the relative path you want (in my case /Twig)
 * - Rename Twig/src to Twig/Twig
 * - include the spl_autoload_register() function below
 * - Do not forget to use Fully Qualified Class Name (FQCN) to reference Twig's classes
 */

spl_autoload_register(function ($classname) {
    $dirs = [
        './Twig/' // ./path/to/dir_where_src_renamed_to_Twig_is_in
    ];

    foreach ($dirs as $dir) {
        $filename = $dir . str_replace('\\', '/', $classname) . '.php';
        if (file_exists($filename)) {
            require_once $filename;
            break;
        }
    }
});

$l3 = new L3_clsPrint();

/**
 * This creates a template environment with a default configuration and a loader
 * that looks up templates in the /PDF/templates directory. Different loaders are 
 * available and you can also write your own if you want to load templates from 
 * a database or other resources.
 */
$loader = new FilesystemLoader('./PDF/templates');
$twig = new Environment($loader, [
    'debug' => true
]);

// Adds Twig's ability to debug template(s) e.g use dump instead of var_dump inside templates
$twig->addExtension(new DebugExtension());

// QrCode in invoice's header
// $image = new barcode_generator();
// $image->output_image('png', 'qr', 'www.google.fr', '');




$priceTotalAllTab = 0;
$previousReduction = null;
$priceTotal = 0;
$reductionKey = null;
$quantityKey = null;
$priceKey = null;
$montantKey = null;
$totalHT = 0;
$idArray = [];
$newId = 0;
$creationInProgress = false;

for ($i = 0; $i < count($invoice); $i++) {
    $returnElement = " <br/>";
    $newId++;
    $idArray[$i] = $newId;

    $returnElement .= '<span id="head_current"' . $newId . '></span><br/><table class="tableau"><thead><tr>';
    $returnElement .= '<span class="bonDeCommande bold"> Bon de commande : ' . $invoice["BonDeCommande"][$i] . '</span>';
    foreach ($invoice["ColumnName"] as $keyHead => $valHead) {
        if ($valHead == "Remise") $reductionKey = $keyHead;
        if ($valHead == "Prix Unitaire") $priceKey = $keyHead;
        if ($valHead == "Montant") $montantKey = $keyHead;
        if ($valHead == "Quantité") $quantityKey = $keyHead;
        $returnElement .= " <th> " . $valHead . " </th>";
    }
    $returnElement .= '</tr></thead><tbody>';
    for ($a = 0; $a < count($dataPourTotaux[0]["Value"]); $a++) {
        $returnElement .= '<tr>';
        $creationInProgress = true;
        foreach ($dataPourTotaux[0]["Value"][$a] as $keyBody => $valBody) {
            if ($keyBody == $reductionKey) {
                if ($valBody == null) {
                    $previousReduction = null;
                    $remise = "";
                    $returnElement .= ' <td class="center"> ' . $remise . ' </td>';
                } else {
                    $previousReduction = $valBody;
                    $remise = strval($valBody) . " %";
                    $returnElement .= ' <td class="center"> ' . $remise . ' </td>';
                }
            } else if ($keyBody == $priceKey) {
                $price = $valBody;
                $priceFormat = number_format(($price), 2, ',', ' ') . $l3->devise;
                $returnElement .= ' <td class="center"> ' . $priceFormat  . ' </td>';
            } else if ($keyBody == $quantityKey) {
                $quantity = $valBody;
                $returnElement .= ' <td class="center"> ' . $quantity  . ' </td>';
            } else if ($keyBody == $montantKey) {
                $thePrice = $price * $quantity;
                if ($previousReduction == null) {
                    $priceTotal += $thePrice;
                    $thePriceFormat = number_format(($thePrice), 2, ',', ' ') . $l3->devise;
                    $returnElement .= ' <td class="center"> ' . $thePriceFormat  . ' </td>';
                    $thePrice = null;
                } else {
                    $reduction = $thePrice * $previousReduction / 100;
                    $thePrice -= $reduction;
                    $priceTotal += $thePrice;
                    $reduction = null;
                    $thePriceFormat = number_format(($thePrice), 2, ',', ' ') . $l3->devise;
                    $returnElement .= ' <td class="center"> ' . $thePriceFormat  . ' </td>';
                    $thePrice = null;
                }
                $previousReduction = null;
            } else {
                $returnElement .= ' <td class="center"> ' . $valBody . ' </td>';
            }
        }
        $creationInProgress = false;
        $returnElement .= '</tr>';
    }
    $priceTotalFormat = number_format(($priceTotal), 2, ',', ' ');
    $returnElement .= '</tbody></table>';
    $returnElement .= '<div class="priceUnderTab"><span class="semibold bold">Sous-total</span> <span id=sousTotaux>' . $priceTotalFormat . '€ </span> </div>';

    $totalHT += $priceTotal;
    array_push($dataPourTotaux, $totalHT);

    $priceTotal = null;
    $priceTotalAllTab += $priceTotal;
}



// Render the template /PDF/templates/test.html with some data , call the render() method:
echo $twig->render('test.html.twig', [
    'invoice' => $invoice, // array line 49
    'footer' => $footer,
    'adresse' => $adresseIole,
    'contact1' => $contactInterlocuteur1,
    'contact2' => $contactInterlocuteur2,
    'facture' => $facture,
    // 'qrCode' => $image->getName(),
    'client' => $client,
    'total' => $l3->subtotals($dataPourTotaux)
]);


/**
 * See Twig docs : https://twig.symfony.com/doc/2.x/
 * Twig basics : 
 * Say that you have an array of names :
 * 
 * $names = [
 *      'Paul',
 *      'John',
 *      'Jack'
 * ];
 * 
 * If you want to display the first name inside a Twig view, you will do as follow :
 * First, you pass the array as key value to the Twig's render function, so it's accessible to the view
 * 
 * echo $twig->render('index.html', ['names' => $names]);
 * 
 * Inside your view, to display the name 'John' :
 *      <p>{{ names[1] }}</p>
 * Will be interpreted as : <p>John</p>
 * 
 * You can loop through each names :
 *      <ul>
 *      {% for name in names %}
 *          <li>
 *              <p>{{ name }}</p>
 *          </li>     
 *      {% endfor %}
 *      </ul>
 * Will be interpreted as  : <ul>
 *                              <li>
 *                                  <p>Paul</p>
 *                              </li>
 *                              <li>
 *                                  <p>John</p>
 *                              </li>
 *                              <li>
 *                                  <p>Jack</p>
 *                              </li>
 *                           </ul> 
 * 
 * You can set variables inside your views :
 *      {% set items = ['apple', 'banana', 'orange'] %}
 * 
 * You can write conditions
 *      <ul>
 *      {% for name in names %}
 *          <li>
 *              <p style="{% if name == 'John' %}color: red {% endif %}">{{ name }}</p>
 *          </li>     
 *      {% endfor %}
 *      </ul>
 * 
 * Will be interpreted as  : <ul>
 *                              <li>
 *                                  <p>Paul</p>
 *                              </li>
 *                              <li>
 *                                  <p style="color: red">John</p>
 *                              </li>
 *                              <li>
 *                                  <p>Jack</p>
 *                              </li>
 *                           </ul> 
 * 
 */