<?php
    session_start();

    $username = $_SESSION['username'];
    $email = $_SESSION['email'];
    $money = $_SESSION['money'];
    $type = $_SESSION['type'];
    $cards = $_SESSION['cards'];

    $url = $_SESSION['url'];

    //echo '<pre>';
    //print_r($cards);
    //echo '</pre>';

    $exchangeRequestSent = false;
    if (isset($_GET['replace']))
    {
        if ($_GET['replace'] == "successful")
            $exchangeRequestSent = true;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/user.css">
    <title>User Detail</title>
</head>
<body>
    <section id="header">
        <ul>
            <li><a href="../index.php<?php echo $url ?>">Home</a></li>
            <?php if ($type === "admin") {
                echo '<li><a href="../admin/createcard.php'. $url . '">Create Card</a></li>';
            }?>
            <li><a href="../accounts/signout.php">Sign Out</a></li>
        </ul>
    </section>

    <h1>User Detail</h1>
    
    <section id="user_data">
        <ul>
            <li>Username: <?php echo $username ?></li>
            <li>Email: <?php echo $email ?></li>
            <li>Money: <?php echo $money ?></li>
        </ul>
    </section>

    <section id="lists">
        <?php 
        
            if (!empty($cards)) {
                echo '<ul id="cards_list">';
                    foreach($cards as $cardName => $c) {
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
                                
                                if ($type === "user") {
                                    echo '<form method="post" action="../transactions/sell.php">';
                                        echo '<button name="sell" value="'. $c['name'] .'">SELL CARD</button>';
                                    echo '</form>';

                                    echo '<form method="post" action="../utils/send_exchange_request.php">';
                                        echo '<button name="exchange" value="'. $c['name'] .'">EXCHANGE CARD</button>';
                                    echo '</form>';
                                }

                                else {
                                    echo '<form mrthod="post" action="../admin/modify.php">';
                                        echo '<button name="card_name" value="'. $c['name'] .'">Modify</button>';
                                    echo '</form>';
                                }
                            }
                            echo '</div>';

                        echo '</li>';
                echo '</ul>';
            }
        ?>
    </section>
    <script src="../scripts/exchange.js"></script>
</body>
</html>