<?php
/**
 * Test scenario for the Code Hint Aggregator where it targets itself.
 * @package CodeHintAggregator
 * @author eCommunities
 * @license unlicense
 */
namespace eCommunities\CodeHintAggregator; 

use eCommunities\CodeHintAggregator\Aggregator;
use \Zend\Loader\StandardAutoloader;

// Ensure your include path includes Zend Framework 2
set_include_path('C:\Program Files (x86)\Zend\ZendServer\share\ZendFramework2\library');
require_once 'Zend/Loader/StandardAutoloader.php';
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->registerNamespace('eCommunities\CodeHintAggregator', __DIR__);
$loader->register();

// Include and instantiate the Aggregator class file
require_once "Aggregator.php";
$aggregator = new Aggregator();

// Set this to the root of your application library
$application_root = __DIR__;

// Traverse the reseource tree to identify PHP files
$aggregator->listFiles($application_root);

// Iterate over the identified PHP files, generate the documented class/method declarations, and output to screen.
$aggregator->output($aggregator::OUTPUT_SCREEN);
