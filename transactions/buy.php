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
        if (isset($_POST['buy'])) {
            $cardName = $_POST['buy'];
            //echo $cardName;
            
            $cardsData = $cardsJson->getDataFromJson(); 
            //print_r($cardsData[$cardName]['price']);
            
            $cardPrice = $cardsData[$cardName]['price'];
            $userMoney = $_SESSION['money'];

            $userData = $usersjson->getDataFromJson();
            $cardsCount = count($userData[$username]['cards']);

            if ($cardsCount >= 5)
                echo "You can't buy any more cards!";

            else if ($userMoney >= $cardPrice)
            {
                $userMoney -= $cardPrice;
                $_SESSION['money'] = $userMoney;

                $userCards[$cardName] = $cardsData[$cardName];

                $userData[$username]['money'] = $_SESSION['money'];

                $_SESSION['cards'] = $userCards;
                $userData[$username]['cards'] = $userCards;

                $userData['admin']['money'] += $cardPrice;
                unset($userData['admin']['cards'][$cardName]);
                //print_r($userData['admin']['cards'][$cardName]);

                $usersjson->putDataInJson($userData);

                unset($cardsData[$cardName]);
                $cardsJson->putDataInJson($cardsData);

                redirectUrl("../accounts/user.php" . $url);
            } else {
                echo "Not enough money to buy the card.";
            }
        }
    }
    