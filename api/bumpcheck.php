<?php

    session_start();

    // Check for being signed in
    $bump = false;
    if ( !isset($_SESSION["status"]) ){
        $bump = true;
    } elseif ( $_SESSION["status"] != "signedin" ){
        $bump = true;
    }

    if ($bump) {
        echo '{"result": "error", "message":"Not signed in"}';
        exit();
    }

?>