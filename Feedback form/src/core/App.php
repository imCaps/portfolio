<?php
/**
 * App class
 * @author alexandrshumilow
 */

namespace Core;

class App
{
    public $request;
    public $response;
    public $container;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }

    /**
     * Prepares and sends response object to client
     * @param \Core\Response $response
     */
    public function respond(Response $response) {
        $this->cleanBuffer();

        header(sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ));

        if ($response->getContent()) {
            echo $response->getContent();
        }
    }

    /**
     * Clean current output buffer
     */
    protected function cleanBuffer()
    {
        if (ob_get_level() !== 0) {
            ob_clean();
        }
    }
}