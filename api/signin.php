<?php
    session_start();

    // https://stackoverflow.com/questions/6041741/fastest-way-to-check-if-a-string-is-json-in-php
    function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    $postdata = file_get_contents('php://input');

    if ( isJson($postdata) ){

        require './connection.php';

        try {
            $jsonData = json_decode($postdata, true);
            $loginUsername = $jsonData['username'];
            $loginPassword = $jsonData['password'];

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpassword);
        
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            $stmt = $conn->prepare('SELECT `password` FROM `'.$prefix.'users` WHERE `username`=:username');
            $stmt->bindParam(':username', $loginUsername, PDO::PARAM_STR);
            #$stmt->bindParam(':password', $loginPassword, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Password found?
            if (sizeof($result) == 1){

                // Passwords match?
                if (password_verify($loginPassword, $result[0]['password'])){

                    $_SESSION["status"] = "signedin";

                    echo '{"result": "success"}';
                }
                else {
                    echo '{"result": "error", "message": "No user with those credentials"}';
                }

                
            }
            else{
                echo '{"result": "error", "message": "No user with those credentials"}';

            }
            
        
        } catch(PDOException $e) {
            echo '{"result": "error", "message": "'. $e->getMessage().'"}';
        }
    }
    else{
        echo '{"result": "error", "message":"Missing data"}';
    }

?>