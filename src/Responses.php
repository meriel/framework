<?php

class Responses {

    protected $status;
    public $headers;
    public $content;
    protected $version;
    protected static $messages = array(
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Unused)',
        307 => '307 Temporary Redirect',
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Long',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested Range Not Satisfiable',
        417 => '417 Expectation Failed',
        418 => '418 I\'m a teapot',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported'
    );

    public function __construct($content = '', $status = 200, $headers = array()) {

        $this->setStatusCode($status);
        $this->headers = new Headers(array('Content-Type' => 'text/html'));
        $this->headers->replace($headers);
        $this->setProtocolVersion('1.0');
        $this->setContent($content);
        //$this->send();
    }

    public function setStatusCode($status) {
        $this->status = (int) $status;
    }

    public function __clone() {
        $this->headers = clone $this->headers;
    }

    public function json($data = array()) {}

    public function sendHeaders() {
        // headers have already been sent by the developer
        if (headers_sent()) {
            return $this;
        }

        header(sprintf('HTTP/%s %s', $this->version, $this->getMessageForCode($this->status)));


        // headers
        foreach ($this->headers->all() as $name => $value) {
            $_values = explode("\n", $value);
            foreach ($_values as $_val) {
                header("$name: $_val", false);
            }
        }

        return $this;
    }

    public function setContent($content) {
        if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable(array($content, '__toString'))
        ) {
            throw new Exception(sprintf('The Response content must be a string or object implementing __toString(), "%s" given.', gettype($content)));
        }



        $this->content = (string) $content;

        return $this;
    }

    public function sendContent() {
        echo $this->content;

        return $this;
    }

    public function send() {

        $this->sendHeaders();
        $this->sendContent();
    }
    
    public function setProtocolVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    public function getMessageForCode($status) {
        if (isset(self::$messages[$status])) {
            return self::$messages[$status];
        } else {
            return null;
        }
    }

}
