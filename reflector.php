<?php
use \Zend\Loader\StandardAutoloader;
use \Zend\Code\Reflection\FileReflection;
use \Zend\Code\Generator\MethodGenerator;

// Ensure your include path includes Zend Framework 2
set_include_path('C:\Program Files (x86)\Zend\ZendServer\share\ZendFramework2\library');
require_once 'Zend/Loader/StandardAutoloader.php';
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->registerNamespace('eCommunities\CodeHintAggregator', __DIR__);
$loader->register();

// Additional Application Specific Autoloaders
if (is_dir(__DIR__.'/loaders')) {
	$loaders = scandir(__DIR__.'/loaders');
	foreach ($loaders as $loader) {
		if(strtolower(substr($loader, -4)) == '.php') {
			include(__DIR__.'/loaders/'.$loader);
		}
	}
}

$file = $_REQUEST['file'];

include_once $file;
$reflection = new FileReflection($file);

foreach ($reflection->getClasses() as $class) {
	$namespace = $class->getNamespaceName();
	$className = $class->getShortName();

	// Open Namespace
	echo ($namespace ? 'namespace '.$namespace.' { '.PHP_EOL.PHP_EOL : NULL);

	// Open Class
	echo 'class '. $className. ' { '.PHP_EOL.PHP_EOL;

	// TODO: Add CONST support
	// TODO: Add public \ protected parameter support

	foreach ($class->getMethods() as $methodReflection) {
		$method = MethodGenerator::fromReflection($methodReflection);
		$docblock = $method->getDocblock();
		if ($docblock) {
			echo $docblock->generate();
		}
		$params = implode(', ', array_map(function($item) {
			return $item->generate();
		}, $method->getParameters()));
		echo 'public function '.$method->getName() . '(' . $params . ') { }'.PHP_EOL.PHP_EOL;
	}

	// Close Class
	echo '} '.PHP_EOL.PHP_EOL;

	// Close Namespace
	echo ($namespace ? '} '.PHP_EOL.PHP_EOL : NULL);
}