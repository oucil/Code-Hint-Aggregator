# PHP Code Hint Aggregator
==========================

A utility for parsing a PHP applications class/function library and compiling a list of all Namespaces, Classes, Class Methods & Parameters and their corresponding code/type hints, BUT stripping out all actual source code guts and other documentation.  

The purpose is to allow contracted developers access to a reference library for your proprietary application, supporting code hinting in applications like Eclipse, or Eclipse based forks, but without having to provide them access to your actual source code.

The Code Hint Aggregator will generate a file similar to the one that Eclipse uses for providing Code Hinting for PHP's function/class libraries (i.e. standard.php).  

## Example Output
```php
namespace eCommunities\CodeHintAggregator {

	class Aggregator {

		/**
		 * Self-referencing directory tree iterator that traverses the application path
		 * root and all subdirectories for PHP files
		 * @param string $path Application root path
		 * @return string
		 */
		public function listFiles($appPath) { }

		/**
		 * Parse docblocks from classes.
		 * NB: It's your responsibility to ensure that all external resources for the
		 * target application are accessible to allow proper loading, i.e. use declarations
		 * for external libraries.
		 * @param string $file
		 * @return string
		 */
		public function parseFile($file) { }

		/**
		 * Parses a list of input files and outputs to either the screen [default] or a
		 * target file.
		 * @param array $fileList
		 * @param Constant $format OUTPUT_SCREEN|OUTPUT_STRING|OUTPUT_DOWNLOAD|OUTPUT_FILE
		 * @return number
		 */
		public function output($fileList, $format) { }

	}

}
```

## TODO List
- Support non `.php` file extensions
- Support 'ignore' lists
- Add proper Exception handling
- Add CONST support, and public\protected parameter support
- Add syntax highlighting support
- Namespace filtering (ignore remote resources)
