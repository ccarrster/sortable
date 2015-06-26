<?php
//Had to install php-json lib on cygwin
//Some memmory cards list products they work with... getting false matches
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
	$matches = 0;
	$products = loadJsonFile($argv[1]);
	$listings = loadJsonFile($argv[2]);
	$output = $argv[3];
	echo("Products loaded: " . count($products) . "\n");
	echo("Listings loaded: " . count($listings) . "\n");
	$output_file = fopen($output, "w");
	$result = [];
	$clean_product_names = [];
	foreach($products as $product){
		$result[$product->product_name] = [];
		$split_product = preg_split("/[ _\-]/", $product->product_name);
		$clean_product_names[$product->product_name] = $split_product;
	}
	foreach($listings as $listing){
		$best_match = null;
		$best_match_length = 0;
		//Remove hyphens
		$clean_listing = str_replace('-', '', $listing->title);
		//Remove spaces
		$clean_listing = str_replace(' ', '', $clean_listing);
		//Restricting to the first 50 characters of the title to avoid accessories
		$short_listing = substr($clean_listing, 0, 50);
		foreach($clean_product_names as $product_name => $split_product){
			$total_match_length = 0;
			$mismatch = false;
			$internal_short_listing = $short_listing;
			foreach($split_product as $product_part){
				//Case insensitive
				$position = stripos($internal_short_listing, $product_part);
				if($position !== false){
					$part_length = strlen($product_part);
					$total_match_length = $total_match_length + $part_length;
					$internal_short_listing = substr($internal_short_listing, $position + $part_length);
				} else {
					$mismatch = true;
					break;
				}
			}
			if(!$mismatch){
				if($total_match_length > $best_match_length){
					$best_match_length = $total_match_length;
					$best_match = $product_name;
				}
			}
		}
		if($best_match != null){
			$result[$best_match][] = $listing;
			$matches = $matches + 1;
		} else {
			//echo('No match for ' . $listing->title . "\n");
		}
	}
	foreach($result as $product_name => $listing_array){
		fwrite($output_file, json_encode(['product_name'=>$product_name, 'listings'=>$listing_array]) . "\n");
	}
	fclose($output_file);
	echo('matches ' . $matches);
} else {
	echo("Invalid Command line arguments. Usage: php product_matcher.php productfile listingsfile outputfile");
}
?>