<?php

function gen_pubic_id() {

    $vc = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $genid = "";

    // Generate 10 character ID
    for ($x = 0; $x < 10; $x++) {
        $randomChar = $vc[rand(0, strlen($vc)-1)];
        $genid = $genid.$randomChar;
    }

    return $genid;
}

?>

