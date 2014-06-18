<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Requests {

    public function method() {
        return $this->server('REQUEST_METHOD');
    }

    public function ajax() {
        $x = $this->server('HTTP_X_REQUESTED_WITH');
        if (isset($x) && strtolower($x) === 'xmlhttprequest') {
            return true;
        }
        return false;
    }

    public function header($key = null, $default = null) {
        $headers = array();
        foreach ($_SERVER as $k => $value) {
            if (strpos($k, 'HTTP_') === 0) {
                $headers[str_replace(' ', '', strtoupper(substr($k, 5)))] = $value;
            }
        }
        return isset($headers[$key]) ? $headers[$key] : null;
    }

    public function is($path) {
        
    }

    public function isJson() {
        return str_contains($this->header('CONTENT_TYPE'), '/json');
    }

    public function server($env) {
        return $_SERVER[$env];
    }

    public function uri() {

        if (null !== $qs = $this->getQueryString()) {
            $qs = '?' . $qs;
        }

        return "http" . (($this->server('SERVER_PORT') == 443) ? "s://" : "://") . $this->server('HTTP_HOST') . $this->server('REQUEST_URI') . $qs;
    }

    public function path() {
        return isset($_GET['url']) ? "/" . rtrim($_GET['url'], '/') : '/';
    }

    private function getQueryString() {

        $qs = $this->server('QUERY_STRING');

        return '' === $qs ? null : $qs;
    }

}
