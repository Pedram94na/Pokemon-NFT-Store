<?php
    include_once("../utils/storage.php");
    include_once("../utils/redirect.php");

    $errors = [];
    
    if (isset($_GET["errors"]))
        $errors[] = $_GET["errors"];

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        foreach ($_POST as $key => $input)
        {
            if (empty($input))
            {
                redirectUrl(htmlspecialchars($_SERVER["PHP_SELF"]) . "?errors=Empty Fields!");
                exit();
            }
        }
        
        if (isset($_POST["username"]) && isset($_POST["password"]))
        {
            $username = $_POST["username"];
            $password = $_POST["password"];
            
            $usersJson = new JsonFile("../users.json");
            $usersData = $usersJson->getDataFromJson();
            
            unset($errors);
            if (!array_key_exists($username, $usersData))
                $errors[] = "Username doesn't exist!";

            else if (!password_verify($password, $usersData[$username]['password']))
                $errors[] = "Wrong Password!";
                
            else {
                session_start();

                $_SESSION['username'] = $username;
                $_SESSION['email'] = $usersData[$username]['email'];
                $_SESSION['money'] = $usersData[$username]['money'];
                //echo $usersData[$username]['money'];
                $_SESSION['type'] = $usersData[$username]['type'];
                $_SESSION['notified'] = $usersData[$username]['notified'];
                $_SESSION['cards'] = $usersData[$username]['cards'];
                
                //echo '<pre>';
                //print_r($_SESSION['cards']);
                //echo '</pre>';

                //echo "LOGGED IN";

                $_SESSION['url'] = "?logged_in=true?username=".$_SESSION['username'];
                
                redirectUrl("../index.php" . $_SESSION['url']);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/signin.css">
    <title>Sign-In Page</title>
</head>
<body>
    <h1>Sign In</h1>

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

    <form action="" method="post">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Sign In</button>
        <a href="../index.php">Back</a>
    </form>
</body>
</html>