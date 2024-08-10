<?php
    include_once("../utils/sessioncontroller.php");
    include_once("../utils/datamanager.php");
    include_once("../utils/storage.php");
    include_once("../utils/redirect.php");

    session_start();

    $username = $_SESSION['username'];
    $userCards = $_SESSION['cards'];
    $url = $_SESSION['url'];

    $cardsJson = new JsonFile("../cards.json");
    $usersjson = new JsonFile("../users.json");
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['sell'])) {
            $cardName = $_POST['sell'];
            
            $userData = $usersjson->getDataFromJson();
            $soldCard = $userData[$username]['cards'][$cardName];
            
            $cardPrice = $soldCard['price'];
            $userMoney = $_SESSION['money'];
            //echo $userMoney;
            
            $userMoney += ($cardPrice * 0.9);
            
            unset($userData[$username]['cards'][$cardName]);
            $userData['admin']['cards'][$cardName] = $soldCard;
            
            //print_r($_SESSION['cards']);
            
            $usersjson->putDataInJson($userData);
            
            $cardsData = $cardsJson->getDataFromJson();
            $cardsData[$cardName] = $soldCard;
            
            $cardsJson->putDataInJson($cardsData);
            
            $_SESSION['money'] = $userMoney;
            $_SESSION['cards'] = $userData[$username]['cards'];
            
            redirectUrl("../accounts/user.php" . $url);
        }
    }
    