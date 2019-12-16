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

class EngineResolver
{

    public $resolved;

    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getResolved()
    {
        return $this->resolved->getEngine();
    }

    public function resolve()
    {

        $view_config = Config::get('view');

        foreach (['php', 'twig'] as $engine) {

            if ($view_config['engine'] == $engine) {

                $engine_class = '\\Meriel\\View\\Engine\\'.ucfirst($engine).'Engine';

                $this->resolved = new $engine_class();
                break;
            }
        }

        return $this;

    }

}