<?php namespace Meriel\View;

use Config;

class Views {

    protected $view;
    protected $data;

    public function make($view, $data = array(), $callback = null) {

        $this->view = $view;
        $this->data = $data;
        
        return $this->render($callback);
    }

    public function render(Closure $callback = null){
        
        $view_config = Config::get('view');
        
        $twig_loader = new \Twig_Loader_Filesystem($view_config['paths']);
        $twig = new \Twig_Environment($twig_loader);


        $contents = $twig->render($this->view . $view_config['file_type'], $this->data);
        
        $response = isset($callback) ? $callback($this, $contents) : null;
        
        return $response ?: $contents;
    }


}
