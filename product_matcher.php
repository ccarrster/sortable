<?php
//Had to install php-json lib on cygwin
function loadJsonFile($file_name){
	$results = [];
	if(file_exists($file_name)){
		$raw_json = file_get_contents($file_name);
		$handle = fopen($file_name, "r");
		if ($handle) {
		    while (($line = fgets($handle)) !== false) {
		        $results[] = json_decode($line);
		    }
		    fclose($handle);
		} else {
		    echo("Error: Error loading file " . $file_name . "\n");
		}
	} else {
		echo("Error: File does not exist or is not readable " . $file_name . "\n");
	}
	return $results;
}
if(count($argv) == 4){
	$products = loadJsonFile($argv[1]);
	$listings = loadJsonFile($argv[2]);
	$output = $argv[3];
	echo("Products loaded: " . count($products) . "\n");
	echo("Listings loaded: " . count($listings) . "\n");
	$output_file = fopen($output, "w");
	foreach($products as $product){
		fwrite($output_file, json_encode(['product_name'=>$product->product_name, 'listings'=>[$listings[0], $listings[1]]]) . "\n");
	}
	fclose($output_file);
} else {
	echo("Invalid Command line arguments. Usage: php product_matcher.php productfile listingsfile outputfile");
}
?>