<?php
namespace app\controllers;
use app\models\Feedback;
class FeedbackController
{
    public  function create()
    {
        $feedback= New Feedback;
        $erro="";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    }
    public  function index()
    {
        $feedbacks = Feedback::all();
        include('views/index.php');
    }
    public  function view($id)
    {
        $feedback = Feedback::find($id);
        if ($feedback) {
            include('views/view.php');
        }
    }

}