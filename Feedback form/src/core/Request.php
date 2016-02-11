<?php
/**
 * Simple request object
 * @author alexandrshumilow
 */

namespace Core;


class Request
{
    /**
     * @var
     */
    public $uri;
    /**
     * @var
     */
    public $method;
    /**
     * @var string
     */
    public $content;

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->content = file_get_contents('php://input');
    }
}