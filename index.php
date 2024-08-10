<?php
    include_once("utils/sessioncontroller.php");
    include_once("utils/datamanager.php");
    include_once("utils/redirect.php");
    include_once("utils/storage.php");

    session_start();
    
    $hasSessionStarted = getSessionStatus();
    
    if ($hasSessionStarted)
    {
        $userType = $_SESSION['type'];
        $userCards = $_SESSION['cards'];
        $url = $_SESSION['url'];
        $notified = $_SESSION['notified'];

        if ($notified && !isset($_GET['message_received']))
            redirectUrl('index.php' . $url . "&message_received=exchange_request");

        //$jsonFile = new JsonFile("url_data.json");
    }  
    
    $jsonFile = new JsonFile("cards.json");
    $data = $jsonFile->getDataFromJson();

    $cardsData = [];
    foreach($data as $key => $d)
        $cardsData[$d['name']] = $d;

    //echo '<pre>';
    //print_r($_SESSION);
    //echo '</pre>';

    //echo $currentUrl; // /index.php?logged_in=true?username=pedram

    $filteredCards = [];
    if (isset($_GET['card_type']))
    {
        $selectedType = $_GET['card_type'];

        //echo $_SESSION['prev_selected_type']. "\n";
        //echo isset($_SESSION['prev_selected_type']);

        //echo $selectedType;
        
        if ($selectedType !== "all")
        {
            //echo "\nWORKS\n";
            foreach ($cardsData as $cardName => $c) {
                if ($c['type'] === $selectedType)
                $filteredCards[$cardName] = $c;
            }
        
            $_SESSION['prev_selected_type'] = $selectedType;
        }

        else
            $filteredCards = $cardsData;

        //echo $url;
        //$url .= "?card_type=" . $selectedType;
        //echo $url;
        
        //$data = array('url_data' => $url);
        //$jsonFile->putDataInJson($data);
        //redirectUrl($url);
    }

    else
        $filteredCards = $cardsData;

    $randomCard = [];
    if (!empty($filteredCards))
        $randomCard = $filteredCards[array_rand($filteredCards)];

    //print_r($randomCard);
    
    $exchangeJson = new JsonFile("exchangepool.json");
    $exchangeData = $exchangeJson->getDataFromJson();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/index.css">
    <title>Home Page</title>
</head>
<body>
    <section id="header">
        <ul>
            <?php if (!$hasSessionStarted) { ?>
                <li><a href="index.php">Home</a></li>
                <li><a href="accounts/signup.php">Sign Up</a></li>
                <li><a href="accounts/signin.php">Sign In</a></li>
            <?php }
            else { ?>
                <li><a href="index.php<?php echo $url ?>">Home</a></li>
                <li><a href="accounts/user.php<?php echo $url ?>"><?php echo $_SESSION['username'] . ': ' . $_SESSION['money'] ?></a></li>
                <li><a href="accounts/signout.php">Sign Out</a></li>
            <?php } ?>
        </ul>
    </section>

    <section id="description">
        <h1>Trading Card Database</h1>
        <h2>Buy And Sell Pokémons NFTs</h2>
    </section>
    
    <?php
        if ($hasSessionStarted) {
            if ($notified) {
                echo '<section id="exchange_description">';
                    echo '<h3>Exchange Requests</h3>';
                echo '</section>';
                
                echo '<section id="exchanges">';
                    echo '<ul id="exchange_list">';
                    foreach($exchangeData as $cardName => $c) {
                        if (!in_array($c['card'], $userCards)) {
                            echo '<li>';
                                echo '<div class="card-container">';
                                    echo '<img src=' . $c['card']['image'] . '>';
                                    echo '<h4>' . $c['card']['name'] . '</h4>';

                                    echo '<ul id="detail_list">';
                                        echo '<li>⚔️ ' . $c['card']['attack'] . '</li>';
                                        echo '<li>🔰 ' . $c['card']['defense'] . '</li>';
                                        echo '<li>❤️ ' . $c['card']['hp'] . '</li>';
                                    echo '</ul>';

                                    echo '<h5>💲 ' . $c['card']['price'] . ' </h5>';
                                    
                                    echo '<form method="post" action="transactions/accept.php">';
                                        echo '<button name="accept" value="'. $c['card']['name'] .'">ACCEPT</button>';
                                    echo '</form>';
                                echo '</div>';
                            echo '</li>';
                        }
                    }
                    echo '</ul>';
                echo '</section>';
            }
        }
    ?>

    <section id="lists">
        <div id="toolbar">
            <form id="filter_form" method="get">
                <label>Filter by Type:</label>
                <select name="card_type">
                    <option value="all">All</option>
                    <option value="normal">Normal</option>
                    <option value="fire">Fire</option>
                    <option value="water">Water</option>
                    <option value="grass">Grass</option>
                    <option value="electric">Electric</option>
                    <option value="bug">Bug</option>
                    <option value="poison">Poison</option>
                </select>
                <button type="submit" id="filter_button">Apply Filter</button>
            </form>

            <?php
                if ($hasSessionStarted && !empty($filteredCards) && $userType === "user") {
                    echo '<form method="post" action="transactions/buy.php">';
                        echo '<button id="random_buy" name="buy" value="'. $randomCard['name'] .'">Random Buy</button>';
                    echo '</form>';
                }
            ?>
        </div>

        <?php 
            if (empty($filteredCards))
                echo '<h3>'. "Found None!" .'</h3>';

            else
            {
                echo '<ul id="cards_list">';
                foreach($filteredCards as $cardName => $c) {
                    echo '<li>';
                        echo '<div class="card-container">';
                            if ($hasSessionStarted)
                                echo '<a href="card.php'. $url  . '&name=' . $c['name'] . '"><img src=' . $c['image'] . '></a>';

                            else
                                echo '<a href="card.php?name=' . $c['name'] . '"><img src=' . $c['image'] . '></a>';
                            echo '<h4>' . $c['name'] . '</h4>';

                            echo '<ul id="detail_list">';
                                echo '<li>⚔️ ' . $c['attack'] . '</li>';
                                echo '<li>🔰 ' . $c['defense'] . '</li>';
                                echo '<li>❤️ ' . $c['hp'] . '</li>';
                            echo '</ul>';

                            echo '<h5>💲 ' . $c['price'] . ' </h5>';
                            
                            if ($hasSessionStarted && $userType === "user") {
                                echo '<form method="post" action="transactions/buy.php">';
                                    echo '<button id="buy" name="buy" value="'. $c['name'] .'">BUY NOW</button>';
                                echo '</form>';
                        }
                        echo '</div>';

                    echo '</li>';
                }
                echo '</ul>';
            }
        ?>
    </section>

    <section>
        <?php
            if ($hasSessionStarted && !empty($filteredCards) && $userType === "user") {
                echo '<form method="post" action="transactions/buy.php">';
                    echo '<button name="buy" value="'. $randomCard['name'] .'">Random Buy</button>';
                echo '</form>';
            }
        ?>
    </section>

    <!--<script src="./scripts/filterform.js"></script>-->
    <script src="./scripts/receivedmessage.js"></script>
</body>
</html>