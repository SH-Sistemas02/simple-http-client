<?php

namespace Simple\Client;

use Exception;
use Simple\SimpleHttpResponse;
use Simple\Utils\FailToken;

class SimpleHttpClient
{
    private $url;

    public function __construct(string $url = null)
    {
        $this->url = $url;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @param string|array $params
     */
    public function get($params = null, FailToken $failToken = null)
    {
        if (!$failToken)
            $failToken = new FailToken;

        return $this->request("GET", $this->url, $params, null, $failToken);
    }

    /**
     * @param string|array $params
     */
    public function request(string $method = "GET", string $url, $params = null, $data = null, FailToken $failToken)
    {
        if ($url === "")
            throw new Exception("Url cannot be empty.");

        if ($method === "")
            throw new Exception("Method cannot be empty.");

        if (!filter_var($url, FILTER_VALIDATE_URL))
            throw new Exception("Url is invalid.");

        $handler = curl_init();
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_URL, $this->buildWithParams($url, $params));

        $response = curl_exec($handler);

        try {
            if (!$response) {
                $failToken->fail(curl_error($handler), curl_errno($handler));
            }

            $httpCode = curl_getinfo($handler, CURLINFO_RESPONSE_CODE);
            $contentType = curl_getinfo($handler, CURLINFO_CONTENT_TYPE);

            return new SimpleHttpResponse(
                $httpCode,
                $contentType,
                $this->content($response, $contentType)
            );
        } finally {
            curl_close($handler);
        }
    }

    /**
     * @param string|array $params
     */
    private function buildWithParams(string $url, $params)
    {
        if (!$params || $params === "")
            return $url;

        if (is_array($params))
            $params = http_build_query($params);

        if (substr($url, strlen($url) - 1) === "/")
            $url = substr($url, 0, strlen($url) - 1);

        $url = "$url?$params";

        return $url;
    }

    private function content($data, string $contentType)
    {
        switch ($contentType) {
            case "application/json":
                return json_decode($data);

            default:
                return $data;
        }
    }
}
