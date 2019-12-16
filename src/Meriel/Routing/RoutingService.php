<?php
/*
 * This file is part of the Meriel package.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meriel\Routing;

class RoutingService extends \Meriel\Support\Services {

    public function register() {
        
        $this->app['router'] = $this->app->share(function() {
            return new \Router();
        });
    }

}
