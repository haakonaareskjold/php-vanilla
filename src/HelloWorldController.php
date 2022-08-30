<?php
declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;

class HelloWorldController
{

    public function __construct(
        public ResponseInterface $response,
    ) {}

    public function __invoke(): ResponseInterface
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $response->getBody()
            ->write("hello world");

        return $response;
    }
}