 <?php 

set_time_limit(12000);

function load_class($clsName){
	include_once "class/db.php";
	include_once "class/$clsName.php";
}

spl_autoload_register("load_class");

$request = rtrim($_SERVER['QUERY_STRING'], '/');
$expld = explode('/', $request);

if( !empty($expld[0]) && sizeof($expld) >= 1){
	$clsCalled = new $expld[0]();
	if(sizeof($expld) >= 2){
		$func = $expld[1];
		$clsCalled->$func();
	}
	else{
		$clsCalled->index();
	}
}else{
	$main = new main();
	$main->loadmain();
}


?>