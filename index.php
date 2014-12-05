<?php

include_once "Parser.php";
$parser = new Parser('C:\Users\alex\Documents\BoxPacker');
$files = $parser->listFiles();

var_dump($files);
