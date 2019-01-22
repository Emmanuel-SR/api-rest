<?php
class Router
{
    private $request;

    private $supportedHttpMethods = array(
        "GET",
        "POST",
    );

    /**
     * Initializes a new instance of the Router class.
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function __call($name, $args)
    {
        list($route, $callback) = $args;
        if (!in_array(strtoupper($name), $this->supportedHttpMethods)) {
            $this->invalidMethodHandler();
        }
        $this->{strtolower($name)}[$this->formatRoute($route)] = $callback;
    }
    /**
     * Removes trailing forward slashes from the right of the route.
     * @param route (string)
     */
    private function formatRoute($route)
    {
        $result = rtrim($route, '/');
        if ($result === '') {
            return '/';
        }
        return $result;
    }
    private function invalidMethodHandler()
    {
        header("HTTP/1.1 405 Method Not Allowed");
    }
    private function defaultRequestHandler()
    {
        header("HTTP/1.1 404 Not Found");
    }
    /**
     * Resolves a route
     */
    public function resolve()
    {
        $methodDictionary = $this->{strtolower($this->request->getMethod())};

        $formatedRoute = $this->formatRoute($this->request->getPath());
        $callback      = $methodDictionary[$formatedRoute];
        if (is_null($callback)) {
            $this->defaultRequestHandler();
            return;
        }
        echo call_user_func_array($callback, array($this->request));
    }
    public function __destruct()
    {
        $this->resolve();
    }
}
