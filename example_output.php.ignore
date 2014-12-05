<?php

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
