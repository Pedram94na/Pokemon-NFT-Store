<?php
include_once("../utils/redirect.php");

session_start();

unset($_SESSION['usrname']);
unset($_SESSION['email']);
unset($_SESSION['money']);
unset($_SESSION['type']);
unset($_SESSION['cards']);
unset($_SESSION['notified']);
unset($_SESSION['url']);

redirectUrl("../index.php");