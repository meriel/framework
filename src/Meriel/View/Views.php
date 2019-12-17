<?php
/*
 * This file is part of the Meriel package.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meriel\View;

use Config;
use Meriel\View\Engine\EngineResolver;

class Views
{

    protected $view;
    protected $data;
    protected $engine;

    public function __construct()
    {
        $engine_resolver = new EngineResolver();
        $this->engine = $engine_resolver->resolve()->getResolved();
    }

    /**
     * @return mixed
     */
    public function getEngine()
    {
        return $this->engine;
    }

    public function make($view, $data = array(), $callback = null)
    {

        $this->view = $view;
        $this->data = $data;


        return $this->render($callback);
    }

    public function render(Closure $callback = null)
    {

        $view_config = Config::get('view');

        $contents = $this->engine->render($this->view.$view_config['file_type'], $this->data);

        $response = isset($callback) ? $callback($this, $contents) : null;

        return $response ?: $contents;
    }


}
