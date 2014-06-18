<?php

class Views {

    protected $view;
    protected $data;

    public function make($view, $data = array(), $callback = null) {

        $this->view = $view;
        $this->data = $data;
        
        return $this->render($callback);
    }

    public function render(Closure $callback = null){
        
        $twig_loader = new Twig_Loader_Filesystem(PATH_VIEWS);
        $twig = new Twig_Environment($twig_loader);

        //$twig->addFunction('parseText', new Twig_Function_Function('parseText'));
        //$twig->addFunction('unserializeArray', new Twig_Function_Function('unserializeArray'));
        $contents = $twig->render($this->view . PATH_VIEW_FILE_TYPE, $this->data);
        
        $response = isset($callback) ? $callback($this, $contents) : null;
        
        return $response ?: $contents;
    }


}
