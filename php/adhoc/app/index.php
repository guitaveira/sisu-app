<?php

function app() {
    $path = $_SERVER["REQUEST_URI"];
    $method = $_SERVER["REQUEST_METHOD"];
    $data = "";
    if ($path == "/app") {
        $data = "Hello World on php!";
    } else {
        if ($path == "/app/feedback") {
            if ($method == "POST") {
                $name = $_POST['name'];
                $email = $_POST['email'];
                $feedback = $_POST['feedback'];
                $data = "$name $email $feedback";
            }
            else {
                include ('feedback.php');
            }
        }
    }

    echo $data;
}

app();