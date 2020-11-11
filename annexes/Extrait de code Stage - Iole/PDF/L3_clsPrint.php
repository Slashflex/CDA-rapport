<?php

use Symfony\Component\Console\Output\Output;

// include_once __DIR__ . "/../../../tools_back/L1/L1_clsAutoLoader.php";
// include __DIR__ . "/../../../exttools_back/barcodeLib/barcode.php";
// include __DIR__ . "/../../../exttools_back/barcodeLib/barcode.php";

// L1_clsAutoLoader::register();

/**
 * Class level : 3
 * Description (en) : 
 * Description (fr) : 
 */
class L3_clsPrint
{

    public $barCodeName;
    public $db;
    public $devise = ' €';

    // public function __construct()
    // {
    //     $this->db = new L2_clsMainDB();
    //     $this->db->connectToDbs(
    //         $GLOBALS["DB_SERVER_BM"],
    //         $GLOBALS["DB_SERVER_PORT_BM"],
    //         $GLOBALS["DB_NAME_BM"],
    //         $GLOBALS["DB_LOGIN_BM"],
    //         $GLOBALS["DB_PASSWORD_BM"],
    //         $GLOBALS["DB_SERVER_TYPE_BM"],
    //         $GLOBALS["DB_SERVER_ED"],
    //         $GLOBALS["DB_SERVER_PORT_ED"],
    //         $GLOBALS["DB_NAME_ED"],
    //         $GLOBALS["DB_LOGIN_ED"],
    //         $GLOBALS["DB_PASSWORD_ED"],
    //         $GLOBALS["DB_SERVER_TYPE_ED"]
    //     );
    // }

    public function getBarCodeName()
    {
        return $this->barCodeName;
    }
    public function setBarCodeName($tempBarCodeName)
    {
        $this->barCodeName = $tempBarCodeName;
    }
    public function getDevise()
    {
        return $this->devise;
    }
    public function setDevise($tempDevise)
    {
        $this->devise = $tempDevise;
    }

    /**
     * Wiki :
     * EN : 
     * FR : Génère le fichier PDF en fonction du type de document et des données contenues dans un tableau
     * @param string $fileType
     * @param array $param
     * @return 
     */

    public function callPhp($fileType, $param)
    {

        switch (trim($fileType)) {
            case ("facture"):
                $content = $this->invoiceHtmlGenerator($param);
                $ret = $this->printToPdf($content, array(
                    "barCode" => $this->barCodeName,
                ));
                break;


            case ("Courrier"):
                $content = $this->htmlGenerator(array("DocumentType" => "courrier"), $param);
                // var_dump($GLOBALS);
                $ret = $this->printToPdf($content, array(
                    "barCode" => $this->barCodeName,

                ));
                break;

            case ("generic"):
                // var_dump("ici");
                $content = $this->genericDoc($param);

                $ret = $this->printToPdf($content);
                break;
        }
        // var_dump($ret);
        return $ret;
    }

