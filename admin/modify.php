<?php
    include_once("../utils/redirect.php");
    include_once("../utils/datamanager.php");
    
    session_start();
    
    $url = $_SESSION['url'];
    
    if (isset($_GET["errors"]))
        $errors[] = $_GET["errors"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        foreach ($_POST as $key => $input)
        {
            if (empty($input))
                redirectUrl(htmlspecialchars($_SERVER["PHP_SELF"]) . "?errors=Empty Fields!");
        }

        if (isset($_POST["attack"]) && isset($_POST["defense"]) && isset($_POST["hp"]) && isset($_POST["price"])) {
            $attack = $_POST["attack"];
            $defense = $_POST["defense"];
            $hp = $_POST["hp"];
            $price = $_POST["price"];

            $hasEmptyField = false;
            foreach ($_POST as $key => $input)
                if (empty($input))
                {
                    $errors[] = "Empty fields!";
                    $hasEmptyField = true;
                    break;
                }

            if (!$hasEmptyField)
            {
                if (!filter_var($attack, FILTER_VALIDATE_INT))
                    $errors[] = "Attack must be a whole number!";
    
                else if ($attack <= 0)
                    $errors[] = "Attack must be a positive number!";
    
                if (!filter_var($defense, FILTER_VALIDATE_INT))
                    $errors[] = "Defense must be a whole number!";
    
                else if ($defense <= 0)
                    $errors[] = "Defense must be a positive number!";
                
                if (!filter_var($hp, FILTER_VALIDATE_INT))
                    $errors[] = "HP must be a whole number!";
        
                else if ($hp <= 0)
                    $errors[] = "HP must be a positive number!";
    
                if (!filter_var($price, FILTER_VALIDATE_INT))
                    $errors[] = "Price should be a number!";
    
                else if ($price <= 0)
                    $errors[] = "Price should be positive!";

                if (empty($errors))
                {
                    $cardsJson = new JsonFile("../cards.json");
                    $cardsData = $cardsJson->getDataFromJson();

                    $cardName = $_GET['card_name'];
                    //echo $cardName;
                    //print_r($cardsData[$cardName]);
                    //print_r(($cardsData));

                    
                    $modifiedCard = [
                        'name' => $cardName,
                        'type' => $cardsData[$cardName]['type'],
                        'hp' => intval($hp),
                        'attack' => intval($attack),
                        'defense' => intval($defense),
                        'price' => intval($price),
                        'description' => $cardsData[$cardName]['description'],
                        'image' => $cardsData[$cardName]['image']
                    ];
                    
                    //unset($cardsData[$cardName]);
                    $cardsData[$cardName] = $modifiedCard;
                    
                    $cardsJson->putDataInJson($cardsData);
                    
                    $usersJson = new JsonFile("../users.json");
                    $usersData = $usersJson->getDataFromJson();
                    //print_r($usersData);
                    $usersData['admin']['cards'][$cardName] = $modifiedCard;

                    $usersJson->putDataInJson($usersData);

                    $_SESSION['cards'][$cardName] = $modifiedCard;
                    
                    redirectUrl(htmlspecialchars("../accounts/user.php" . $url));
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/admin.css">
    <title>Modify Card</title>
</head>
<body>
    <h1>Modify The Card</h1>

    <?php
        
        if (!empty($errors))
        {
            echo '<h2>ERRORS</h2>';
            
            echo '<ul>';

                foreach ($errors as $err)
                    echo '<li>' . $err . '</li>';

            echo '</ul>';
        }
    ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="attack" placeholder="Attack">
        <input type="text" name="defense" placeholder="Defense">
        <input type="text" name="hp" placeholder="HP">
        <input type="text" name="price" placeholder="Price">

        <button type="submit">Submit</button>
        <a href="../accounts/user.php" . $url>Back</a>
    </form>
</body>
</html>