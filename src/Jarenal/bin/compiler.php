#!/usr/bin/php
<?php

/* DEFAULT CONFIGURATION */
define("PATH_BASE", str_replace("src/Jarenal/bin", "", dirname(__FILE__)));
define("PATH_TEMPLATE", PATH_BASE."src/Jarenal/Template/product-picker.tpl");
define("PATH_CLASSES", PATH_BASE."src/Jarenal");
define("PATH_OUTPUT", PATH_BASE."bin/product-picker.php");

$template = file_get_contents(PATH_TEMPLATE);

$controller = file_get_contents(PATH_CLASSES."/Controller.php");
$controller = substr($controller, strpos($controller, "class"));

$parser = file_get_contents(PATH_CLASSES."/Parser.php");
$parser = substr($parser, strpos($parser, "class"));

$product_picker = file_get_contents(PATH_CLASSES."/ProductPicker.php");
$product_picker = substr($product_picker, strpos($product_picker, "class"));

$output = preg_replace(array("/{controller}/", "/{parser}/", "/{product_picker}/"), array($controller, $parser, $product_picker), $template);

$handle = @fopen(PATH_OUTPUT, "w+");
fwrite($handle, $output);
fclose($handle);

echo "Compilation was successful!\n";
echo "NOTICE! The new script was generated at ".PATH_OUTPUT."\n";