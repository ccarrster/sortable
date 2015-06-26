<?php
if(count($argv) == 3){
	$products = $argv[1];
	$listings = $argv[2];
	if(file_exists($products)){
	} else {
		echo("Error: Could not load product file " . $products . "\n");
	}
	if(file_exists($listings)){
	} else {
		echo("Error: Could not load listings file " . $listings . "\n");
	}
} else {
	echo("Invalid Command line arguments. Usage: php product_matcher.php productfile listingsfile");
}
?>