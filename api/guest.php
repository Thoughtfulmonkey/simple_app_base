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
        require './utils.php';

        try {
            $jsonData = json_decode($postdata, true);
            $loginUsername = $jsonData['username'];

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpassword);
        
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            // Check if guests are allowed
            $stmt = $conn->prepare('SELECT `value` FROM `'.$prefix.'settings` WHERE `name`="allow_guests" AND `value`="yes"');
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // guests aren't allowed
            if (sizeof($result) == 0){
                echo '{"result": "error", "message": "No guests allowed"}';
                exit();
            }

            // Search for a guest user with matching username
            $stmt = $conn->prepare('SELECT `public_id` FROM `'.$prefix.'users` WHERE `username`=:username AND `role`=3');
            $stmt->bindParam(':username', $loginUsername, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Guest found?
            if (sizeof($result) == 1){

                $_SESSION["status"] = "signedin";
                $_SESSION["pubid"] = $result[0]['public_id'];

                echo '{"result": "success"}';
            }
            else{
                // Create the guest account
                $publicID = gen_pubic_id(); // TODO: prevent duplicates

                $stmt = $conn->prepare('INSERT INTO `'.$prefix.'users` (public_id, username, password, role) VALUES(:pubid, :username, "guest", 3)');
                $stmt->bindParam(':pubid', $publicID, PDO::PARAM_STR);  
                $stmt->bindParam(':username', $loginUsername, PDO::PARAM_STR);
                $stmt->execute();

                $_SESSION["status"] = "signedin";
                $_SESSION["pubid"] = $publicID;

                echo '{"result": "success"}';
            }
            
        
        } catch(PDOException $e) {
            echo '{"result": "error", "message": "'. $e->getMessage().'"}';
        }
    }
    else{
        echo '{"result": "error", "message":"Missing data"}';
    }

?>