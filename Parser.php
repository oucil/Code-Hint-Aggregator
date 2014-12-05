<?php 

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
			} else {
				echo "<p> $resource = NO </p>";
			}
		}
		return $resources;
	}
}
