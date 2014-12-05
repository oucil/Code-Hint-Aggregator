<?php

require_once 'Zend/Loader/StandardAutoloader.php';
$loader = new Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));

// Register with spl_autoload:
$loader->register();


include_once "Parser.php";
//$parser = new Parser('C:\Users\alex\Documents\BoxPacker');
$parser = new Parser(__DIR__);
$files = $parser->listFiles();

echo '<h1 style="color: #090;"> FINISHED </h1>';
var_dump($files);

$parsedFiles = array();
foreach($files as $file) {
	$parsedFiles[] = $parser->parse($file);
}

echo implode('', $parsedFiles);

