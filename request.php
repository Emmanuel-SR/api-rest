<?php
class Request
{
    private $method;
    private $path;
    private $pathSegments;
    private $params;
    private $body;
    private $headers;

    /**
     * Initializes a new instance of the Request class.
     * @param string|null $method
     * @param string|null $path
     * @param string|null $query
     * @param array|null $headers
     * @return void
     */
    public function __construct(?string $method = null, ?string $path = null, ?string $query  = null, ?String $headers  = null)
    {
        $this->parseMethod($method);
        $this->parsePath($path);
        $this->parseParams($query);
        $this->parseHeaders($headers);
        $this->parseBody($body);
    }

    /**
     *
     * @param string|null $path
     * @return void
     */
    private function parsePath(?string $path) : void
    {
        if (!$path) {
            $path = $_SERVER['PATH_INFO'] ?? '/';
        }
        $this->path         = $path;
        $this->pathSegments = explode('/', ltrim($path, '/'));
    }

    /**
     * Description
     * @param string|null $method
     * @return void
     */
    private function parseMethod(?string $method) : void
    {
        if (!$method) {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        }
        $this->method = $method;
    }

    /**
     * Description
     * @param string|null $query
     * @return void
     */
    private function parseParams(?string $query) : void
    {
        if (!$query) {
            $query = $_SERVER['QUERY_STRING'] ?? '';
        }
        $query = str_replace('][]=', ']=', str_replace('=', '[]=', $query));
        parse_str($query, $this->params);
    }

    private function parseHeaders(?array $headers) : void
    {
        if (!$headers) {
            $headers = array();
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $key           = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                    $headers[$key] = $value;
                }
            }
        }
        $this->headers = $headers;
    }

    private function decodeBody($body)
    {
        $first = substr($body, 0, 1);
        if ($first == '[' || $first == '{') {
            $assoc     = json_decode($body, true);
            $causeCode = json_last_error();
            if ($causeCode !== JSON_ERROR_NONE) {
                $assoc = null;
            }
        } else {
            parse_str($body, $input);
            foreach ($input as $key => $value) {
                if (substr($key, -9) == '__is_null') {
                    $input[substr($key, 0, -9)] = null;
                    unset($input[$key]);
                }
            }
            $assoc = $input;
        }
        return $assoc;
    }
    private function parseBody(?string $body) : void
    {
        if (!$body) {
            $body = file_get_contents('php://input');
        }
        $this->body = $this->decodeBody($body);
    }

    public function addHeader(string $key, string $value):void
    {
        $this->headers[$key] = $value;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getMethod()
    {
        return $this->method;
    }
    public function getParams()
    {
        return $this->params;
    }
    public function getHeaders()
    {
        return $this->headers;
    }
    public function getBody() /*: ?array*/
    {
        return $this->body;
    }
    public function setBody($body) /*: void*/
    {
        $this->body = $body;
    }
    public function getPathSegment($part)
    {
        if ($part < 0 || $part >= count($this->pathSegments)) {
            return '';
        }
        return $this->pathSegments[$part];
    }

    public function headersToString()
    {
        $str = "";
        foreach ($this->headers as $key => $value) {
            $str .= "$key: $value\n";
        }
        return $str;
    }
}
