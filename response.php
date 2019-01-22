<?php

class Response
{
    private $status;

    private $headers;

    private $body;

    public function __construct($status, $body)
    {
        $this->status  = $status;
        $this->headers = array();
        $this->parseBody($body);
    }
    private function parseBody($body)
    {
        if ($body === '') {
            $this->body = '';
        } else {
            $data = json_encode($body);
            $this->addHeader('Content-Type', 'application/json');
            $this->addHeader('Content-Length', strlen($data));
            $this->body = $data;
        }
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getBody()
    {
        return $this->body;
    }
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }
    public function getHeader($key)
    {
        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        }
        return null;
    }
    public function getHeaders()
    {
        return $this->headers;
    }
    public function output()
    {
        http_response_code($this->getStatus());
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        echo $this->getBody();
    }
    public function __toString()
    {
        $str = "$this->status\n";
        foreach ($this->headers as $key => $value) {
            $str .= "$key: $value\n";
        }
        if ($this->body !== '') {
            $str .= "\n";
            $str .= "$this->body\n";
        }
        return $str;
    }
}
