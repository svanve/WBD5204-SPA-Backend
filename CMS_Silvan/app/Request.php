<?php 

namespace App;

class Request {
    private $getParams;
    private $postParams;
    private $fileParams;
    // private $headers;

    public function __construct(array $getParams)
    {
        $this->getParams = $getParams;
        $this->postParams = $_POST;
        $this->fileParams = $_FILES;

        // $this->headers = $this->parseHeaders();
    }

    public function hasInput()
    {
        return !empty($this->postParams) || !empty($this->getParams);
    }

    public function hasFile($file = null)
    {
        if (isset($file)) {
            return isset($this->fileParams[$file]);
        }

        return !empty($this->fileParams);
    }

    public function getInput()
    {
        return array_merge(
            $this->sanitize(array_merge(
                $this->getParams,
                $this->postParams,
            )),
            $this->fileParams
        );
    }

    public function only(...$params)
    {
        if (is_array($params[0])) {
            $params = $params[0];
        }

        return array_filter($this->getInput(), function($value, $param) use($params) {
            return in_array($param, $params);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function sanitize($input)
    {
        return array_map(function ($element) {
            return trim($element);
        }, $input);
    }

    // Asynchrone Programmierung, dann benutzen: Braucht man, wenn man zus. mit js arbeitet, zb. zuerst frontend validierung mit js dann mit php, meistens über JSON & AJAX Request vermittelt
    // (Verbesserungspunkt), oder auch zb asynchrones Laden der Seite

    // public function parseHeaders() 
    // {
    //     $rawHeaders = array_filter($_SERVER, function ($element) {
    //         return str_starts_with($element, 'HTTP_');
    //     }, ARRAY_FILTER_USE_KEY);

    //     foreach($rawHeaders as $key => $header) {
    //         $keyFragments
    //     }
    // }
}