<?php
namespace PapayaWithSugar;

class Application {
    private $requestHandlerNamespace = null;
    private $repositoriesNamespace = null;
    private $defaultUrl = null;
    private $applicationRoot = '/';
    private $config = [];

    public function getApplicationSystemPath() {
        return str_replace('/', DIRECTORY_SEPARATOR, ($_SERVER['DOCUMENT_ROOT'] . $this->applicationRoot));
    }

    private function parseConfig() {
        $configPath = ((substr($this->getApplicationSystemPath(), strrpos($this->getApplicationSystemPath(), DIRECTORY_SEPARATOR) == '/public'))) ? '../config' : '/config';
        if(file_exists($configPath)) {
            $d = dir($configPath);
            while($file = $d->read())
                if(substr($file, strrpos($file, '.')) == '.php')
                    $this->config[str_replace('.php', '', $file)] = include($configPath.'/'.$file);
        }
    }

    public function getConfig($section = null) {
        if(count($this->config) == 0)
            $this->parseConfig();
        return is_null($section) ? $this->config : $this->config[$section];
    }

    public function setApplicationRoot(string $url) {
        $this->applicationRoot = $url;
    }

    public function getApplicationRoot() {
        return $this->applicationRoot;
    }

    public function setRepositoriesNamespace(string $namespace) : void {
        $this->repositoriesNamespace = $namespace;
    }

    public function getRepositoriesNamespace() : string {
        return $this->repositoriesNamespace;
    }

    public function setRequestHandlerNamespace(string $namespace) : void {
        $this->requestHandlerNamespace = $namespace;
    }

    public function getRequestHandlerNamespace() : string {
        return $this->requestHandlerNamespace;
    }
    
    public function setDefaultUrl(string $url) : void {
        $this->defaultUrl = $url;
    }

    public function getDefaultUrl() : string {
        return $this->defaultUrl;
    }

    private function parseUri(string $uri, array $info = []) : array {
        $className = $this->getRequestHandlerNamespace();
        foreach(explode('/', $uri) as $segment)
            $className .= '\\' . ucfirst($segment);
        $className .= 'Handler';
        if(class_exists($className))
            return ['class' => $className, 'params' => $info];
        else {
            array_unshift($info, substr($uri, strrpos($uri, '/') + 1));
            $uri = substr($uri, 0, strrpos($uri, '/'));
            if($uri == '')
                return ['class' => null, 'params' => $info];
            else
                return $this->parseUri($uri, $info);
        }
    }

    private function parseRequest() {
        $requestInfo = [
            'method' => strtolower($_SERVER['REQUEST_METHOD']),
            'uri' => (trim($_SERVER['REQUEST_URI'], '/') == '') ? $this->getDefaultUrl() : trim($_SERVER['REQUEST_URI'], '/'),
            'class' => null,
            'parameters' => []
        ];
        $uriInfo = $this->parseUri($requestInfo['uri']);
        $requestInfo['class'] = $uriInfo['class'];
        if(!is_null($uriInfo['class'])) {
            $rf = new \ReflectionMethod($requestInfo['class'], $requestInfo['method']);
            $methodParameters = $rf->getParameters();
            for($i = 0;$i < count($methodParameters);$i++)
                $requestInfo['parameters'][$methodParameters[$i]->name] =isset($uriInfo['params'][$i]) ? $uriInfo['params'][$i] : null;
        }
        return $requestInfo;
    }

    public function run() {
        $request = $this->parseRequest();
        if(!is_null($request['class'])) {
            $handler = new $request['class']($this);
            return call_user_func_array([$handler, $request['method']], $request['parameters']);
        } else {
            http_response_code(404);
            die();
        }
    }

    public function getRepository($entityClassName) {
        $dsn = 'mysql:host='.$this->getConfig('database')['host'].
            ';port='.$this->getConfig('database')['port'].
            ';dbname='.$this->getConfig('database')['dbname'];
        $dbConnection = new \PDO($dsn, $this->getConfig('database')['username'], $this->getConfig('database')['password']);
        return new Repository($entityClassName, $dbConnection);
    }
}