<?php
//https://github.com/ccarrster/sortable
//ccarrster@gmail.com
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

function initializeResult($products){
	$result = [];
	foreach($products as $product){
		$result[$product->product_name] = [];
	}
	return $result;
}

function splitProducts($products){
	$result = [];
	foreach($products as $product){
		$split_product = preg_split("/[ _\-]/", $product->product_name);
		$result[$product->product_name] = $split_product;
	}
	return $result;
}

function writeOutput($result, $file_name){
	$output_file = fopen($file_name, "w");
	foreach($result as $product_name => $listing_array){
		fwrite($output_file, json_encode(['product_name'=>$product_name, 'listings'=>$listing_array]) . "\n");
	}
	fclose($output_file);
}

function cleanTitle($title){
	$result = str_replace('-', '', $title);
	$result = str_replace(' ', '', $result);
	//Restricting to the first 50 characters of the title to avoid accessories
	$result = substr($result, 0, 50);
	return $result;
}

if(count($argv) == 4){

	$products = loadJsonFile($argv[1]);
	$listings = loadJsonFile($argv[2]);
	$output_file_name = $argv[3];

	$result = initializeResult($products);
	$clean_product_names = splitProducts($products);

	foreach($listings as $listing){
		$best_match = null;
		$best_match_length = 0;

		$short_listing = cleanTitle($listing->title);

		foreach($clean_product_names as $product_name => $split_product){
			$total_match_length = 0;
			$mismatch = false;
			$internal_short_listing = $short_listing;
			foreach($split_product as $product_part){
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
		}
	}

	writeOutput($result, $output_file_name);
} else {
	echo("Invalid Command line arguments. Usage: php product_matcher.php productfile listingsfile outputfile");
}
?>