    /**
     * Wiki : TODO
     * EN : 
     * FR : 
     * @return 
     */
    public function tabGenerator($data)
    {
            $value = null;
            $param = null;
            // Vérification de la présence des value et si l'on doit faire des sous-totaux
            if (array_key_exists("value", $data)) $value = $data["value"];
            if (array_key_exists("sous-toto", $data)) $param = $data["sous-toto"];
            $comboColTitle = [];
            $returnElement = "";
            $returnElement .= '<div class="items"> <br/>
            <div class="containers"><table class="tableau"><thead><tr>';
            // Ajout des noms de colonnes dans le tableau avec les tailles définies dans le front 
            foreach ($data["colInfos"] as $valTab) {

                if ($valTab["title"]) {
                    $returnElement .= ' <th style="width : ' . $valTab["width"] . 'px"> ' . $valTab["title"] . ' </th>';
                    $comboColTitle[$valTab["colName"]] = $valTab["width"] . "px";
                }
            }
            // On stocke le nombre de colonnes 
            $numberOfCol = count($data["colInfos"]);
            $returnElement .= '</tr></thead><tbody>';

            $table = $data[2]["Table"];

            // Partie un peu "compliquée", tableau générique qui gère les sous-totaux
            if ($param) {
                $numberOfIndent = false;
                $numberOfIndent = count($param);
                $fonctionArray = [];
                $targetArray = [];
                $sourceArray = [];
                $aliasArray = [];
                /*Les array pour les sous-totaux sont découpés*/
                for ($t = 0; $t < $numberOfIndent; $t++) {
                    foreach ($param[$t] as $keyP => $valP) {
                        if ($keyP == "Fonction") array_push($fonctionArray, $valP);
                        if ($keyP == "Target") array_push($targetArray, $valP);
                        if ($keyP == "Source") array_push($sourceArray, $valP);;
                        if ($keyP == "Alias") array_push($aliasArray, $valP);
                    }
                }

                $lastEntry = false;
                $stringToAddSource = [];
                $stringToAddValue = [];
                $lastValTarget = false;
                $source = [];
                $lastVal = [];
                $newLine = false;
                $totalTarget = [];
                $firstiteration = true;
                // On boucle sur tout les array de valeurs reçus
                for ($i = 0; $i < count($value); $i++) {
                    // On vire le champ Rec qui ne sera jamais affiché dans un tableau
                    foreach ($value[$i] as $keyUn => $valUn) {
                        if ($keyUn == DB_RECORD_ID_NAME) unset($value[$i][$keyUn]);
                    }
                    // Dans le cas ou l'on se trouve dans la dernière boucle du tableau
                    if ($i == count($value) - 1) $lastEntry = true;
                    // On rentre dans cette condition seulement si une première ligne a été insérée
                    if ($lastValTarget) {
                        // On vérifie si les champs ciblés subissent des modifications 
                        foreach ($source as $keySource => $valSource) {
                            if ($value[$i][$keySource] != $valSource) {
                                // Booléen qui permet de rentrer dans la condition qui va générer les sous-totaux 
                                $newLine = true;
                                array_push($stringToAddSource, array("NameCol" => $valSource));
                            }
                        }
                        // Dans le cas où les valeurs des champs sources ne sont pas modifiées
                        if (!$newLine) {
                            foreach ($totalTarget as $keyValTar => $valTar) {
                                // On met à jour la valeur des champs target 
                                $lastValTarget[$keyValTar] = $value[$i][$keyValTar];
                                $totalTarget[$keyValTar] = $valTar + $value[$i][$keyValTar];
                            }
                            $returnElement .= '<tr>';
                            // On écrit les col du tableau et on vérifie a chaque boucle si la valeur est différente de la précédente,
                            // si elles sont égales on ne l'affiche pas 


                            foreach ($value[$i] as $keyTabValue => $tabValue) {
                                if (array_key_exists($keyTabValue, $lastVal) && $lastVal[$keyTabValue] == $tabValue) {

                                    $returnElement .= ' <td style="width : ' . $comboColTitle[$keyTabValue] . '">' . '//' . '</td>';
                                } else {

                                    $returnElement .= ' <td style="width : ' . $comboColTitle[$keyTabValue] . '">' . $tabValue . '</td>';
                                }
                                $lastVal[$keyTabValue] = $tabValue;
                            }

                            $returnElement .= '</tr>';
                        }
                        // Si les valeurs des champs sources on été modifiées 
                        else if ($newLine) {
                            foreach ($totalTarget as $keyTarget => $valTarget) {
                                // On ajoute la valeur total dans le string qui sera affiché 
                                array_push($stringToAddValue, $valTarget . ' € </td>');
                                $totalTarget[$keyTarget] = $value[$i][$keyTarget];
                            }
                            $returnElement .= '<tr>';
                            // On boucle sur le nombre de col et on ajoute des éléments vides pour que le tableau soit construit correctement 
                            for ($col = 0; $col < $numberOfCol - 1; $col++) {
                                $returnElement .= '<td></td>';
                            }
                            // Ligne qui affiche le sous-total
                            $returnElement .= '<td class=valeurTotale>Valeur totale pour ' . $stringToAddSource[0]["NameCol"] . ' de : ' . $stringToAddValue[0] . '</tr>';
                            // }
                            // On vérifie que ce n'est pas la dernière ligne du tableau
                            if (!$lastEntry) {
                                foreach ($value[$i] as $keyTabValue => $tabValue) {
                                    // Ajout des col avec leurs tailles
                                    $returnElement .= ' <td style="width: ' . $comboColTitle[$keyTabValue] . '"> ' . $tabValue . ' </td>';
                                    $lastVal[$keyTabValue] = $tabValue;
                                }
                            }
                            $stringToAddSource = [];
                            $stringToAddValue = [];
                            $source = [];
                            foreach ($sourceArray as $valSou) {
                                $source[$valSou] = $value[$i][$valSou];
                            }
                            $newLine = false;
                        }
                    }
                    // Si on se trouve sur la première ligne du tableau
                    else if ($firstiteration) {
                        // On définit le nom du tableau via le nom de la table
                        if ($table) {
                            $ref = $this->db->recordFindS(array(
                                "TableName" => DB_MAIN_CLIENT_TABLE_DEF,
                                "ColumnName" => array(
                                    array(
                                        "Table" => DB_MAIN_CLIENT_TABLE_DEF,
                                        "Field" => "TableDisplayName"
                                    )
                                ),
                                "ConditionSimple" => array(
                                    "TableName" => $table
                                ),
                            ));
                            $trans = $this->db->translationGet($ref[0]["TableDisplayName"]);
                        }

                        $returnElement .= '<div class="nameTab"> ' . $trans . '</div>';
                        /*Mise à jour des données qui seront stockées dans les array pour les futures lignes */
                        foreach ($targetArray as $valTar) {
                            $lastValTarget[$valTar] = $value[$i][$valTar];
                            $totalTarget[$valTar] = $value[$i][$valTar];
                        }
                        foreach ($sourceArray as $valSou) {
                            $source[$valSou] = $value[$i][$valSou];
                        }
                        // Ajout des premières valeurs 
                        $returnElement .= '<tr>';
                        foreach ($value[$i] as $keyTabValue => $tabValue) {
                            $returnElement .= ' <td style= "width : ' . $comboColTitle[$keyTabValue] . '"> ' . $tabValue . ' </td>';
                            $lastVal[$keyTabValue] = $tabValue;
                        }
                        $returnElement .= '</tr>';
                        $firstiteration = false;
                    }
                    // Dernière boucle du tableau
                    if ($lastEntry) {
                        // On récupère toutes les infos
                        // On ajoute d'abord les valeurs avant de mettre les sous-totaux
                        foreach ($value[$i] as $keyTabValue => $tabValue) {
                            $returnElement .= ' <td style="width :' . $comboColTitle[$keyTabValue] . '"> ' . $tabValue . ' </td>';
                        }

                        foreach ($source as $keySource => $valSource) {
                            array_push($stringToAddSource, array("NameCol" => $valSource));
                        }
                        foreach ($totalTarget as $keyTarget => $valTarget) {
                            array_push($stringToAddValue, $valTarget . ' € </td>');
                        }
                        // Ajout du sous total
                        $returnElement .= '<tr>';
                        for ($col = 0; $col < $numberOfCol - 1; $col++) {
                            $returnElement .= '<td></td>';
                        }

                        $returnElement .= '<td class=valeurTotale>Valeur totale pour ' . $stringToAddSource[0]["NameCol"] . ' de : ' . $stringToAddValue[0] . '</tr>';
                    }
                }
            }
            // Tableau simple sans sous-total 
            else {
                // On définit le nom du tableau via le nom de la table 
                $lastEntry = false;
                $lastVal = [];
                if ($table) {
                    $ref = $this->db->recordFindS(array(
                        "TableName" => DB_MAIN_CLIENT_TABLE_DEF,
                        "ColumnName" => array(
                            array(
                                "Table" => DB_MAIN_CLIENT_TABLE_DEF,
                                "Field" => "TableDisplayName"
                            )
                        ),
                        "ConditionSimple" => array(
                            "TableName" => $table
                        ),
                    ));
                    $trans = $this->db->translationGet($ref[0]["TableDisplayName"]);
                }

                // On parcours les array
                // $returnElement .= '<div class="nameTab"> ' . $trans . '</div>';
                $returnElement .= '<div class="nameTab"> ' . $trans . '</div>';
                for ($i = 0; $i < count($value); $i++) {
                    $returnElement .= '<tr>';
                    if ($i == count($value) - 1) $lastEntry = true;
                    foreach ($value[$i] as $keyTabValue => $tabValue) {
                        /*Le traitement est le même, si la valeur précédente est la même que la valeur actuelle on ne l'affiche pas, l'id du champs est retiré */
                        if ($lastEntry && $keyTabValue != DB_RECORD_ID_NAME) {
                            $returnElement .= ' <td "style="width : ' . $comboColTitle[$keyTabValue] . '"> ' . $tabValue . ' </td>';
                        } else if (array_key_exists($keyTabValue, $comboColTitle) && $keyTabValue != DB_RECORD_ID_NAME) {
                            if (array_key_exists($keyTabValue, $lastVal) && $lastVal[$keyTabValue] == $tabValue) {

                                $returnElement .= ' <td style="width :' . $comboColTitle[$keyTabValue] . '">  </td>';
                            } else {

                                $returnElement .= ' <td style="width: ' . $comboColTitle[$keyTabValue] . '"> ' . $tabValue . ' </td>';
                            }
                            $lastVal[$keyTabValue] = $tabValue;
                        }
                    }
                    $returnElement .= '</tr>';
                }
            }
            return $returnElement;
    }

