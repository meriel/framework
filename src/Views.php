<?php




class Views{



	public function make($view, $data_array = array())
        {
        // load Twig, the template engine
        // @see http://twig.sensiolabs.org
        $twig_loader = new Twig_Loader_Filesystem(PATH_VIEWS);
        $twig = new Twig_Environment($twig_loader);
		
		//$twig->addFunction('parseText', new Twig_Function_Function('parseText'));
		//$twig->addFunction('unserializeArray', new Twig_Function_Function('unserializeArray'));


        // render a view while passing the to-be-rendered data
        return $twig->render($view . PATH_VIEW_FILE_TYPE, $data_array);
        }

}
