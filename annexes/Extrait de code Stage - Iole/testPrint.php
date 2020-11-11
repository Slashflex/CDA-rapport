<?php
// include_once __DIR__ . "/../../tools_back/L1/L1_clsAutoLoader.php";
// L1_clsAutoLoader::register();

// Shell Command that actually render an html file to a pdf
// https://wkhtmltopdf.org/usage/wkhtmltopdf.txt flag options
$cmd = "wkhtmltopdf http://localhost:1664/loadTwig.php " . __DIR__ . "/sdftest.pdf";
// echo __DIR__;
// $shell = new L1_clsWWWFileSystem();
// Execute the command $cmd in the server shell
// echo $shell->runShellCommand($cmd, true); // true => display command status
echo shell_exec($cmd);