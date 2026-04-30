<?php

namespace App\Core;

class App
{
  private Router $router;

  public function __construct()
  {
    $this->router = new Router();
  }

  public function run(): void
  {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];

    $this->router->dispatch($uri, $method);
  }
}
