<?php

function app() {
    $path = $_SERVER["REQUEST_URI"];
    $method = $_SERVER["REQUEST_METHOD"];
    $data = "";
    $name = "";
    $email = "";
    $feedback ="";
    $error= "";
    if ($path == "/app") {
        $data = "Hello World on php!";
    } else {
        if ($path == "/app/feedback") {
            if ($method == "POST") {
                $name = $_POST['name'];
                $email = $_POST['email'];
                $feedback = $_POST['feedback'];
                if (strpos($email,"@")) {
                    $data = "$name $email $feedback";
                } else{
                    $error= "Email não possui arroba";
                    include ('feedback.php');
                }
            }
            else {
                include ('feedback.php');
            }
        }
    }
    echo $data;
}
app();
