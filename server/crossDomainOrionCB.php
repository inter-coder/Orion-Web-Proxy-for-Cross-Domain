<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
ini_set('error_reporting', E_ALL );


//*************************************************
//**************** CALLBACK FUNC ******************
//*************************************************
function function1($f,$l){
	echo "callback function ... ".$f." - - ".$l;
};
function function2($f,$l){
	echo "callback function ... ".$f." - - ".$l;
};
function functionBattLow($f,$l){
	echo "callback function ... ".$f." - - ".$l;
};
function functionBattCritical($f,$l){
	echo "callback function ... ".$f." - - ".$l;
};
//************************************************



$p=json_decode($_POST['data'],true);
if($p["updateAction"]=="PUSH"){$_POST['push']="true";}else{$_POST['push']="false";};
if($p["updateAction"]=="UNSET"){$_POST['unset']="true";}else{$_POST['unset']="false";};
if($p["updateAction"]!=NULL){$_POST['context']="updateContext";}else{$_POST['context']="queryContext";};

if($_POST['push']=="true"){	
	$type=$p["contextElements"][0]["type"];
	$isPattern=$p["contextElements"][0]["isPattern"];
	$id=$p["contextElements"][0]["id"];
	$query="(curl ".trim($_POST['ocbIP']).":".trim($_POST['ocbPort'])."/ngsi10/queryContext"." -s -S --header 'Content-Type: application/json' --header 'Accept: application/json' -d @- | python -mjson.tool)";
	$query=$query.' <<EOF 
'.'{"entities":[{"type":"'.$type.'","isPattern":"'.$isPattern.'","id":"'.$id.'"}]}'.' 
EOF';
	$output = shell_exec($query);
	$o1=json_decode($output,true);
	$pp=$p["contextElements"][0]["attributes"];
	$oo=$o1["contextResponses"][0]["contextElement"]["attributes"];	
	for ($i=0; $i <count($pp) ; $i++) {
		for ($x=0; $x <count($oo) ; $x++) {			
			if($pp[$i]["name"]==$oo[$x]["name"] && $pp[$i]["type"]==$oo[$x]["type"]){
				$lastValue=$oo[$x]["value"][count($oo[$x]["value"])-1];				
				$oo[$x]["value"]=array_merge($oo[$x]["value"],$pp[$i]["value"]);// fix merging old and new array of data
				//*** find observer
				$meta=$oo[$x]["metadatas"];	
				for ($m=0; $m <count($meta) ; $m++) {
					if($meta[$m]["type"]=="observer"){//if meta is observer then find what vlues to compare
						$val=$pp[$i]["value"][$meta[$m]["name"]];//inserted value
						$valLast=$lastValue[$meta[$m]["name"]];//before inserted value
						$mv= json_decode(str_replace("'",'"',$meta[$m]["value"]),true);//oberve value on this rules
						
						//now we checking ...
						for ($ob=0; $ob <count($mv) ; $ob++) { 
							foreach(array_keys($mv[$ob]) as $key){
								if($key=="offset"){
									$rez=$val-$valLast;
									if($rez<=$mv[$ob][$key][0]){
										$mv[$ob]["callback"]($val,$valLast);
									}
									if($rez>=$mv[$ob][$key][1]){
										$mv[$ob]["callback"]($val,$valLast);
									}
								}
								
								if($key=="extreme"){
									if($val<=$mv[$ob][$key][0]){
										$mv[$ob]["callback"]($val,$valLast);
									}
									if($val>=$mv[$ob][$key][1]){
										$mv[$ob]["callback"]($val,$valLast);
									}
								}
								
								if($key=="target"){
									if($val==$mv[$ob][$key]){
										$mv[$ob]["callback"]($val,$valLast);
									}
								}
							}
						}
					}				
				}
				//**** end observer
			}			
		}
	}	
	$p["contextElements"][0]["attributes"]=$oo;
	$p["updateAction"]="UPDATE";
	$_POST['data']=json_encode($p);
}

if($_POST['unset']=="true"){	
	$type=$p["contextElements"][0]["type"];
	$isPattern=$p["contextElements"][0]["isPattern"];
	$id=$p["contextElements"][0]["id"];
	$query="(curl ".trim($_POST['ocbIP']).":".trim($_POST['ocbPort'])."/ngsi10/queryContext"." -s -S --header 'Content-Type: application/json' --header 'Accept: application/json' -d @- | python -mjson.tool)";
	$query=$query.' <<EOF 
'.'{"entities":[{"type":"'.$type.'","isPattern":"'.$isPattern.'","id":"'.$id.'"}]}'.' 
EOF';
	$output = shell_exec($query);
	$o1=json_decode($output,true);
	$pp=$p["contextElements"][0]["attributes"];
	$oo=$o1["contextResponses"][0]["contextElement"]["attributes"];	
	for ($i=0; $i <count($pp) ; $i++) {
		for ($x=0; $x <count($oo) ; $x++) {
			if($pp[$i]["name"]==$oo[$x]["name"] && $pp[$i]["type"]==$oo[$x]["type"]){
				for ($s=0; $s <count($pp[$i]["value"]) ; $s++) { 
					unset($oo[$x]["value"][$pp[$i]["value"][$s]]);
				}
				$oo[$x]["value"]=array_values($oo[$x]["value"]);
			}			
		}
	}
	
	
	$p["contextElements"][0]["attributes"]=$oo;
	$p["updateAction"]="UPDATE";
	$_POST['data']=json_encode($p);
}
$query="(curl ".trim($_POST['ocbIP']).":".trim($_POST['ocbPort'])."/ngsi10/".trim($_POST['context'])." -s -S --header 'Content-Type: application/json' --header 'Accept: application/json' -d @- | python -mjson.tool)";
$query=$query.' <<EOF 
'.trim($_POST['data']).' 
EOF';
$output = shell_exec($query);
die($output);
?>
