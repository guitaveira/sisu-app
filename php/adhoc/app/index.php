<?php
include_once "vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use app\controllers\FeedbackController;

$capsule= New Capsule;
$capsule->addConnection([
    "driver"=>"pgsql",
    "host"=>"db",
    "database"=>trim(getenv("DB_DATABSE")),
    "username"=>trim(getenv("DB_USER")),
    "password"=>trim(getenv("DB_PASSWORD"))
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

function app() {
    $path = strtok($_SERVER["REQUEST_URI"],'?');
    $path_array = explode('/',$path);
    $classname= "\\app\controllers\\".ucfirst($path_array[2]).'Controller';
    $instance = new $classname();
    parse_str($_SERVER['QUERY_STRING'],$params);
    $instance->{$path_array[3]??'index'}(...array_values($params));
}

app();



