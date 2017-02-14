<?
$method = $_SERVER['REQUEST_METHOD'];

$request = $_SERVER['REQUEST_URI'];
$request = str_replace("/collections/api.php/", "", $request);
$request = rtrim($request, "/");

$rec = split("/", $request);
#echo "REC: " . sizeof($rec) . "\n";
$itemName = "";

if(sizeof($rec) == 2){
$rec[1] = "./store/" . $rec[1];
}


if(sizeof($rec) == 3){
$itemName = $rec[2];
$rec[1] = "./store/" . $rec[1];
}

#echo "REQUEST: $request\n";

###### Implementing the functions:

# Getting all collections:
if($request == "collections"){
	echo "[";
	$temp = "";
	foreach (new DirectoryIterator("store") as $file) {
	  	if ($file->isFile()) {
			$fileName = $file->getFilename();

			if(substr($fileName, 0, 1) != "." ){
				$temp = $temp . "\"$fileName\"" . ",";
			}
 	 	}
	}
	$temp = rtrim($temp, ",");
	echo "$temp";
	echo "]\n";
}

# Create a new collection:
if($method == "POST" && sizeof($rec) == 2){
	if(!file_exists($rec[1])){
		$collectionFile = fopen($rec[1], "w");
		fclose($collectionFile); 
		echo "{\"success\" : \" Collection $rec[1] was created\"}\n";
	} else {
		echo "{\"error\" : \" Collection $rec[1] already exists\"}\n";
	}
}

# Read a collection:
if($method == "GET" && sizeof($rec) == 2){
	if(file_exists($rec[1])){
		$items = file($rec[1], FILE_IGNORE_NEW_LINES);
		echo json_encode($items);	
		echo "\n";
        } else {
                echo "{\"error\" : \" Collection $rec[1] does not exists\"}\n";
        }
}

# Update - add item:
if($method == "POST" && sizeof($rec) == 3){
	if(file_exists($rec[1])){
		$collectionFile = fopen($rec[1], "a");
		fwrite($collectionFile, $rec[2]);
		fwrite($collectionFile, "\n");
		fclose($collectionFile);
		echo "{\"success\" : \" Item $rec[2] was added to collection $rec[1]\"}\n";
		
        } else {
                echo "{\"error\" : \" Collection $rec[1] does not exists\"}\n";
        }
}

# Update - delete item:
if($method == "DELETE" && sizeof($rec) == 3){
        if(file_exists($rec[1])){
                $items = file($rec[1], FILE_IGNORE_NEW_LINES);

		$collectionFile = fopen($rec[1], "w");
		foreach($items as $item){
			if($item != $rec[2]){
				fwrite($collectionFile, $item);
				fwrite($collectionFile, "\n");	
			}
		}

		fclose($collectionFile);
		 echo "{\"success\" : \" All appearances of item $rec[2] in collection $rec[1] were deleted\"}\n";
        } else {
                echo "{\"error\" : \" Collection $rec[1] does not exists\"}\n";
        }
}

# Delete Collection:
if($method == "DELETE" && sizeof($rec) == 2){
	if(file_exists($rec[1])){
		unlink($rec[1]);
                echo "{\"success\" : \" Collection $rec[1] was successfully deleted\"}\n";

        } else {
                echo "{\"error\" : \" Collection $rec[1] does not exists and cant be deleted\"}\n";
        }
}
?>
