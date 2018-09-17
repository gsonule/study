<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
date_default_timezone_set("America/Chicago");
$site_url = htmlspecialchars($_SERVER["HTTP_HOST"], ENT_QUOTES, "UTF-8");
$protocol = ($_SERVER["HTTP_X_FORWARDED_PROTO"] == "https") ? "https://" : "http://";

$host_name = $_SERVER["HTTP_HOST"];
$url       = $_SERVER["SCRIPT_NAME"];

$parts   = explode("/", $url);
$siteUrl = $protocol . $_SERVER["SERVER_NAME"];

for ($i = 0; $i < count($parts) - 1; $i++) {
    $siteUrl .= $parts[$i] . "/";
}

$siteUrl = rtrim($siteUrl, "/");
define("SITE_URL", $siteUrl);

if ($protocol == "http://" && ((strpos($host_name, "172") !== false)) || $protocol == "http://" && ((strpos($host_name, "local") !== false))) {
    define("MODE", 1);
} else {
    define("MODE", 2);
}

switch (MODE) {
case 1: // (LOCAL)

    define("DIR_SITE_ROOT", dirname(__file__));

    if ($site_url == "local.eyecheckpro.com" || $site_url == "localhost") {
        define("DB_HOST", "localhost");
        define("DB_USER", "root");
        define("DB_PASS", "root");
        define("DB_NAME", "");
        define("DB_PORT", "");
    } else {
        define("DB_HOST", "");
        define("DB_USER", "");
        define("DB_PASS", "");
        define("DB_NAME", "db");
        define("DB_PORT", "");
    }

    break;

case 2: // (PRE PRODUCTION AND PRODUCTION)
    define("ADMIN_EMAIL", serialize(array(
        "",
    )));
        // "rjager@gmail.com",
        // "lauren.harnew@eyecheck.com",
default;
    break;
}

if (!defined("DIR_SITE_ROOT")) {
    define("DIR_SITE_ROOT", "/var/www/html");
}

$url = parse_url($_ENV["DATABASE_URL"]);
define("DB_HOST", $url["host"]);
define("DB_PORT", $url["port"]);
define("DB_NAME", substr($url["path"], 1));
define("DB_USER", $url["user"]);
define("DB_PASS", $url["pass"]);

define("FROM_EMAIL", "");
define("FROM_NAME", "");
define("FROM_EMAIL_RESET_PASSWORD", "");
define("FROM_NAME_RESET_PASSWORD", "");

define("INC_URL_IMG", SITE_URL . DS . "images");
define("ECP_INC_URL_IMG", SITE_URL . DS . "assets/images");
define("SERVICEDIR", DIR_SITE_ROOT . "/Services/");
define("ECP_PATH_DIR", DIR_SITE_ROOT ."/ecp/");
define("INC_PATH_EMAIL_TEMPLATE", DIR_SITE_ROOT . DS . "email_template");
define("PRIVATEKEY", "");
define("API_URL", "http://");

define("SMTPHost", "ssl://smtp.gmail.com");
define("Port", "465");
define("Username", "gad@fa.com");
define("Password", "212");
define("SMTPAuth", "true");
define("EMAIL_RESPOND", "ADMIN");

if (!defined("ADMIN_EMAIL")) {
    define("ADMIN_EMAIL", serialize(array("sf@ga.com")));
}

$getGitVersion  = exec("git describe --all --long");
$gitBuildNumber = explode("-", $getGitVersion);
$gitBuildNumber = array_reverse($gitBuildNumber);

if (MODE == 1) {
    define("Git_Version", rand(1, 999999999));
} else {
    define("Git_Version", trim($gitBuildNumber[0]));
}
?>
