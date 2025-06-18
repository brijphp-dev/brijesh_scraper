 <?php 
 

function load_class($clsName){
	include_once "class/db.php";
	include_once "class/$clsName.php";
}

spl_autoload_register("load_class");

$request = $_SERVER['QUERY_STRING'];

$expld = explode('/', $request);

if(sizeof($expld) > 1){
	$clsCalled = new $expld[0]();
	if(sizeof($expld) > 2){
		$clsCalled->$expld[1]();
	}
	else{
		$clsCalled->index();
	}
}else{
	$main = new main();
	$main->loadmain();
}


?>