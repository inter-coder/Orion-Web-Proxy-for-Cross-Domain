Orion-Web-Proxy-for-Cross-Domain
================================

PHP & JS scripts that allows you to create crossdomain queries with JSON to the Orion (Orion Context Broker - Fi-Ware) server 

Generating query is compatible with the guidelines that were set up on the [official instruction](http://forge.fiware.org/plugins/mediawiki/wiki/fiware/index.php/Publish/Subscribe_Broker_-_Orion_Context_Broker_-_User_and_Programmers_Guide#Additional_information_and_resources)

INCLUDES
========
* PHP script - crossDomainOrionCB.php
* JS script - OCB.js

### Dependencies
cURL for PHP

```
sudo apt-get install php5-curl
```

FEATURES
========
* Entity creation - APPEND
* Entity edit - UPDATE
* Entity push data - PUSH
* Entity push data - UNSET
* Entity delete - DELETE
* Entity query
* There is also an option of using Curl applications from the console

### TODO

* Entity push data - PUSH + OBSERVER
observe data offset or limit values to trigger server event functions

### Entity creation - APPEND
```javascript
var element={"contextElements": [
	        {
	            "type": "city",
	            "isPattern": "false",
	            "id": "Rome",
	            "attributes": [
	            {
	                "name": "temperature",
	                "type": "array",
	                "value": [{
	                	"time":"00:10:34",
	                	"temp":"21.0"
	                },{
	                	"time":"02:10:34",
	                	"temp":"18.3"
	                }]
	            },
	            {
	                "name": "geolocation",
	                "type": "array",
	                "value": ["41.9100711","12.5359979"]
	            }
	            ]
	        }
	    ],
	    "updateAction": "APPEND"
	}
```
### Entity edit - UPDATE
```javascript
var element={
	    "contextElements": [
	        {
	            "type": "city",
	            "isPattern": "false",
	            "id": "Rome",
	            "attributes": [
	            {
	                "name": "geolocation",
	                "type": "array",
	                "value":["44.836647","20.361267"]
	            }
	            ]
	        }
	    ],
	    "updateAction": "UPDATE"
	}
```
### Entity edit - PUSH
```javascript
var element={
		"contextElements": [
	        {
	            "type": "city",
	            "isPattern": "false",
	            "id": "Rome",
	            "attributes": [
	            {
	                "name": "temperature",
	                "type": "array",
	                "value": [{
	                	"time":"11.12.2015 00:10:34",
	                	"temp":"21.0"
	                },{
	                	"time":"22.12.2015 02:10:34",
	                	"temp":"18.3"
	                }]
	            }
	            ]
	        }
	    ],
	    "updateAction": "PUSH"
	}
```
#### limitations:
It is not possible to insert more than one member of the set of values in single push,
but there is no limit to the depth and structure of the added data


### Entity edit - UNSET
```javascript
var element={
		"contextElements": [
	        {
	            "type": "city",
	            "isPattern": "false",
	            "id": "Rome",
	            "attributes": [
	            {
	                "name": "temperature",
	                "type": "array",
	                "value": [0,1] // series of indexes from array of values that should be removed
	            }
	            ]
	        }
	    ],
	    "updateAction": "UNSET"
	}
```
### Entity edit - DELETE
```javascript
var element={
			"contextElements": [
	        {
	            "type": "city",
	            "isPattern": "false",
	            "id": "Rome"
	        }
	    ],
	    "updateAction": "DELETE"
	}
```
### Entity query
```javascript
var element={
	    "entities": [
	        {
	            "type": "city",
	            "isPattern": "false",
	            "id": "Rome"
	        }
	    ]
	}
```
### Entity push data - PUSH + OBSERVER
Not yet tested!
The basic idea is to define metatags sets to determine how and how the observation parameters and callback functions works.

There are three types of the observer methods:
* offset -  trigger function if the new value in relation to the previous
* extreme - trigger function if the new value is out of range
* target - if the value is equal to a specified

```javascript
var element={"contextElements": [
	        {
	            "type": "city",
	            "isPattern": "false",
	            "id": "Rome",
	            "attributes": [
	            {
	                "name": "temperature",
	                "type": "array",
	                "metadatas":[{
	                	"name": "temp",
	                	"type": "observer",
	                	"value":"[{'offset':[-5,5],'callback':'function1'},{'extreme':[-25,100],'callback':'function2'}]"
	                },{
	                	"name": "batt",
	                	"type": "observer",
	                	"value":"[{'target':'15','callback':'functionBattLow'},{'target':5,'callback':'functionBattCritical'}]"
	                }],
	                "value": [{
	                	"time":"00:10:34",
	                	"temp":"21.0",
	                	"batt":"99"
	                },{
	                	"time":"02:10:34",
	                	"temp":"18.3",
	                	"batt":"99"
	                }]
	            },
	            {
	                "name": "geolocation",
	                "type": "array",
	                "value": ["41.9100711","12.5359979"]
	            }
	            ]
	        }
	    ],
	    "updateAction": "APPEND"
	}
```





AJAX query will look like this:
```javascript
$.OCB({
		url:"server/crossDomainOrionCB.php",
		parm:"some params ...",
		type:"POST",
		ocbIP:"x.x.x.x",//Specifies the Orion Server IP to send the request to. Default is the current server (localhost) 
		data:element,
		success:function(data){
			console.log(data);//return respons from Orion server
		},
		error:function(xhr,status,error){
			console.log(xhr,status,error);//retur ajax error
		}
	});
```

### Curl example from the console
With vars:
```
ocbIP="x.x.x.x" 
ocbPort="1026"
URL="http://localhost/Orion-Web-Proxy-for-Cross-Domain/server/crossDomainOrionCB.php"

curl --data 'data={"contextElements":[{"type":"city","isPattern":"false","id":"Rome"}],"updateAction":"DELETE"}&ocbIP='$ocbIP'&ocbPort='$ocbPort'
' $URL
```

Or like this:
```javascript
curl --data 'data={"contextElements":[{"type":"city","isPattern":"false","id":"Rome"}],"updateAction":"DELETE"}&ocbIP=x.x.x.x&ocbPort=1026
' http://localhost/Orion-Web-Proxy-for-Cross-Domain/server/crossDomainOrionCB.php
```
