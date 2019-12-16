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


class Headers{
    
    
    protected $headers = array();
    
    protected static $special = array(
        'CONTENT_TYPE',
        'CONTENT_LENGTH',
        'PHP_AUTH_USER',
        'PHP_AUTH_PW',
        'PHP_AUTH_DIGEST',
        'AUTH_TYPE'
    );
    /**
     * Constructor.
     *
     * @param array $headers An array of HTTP headers
     *
     * @api
     */
    public function __construct(array $headers = array())
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }
    
    
    public function replace(array $headers = array())
    {
        $this->headers = array();
        $this->add($headers);
    }
    
    
    public function add(array $headers)
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }
    
    
    public function get($key, $default = null, $first = true)
    {
        $key = strtr(strtolower($key), '_', '-');

        if (!array_key_exists($key, $this->headers)) {
            if (null === $default) {
                return $first ? null : array();
            }

            return $first ? $default : array($default);
        }

        if ($first) {
            return count($this->headers[$key]) ? $this->headers[$key][0] : $default;
        }

        return $this->headers[$key];
    }
    
    
    
    public function set($key, $values, $replace = true)
    {
        $key = strtr(strtolower($key), '_', '-');

        $values = array_values((array) $values);

        if (true === $replace || !isset($this->headers[$key])) {
            $this->headers[$key] = $values;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $values);
        }

    }
    
    public function all()
    {
        return $this->headers;
    }
    
    
    public function has($key)
    {
        return array_key_exists(strtr(strtolower($key), '_', '-'), $this->headers);
    }
    
    
    public static function getHeaders($data){
        $results = array();
        foreach ($data as $key => $value) {
            $key = strtoupper($key);
            if (strpos($key, 'X_') === 0 || strpos($key, 'HTTP_') === 0 || in_array($key, static::$special)) {
                if ($key === 'HTTP_CONTENT_LENGTH') {
                    continue;
                }
                $results[$key] = $value;
            }
        }

        return $results;
    }
}