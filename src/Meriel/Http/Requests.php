<?php
/*
 * This file is part of the Meriel package.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meriel\Http;


use Config;

class Requests {

    public $headers;

    public function __construct() {

        $this->headers = new Headers(Headers::getHeaders($this->server()));
    }

    /**
     * Return current server request method
     *
     * @return string
     */
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
        /* $headers = array();
          foreach ($_SERVER as $k => $value) {
          if (strpos($k, 'HTTP_') === 0) {
          $headers[str_replace(' ', '', strtoupper(substr($k, 5)))] = $value;
          }
          }
          return isset($headers[$key]) ? $headers[$key] : null; */

        if ($key) {
            return $this->headers->get($key, $default);
        }

        return $this->headers;
    }

    public function is() {
        foreach (func_get_args() as $pattern) {

            if (substr($pattern, 0, 1) !== '/') {
                $pattern = "/" . $pattern;
            }

            if ($pattern == urldecode($this->path()))
                return true;

            $pattern = preg_quote($pattern, '#');

            $pattern = str_replace('\*', '.*', $pattern) . '\z';

            return (bool) preg_match('#^' . $pattern . '#', urldecode($this->path()));
        }

        return false;
    }

    public function isJson() {
        return str_contains($this->header('CONTENT_TYPE'), '/json');
    }

    public function server($env = null) {
        if ($env)
            return isset($_SERVER[$env]) ? $_SERVER[$env] : null;
        else
            return $_SERVER;
    }

    public function uri() {

        if (null !== $qs = $this->getQueryString()) {
            $qs = '?' . $qs;
        }

        return "http" . (($this->server('SERVER_PORT') == 443) ? "s://" : "://") . $this->server('HTTP_HOST') . $this->server('REQUEST_URI') . $qs;
    }

    public function path() {
        $path_info = $this->server('PATH_INFO');
        if (isset($path_info)) {
            return $path_info;
        }
        $config = Config::get('app');
        return  str_replace($config['url'], '', $this->uri());
        //return isset($_GET['url']) ? "/" . rtrim($_GET['url'], '/') : '/';
    }

    private function getQueryString()
    {

        $qs = $this->server('QUERY_STRING');

        return '' === $qs ? null : $qs;
    }

    public function isMethod($type)
    {
        $method = $this->server('REQUEST_METHOD');
        if ($method === $type) {
            return true;
        }
        return false;
    }

}
