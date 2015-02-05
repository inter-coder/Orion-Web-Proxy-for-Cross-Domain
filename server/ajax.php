<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
ini_set('error_reporting', E_ALL );
function objectToArray($d) {if (is_object($d)) {$d = get_object_vars($d);}if (is_array($d)) {return array_map(__FUNCTION__, $d);}else {return $d;}}
if($_POST['push']=="true"){
	$d=json_decode($_POST['data']);
	$p=objectToArray($d);
	$type=$p["contextElements"][0]["type"];
	$isPattern=$p["contextElements"][0]["isPattern"];
	$id=$p["contextElements"][0]["id"];
	$query="(curl ".$_POST['ocbIP'].":".$_POST['ocbPort']."/ngsi10/queryContext"." -s -S --header 'Content-Type: application/json' --header 'Accept: application/json' -d @- | python -mjson.tool)";
	$query=$query.' <<EOF 
'.'{"entities":[{"type":"'.$type.'","isPattern":"'.$isPattern.'","id":"'.$id.'"}]}'.' 
EOF';
	$output = shell_exec($query);
	$o=json_decode($output);
	$o1=objectToArray($o);
	$pp=$p["contextElements"][0]["attributes"];
	$oo=$o1["contextResponses"][0]["contextElement"]["attributes"];
	for ($i=0; $i <count($pp) ; $i++) {
		for ($x=0; $x <count($oo) ; $x++) {
			if($pp[$i]["name"]==$oo[$x]["name"] && $pp[$i]["type"]==$oo[$x]["type"]){
				array_push($oo[$x]["value"],$pp[$i]["value"]);
			}			
		}
	}	
	$p["contextElements"][0]["attributes"]=$oo;
	$p["updateAction"]="UPDATE";
	$_POST['data']=json_encode($p);
}

$query="(curl ".$_POST['ocbIP'].":".$_POST['ocbPort']."/ngsi10/".$_POST['context']." -s -S --header 'Content-Type: application/json' --header 'Accept: application/json' -d @- | python -mjson.tool)";
$query=$query.' <<EOF 
'.$_POST['data'].' 
EOF';
$output = shell_exec($query);
die($output);
?>
