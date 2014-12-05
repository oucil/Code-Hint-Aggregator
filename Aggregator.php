<?php 
/**
 * eCommunities Code Hint Agregator
 * @package CodeHintAggregator
 * @author Kevin Farley | eCommunities http://www.ecommunities.ca
 * @author Original Reflection Parser: motanelu http://stackoverflow.com/questions/25045114/is-it-possible-to-generate-a-code-hint-reference-for-my-application-like-phps-s?noredirect=1#comment41150252_25045114
 * @license unlicense
 */
namespace eCommunities\CodeHintAggregator;

use \Zend\Code\Reflection\FileReflection;
use \Zend\Code\Generator\MethodGenerator;
use Zend\Config\Processor\Constant;
	
class Aggregator {
	
	const OUTPUT_SCREEN = 1; 
	const OUTPUT_STRING = 2;
	const OUTPUT_DOWNLOAD = 3;
	const OUTPUT_FILE = 4;
	
	/**
	 * Self-referencing directory tree iterator that traverses the application path root and all subdirectories for PHP files
	 * @param string $path Application root path
	 * @return string
	 */
	public function listFiles($appPath) {
		$resources = scandir($appPath);
		foreach($resources as $key=>$resource) {
			if(substr($resource, 0, 1) == '.') {
				unset($resources[$key]);
			} elseif(is_dir($appPath . DIRECTORY_SEPARATOR . $resource)) {
				$subResources = $this->_dirIterator($appPath . DIRECTORY_SEPARATOR . $resource);
				unset($resources[$key]);
				$resources = array_merge($resources, $subResources);
			} elseif(strtolower(substr($resource, -4)) == '.php') {
				$resources[$key] = $appPath . DIRECTORY_SEPARATOR . $resource;
			} else {
				unset($resources[$key]);
			}
		}
		return $resources;
	}
	
	/**
	 * Parse docblocks from classes.
	 * NB: It's your responsibility to ensure that all external resources for the target application are accessible to allow proper loading, i.e. use declarations for external libraries. 
	 * @param string $file
	 * @return string
	 */
	public function parseFile($file) {
		$output = '';
		
		// File must be included for reflection.
		include_once $file;
		$reflection = new FileReflection($file);
		
		foreach ($reflection->getClasses() as $class) {
			$namespace = $class->getNamespaceName();
		    $className = $class->getShortName();
		    
		    // Open Namespace
		    $output .= ($namespace ? 'namespace '.$namespace.' { '.PHP_EOL.PHP_EOL : NULL);
		    
		    // Open Class
		    $output .= 'class '. $className. ' { '.PHP_EOL.PHP_EOL;
		    
		    // TODO: Add CONST support
		    // TODO: Add public \ protected parameter support
		    
		    foreach ($class->getMethods() as $methodReflection) {
		        $method = MethodGenerator::fromReflection($methodReflection);
		        $docblock = $method->getDocblock();
		        if ($docblock) {
		            $output .= $docblock->generate();
		        }
		        $params = implode(', ', array_map(function($item) {
		            return $item->generate();
		        }, $method->getParameters()));
		        $output .= 'public function '.$method->getName() . '(' . $params . ') { }'.PHP_EOL.PHP_EOL;
		    }
		    
		    // Close Class
		    $output .= '} '.PHP_EOL.PHP_EOL;
		    
		    // Close Namespace
		    $output .= ($namespace ? '} '.PHP_EOL.PHP_EOL : NULL);
		}
		return $output;
	}
	
	/**
	 * Parses a list of input files and outputs to either the screen [default] or a target file.
	 * @param array $fileList
	 * @param Constant $format OUTPUT_SCREEN|OUTPUT_STRING|OUTPUT_DOWNLOAD|OUTPUT_FILE
	 * @return number
	 */
	public function output($fileList,$format) {
		// Parse the file list
		$parsedFiles = array();
		foreach($fileList as $file) {
			$parsedFiles[$file] = $this->parseFile($file);
		}
		// Output to target
		switch($format):
		case(self::OUTPUT_DOWNLOAD):
			// Output to file
			$output = '<?php '.PHP_EOL.PHP_EOL;
			foreach($parsedFiles as $file => $block) {
				$output .= $block.PHP_EOL.PHP_EOL;
			}
			header('HTTP/1.1 200 OK');
			header('Content-Length: '.strlen($output));
			header('Content-Type: application/php');
			header('Content-Disposition: attachment; filename="reference_manual.php"');
			echo $output;
			die;
			break;
		case(self::OUTPUT_STRING):
			echo "COMING SOON!";	
			break;
		case(self::OUTPUT_FILE):
			echo "COMING SOON!";
			break;
		case(self::OUTPUT_SCREEN):
		default:
			// Output to screen
			// TODO: Add syntax highlighting support
			echo
			'<h1>Parsed PHP Files</h1>'.
			'<ol><li>'.implode('</li><li>',array_keys($parsedFiles)).'</li></ol>'.
			'<h1>Output</h1>'.
			'<pre>'.implode(PHP_EOL.PHP_EOL, $parsedFiles).'</pre>';
			break;
		endswitch;
	}
}