    /**
     * Wiki : //TODO
     * EN : 
     * FR : 
     * @param array
     * @return array resultArray
     */
    public function genericDoc($data)
    {
        $header = $this->headerGenerator(true);
        $footer = $this->footerGenerator();
        // if ($data["isTable"]) {
        if ($data["isTable"]) {
            $returnElement = '<!DOCTYPE html>
            <head>
            <title>Génération de PDF</title>

            <meta charset="utf-8">
            <link type="text/css" rel="stylesheet" href= "css/pdf.css"/>
            </head>';
            //TODO Il  faudra remplacer cet appel par le dataRequest
            $returnElement .= $this->tabGenerator($data);
        } else {
            $returnElement = '<!DOCTYPE html>
            <head>
            <title>Génération de PDF</title>

            <meta charset="utf-8">

            <link type="text/css" rel="stylesheet" href= "css/materialize.css"/>
            <link type="text/css" rel="stylesheet" href= "css/iconfont/material-icons.css"/>
            <link type="text/css" rel="stylesheet" href= "css/iole.min.css"/>
            </head>
            <body>
            <div id="view" class ="view">
            <div class ="row">';
            $returnElement .= $data["html"]["forms"][0];
            $returnElement .= '</div></div></body>';
            // <link type="text/css" rel="stylesheet" href= "css/pdfGeneric.css"/>
            // <link type="text/css" rel="stylesheet" href= "css/materialize.css"/>
            // <link type="text/css" rel="stylesheet" href= "css/iconfont/material-icons.css"/>
            // <link type="text/css" rel="stylesheet" href= "css/iole.min.css"/>
        }

        $returnElement .= '</html>';
        $valueFields = $data["html"]["data"];
        $resultArray = [];
        array_push($resultArray, $header);
        array_push($resultArray, $returnElement);
        array_push($resultArray, $footer);
        array_push($resultArray, $valueFields);
        return $resultArray;
    }

