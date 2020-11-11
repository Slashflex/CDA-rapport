<?php

include_once __DIR__ . "/../../../tools_back/L1/L1_clsAutoLoader.php";
// include __DIR__ . "/../../exttools_back/barcode.php";
L1_clsAutoLoader::register();

/**
 * Class level : 2
 * Description (en) : 
 * Description (fr) : 
 */

class L2_clsPrint extends L1_clsPrint
{
    public function printToPdf($display, $nameBarcode = false)
    {
        return $this->print($display, $nameBarcode);
    }
}
