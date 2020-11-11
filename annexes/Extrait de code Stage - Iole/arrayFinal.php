<?php

// Arrays of data; will change with database data
$invoice = [
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
];
// Header
$adresseIole = [
    'adresse1' => 'Bâtiment Piren',
    'adresse2' => '12, rue Henri Becquerel',
    'codePostal' => '56000',
    'ville' => 'Vannes',
    'tel' => '+33 6 23 24 55 66'
];
$contactInterlocuteur1 = [
    'nom' => 'Jean Vassal',
    'tel' => '+33 6 24 65 89 01',
    'mail' => 'jvassal@iole.fr'
];
$contactInterlocuteur2 = [
    'nom' => 'Valentine Pontel',
    'tel' => '+33 6 24 65 89 01',
    'mail' => 'v.pontel@iole.fr'
];
$facture = [
    'num' => 'FAC2020-01-025',
    'dateEmission' => '06/01/20',
    'dateEcheance' => '06/01/20',
    'referenceClient' => 'GLA97432'
];
// Footer
$footer = [
    'nom' => 'Iole Solutions',
    'forme' => 'SASU Société par actions simplifiée à associé unique',
    'capital' => '100 000.00€',
    'siege' => '12 rue Henri Becquerel 56000 Vannes',
    'rcs' => 'Vannes B842 110 835',
    'siret' => '84211083500027',
    'tvaIntra' => 'FR 3903050',
    'ape' => '6201Z',
];
$client = [
    'Nom' => 'LAUREEN',
    'Prénom' => 'NAUD',
    'Adresse1' => 'BÂTIMENT A, LOGEMENT 2',
    'Adresse2' => 'RESIDENCE LES COSTES',
    'CodePostal' => '34360',
    'Ville' => 'SAINT THIBERY',
];
$dataPourTotaux = [
    [
        'ColumnName' => [
            0 =>  'Taux TVA',
            1 => 'Base',
            2 => 'Total TVA'
        ],
        'Value' => [
            [
                0 => 20,
                1 => 1004,
                2 => ""
            ],
            [
                0 => 10,
                1 => 83.30,
                2 => ""
            ],
            [
                0 => 5.5,
                1 => 127.46,
                2 => ""
            ]
        ]
    ]
];
// end of Arrays of data