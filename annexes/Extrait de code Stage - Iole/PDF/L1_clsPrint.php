<?php

/**
 * Class level : 1
 * Description (en) : Class of function dedicated to PDF edition from view.
 * Description (fr) : Classe de fonction dédiées à l'édition de fichiers pdf à partir d'une vue.
 * Class L1_clsPrint
 */

include_once __DIR__ . "/../../../tools_back/L1/L1_clsAutoLoader.php";
L1_clsAutoLoader::register();


class L1_clsPrint
{
    public $pdfName = "";
    public $headerName = "";
    public $footerName = "";

    /**
     * L1_clsPrint constructor.
     * Description : This function will be executed by default on each objet instanciation
     */
    public function __construct()
    {
    }
    /**
     *L1_clsPrint DeConstructor
     * Description : This function will be executed by default on each objet destruction
     */
    public function __destruct()
    {
    }


    /**
     * Wiki :
     * EN : 
     * FR : Fonction servant à générer puis afficher le PDF
     * @param array $param
     * @param boolean $nameArray
     * @return 
     */
    public function print($param, $nameArray = false)
    {
        $this->setPdfName();
        // Données définies dans un tableau créé par htmlGenerator() dans L3_clsPrint
        $header = $param[0];
        $content = $param[1];
        $footer = $param[2];
        $data = false;
        if (array_key_exists(3, $param)) {
            $data = $param[3];
        }

        if ($nameArray) {
            $barCodeName = $nameArray["barCode"];
        }
        // Noms aléatoires
        $headerName = $this->ranNameHTML();
        $footerName = $this->ranNameHTML();

        // Crée et écrit dans $headerName.html
        $fileHeader = fopen($GLOBALS["DIRTESTWS"] . $headerName,  "w");
        fwrite($fileHeader, $header);
        fclose($fileHeader);

        // Crée et écrit dans $footerName.html
        $fileFooter = fopen($GLOBALS["DIRTESTWS"] . $footerName, "w");
        fwrite($fileFooter, $footer);
        fclose($fileFooter);

        // Crée et écrit dans getPdfName().html
        $fileContent = fopen($GLOBALS["DIRTESTWS"] . $this->getPdfName() . ".html", "w");

        if ($data) {
            $data = json_encode(($data));
            // $content .= "<script src='../../../tools_front/L3/L3_clsFormParser.js'></script>";
            $content .= " <script> var _value = {$data};
            for (var id in _value) {
                if (_value.hasOwnProperty(id)) {
                  document.querySelector('#'.concat(id)).setAttribute('value', _value[id]);
                }
              } </script>";
            // $content .= "<script js/parsePdf.js'></script>";
            // $content .= '<script>"use strict"; applyValue(' .$data.')</script>';
            // $content .= "<script>'use script'; document.addEventListener('DOMContentLoaded', function(e) { applyValue({$data}); });</script>";
        }

        fwrite($fileContent, $content);
        fclose($fileContent);

        // Ligne de commande WKHTMLtoPDF - Syntaxe : wkhtmltopdf [GLOBAL OPTION]... [OBJECT]... <output file>
        $cmd = "wkhtmltopdf --header-html " . $GLOBALS["DIRTESTWS"] . $headerName . " --header-spacing 15 --footer-html " . $GLOBALS["DIRTESTWS"] . $footerName . " file://" . $GLOBALS["DIRTESTWS"] . $this->getPdfName() . ".html " . $GLOBALS["DIRTESTWS"] . $this->getPdfName() . ".pdf";
        $shell = new L1_clsWWWFileSystem();
        $shell->runShellCommand($cmd, false);

        // Supprime les fichiers temporaires
        unlink($GLOBALS["DIRTESTWS"] . $this->getPdfName() . ".html");
        if ($nameArray) {
            unlink($GLOBALS["DIRTESTWS"] . $barCodeName);
        }
        // unlink($GLOBALS["DIRTESTWS"] . $footerName);
        // unlink($GLOBALS["DIRTESTWS"] . $headerName);
        $ret = $this->displayPdf($GLOBALS["DIRTESTWS"] . $this->getPdfName() . ".pdf", true, "application/pdf", array("link" => $GLOBALS['DS'] . $GLOBALS['WORKSPACE'] . $GLOBALS['DS'] . $GLOBALS['DIRWS'], "name" => $this->getPdfName() . ".pdf"));
        return $ret;
    }

    private function getPdfName()
    {
        return $this->pdfName;
    }

    // Définit le nom du PDF à partir de la date du jour
    private function setPdfName()
    {
        $this->pdfName = $this->today();
    }

    /**
     * Wiki :
     * EN : Returns today's date in format dd-mm-yyy
     * FR : Retourne la date du jour en format jj-mm-aaaa
     * @return DateTime 
     */
    private function today()
    {
        $formatedDate = null;

        $timeZone = "Europe/Paris";
        $newDate = new DateTime('NOW', new DateTimeZone($timeZone));
        $formatedDate = $newDate->format('d-m-Y');

        return $formatedDate;
    }

    /**
     * Wiki :
     * EN : Returns 5 random lowercase characters with .html extension
     * FR : Retourne 5 caractères aléatoires minuscules avec une extension .html
     * @return string
     */
    public function ranNameHTML()
    {
        return chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . '.html';
    }
    /**
     * Wiki :
     * EN : Function for displaying generated PDF file.
     * FR : Fonction permettant l'affichage du PDF généré.
     * @param string $pdflink
     * @param boolean $inline
     * @param string $contentType
     * @param array $returnedArray
     * @return string
     */

    public function displayPdf($pdfLink, $inline, $contentType = "application/pdf", $returnedArray)
    {

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: ' . $contentType);
        header('Content-Transfer-Encoding: binary');
        // header('Content-Disposition: inline; filename = toto');
        // #84: Content-Length leads to "network connection was lost" on iOS
        $isIOS = preg_match('/i(phone|pad|pod)/i', $_SERVER['HTTP_USER_AGENT']);
        if (!$isIOS) {
            header('Content-Length: ' . filesize($pdfLink));
        }

        if ($pdfLink !== null || $inline) {
            //inline = le PDF s'affiche dans le navigateur, attachment = une fenêtre s'ouvre pour sauvegarder le fichier
            $disposition = $inline ? 'inline' : 'attachment';
            // var_dump($disposition);
            header('Content-Disposition: ' . $disposition . '; filename=' . $this->getPdfName() . '; filename*=UTF-8\'\'' . rawurlencode($this->getPdfName()));
        }
        // readfile($pdfLink);
        return array("data" => $returnedArray);
    }
}
