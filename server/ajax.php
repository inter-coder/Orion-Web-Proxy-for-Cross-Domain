<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
ini_set('error_reporting', E_ALL );
$query="(curl ".$_POST['ocbIP'].":".$_POST['ocbPort']."/ngsi10/".$_POST['context']." -s -S --header 'Content-Type: application/json' --header 'Accept: application/json' -d @- | python -mjson.tool)";
$query=$query.' <<EOF 
'.$_POST['data'].' 
EOF';
$output = shell_exec($query);
die($output);
?>
