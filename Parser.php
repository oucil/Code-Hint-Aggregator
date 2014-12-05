<?php 

use Zend\Code\Reflection\FileReflection;
use Zend\Code\Generator\MethodGenerator;
	
class Parser {
	
	/**
	 * Root directory path. 
	 */
	private $_path;
	
	/**
	 * Parser construct.
	 * @param string $path
	 */
	public function __construct($path) {
		$this->_path = $path; 
	}
	
	public function listFiles() {
		$file = $this->_dirIterator($this->_path);
		return $file;	
	}
	
	private function _dirIterator($path) {
		echo "<h1> $path </h1>";
		$resources = scandir($path);
		var_dump($resources);
		foreach($resources as $key=>$resource) {
			if(substr($resource, 0, 1) == '.') {
				echo "<p> $resource = SKIPPING </p>";
				unset($resources[$key]);
			} elseif(is_dir($path . DIRECTORY_SEPARATOR . $resource)) {
				echo "<p> $resource = YES </p>";
				$subResources = $this->_dirIterator($path . DIRECTORY_SEPARATOR . $resource);
				unset($resources[$key]);
				$resources = array_merge($resources, $subResources);
			} elseif(strtolower(substr($resource, -4)) == '.php') {
				echo "<p> $resource = PHP </p>";
				$resources[$key] = $path . DIRECTORY_SEPARATOR . $resource;
			} else {
				unset($resources[$key]);
			}
		}
		return $resources;
	}
	
	/**
	 * Parse docblocks from classes. 
	 */
	public function parse($file) {
		
		$output = '';
		include_once $file;
		$reflection = new FileReflection($file);
		
		foreach ($reflection->getClasses() as $class) {
			
			echo "<h3>Class: {$class->getShortName()}</h3>";
			
		    $namespace = $class->getNamespaceName();
		    $className = $class->getShortName();
		    foreach ($class->getMethods() as $methodReflection) {
		        $method = MethodGenerator::fromReflection($methodReflection);
		        $docblock = $method->getDocblock();
		        if ($docblock) {
		            $output .= $docblock->generate();
		        }
		        $params = implode(', ', array_map(function($item) {
		            return $item->generate();
		        }, $method->getParameters()));
		
		        $output .= $namespace . ' ' . $className . '::' . $method->getName() . '(' . $params . ')';
		        $output += PHP_EOL . PHP_EOL;
				
				echo $output;
				
		    }
		}
		return $output;
	}
}
