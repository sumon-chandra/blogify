<?php

ini_set("session.use_only_cookie", 1);
ini_set("session.strict_mode", 1);

session_set_cookie_params([
    'lifetime' => 60 * 60 * 24 * 1, // 1 days
    'path' => '/',
    'domain' => 'localhost',
    'secure' => true,
    'httponly' => true
]);

session_start();

$url = basename($_SERVER["PHP_SELF"]);
$query  = $_SERVER["QUERY_STRING"];

if ($query) {
    $url .= '?' . $query;
}

$_SERVER["current_page"] = $url;

if (isset($_SESSION["user_id"])) {
    if (!isset($_SESSION["last_regeneration_time"])) {
        regenerateSessionIdLoggedIn();
    } else {
        $interval = 60 * 60 * 24 * 1;
        if (time() - $_SESSION["last_regeneration_time"] >= $interval) {
            regenerateSessionId();
        }
    }
} else {
    if (!isset($_SESSION["last_regeneration_time"])) {
        regenerateSessionId();
    } else {
        $interval = 60 * 60 * 24 * 1;
        if (time() - $_SESSION["last_regeneration_time"] >= $interval) {
            regenerateSessionId();
        }
    }
}


function regenerateSessionId()
{
    session_regenerate_id(true);
    $_SESSION["last_regeneration_time"] = time();
}

function regenerateSessionIdLoggedIn()
{
    session_regenerate_id(true);
    $userId = $_SESSION["user_id"];
    $newSessionId = session_create_id();
    $sessionId = $newSessionId . "_" . $userId;
    session_id($sessionId);

    $_SESSION["last_regeneration_time"] = time();
}
