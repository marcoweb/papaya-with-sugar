<?php
namespace PapayaWithSugar;

class RequestHandler {
    protected $application;

    public function __construct(Application $application) {
        $this->application = $application;
    }

    public function view(array $parameters = []) {
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        $viewsRoot = $documentRoot . ((substr($documentRoot, strrpos($documentRoot, DIRECTORY_SEPARATOR) + 1) == 'public') ? '/views' : '/public/views');
        $viewFile = $viewsRoot . strtolower(str_replace([$this->application->getRequestHandlerNamespace(), '\\', 'Handler'], ['', DIRECTORY_SEPARATOR, ''], get_class($this))) . '.php';
        $view_content = '';
        $template = 'default';
        $result = '';
        if(file_exists($viewFile)) {
            ob_start();
            foreach($parameters as $name => $value)
                $$name = $value;
            include($viewFile);
            $view_content = ob_get_clean();
            $templateFile = $viewsRoot . '/_templates/' . $template . '.php';
            if(file_exists($templateFile)) {
                ob_start();
                include($templateFile);
                $result = ob_get_clean();
            } else {
                $result = $view_content;
            }
        }
        return $result;
    }
}