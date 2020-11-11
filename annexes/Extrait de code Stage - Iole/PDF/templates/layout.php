<?php

include_once __DIR__ . "/../../../../tools_back/L1/L1_clsAutoLoader.php";
// include __DIR__ . "/../../../../exttools_back/barcodeLib/barcode.php";
L1_clsAutoLoader::register();


$file = $GLOBALS['DIRTESTWS'] . '/PDF/templates/invoiceTemplate.html';
$renderedFilename = $GLOBALS['DIRTESTWS'] . '/PDF/templates/invoiceFinal-' . today() . '.html';

$L3 = new L3_clsPrint();

// $image = new barcode_generator();
// $image->output_image('png', 'qr', 'www.google.fr', '');
// $qrCode = $L3->setBarCodeName($image->getName());

/* TESTS */
// Data from Database (WIP)
$adresseIole = [
    'adresse1' => 'Bâtiment Piren',
    'adresse2' => '12, rue Henri Becquerel',
    'codePostal' => '56000',
    'ville' => 'Vannes',
    'tel' => '+33 6 23 24 55 66'
];
$contactInterlocuteur1 = [
    'nom1' => 'Jean Vassal',
    'tel1' => '+33 6 24 65 89 01',
    'mail1' => 'jvassal@iole.fr'
];
$contactInterlocuteur2 = [
    'nom2' => 'Valentine Pontel',
    'tel2' => '+33 6 24 65 89 01',
    'mail2' => 'v.pontel@iole.fr'
];
$invoiceDetails = [
    'num' => 'FAC2020-01-025',
    'dateEmission' => '06/01/20',
    'dateEcheance' => '06/01/20',
    'referenceClient' => 'GLA97432'
];
$client = [
    'Nom' => 'LAUREEN',
    'Prénom' => 'NAUD',
    'Adresse1' => 'BÂTIMENT A, LOGEMENT 2',
    'Adresse2' => 'RESIDENCE LES COSTES',
    'CodePostal' => '34360',
    'Ville' => 'SAINT THIBERY',
];
$invoice = [
    'docType' => 'facture',
    [
        'BonDeCommande' => [
            0 => "459688M",
            1 => "456788F",
            2 => "ZJ23MQ"
        ],
        "ColumnName" => [
            0 => "Référence",
            1 => "Desription",
            2 => "Quantité",
            3 => "Prix Unitaire",
            4 => "Code TVA",
            5 => "Remise",
            6 => "Montant"
        ],
        "Value" => [
            [
                0 => "ZJG87",
                1 => "Item 1",
                2 => 1,
                3 => 90,
                4 => 1,
                5 => 10,
                6 => ""
            ],
            [
                0 => "ZJG88",
                1 => "Item 2",
                2 => 5,
                3 => 40,
                4 => 1,
                5 => 70,
                6 => ""
            ],
            [
                0 => "ZJ788",
                1 => "Item 3",
                2 => 2,
                3 => 800,
                4 => 1,
                5 => 80,
                6 => ""
            ]
        ]
    ],
    [
        "ColumnName" => [
            0 => "Référence",
            1 => "Desription",
            2 => "Quantité",
            3 => "Prix Unitaire",
            4 => "Code TVA",
            5 => "Remise",
            6 => "Montant"
        ],
        "Value" => [
            [
                0 => "ZJG89",
                1 => "Item 4",
                2 => 5,
                3 => 90,
                4 => 1,
                5 => 40,
                6 => ""
            ],
            [
                0 => "ZJG90",
                1 => "Item 5",
                2 => 18,
                3 => 20,
                4 => 1,
                5 => 20,
                6 => ""
            ],
            [
                0 => "ZJ791",
                1 => "Item 6",
                2 => 1,
                3 => 955,
                4 => 1,
                5 => 30,
                6 => ""
            ]
        ]
    ]
];
// Placeholders from HTML file (invoiceTemplate.html)
$adresse = [
    '{-adresse1-}',
    '{-adresse2-}',
    '{-codePostal-}',
    '{-ville-}',
    '{-tel-}'
];
$facture = [
    '{-num-}',
    '{-dateEmission-}',
    '{-dateEcheance-}',
    '{-referenceClient-}'
];
$contact1 = [
    '{-nom1-}',
    '{-tel1-}',
    '{-mail1-}'
];
$contact2 = [
    '{-nom2-}',
    '{-tel2-}',
    '{-mail2-}'
];
$clientPlaceholder = [
    '{-clientNom-}', '{-clientPrenom-}', '{-clientAdresse1-}', '{-clientAdresse2-}', '{-clientCodePostal-}', '{-clientVille-}'
];
$invoicePlaceholder = [];

$search = array_merge($adresse, $facture, $contact1, $contact2, $client, $invoice);
$replace = array_merge($adresseIole, $invoiceDetails, $contactInterlocuteur1, $contactInterlocuteur2, $clientPlaceholder, $invoicePlaceholder);

/**
 * Return the current date eg. 24-07-2020
 *
 * @return string
 */
function today()
{
    $timeZone = "Europe/Paris";
    $newDate = new DateTime('NOW', new DateTimeZone($timeZone));

    return $newDate->format('d-m-Y');
}

/**
 * Retrieves the content of a given file
 *
 * @param string $path
 * @return string
 */
function getHtmlWithPlaceholder(string $path): string
{
    return file_get_contents($path);
}

/**
 * Replace an array of placeholder values (from html file) and replace them to actual php data
 *
 * @param array $search
 * @param array $replace
 * @param string $inHayStack
 * @return string
 */
function replacePlaceholder(array $search, array $replace, string $inHayStack): string
{
    return str_replace($search, $replace, $inHayStack);
}

$htmlOutput = replacePlaceholder($search, $replace, getHtmlWithPlaceholder($file));

/**
 * Takes a path to a file and write to it the content of a string 
 * 
 * @param string $path
 * @param string $result
 * @return void
 */
function fromPhpToHtml(string $path, string $result): void
{
    $fp = fopen($path, 'w');
    fwrite($fp, $result);
    fclose($fp);
}

fromPhpToHtml($renderedFilename, $htmlOutput);
