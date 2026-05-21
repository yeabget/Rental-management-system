<?php

class Auth {

    public static function login($user) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
    }

    public static function isLoggedIn() {

        return isset($_SESSION['user_id']);
    }

    public static function logout() {

        session_unset();
        session_destroy();
    }
}
?>