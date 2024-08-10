<?php

    function getSessionStatus() {

        if (isset($_GET['logged_in']))
        {
            //echo "SESSION HAS STARTED!";
            return true;
        }

        return false;
    }