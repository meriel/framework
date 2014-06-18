<?php


class Responses{
    
    public $content;
    
    public function __construct($content = '', $status = 200, $headers = array()){}
    
    public function json($data = array()){
        
    }
    
   
    
    public function sendHeaders()
    {
        // headers have already been sent by the developer
        if (headers_sent()) {
            return $this;
        }
        
        return $this;
    }
    
    public function setContent($content)
    {
        if (null !== $content && !is_string($content) && 
                !is_numeric($content) && 
                !is_callable(array($content, '__toString'))) {
            throw new Exception(sprintf('The Response content must be a string or object implementing __toString(), "%s" given.', gettype($content)));
        }

        $this->content = (string) $content;

        return $this;
    }
    
    
    public function sendContent()
    {
        echo $this->content;

        return $this;
    }
}