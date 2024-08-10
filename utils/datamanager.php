<?php
include_once("storage.php");

class CardData {
    public function cardExists($cardsData, $cardName)
    {
        if (array_key_exists($cardName, $cardsData))
            return true;

        return false;
    }
}

class UserData {
    public function userExists($usersData, $username, $email) {
        if (array_key_exists($username, $usersData))
            return true;

        foreach ($usersData as $userKey => $u)
            if ($email == $u['email'])
                return true;

        return false;
    }
}