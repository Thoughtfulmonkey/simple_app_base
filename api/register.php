<?php

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
            $newusername = $jsonData['username'];
            $newPassword = $jsonData['password'];
            $invitetoken = $jsonData['token'];

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpassword);
        
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            // Check if token is valid
            $querystring = "SELECT `uid` FROM `".$prefix."invite_table` WHERE `invite_string`=:token";
            $stmt = $conn->prepare($querystring);
            $stmt->bindParam(':token', $invitetoken, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Correct password?
            if (sizeof($result) == 0){

                echo '{"result": "error", "message": "The invite is invalid or has expired"}';
                exit();
            }

            // Check if username already exists
            $stmt = $conn->prepare('SELECT `username` FROM `'.$prefix.'users` WHERE `username`=:username');
            $stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Correct password?
            if (sizeof($result) > 0){

                echo '{"result": "error", "message": "That username is taken"}';
                exit();
            }

            // Password complexity check?

            // Hash password
            $password_hash = password_hash($newPassword, PASSWORD_BCRYPT);

            // Create user
            $stmt = $conn->prepare('INSERT INTO `'.$prefix.'users` (username, password, role) VALUES(:username, :password, 1)');
            $stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
            $stmt->execute();

            // Delete invite token
            $stmt = $conn->prepare('DELETE FROM `'.$prefix.'invite_table`  WHERE `invite_string`=:token');
            $stmt->bindParam(':token', $invitetoken, PDO::PARAM_STR);
            $stmt->execute();

            // Complete
            echo '{"result": "success"}';
        
        } catch(PDOException $e) {
            echo '{"result": "error", "message": "'. $e->getMessage().'"}';
        }
    }
    else{
        echo '{"result": "error", "message":"Missing data"}';
    }

?>