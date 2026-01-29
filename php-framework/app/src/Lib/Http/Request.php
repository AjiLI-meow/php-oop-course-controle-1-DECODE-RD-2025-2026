<?php

namespace App\Lib\Http;

class Request {
    private string $uri;
    private string $method;
    private array $headers;
    private ?string $body = null;

    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();
        $this->body = file_get_contents('php://input');
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function getBody(): ?string {
        return $this->body;
    }

}
