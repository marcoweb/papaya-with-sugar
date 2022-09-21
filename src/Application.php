<?php
namespace PapayaWithSugar;

use PapayaWithSugar\Routes\RouteManager;

class Application {
    private ?RouteManager $routeManager = null;

    public function __construct() {
        $this->routeManager = new RouteManager();
    }
}