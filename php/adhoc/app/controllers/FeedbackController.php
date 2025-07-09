<?php
namespace app\controllers;
use app\models\Feedback;
use app\controllers\Controller;

class FeedbackController  extends Controller
{
    public  function create()
    {
        $feedback= New Feedback;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->loadForm($feedback);
            if ($feedback->save()) {
                $this->redirect('view', ['id' => $feedback->id]);
            }
        }
        include ('views/feedback/create.php');
    }
    public  function index()
    {
        $feedbacks = Feedback::all();
        $message="";
        if (isset($_SESSION['flash']) ){
            $message =$_SESSION['flash'];
            $_SESSION['flash']= "";
        }
        include('views/feedback/index.php');
    }
    public  function view($id)
    {
        $feedback = Feedback::find($id);
        if ($feedback) {
            include('views/feedback/view.php');
            return;
        }
        $this->notFound();
    }

    public  function delete($id)
    {
        $feedback =is_numeric($id)?Feedback::find($id):null;
        if ($feedback) {
            $feedback->delete();
            $_SESSION['flash'] = 'Feedback Deletado com sucesso';
            $this->redirect('index');
            return;
        }
        $this->notFound();
    }
}