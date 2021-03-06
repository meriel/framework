<?php
/*
 * This file is part of the Meriel package.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meriel\Support;

use ReflectionClass;

abstract class Services {

    protected $app;

    public function __construct($app) {
        $this->app = $app;
    }
    
    
    abstract public function register();

}
