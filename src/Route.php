<?php
namespace PapayaWithSugar;

class Route {
    private ?string $name = null;
    private ?string $controllerName = null;
    private ?string $actionName= null;
    private ?string $pattern = null;

	function getName() {
		return $this->name;
	}
	
	function setName(string $name) : void {
		$this->name = $name;
	}

    function getControllerName() : string {
		return $this->controllerName;
	}
	
	function setControllerName(string $controllerName) : void {
		$this->controllerName = $controllerName;
	}

    function getActionName() : string {
		return $this->actionName;
	}
	
	function setActionName(string $actionName) : void {
		$this->actionName = $actionName;
	}

    function getPattern() : string {
		return $this->pattern;
	}
	
	function setPattern(string $pattern) : void {
		$this->pattern = $pattern;
	}

	public static function getRouteOfRequest(string $requestUri) : Route {
		$requestUri = trim($requestUri, '/');
		return new Route($requestUri, '', '');
	}

	public function __construct(string $pattern, string $controllerName, string $actionName, ?string $name = null) {
		$this->setPattern(pattern: $pattern);
		$this->setControllerName(controllerName: $controllerName);
		$this->setActionName(actionName: $actionName);
		if(is_null($name)) {
			$this->setName(name: $pattern);	
		} else {
			$this->setName(name: $name);
		}
	}
}