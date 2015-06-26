# sortable coding challenge
http://sortable.com/challenge/
Usage: php product_matcher.php productfile listingsfile outputfile  
*NOTE: I Had to install php-json lib on cygwin to get json_encode/decode to work*  
Loads JSON product file  
Loads JSON listings file  
Splits the product name into words  
For each listing  
--Tries to match each product to the first 50 characters listing title  
--If a product matches all words in the correct order and has the most characters match, then the listing it matched with the product.  
Outputs a file with JSON product name and listing on each line.

Sample output file is included.
