<?php





header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, x-emeyz-key, x-dev-key, x-access-token");


require_once(__DIR__.'/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

\Atabasch\Init::run();


$urls = [
    $_SERVER['REQUEST_SCHEME'] . '://' .'emeyz.com',
    $_SERVER['REQUEST_SCHEME'] . '://' .'emeyz.asw',
    $_SERVER['REQUEST_SCHEME'] . '://' .'localhost/emeyz'
];

$namespace = "Atabasch";
$controllerPre = "\\". $namespace ."\\Controllers\\";


$fullPath   = $_SERVER['REQUEST_SCHEME'] . '://' . str_replace('//', '/', ($_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI']));
$fullPath   = str_replace($urls, '', $fullPath);
//$path       = trim(preg_replace('/^\//i', '', $fullPath));
$path       = trim(preg_replace('/^\//i', '', $_SERVER['REQUEST_URI']));

$pathParts  = explode("?", $path);
$pathParts  = explode('/', $pathParts[0]);

$controllerFolderPath = __DIR__.'/src/Controllers/'.$pathParts[0];

if(file_exists($controllerFolderPath) && @is_dir($controllerFolderPath)){
    $controllerPre .= $pathParts[0] .'\\';

    array_shift($pathParts);
}


if(count($pathParts) < 2){
    $pathParts[1] = "index";
}

$className = ucfirst(!isset($pathParts[0])? 'main' : $pathParts[0]).'Controller';
$controllerName = $controllerPre . $className ;
$controllerName = str_replace('\\\\', '\\', $controllerName);
$methodName     = !$pathParts[1]? 'index' : $pathParts[1];

array_shift($pathParts);
array_shift($pathParts);


$controller = new $controllerName;
if(method_exists($controller, $methodName)){
    $runMethod  = [$controller, $methodName];
}else{
    $runMethod  = [$controller, "index"];
    array_unshift($pathParts, $methodName);
}
call_user_func_array($runMethod, $pathParts);

?>
