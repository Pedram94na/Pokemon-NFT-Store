<?php
    include_once("sessioncontroller.php");
    include_once("datamanager.php");
    include_once("storage.php");
    include_once("redirect.php");

    session_start();

    $username = $_SESSION['username'];
    $userCards = $_SESSION['cards'];
    $url = $_SESSION['url'];

    $usersjson = new JsonFile("../users.json");
    $exchangeJson = new JsonFile("../exchangepool.json");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['exchange'])) {
            $userData = $usersjson->getDataFromJson();

            foreach ($userData as $user => $u) {
                if ($userData[$user]['username'] === $username || $userData[$user]['username'] === 'admin')
                    continue;

                $userData[$user]['notified'] = true;

                //echo $u['username'] . " " . $u['notified'];
            }

            //print_r($userData);
            $usersjson->putDataInJson($userData); // UNCOMMENT!!!!
            
            $cardName = $_POST['exchange'];
            //echo $cardName;
            
            $offer = [];
            foreach ($userCards as $card => $c) {
                if ($cardName = $c['name'])
                {
                    $offer = [
                        "from" => $username,
                        "card" => [
                            "name" => $c['name'],
                            "type" => $c['type'],
                            "hp" => $c['hp'],
                            "attack" => $c['attack'],
                            "defense" => $c['defense'],
                            "price" => $c['price'],
                            "description" => $c['description'],
                            "image" => $c['image']
                        ]
                    ];

                    break;
                }
            }

            $exchangeData = $exchangeJson->getDataFromJson();

            $exchangeData[$cardName] = $offer;

            $exchangeJson->putDataInJson($exchangeData);

            redirectUrl("../accounts/user.php" . $url . "&sent_request=successful");
        }
    }

    redirectUrl("../account/user.php" . $url . "&sent_request=failed");