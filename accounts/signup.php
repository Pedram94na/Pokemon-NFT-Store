<?php
    include_once("../utils/redirect.php");
    include_once("../utils/storage.php");
    include_once("../utils/datamanager.php");

    $errors = [];
    
    if (isset($_GET["errors"]))
        $errors[] = $_GET["errors"];

    //echo htmlspecialchars($_SERVER["PHP_SELF"]);

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        foreach ($_POST as $key => $input)
        {
            if (empty($input))
                redirectUrl(htmlspecialchars($_SERVER["PHP_SELF"]) . "?errors=Empty Fields!");
        }
        
        if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["repeat_password"]))
        {
            $username = $_POST["username"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $repeatPassword = $_POST["repeat_password"];

            $userData = new UserData();

            unset($errors);
            
            if (strlen($username) < 6 || strlen($username) > 15)
                $errors[] = "Username must contain between 6 and 15 characters!";
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                $errors[] = "Invalid email format!";
            
            if (strlen($password) < 8 || strlen($password) > 32)
                $errors[] = "Password must contain 8 to 32 characters";
            
            if (!preg_match('/^(?=.*[0-9])(?=.*[!@#$%&])[A-Za-z0-9@$!&#]{8,}$/', $password))
                $errors[] = "Password must contain at least one number and one special character!";

            if ($password !== $repeatPassword)
                $errors[] = "Password doens't match!";

            if (empty($errors)) {
                $jsonFile = new JsonFile("../users.json");
                $usersData = $jsonFile->getDataFromJson();

                if (!is_array($usersData))
                    throw new Exception("userdata exception: Invalid data type!");

                $userExists = $userData->userExists($usersData, $username, $email);

                if ($userExists)
                    $errors[] = "User already exists!";

                else if (!$userExists) {
                    $newUser = [
                        'username' => $username,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'money' => 300,
                        'type' => "user",
                        'notified' => false,
                        'cards' => []
                    ];
                    $usersData[$username] = $newUser;

                    $jsonFile->putDataInJson($usersData);

                    session_start();

                    $_SESSION['username'] = $newUser['username'];
                    $_SESSION['email'] = $newUser['email'];
                    $_SESSION['money'] = $newUser['money'];
                    $_SESSION['type'] = $newUser['type'];
                    $_SESSION['notified'] = $newUser['notified'];
                    $_SESSION['cards'] = $newUser['cards'];

                    $_SESSION['url'] = "?logged_in=true?username=".$_SESSION['username'];

                    //echo "New user added!";
                    redirectUrl("../index.php" . $_SESSION['url']);
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
    <link rel="stylesheet" href="../styles/signup.css">
    <title>Sign-Up Page</title>
</head>
<body>
    <h1>Sign Up</h1>

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
        <input type="email" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
        <input type="password" name="repeat_password" placeholder="Repeat Password">

        <button type="submit">Sign Up</button>
        <a href="../index.php">Back</a>
    </form>
</body>
</html>