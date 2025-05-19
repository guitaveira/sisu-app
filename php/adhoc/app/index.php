<?php
include_once "/vendor/autoload.php";

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Feedback extends Model
{
    protected $table = 'feedback';
    public $timestamps = false;
}
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
    $method = $_SERVER["REQUEST_METHOD"];
    if ($path == "/app/feedback/create") {
        $feedback= New Feedback;
        $erro="";
        if ($method == "POST") {
            $feedback->nome=$_POST['name'];
            $feedback->email=$_POST['email'];
            $feedback->feedback= htmlspecialchars($_POST['feedback']);
            if (strpos($feedback->email,"@")) {
                if ($feedback->save()) {
                    http_response_code(302);
                    $redirect_url = "/app/feedback/view?id=$feedback->id";
                    header("Location: " . $redirect_url);
                }
            } else {
                $feedback->error="Email deve conter @";
            }
        }
        include ('views/create.php');
    }elseif ($path == "/app/feedback/view" ){
        $feedback = Feedback::find($_GET["id"]);
        if ($feedback) {
            include('views/view.php');
        }
        else echo "not found";
    }elseif ($path == "/app/feedback/index" ){
        $feedbacks = Feedback::all();
        include('views/index.php');
        $asdsad= new "\\app\Controller\FeedbackController"();
        $asdsad->{"create"}($dd);
    }
}

app();



