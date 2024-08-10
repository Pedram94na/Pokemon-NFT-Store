<?php
    include_once("../utils/redirect.php");
    include_once("../utils/datamanager.php");
    
    session_start();
    
    $url = $_SESSION['url'];

    //$adminCards = $_SESSION['cards'];
    //print_r($_SESSION['cards']);
    
    $errors = [];
    
    if (isset($_GET["errors"]))
        $errors[] = $_GET["errors"];

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        foreach ($_POST as $key => $input)
        {
            if (empty($input))
                redirectUrl(htmlspecialchars($_SERVER["PHP_SELF"]) . "?errors=Empty Fields!");
        }
        //echo "<pre>";
        //print_r($_FILES);
        //echo "</pre>";
        
        //echo "::: " . isset($_FILES["image"]);
        if (isset($_POST["url"]) && isset($_POST["name"]) && isset($_POST["attack"]) && isset($_POST["defense"]) && isset($_POST["hp"]) && isset($_POST["type"]) && isset($_POST["price"]) && isset($_POST["description"]))
        {
            unset($errors);

            $url = $_POST["url"];
            
            $name = $_POST["name"];
            $attack = $_POST["attack"];
            $defense = $_POST["defense"];
            $hp = $_POST["hp"];
            $element = $_POST["type"];
            $price = $_POST["price"];
            $description = $_POST["description"];

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
                if (!ctype_alpha($name))
                    $errors[] = "Name must only contain alphabets!";
    
                if (strlen($name) < 6 || strlen($name) > 15)
                    $errors[] = "Number of valid characters for name must be between 6 and 15!";
            
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
                //echo strlen(($description));
                if (strlen($description) > 50 || strlen($description) < 10)
                    $errors[] = "Description's must contain between 10 and 50 characters!";

                if (empty($errors))
                {
                    $cardsJson = new JsonFile("../cards.json");
                    $data = $cardsJson->getDataFromJson();

                    $cardsData = [];
                    foreach($data as $key => $d)
                        $cardsData[$d['name']] = $d;

                    //echo '<pre>';
                    //print_r($cardsData);
                    //echo '</pre>';

                    $newCard = [
                        'name' => $name,
                        'type' => $element,
                        'hp' => $hp,
                        'attack' => $attack,
                        'defense' => $defense,
                        'price' => $price,
                        'description' => $description,
                        'image' => $url
                    ];
                    $cardsData[$name] = $newCard;

                    $cardsJson->putDataInJson($cardsData);

                    $usersJson = new JsonFile("../users.json");
                    $usersData = $usersJson->getDataFromJson();

                    $usersData['admin']['cards'][$name] = $newCard;
                    $_SESSION['cards'] = $usersData['admin']['cards'];
                    
                    //echo '<pre>';
                    //print_r($_SESSION);
                    //echo '</pre>';

                    $usersJson->putDataInJson($usersData);

                    //echo "NEW CARD ADDED SUCCESSFULLY!";
                    redirectUrl(htmlspecialchars($_SERVER["PHP_SELF"]) . "?new_card=success");
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
    <title>Create Card</title>
</head>
<body>
    <section id="header">
        <ul>
            <li><a href="../index.php<?php echo $url ?>">Home</a></li>
            <li><a href="../accounts/signout.php">Sign Out</a></li>
        </ul>
    </section>

    <h1>Create A New Card</h1>

    <?php
        if (!empty($errors))
        {
            echo '<h2 id="error">Errors</h2>';
            echo '<ul id="errors_list">';
                foreach ($errors as $err)
                    echo '<li>' . $err . '</li>';
            echo '</ul>';
        }
    ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="url" name="url" placeholder="URL">
        <input type="text" name="name" placeholder="Name">
        <input type="text" name="attack" placeholder="Attack">
        <input type="text" name="defense" placeholder="Defense">
        <input type="text" name="hp" placeholder="HP">

        <select id="element" name="type" aria-placeholder="Element">
            <option value="normal">Normal</option>
            <option value="fire">Fire</option>
            <option value="water">Water</option>
            <option value="grass">Grass</option>
            <option value="poison">Poison</option>
            <option value="bug">Bug</option>
            <option value="electric">Electric</option>
        </select>

        <input type="text" name="price" placeholder="Price">
        <textarea name="description" rows="5" cols="50" placeholder="Description. . ."></textarea>

        <button type="submit">Submit</button>
        <a href="../accounts/user.php" . $url>Back</a>
    </form>
</body>
</html>