# PHP Code Hint Aggregator

A utility for parsing a PHP applications class/function library and compiling a list of all Namespaces, Classes, Class Methods & Parameters and their corresponding code/type hints, BUT stripping out all actual source code guts and other documentation.  

The purpose is to allow contracted developers access to a reference library for your proprietary application, supporting code hinting in applications like Eclipse, or Eclipse based forks, but without having to provide them access to your actual source code.

The Code Hint Aggregator will generate a file similar to the one that Eclipse uses for providing Code Hinting for PHP's function/class libraries (i.e. standard.php).  

### Example Output

The following is an example of the output generated when the Code Hint Agregator is aimed at itself...

```php
namespace eCommunities\CodeHintAggregator { 

	class Aggregator { 
		
		/**
		 * Set the verbosity level
		 * @param boolean $verbose
		 * @return Aggregator
		 */
		public function setVerbose($verbose) { }
		
		/**
		 * Self-referencing directory tree iterator that traverses the application path
		 * root and all subdirectories for PHP files
		 * @param string $path Application root path
		 * @return Aggregator
		 */
		public function listFiles($appPath) { }
		
		/**
		 * Parses a list of input files and outputs to either the screen [default] or a
		 * target file.
		 * @param Constant $format OUTPUT_SCREEN|OUTPUT_STRING|OUTPUT_DOWNLOAD|OUTPUT_FILE
		 * @param string $filename Only required for OUTPUT_FILE
		 * @return mixed
		 */
		public function output($format, $filename = null) { }
		
		/**
		 * Returns the file list generated from the path traversal
		 * @return array
		 */
		public function getFiles() { }
		
	} 

}
```

### How to Use
1. If your application requires it's own `autoloader`, create a PHP include file in the `/loaders/` directory and it will automatically be included by the `reflection` utility while parsing your application files.
2. Edit the `example.php` file or create a copy of it and modify the `$application_root` variable with the path for your application.
3. It is recommended that you run the utility with the default output method (`OUTPUT_SCREEN`) first, if all is well, you can use any of the other output methods available (`OUTPUT_FILE`, `OUTPUT_DOWNLOAD`, `OUTPUT_STRING`).
4. Profit!

### Known Issues
- None right now, but this is still al ALPHA so beware!

### TODO List
######(in no specific order of importance)
1. Support non `.php` file extensions
2. Support 'ignore' lists
3. Add proper Exception handling
4. Add support for:
  - Interface
  - CONST
  - public\protected parameters
5. Add syntax highlighting support
6. Namespace filtering (ignore remote resources)
