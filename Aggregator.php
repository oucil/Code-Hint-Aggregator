<?php 
/**
 * eCommunities Code Hint Agregator
 * @package CodeHintAggregator
 * @author Kevin Farley | eCommunities http://www.ecommunities.ca
 * @author Original Reflection Parser: motanelu http://stackoverflow.com/questions/25045114/is-it-possible-to-generate-a-code-hint-reference-for-my-application-like-phps-s?noredirect=1#comment41150252_25045114
 * @license unlicense
 */
namespace eCommunities\CodeHintAggregator;

class Aggregator {
	
	const OUTPUT_SCREEN = 1; 
	const OUTPUT_STRING = 2;
	const OUTPUT_DOWNLOAD = 3;
	const OUTPUT_FILE = 4;
	
	/**
	 * Holds the list of found php files.
	 * @var array
	 */
	private $_files = array();
	
	/**
	 * Indicates if operations should be run with verbose output.
	 * @var boolean
	 */
	private $_verbose = FALSE;
	
	/**
	 * Set the verbosity level
	 * @param boolean $verbose
	 * @return Aggregator
	 */
	public function setVerbose($verbose) {
		if ($verbose) {
			$this->_verbose = TRUE;
		} else {
			$this->_verbose = FALSE;
		}
		return $this;
	}
	
	/**
	 * Self-referencing directory tree iterator that traverses the application path root and all subdirectories for PHP files
	 * @param string $path Application root path
	 * @return Aggregator
	 */
	public function listFiles($appPath) {
		$resources = scandir($appPath);
		foreach($resources as $key => $resource) {
			if(substr($resource, 0, 1) !== '.') {
				if(is_dir($appPath . DIRECTORY_SEPARATOR . $resource)) {
					$this->listFiles($appPath . DIRECTORY_SEPARATOR . $resource);
				} elseif(strtolower(substr($resource, -4)) == '.php') {
					$tokens = token_get_all(file_get_contents($appPath . DIRECTORY_SEPARATOR . $resource,TRUE));
					$valid = FALSE;
					foreach($tokens as $token) {
						// TODO: Test effect of including T_CONST,T_DECLARE below
						if (isset($token[0]) && in_array($token[0],array(T_CLASS,T_FUNCTION,T_INTERFACE))) {
							$valid = TRUE;
							break;
						}
					}
					if (!$valid) { continue; }
					// TODO: Support non-php file extensions
					$this->_files[] = $appPath . DIRECTORY_SEPARATOR . $resource;
				}
			}
		}
		return $this;
	}
	
	/**
	 * Parses a list of input files and outputs to either the screen [default] or a target file.
	 * @param Constant $format OUTPUT_SCREEN|OUTPUT_STRING|OUTPUT_DOWNLOAD|OUTPUT_FILE
	 * @param string $filename Only required for OUTPUT_FILE
	 * @return mixed
	 */
	public function output($format,$filename=NULL) {
		// If verbosity is on, switcch format to SCREEN.
		if ($this->_verbose && $format !== self::OUTPUT_SCREEN) { echo '<h3 style="color:#900;">*** Forcing output to SCREEN ***</h3>'; flush(); $format = self::OUTPUT_SCREEN; }
		
		// Initialize curl and use it to get the output for each file, then store that files response in a temporary file, so it can be output later.
		$curl = curl_init();
		$uri = dirname($_SERVER['SCRIPT_NAME']);
		if ($uri == '.' || $uri == '\\') { $uri = '/'; } else { $uri = str_replace('\\',DIRECTORY_SEPARATOR,$uri); }
		$reflector = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$uri.'reflector.php?file=';
		$temp_file = tempnam(sys_get_temp_dir(), 'AGG');
		$reference_handle = fopen($temp_file,'w+b');
		if ($reference_handle === FALSE) { trigger_error('Failed to open file for writing!',E_USER_ERROR); }
		
		if ($this->_verbose) { echo '<h1>Opening temporary file ('.$temp_file.') for writing...</h1>'; flush(); }
		
		fwrite($reference_handle,'<?php '.PHP_EOL.PHP_EOL);
		foreach($this->_files as $fid => $file) {
			set_time_limit(10);
			curl_setopt($curl, CURLOPT_URL, $reflector.urlencode($file));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			$output = curl_exec($curl);
			
			if ($this->_verbose) { echo '<h3>'.($fid+1).'. '.$file.' Output...</h3><pre>'.$output.'</pre>'; flush(); }
			
			if($output === FALSE) {
				trigger_error('Curl error: '.curl_error($curl),E_USER_ERROR);
				die;
			} else {
				if (fwrite($reference_handle, $output.PHP_EOL.PHP_EOL) === FALSE) {
					trigger_error('Writing to temporary file failed!',E_USER_ERROR);
					die;
				}
			}
		}
		curl_close($curl);
		
		// Output to target
		switch($format):
		case(self::OUTPUT_DOWNLOAD):
			// Output to file
			$meta = stream_get_meta_data($reference_handle);
			header('HTTP/1.1 200 OK');
			header('Content-Length: '.filesize($meta['uri']));
			header('Content-Type: application/php');
			header('Content-Disposition: attachment; filename="reference_manual.php"');
			while (!feof($reference_handle)) {
				set_time_limit(10);
		        echo fread($reference_handle, 2048);
		    }
		    fclose($reference_handle);
			die;
			break;
		case(self::OUTPUT_STRING):
			$output = NULL;
			// Skip the <?php portion or the output will not be visible.
			fseek($reference_handle, 5);
			while (!feof($reference_handle)) {
				set_time_limit(10);
				$output = $output.fread($reference_handle, 2048);
			}
			fclose($reference_handle);
			return $output;
			break;
		case(self::OUTPUT_FILE):
			$meta = stream_get_meta_data($reference_handle);
			rename($meta['uri'], $filename);
			fclose($reference_handle);
			break;
		case(self::OUTPUT_SCREEN):
		default:
			// Output to screen
			// TODO: Add syntax highlighting support
			echo
			'<h1>Parsed PHP Files</h1>'.
			'<ol><li>'.implode('</li><li>',$this->_files).'</li></ol>'.
			'<h1>Output</h1>'.
			'<pre>';
			// Skip the <?php portion or the output will not be visible.
			fseek($reference_handle, 5);
			while (!feof($reference_handle)) {
				set_time_limit(10);
				echo fread($reference_handle, 2048);
			}
			echo '</pre>';
			fclose($reference_handle);
			break;
		endswitch;
	}
	
	/**
	 * Returns the file list generated from the path traversal
	 * @return array
	 */
	public function getFiles() {
		return $this->_files;
	}
}
