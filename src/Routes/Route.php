<?php
namespace PapayaWithSugar\Routes;

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
}