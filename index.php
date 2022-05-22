<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'autoloader.php';

use Cooking\Recipe\Web\Controllers\HomeController;
use Cooking\Recipe\Web\Lib\Conn;

define('APP_NAME', 'cooking-recipe');

$home = new HomeController();


function createConn(): Conn
{
    return new Conn();
}

$conn = $home->service(createConn(), $home->init(null));
$home->dispatch($conn, null);
