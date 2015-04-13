<?php namespace Duffleman\baelor;

use Duffleman\baelor\Exceptions\BaeAPIOverloadException;
use Duffleman\baelor\Results\ResultParser;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class BaelorAPI {

    protected $guzzle;
    protected $base_url = 'https://baelor.io/api/v0/';
    protected $key = null;
    private $currentRequest;
    private $lastMethod;
    private $rateLimit = 0;
    private $remainingCalls = 0;

    public function __construct($api_key = null)
    {
        $this->guzzle = new Client;
        $this->key = $api_key;
    }

    public function __call($method, $params = [])
    {
        if (method_exists($this, $method)) {
            return call_user_func_array($this->$method, $params);
        }

        $method = $this->convertName($method);
        $this->lastMethod = $method;

        $urlExtension = '';
        if ( !empty($params)) {
            $urlExtension = '/';
            $urlExtension .= implode('/', $params);
        }

        $this->prepareRequest($method->getType(), $method->getName() . $urlExtension);
        $result = $this->process();

        return $result;
    }

    public function login($identity, $password)
    {
        $this->prepareRequest('post', 'sessions');
        $this->attachPayload([
            'identity' => $identity,
            'password' => $password,
        ]);
        $result = $this->process();
        $this->key = $result->api_key;

        return $result;
    }

    private function convertName($methodName)
    {
        return new MagicMethod($methodName);
    }

    private function prepareRequest($method, $endpoint, $headers = [])
    {
        $endpoint = $this->base_url . $endpoint;
        $this->currentRequest = $this->guzzle->createRequest($method, $endpoint, $headers);
        $this->currentRequest->setHeader('Content-Type', 'application/json');
        $this->currentRequest->setHeader('Accept', 'application/json');
        foreach ($headers as $key => $value) {
            $key = strtolower(ucwords($key));
            $this->currentRequest->setHeader($key, $value);
        }

        if ( !empty($this->key)) {
            $this->currentRequest->addHeader('Authorization', 'bearer ' . $this->key);
        }

        return $this->currentRequest;
    }

    private function attachPayload($payload)
    {
        $body = Stream::factory(json_encode($payload));
        $this->currentRequest->setBody($body);
    }

    private function process()
    {
        $response = $this->guzzle->send($this->currentRequest);
        $this->populateHeaders($response);

        $this->checkCallCount();

        $body = $response->getBody()->getContents();
        $result = ResultParser::build($body, $this->lastMethod);

        return $result;
    }

    private function checkCallCount()
    {
        if ($this->remainingCalls <= 0) {
            throw new BaeAPIOverloadException('You have hit the limit for this API key.');
        }
    }

    private function populateHeaders(Response $response)
    {
        $headers = $response->getHeaders();
        if (isset($headers['X-RateLimit-Remaining'])) {
            $this->rateLimit = intval($headers['X-RateLimit-Limit'][0]);
            $this->remainingCalls = intval($headers['X-RateLimit-Remaining'][0]);
        } else {
            $this->remainingCalls = 1;
        }
    }
}