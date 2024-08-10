<?php

function redirectUrl($url) {
    //echo "WORKS\n\n\n";
    header("Location: " . $url);
    exit();
}