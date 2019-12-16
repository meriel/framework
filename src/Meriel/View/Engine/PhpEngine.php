<?php
/*
 * This file is part of the Meriel package.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meriel\View\Engine;

use Config;

class PhpEngine implements EngineInterface
{

    public function __construct()
    {

    }

    public function getEngine()
    {
        return $this;
    }

    public function render($template, $data)
    {
        $view_config = Config::get('view');

        if ($data) {
            foreach ($data as $key => $val) {
                ${$key} = $val;
            }
        }


        ob_start();

        require_once $view_config['paths'].$template;

        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

}