    /**
     * Wiki :
     * EN : 
     * FR : Fonction qui génère la facture en HTML
     * @param array $param
     * @return array $resultArray
     */
    public function invoiceHtmlGenerator($param)
    {
            $rib = '<div id="rib" style="width: 60%"> <p><span class="semibold bold">Conditions et modalités de règlement : </span></p>
           <p> <span class="semibold bold">Règlement par virement :</span></br>
            IBAN FR76 4590 4857 0301 9867 045 (Ajouter Libellé : FAC2020-IOLE)</p>
            <p><span class="semibold bold">Règlement par chèque à l’odre de :</span></br>
            Iole Solutions</p>

            <p>Paiement sous 30 jours à réception de la facture. Pas d’escompte pour paiement anticipé. En cas de retard de paiement, 
            application d’une indemnité forfaitaire pour frais de recouvrement de 40€, selon l’article D.441-5 du code du commerce.</p></div>';

            $dataPourTotaux = array(array(

                'ColumnName' => array(
                    0 =>  'Taux TVA',
                    1 => 'Base',
                    2 => 'Total TVA'
                ),

                'Value' => array(
                    array(
                        0 => 20,
                        1 => 1004,
                        2 => ""
                    ),

                    array(
                        0 => 10,
                        1 => 83.30,
                        2 => ""

                    ),
                    array(
                        0 => 5.5,
                        1 => 127.46,
                        2 => ""
                    ),

                )
            ));
            $priceTotalAllTab = 0;
            $previousReduction = null;
            $priceTotal = 0;
            $reductionKey = null;
            $quantityKey = null;
            $priceKey = null;
            $montantKey = null;
            $headerInfoElement = $this->headerGenerator();
            $footer = $this->footerGenerator();
            $client = array(
                'Nom' => 'LAUREEN',
                'Prénom' => 'NAUD',
                'Adresse1' => 'BÂTIMENT A, LOGEMENT 2',
                'Adresse2' => 'RESIDENCE LES COSTES',
                'CodePostal' => '34360',
                'Ville' => 'SAINT THIBERY',
            );

            $returnElement = '<!DOCTYPE html>
                                <head>
                                <script src="js/pdf.js"></script>
                                <title>Facture</title>
                                <meta charset="utf-8">
                                <link type="text/css" rel="stylesheet" href= "css/pdf.css"/>
                                </head>
                                <body onload = setTotal()>
                                <div class="clientContact">';
            $returnElement .= $client['Nom'] . ' ' . $client['Prénom'] . '<br/>' . $client['Adresse1'] . '<br/>' . $client['Adresse2'] . '<br/>'
                . $client['CodePostal'] . ' ' . $client['Ville'];
            $returnElement .=
                '</div>
                            <div class="title">
                                Facture
                                </div>';

            $totalHT = 0;
            $creationInProgress = false;
            $idArray = [];
            $newId = 0;
            $returnElement .= '<div class="items">';

            for ($i = 0; $i < count($param); $i++) {
                $returnElement .= " <br/>";
                $newId++;
                $idArray[$i] = $newId;
                // $returnElement .= '<span style=display:none id=tabArray></span>';
                //TODO à quoi ça sert ?
                if ($creationInProgress) {
                    $returnElement .= '<span>TESSST</span>';
                }

                $returnElement .= '<span id=head_current' . $newId . '></span><br/><table class="tableau"><thead><tr>';
                $returnElement .= '<span class="bonDeCommande bold"> Bon de commande : ' . $param[0]["BonDeCommande"][$i] . '</span>';
                foreach ($param[0]["ColumnName"] as $keyHead => $valHead) {

                    if ($valHead == "Remise") $reductionKey = $keyHead;
                    if ($valHead == "Prix Unitaire") $priceKey = $keyHead;
                    if ($valHead == "Montant") $montantKey = $keyHead;
                    if ($valHead == "Quantité") $quantityKey = $keyHead;
                    $returnElement .= " <th> " . $valHead . " </th>";
                }
                $returnElement .= '</tr></thead><tbody>';
                for ($a = 0; $a < count($param[0]["Value"]); $a++) {
                    $returnElement .= '<tr>';
                    $creationInProgress = true;
                    foreach ($param[0]["Value"][$a] as $keyBody => $valBody) {
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
                            $priceFormat = number_format(($price), 2, ',', ' ') . $this->devise;
                            $returnElement .= ' <td class="center"> ' . $priceFormat  . ' </td>';
                        } else if ($keyBody == $quantityKey) {
                            $quantity = $valBody;
                            $returnElement .= ' <td class="center"> ' . $quantity  . ' </td>';
                        } else if ($keyBody == $montantKey) {
                            $thePrice = $price * $quantity;
                            if ($previousReduction == null) {
                                $priceTotal = $priceTotal + $thePrice;
                                // $thePrice  = strval($thePrice) . " €";
                                $thePriceFormat = number_format(($thePrice), 2, ',', ' ') . $this->devise;
                                $returnElement .= ' <td class="center"> ' . $thePriceFormat  . ' </td>';
                                $thePrice = null;
                            } else {

                                $reduction = $thePrice * $previousReduction / 100;
                                $thePrice = $thePrice - $reduction;
                                $priceTotal = $priceTotal + $thePrice;
                                $reduction = null;
                                // $thePrice  = strval($thePrice) . " €";
                                $thePriceFormat = number_format(($thePrice), 2, ',', ' ') . $this->devise;
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

                $totalHT = $totalHT + $priceTotal;
                array_push($dataPourTotaux, $totalHT);

                $priceTotal = null;
                $priceTotalAllTab = $priceTotalAllTab + $priceTotal;
            }
            $returnElement .= '</div>';

            $totauxData = $this->subtotals($dataPourTotaux);

            $totaux = '<div class=totaux></div> </body><br/>';
            $returnElement .= '<div class="flex" style="display: flex; width: 100%; justify-content: center">' . $rib . $totauxData . '</div>';
            // $returnElement .= $totaux . $totauxData;

            $resultArray = [];
            array_push($resultArray, $headerInfoElement);
            array_push($resultArray, $returnElement);
            array_push($resultArray, $footer);
            return $resultArray;
    }

    /**
     * Wiki :
     * EN : 
     * FR : Retourne les données sous forme d'un tableau de contenus HTML
     * @param string $docType
     * @param array $param
     * @return array $resultArray
     */

    public function htmlGenerator($docType, $param)
    {
            $client = array(
                'Nom' => 'LAUREEN',
                'Prénom' => 'NAUD',
                'Adresse1' => 'BATIMENT A, LOGEMENT 2',
                'Adresse2' => 'RESIDENCE LES COSTES',
                'CodePostal' => '34360',
                'Ville' => 'SAINT THIBERY',
            );
            $signatureIole = 'Philippe GUY<br />
        Directeur<br /><br />
        *SIGNATURE*';


            if ($docType["DocumentType"] === "courrier") {
                $headerInfoElement = $this->headerGenerator(true);
                $footer = $this->footerGenerator();

                $returnElement = '<!DOCTYPE html>
                                    <head>
                                    <script src="js/pdf.js"></script>
                                    <title>Facture</title>
                                    <meta charset="utf-8">
                                    <link type="text/css" rel="stylesheet" href= "css/pdf.css"/>
                                    </head>
                                    <body onload = setTotal()>
                                    <div class="clientContact">';
                $returnElement .= $client['Nom'] . ' ' . $client['Prénom'] . '<br/>' . $client['Adresse1'] . '<br/>' . $client['Adresse2'] . '<br/>'
                    . $client['CodePostal'] . ' ' . $client['Ville'];
                $returnElement .= '
                                </div>
                                <div class="date">
                                Vannes, le 06/01/2020
                            </div>
                        </div>
                                <div class="main">
                                <p class = appel>Madame,</p>
                                <p class=paragraphe>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer suscipit pharetra dolor.
                                    Duis ultricies venenatis neque quis semper. Mauris hendrerit orci in ipsum tincidunt, et consectetur nulla
                                    venenatis. Quisque ut euismod est. Phasellus elementum tincidunt aliquam. Vestibulum ante ipsum primis in
                                    faucibus orci luctus et ultrices posuere cubilia Curae; Etiam rutrum quis nisi sit amet dapibus. Morbi
                                    fringilla magna at purus lacinia accumsan. Sed elit ligula, pulvinar ac lectus condimentum, venenatis varius
                                    dui. Ut sapien purus, porta in metus vel, ultrices blandit nisi. Etiam quis urna suscipit, porttitor felis at,
                                    vulputate lacus. In ac nisl id ipsum egestas porta non sit amet est. Etiam auctor placerat eleifend. Etiam ut
                                    ipsum sollicitudin diam volutpat aliquet eu ut est.
                                </p>
                                <br />
                                <p class=paragraphe>
                                    Nunc at ornare enim. Ut bibendum, neque a varius dignissim, turpis dolor dictum libero, dignissim facilisis dolor
                                    nunc semper arcu. Vivamus congue maximus placerat. Aenean facilisis orci eros, id accumsan neque tristique et.
                                    Curabitur pretium, quam hendrerit elementum gravida, lectus nisl fermentum tellus, tempus sodales libero magna
                                    non nunc. Sed lobortis faucibus sapien id posuere. Integer id diam neque. Nulla dictum venenatis sapien in tincidunt.
                                    Suspendisse potenti. Curabitur eget congue diam, eu tempor velit. Vestibulum ultrices finibus diam, vel vehicula nisi
                                    convallis a. Suspendisse suscipit, ligula id pellentesque consequat, ante sem vehicula massa, sit amet vehicula
                                    lacus neque sed nunc.
                                </p>
                                </div>
                                <br />

                                <div class=signature>
                                   ' . $signatureIole . ' 
                                </div>';
                // var_dump($param);
                $returnElement .= '<div class="items">';
            }
            $returnElement .= '</html>';
            $resultArray = [];
            array_push($resultArray, $headerInfoElement);
            array_push($resultArray, $returnElement);
            array_push($resultArray, $footer);
            return $resultArray;
    }


    /**
     * Wiki :
     * EN : Generates HTML header
     * FR : Génère le header en HTML
     * @param boolean $isGeneric
     * @return string
     */
    public function invoiceHeader($isGeneric = false)
    {
        $htmlHeader = '<!DOCTYPE html>
        <head>
        <meta charset="utf-8">
        <link type="text/css" rel="stylesheet" href="css/pdf.css"/>
        </head>
        <body>
        <div class="headFac"><div class="logoAndInfo"><img src="images/logo.png" class="logoIole"/>';

        $output = [];

        $image = new barcode_generator();
        $image->output_image('png', 'qr', 'www.google.fr', '');
        $this->setBarCodeName($image->getName());

        if ($isGeneric) {
            echo $isGeneric;
            $headerGeneric1 = [
                0 => 'Expédition non facturée',
                1 => 'Anjou Maine Céréales',
            ];

            $headerGeneric2 = [
                'Utilisateur' => 'Jean Vassal',
                'DateTime' => '23/02/20 à 13h56',
            ];

            array_push($output, $headerGeneric1);
            array_push($output, $headerGeneric2);
            return $output;
            $htmlHeader .= '<div class="firstLine"></div>
            <div class=bloc><div class=semibold>Recherche :</div>'
                . $headerGeneric1[0] . '<br/>' .
                $headerGeneric1[1] . '</div>
                <div class="secondLine"></div>
                <div class=bloc><div class=semibold>Utilisateur :</div>' .
                $headerGeneric2['Utilisateur'] . ' <br/></br><div class=semibold>Date et heure : </div>' .
                $headerGeneric2['DateTime'] . '</div>
                <div class="thirdLine"></div>
             <div class="barCodeHeader">
            <img src=' . $this->barCodeName . ' class=barCode style=width:150px; height:150px/>
            </div></div>';
        } else {
            // echo 'good';
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

            $invoiceDetails = [
                'num' => 'FAC2020-01-025',
                'dateEmission' => '06/01/20',
                'dateEcheance' => '06/01/20',
                'referenceClient' => 'GLA97432'
            ];

            array_push($output, $adresseIole);
            array_push($output, $contactInterlocuteur1);
            array_push($output, $contactInterlocuteur2);
            array_push($output, $invoiceDetails);

            return $output;

                $htmlHeader .= ' <br/> <br/>' .
                    $adresseIole['adresse1'] . '<br/>' .
                    $adresseIole['adresse2'] . '<br/>' .
                    $adresseIole['codePostal'] .
                    $adresseIole['ville'] . '<br/>' .
                    $adresseIole['tel'] . '</div>
                <div class="firstLine"></div>
                <div class="facDetail">
                <div class="headFacTitle bold"> Détails facture : </div>
                N° de facture : ' . $facture['num'] . '<br/>
                Date d\'émission : ' . $facture['dateEmission'] . ' <br/>
                Date d\'échéance : ' . $facture['dateEcheance'] . '<br/>
                Référence client : ' . $facture['referenceClient'] . '
                </div>
                <div class="secondLine"></div>

                <div class="ioleDetail">
                <div class="headFacTitle bold"> Contact interlocuteur 1 : </div>'
                    . $contactInterlocuteur1['nom'] . '<br/> 
                Tél : ' . $contactInterlocuteur1['tel'] . '<br/>
                Mail : ' . $contactInterlocuteur1['mail'] . ' 
                <div class="headFacTitle mt-2 bold"> Contact interlocuteur 2 : </div>'
                    . $contactInterlocuteur2['nom'] . '<br/> 
                Tél : ' . $contactInterlocuteur2['tel'] . '<br/>
                Mail : ' . $contactInterlocuteur2['mail'] . ' 
                </div>

                <div class="thirdLine"></div>
                <div class="barCodeHeader">
                <img src=' . $this->barCodeName . ' class=barCode style=width:150px; height:150px/>
                </div>
                </div>';
        }
        return $htmlHeader;
    }

    /**
     * Wiki :
     * EN : 
     * FR : Génère le footer en HTML
     * @return string
     */
    public function footerGenerator()
    {
        $htmlFooter1 = '<!DOCTYPE html><head>
        <meta charset="utf-8">
        <link type="text/css" rel="stylesheet" href= "css/pdf.css"/>
       <script src="js/pdf.js"></script>
        </head>
        <body onload = getPdfInfo() class=footer>';

        $htmlFooter2 = ' <br />
        <div id=page_numbers>
        Page
        <span id=page_current> </span>
        <span>/</span>
        <span id=page_count></span>
        </div>
        </body>';

        //Test avec accès à la BD
        $nom = $this->db->recordFindS(array(
            "TableName" => "ProjectTable",
            "ColumnName" => array(
                array(
                    "Table" => "ProjectTable",
                    "Field" => "TableName",
                ),

            ),
            "ConditionExtends" => array(
                "Type" => "Condition",
                "Arg1" => array(
                    "Table" => "ProjectTable",
                    "Field" => "RecId",
                ),
                "Sign" => "=",
                "Arg2" => 28
            ),

        ), 'direct');
        // var_dump($nom);

        $statuts = array(
            'nom' => 'Iole Solutions',
            'forme' => 'SASU Société par actions simplifiée à associé unique',
            'capital' => '100 000.00€',
            'siege' => '12 rue Henri Becquerel 56000 Vannes',
            'rcs' => 'Vannes B842 110 835',
            'siret' => '84211083500027',
            'tvaIntra' => 'FR 3903050',
            'ape' => '6201Z',
        );
        $statuts = '<div  id="statuts">' . /* $nomSociete */ $statuts['nom'] . ' - ' . $statuts['forme'] . ' - Capital social : ' . $statuts['capital'] . ' - Siège social : ' . $statuts['siege'] . ' -
            <br /> RCS : ' . $statuts['rcs'] . ' - SIRET : ' . $statuts['siret'] . ' - Numéro de TVA intracommunautaire : ' . $statuts['tvaIntra'] . ' - Code APE : ' . $statuts['ape'] . '  
          </div>
          <br />';
        $footer = $htmlFooter1 . $statuts . $htmlFooter2;
        return $footer;
    }

    /**
     * Wiki :
     * EN : 
     * FR : Génère le tableau des sous-totaux en bas de facture, en HTML
     * @param array $data
     * @return string
     */
    public function subtotals($data)
    {
        // récupère la dernière ligne de $data, qui correspond au sous-total HT calculé dans l'édition de la facture
        $totalHT = end($data);
        $totalTVA = 0;
        $totalTTC = 0;
        $returnElement = '<div class="items">';
        $returnElement .= " <br/>";

        // mise en forme des nombres au format français avec 2 chiffres après la virgule
        $totalHTFormat = number_format(($totalHT), 2, ',', ' ') . $this->devise;
        $returnElement .= '<br/><table class="totaux"><thead><tr>';
        $returnElement .= '<tr id="totalHT"><td> Total HT </td><td></td><td>' . $totalHTFormat . '</td></tr>';

        // parcourt les valeurs ColumnName de la clé 0 du tableau
        foreach ($data[0]["ColumnName"] as $keyHead => $valHead) {
            if ($valHead == "Taux TVA") $tvaKey = $keyHead;
            if ($valHead == "Base") $baseKey = $keyHead;
            if ($valHead == "Total TVA") $totalKey = $keyHead;
            $returnElement .= " <th> " . $valHead . " </th>";
        }
        $returnElement .= '</tr></thead><tbody>';
        for ($a = 0; $a < count($data[0]["Value"]); $a++) {
            $returnElement .= '<tr>';
            // parcourt les valeurs Value de la clé 0 du tableau
            foreach ($data[0]["Value"][$a] as $keyBody => $valBody) {
                // affiche les valeurs de la colonne Taux TVA
                if ($keyBody == $tvaKey) {
                    $tvaCalcul = $valBody;
                    $tva = strval($valBody) . " %";
                    $returnElement .= ' <td class="center"> TVA ' . $tva . ' </td>';
                    // affiche les valeurs de la colonne Base
                } else if ($keyBody == $baseKey) {
                    $baseCalcul = $valBody;
                    $baseFormat = number_format(($valBody), 2, ',', ' ') . $this->devise;
                    $returnElement .= ' <td class="center">' . $baseFormat  . ' </td>';
                    // affiche les valeurs de la colonne Total TVA
                } else if ($keyBody == $totalKey) {
                    $totalTVALigne = ($baseCalcul * $tvaCalcul) / 100;
                    $totalTVALigneFormat = number_format(($totalTVALigne), 2, ',', ' ');
                    $totalTVA = $totalTVA + $totalTVALigne;
                    $returnElement .= ' <td class="center"> ' . $totalTVALigneFormat  . $this->devise . ' </td>';
                }
            }
            $returnElement .= '</tr>';
        }
        // calcul de la somme des totaux TVA
        $totalTTC = $totalHT + $totalTVA;
        $totalTVAFormat = number_format(($totalTVA), 2, ',', ' ') . $this->devise;
        $totalTTCFormat = number_format(($totalTTC), 2, ',', ' ') . $this->devise;
        $returnElement .= '<tr class=semibold id=totalTVA><td> Total TVA </td><td></td><td>' . $totalTVAFormat . '</td></tr>';
        $returnElement .= '<tr><td> </td></tr>';
        $returnElement .= '<tr id="totalTTC"><td> Total TTC </td><td></td><td>' . $totalTTCFormat . '</td></tr>';
        $returnElement .= '</tbody></table>';
        return $returnElement;
    }
}