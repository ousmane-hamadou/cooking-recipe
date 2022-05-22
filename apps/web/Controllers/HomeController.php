<?php

declare(strict_types=1);

namespace Cooking\Recipe\Web\Controllers;

use Cooking\Recipe\Web\Lib\AbstractController;
use Cooking\Recipe\Web\Lib\Conn;

class HomeController extends AbstractController
{
    public function ressources(): array
    {
        return ['index', 'show'];
    }

    public function index(Conn $conn, mixed $opts): Conn
    {
        return $this->render($conn, 'index.php');
    }
}