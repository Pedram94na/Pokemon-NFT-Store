<?php
    include_once("../utils/sessioncontroller.php");
    include_once("../utils/datamanager.php");
    include_once("../utils/storage.php");
    include_once("../utils/redirect.php");

    session_start();

    $username = $_SESSION['username'];
    $userCards = $_SESSION['cards'];
    $url = $_SESSION['url'];

    $usersjson = new JsonFile("../users.json");
    $exchangeJson = new JsonFile("../exchangepool.json");

    $exchangeData = $exchangeJson->getDataFromJson();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['accept'])) {
            $cardName = $_POST['accept'];

            $_SESSION['offer'] = $exchangeData[$cardName];
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (isset($_GET['deal'])) {
            $cardName = $_GET['deal'];

            $usersjson = new JsonFile("../users.json");
            $usersData = $usersjson->getDataFromJson();

            $from = $_SESSION['offer']['from'];
            $offer = $_SESSION['offer']['card'];

            $usersData[$from]['cards'][$cardName] = $usersData[$username]['cards'][$cardName];

            //print_r($usersData[$username]['cards']);
            unset($usersData[$username]['cards'][$cardName]);
            //print_r($usersData[$username]['cards']);
            
            $usersData[$username]['cards'][$offer['name']] = $offer;
            
            print_r($exchangeData[$offer['name']]);
            unset($exchangeData[$offer['name']]);
            unset($usersData[$from]['cards'][$offer['name']]);
            
            unset($_SESSION['offer']);
            
            $exchangeJson->putDataInJson($exchangeData);

            $_SESSION['cards'] = $usersData[$username]['cards'];

            if (empty($exchangeData)) {
                foreach ($usersData as $user => $u) {
                    if ($usersData[$user]['username'] === 'admin')
                        continue;
    
                    $usersData[$user]['notified'] = false;
                }

                $_SESSION['notified'] = false;

            }

            //print_r($exchangeData);
            //print_r($_SESSION['notified']);
            
            $usersjson->putDataInJson($usersData);

            redirectUrl("../accounts/user.php" . $url);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/accept.css">
    <title>Exchange Cards</title>
</head>
<body>
    <section id="header">
        <ul>
            <li><a href="../index.php<?php echo $url ?>">Home</a></li>
            <li><a href="../accounts/user.php<?php echo $url ?>"><?php echo $_SESSION['username'] . ': ' . $_SESSION['money'] ?></a></li>
            <li><a href="../accounts/signout.php">Sign Out</a></li>
        </ul>
    </section>

    <h1>Choose a card to exchange with</h1>

    <section id="lists">
        <?php 
            if (!empty($userCards)) {
                echo '<ul id="cards_list">';
                    foreach($userCards as $cardName => $c) {
                        echo '<li>';
                            echo '<div class="card-container">';
                                echo '<img src=' . $c['image'] . '>';
                                echo '<h4>' . $c['name'] . '</h4>';

                                echo '<ul id="detail_list">';
                                    echo '<li>⚔️ ' . $c['attack'] . '</li>';
                                    echo '<li>🔰 ' . $c['defense'] . '</li>';
                                    echo '<li>❤️ ' . $c['hp'] . '</li>';
                                echo '</ul>';

                                echo '<h5>💲 ' . $c['price'] . ' </h5>';
                                
                                echo '<form method="get" action="">';
                                    echo '<button name="deal" value="'. $c['name'] .'">CHOOSE CARD</button>';
                                echo '</form>';
                            echo '</div>';
                            
                        echo '</li>';
                    }
                echo '</ul>';
            }
        ?>
    </section>
</body>
</html>