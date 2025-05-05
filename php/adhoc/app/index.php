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
        echo  "Hello World on php!";
    } else {
        if ($path == "/app/feedback") {
            if ($method == "POST") {
                $name = $_POST['name'];
                $email = $_POST['email'];
                $feedback = $_POST['feedback'];
                if (strpos($email,"@")) {
                    $dbname= trim(getenv("DB_DATABSE"));
                    $username = trim(getenv("DB_USER"));
                    $password = trim(getenv("DB_PASSWORD"));
                    $dsn= "pgsql:host=db;dbname=$dbname;port=5432";
                    $conexao = new \PDO($dsn, $username, $password);
                    $sql = "INSERT INTO feedback(nome,email,feedback)
                                 VALUES (:nome, :email,:feedback)";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bindParam(':nome', $nome);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':feedback', $feedback);
                    $stmt->execute();
                    include ('view.php');

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
}
app();
