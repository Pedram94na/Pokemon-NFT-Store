<?php
    include_once("utils/sessioncontroller.php");
    include_once("utils/storage.php");

    session_start();

    $hasSessionStarted = getSessionStatus();

    if ($hasSessionStarted)
        $url = $_SESSION['url'];

    $jsonFile = new JsonFile("cards.json");
    $cardsData = $jsonFile->getDataFromJson();

    $cardsArr = array();
    foreach ($cardsData as $card)
        $cardsArr[$card["name"]] = $card;

    //print_r($cardsArr);
    //echo $_GET['name'];
    
    if (isset($_GET["name"]))
    {
        $cardName = $_GET["name"];
        
        $imageUrl = $cardsArr[$cardName]["image"];
        $description = $cardsArr[$cardName]["description"];
        $element = $cardsArr[$cardName]["type"];
        $attack = $cardsArr[$cardName]["attack"];
        $defense = $cardsArr[$cardName]["defense"];
        $hp = $cardsArr[$cardName]["hp"];
        $price = $cardsArr[$cardName]["price"];

        switch ($element) {
            case "electic":
                $backgroundColor = "yellow";
                break;

            case "fire":
                $backgroundColor = "red";
                break;

            case "grass":
                $backgroundColor = "green";
                break;

            case "water":
                $backgroundColor = "blue";
                break;

            case "bug":
                $backgroundColor = "brown";
                break;
                
            case "poison":
                $backgroundColor = "violet";
                break;

            case "normal":
                $backgroundColor = "gray";
                break;
                
            default:
                break;
        }

    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/styles/card.css">
        <title>Card Detail</title>
    </head>
    <body style="background-color: <?php echo $backgroundColor ?>;">
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
        
        
    <h1><?php echo $cardName; ?></h1>

    <section>
        <div>
            <?php echo '<img src="' . $imageUrl . '">' ?>
            <h3>Description: <?php echo $description; ?></h3>

            <ul id="detail_list">
                <?php
                    echo '<li>⚔️: ' . $attack . '</li>';
                    echo '<li>🔰: ' . $defense . '</li>';
                    echo '<li>❤️: ' . $hp . '</li>';
                    echo '<li>💲: ' . $price . '</li>';
                ?>
            </ul>
        </div>
    </section>
</body>
</html>