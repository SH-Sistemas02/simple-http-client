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

        return $this->request(
            "GET",
            $this->url,
            $params,
            null,
            null,
            $failToken
        );
    }

    /**
     * @param string|array $params
     */
    public function post($params = null, $data = null, $contentType = "json", FailToken $failToken = null)
    {
        if (!$failToken)
            $failToken = new FailToken;

        $headers = [];
        $body = null;

        if ($data) {
            if (is_array($data)) {
                switch ($contentType) {
                    case "json":
                        array_push($headers, "Content-Type: application/json");
                        $body = json_encode($data);
                        break;

                    default:
                        $body = $data;
                }
            } else {
                switch ($contentType) {
                    case "json":
                        array_push($headers, "Content-Type: application/json");
                        $body = $data;
                        break;

                    default:
                }
            }
        }

        return $this->request(
            "POST",
            $this->url,
            $params,
            $body,
            $headers,
            $failToken
        );
    }

    /**
     * @param string|array $params
     */
    public function request(
        string $method,
        string $url,
        $params = null,
        $data = null,
        $headers = null,
        FailToken $failToken
    ) {
        if ($url === "")
            throw new Exception("Url cannot be empty.");

        if ($method === "")
            throw new Exception("Method cannot be empty.");

        if (!filter_var($url, FILTER_VALIDATE_URL))
            throw new Exception("Url is invalid.");

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_URL, $this->buildWithParams($url, $params));
        curl_setopt($handle, CURLOPT_HEADER, $headers);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data); 

        $response = curl_exec($handle);

        try {
            if (!$response) {
                $failToken->fail(curl_error($handle), curl_errno($handle));
            }

            $httpCode = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
            $contentType = curl_getinfo($handle, CURLINFO_CONTENT_TYPE);

            return new SimpleHttpResponse(
                $httpCode,
                $contentType,
                $this->content($response, $contentType)
            );
        } finally {
            curl_close($handle);
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
