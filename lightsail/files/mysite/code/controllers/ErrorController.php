<?php

use Psr\Log\LoggerInterface;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPResponse;

class ErrorController extends Controller {
  private static $dependencies = [
    'logger' => '%$' . LoggerInterface::class
  ];

  public $logger;

  private static $allowed_actions = [
    'index',
    'server',
    'client',
  ];

  public function index(){
    $this->logger->warning('Something is causing a warning 🚧');
    parent::init();
  }

  public function server(){
    user_error("Server error 😱", E_USER_WARNING);
  }

  public function client(){
    $this->setResponse(new HTTPResponse());
    $this->getResponse()->setStatusCode(400);
    $this->getResponse()->setBody('Invalid, but no server error 🤯');
    return $this->getResponse();
  }
}
