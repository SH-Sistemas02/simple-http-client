<?php
namespace Simple;

class SimpleHttpResponse
{
    private $statusCode, $data, $contentType, $size;

    public function __construct($statusCode = 200, $contentType = null, $data = null) 
    {
        $this->statusCode = $statusCode;
        $this->contentType = $contentType;
        $this->data = $data;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function length()
    {
        return $this->size;
    }
}