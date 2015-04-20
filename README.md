Framework
=========

This is basically a concept framework to see if I could get something with some cool functionality. This is based off a system similar to Meta Tables implemented in Lua

## Meta Tables
Meta Tables were based off those present in Lua, however with some quite noticable differences due to the neccessities of this project. Meta Tables can be accessed and modified through Array Notation or Object notation, however they can only be constructed using an array. 

There is currently 6 Meta Methods that can be utilized. These are:
- **NewIndex**  - When a new index is added
- **Modify**    - When an element is changed
- **Isset**     - When isset() is ran on a value
- **Unset**     - When unset() is ran
- **Index**     - When an element is requested that does not exist
- **Count**     - When count() is ran. This will exclude MetaMethods when counting elements

```php
$Table = new MetaTable(array(
  "__Index" => 7,
  "__Modify"=> function($self, $Params){
    return false; //Would block modifications to the Table
  },
  "25564"   => 1337
));

echo $Table->25564   //1337
$Table->25564 = 2
echo $Table["25564"] //Still 1337 becuase modifications are being blocked by the Meta Method __Modify
echo $Table["John"]  //7 since it is requesting an unknown index and the default value is 7
```




## User
The User class is basically a series of MetaTables with come neat functionality from Meta Methods. Firstly if you try and call an element that is not present (in this case account is the only thing set up) it will call a class which will return a SubMetaTable that will be nested within the original Table so it is not loaded in again next time it is requested. Secondly I have set this Account class up so that values that are changed are automagically updated in the DataBase by extending the Helper class SaveMetaTable.

```php
$User = false;
try {
	if(Session::exists(Config::get("session/session_name"))){ //See if the User is authenticated. This value stores the UserID
    	$User = new User(Session::get(Config::get("session/session_name")));  //Construct the class
	}
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    $User = false;
}

if($User !== false){
	echo $User->Data->Account->Points; //Will automagically load Account since not currently present
	$User->Data->Account->Points = 7; //Will update the server automatically
}
```
