<?php namespace Meriel\Routing;

class RoutingService extends \Meriel\Support\Services {

    public function register() {
        
        $this->app['router'] = $this->app->share(function() {
            return new \Router();
        });
    }

}
