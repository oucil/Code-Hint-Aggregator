<?php
/**
 * Test scenario for the Code Hint Aggregator where it targets itself.
 * @package CodeHintAggregator
 * @author eCommunities
 * @license unlicense
 */
namespace eCommunities\CodeHintAggregator; 
use eCommunities\CodeHintAggregator\Aggregator;

// Include and instantiate the Aggregator class file
require_once "Aggregator.php";
$aggregator = new Aggregator();

// TODO Set Ignore Resource list
//$aggregator->ignore(array());

// Set this to the root of your application library
$application_root = __DIR__;

// Traverse the reseource tree to identify PHP files
$aggregator->listFiles($application_root);

// Iterate over the identified PHP files, generate the documented class/method declarations, and output to screen.
$aggregator->output($aggregator::OUTPUT_SCREEN);

// The alternative format is via method chaining
//$aggregator->listFiles($application_root)->output($aggregator::OUTPUT_SCREEN);
