<?php namespace Duffleman\baelor;

use Duffleman\baelor\Exceptions\BaeAPIOverloadException;
use Duffleman\baelor\Exceptions\InvalidBaePIException;
use Duffleman\baelor\Exceptions\UnauthorizedBaeException;
use Duffleman\baelor\Results\Generic;
use Duffleman\baelor\Results\ResultParser;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

/**
 * The big juicy class where everything happens.
 *
 * Class BaelorAPI
 * @package Duffleman\baelor
 */
class BaelorAPI {

    /**
     * Holds our Guzzle client so all methods can access it.
     *
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;
    /**
     * Base URL for requests.
     *
     * @var string
     */
    protected $base_url = 'https://baelor.io/api/v0/';
    /**
     * Holds our API key.
     *
     * @var null
     */
    protected $key = null;
    /**
     * Holds the current request object.
     *
     * @var
     */
    private $currentRequest;
    /**
     * Holds the last MagicMethod used.
     *
     * @var
     */
    private $lastMethod;
    /**
     * Holds the current rate limit.
     *
     * @var int
     */
    private $rateLimit = 0;
    /**
     * Holds how many calls we have left to make.
     *
     * @var int
     */
    private $remainingCalls = 0;

    /**
     * Constructor.
     *
     * @param null $api_key
     */
    public function __construct($api_key = null)
    {
        $this->guzzle = new Client;
        $this->key = $api_key;
    }

    /**
     * Handles our magic calls.
     *
     * @param       $method
     * @param array $params
     * @throws \Duffleman\baelor\Exceptions\InvalidBaePIException
     * @throws \Duffleman\baelor\Exceptions\UnauthorizedBaeException
     * @internal param $metho
     */
    public function __call($method, $params = [])
    {
        // If the method already exists, call it directly.
        if (method_exists($this, $method)) {
            return call_user_func_array($this->$method, $params);
        }

        // Grab our MagicMethod object.
        $method = $this->convertName($method);
        $this->lastMethod = $method;

        // If they pass in multiple URL segments, implode them in.
        $urlExtension = '';
        if ( !empty($params)) {
            $urlExtension = '/';
            $urlExtension .= implode('/', $params);
        }

        // Prepare the request and send it out.
        $this->prepareRequest($method->getType(), $method->getName() . $urlExtension);
        $result = $this->process();

        // Return the item set.
        return $result;
    }

    /**
     * Special function to handle login.
     *
     * @param $identity
     * @param $password
     * @return \Duffleman\baelor\Results\Generic|mixed
     * @throws \Duffleman\baelor\Exceptions\InvalidBaePIException
     * @throws \Duffleman\baelor\Exceptions\UnauthorizedBaeException
     */
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

    /**
     * @param $username
     * @param $email
     * @param $password
     * @return \Duffleman\baelor\Results\Generic|mixed
     * @throws \Duffleman\baelor\Exceptions\InvalidBaePIException
     * @throws \Duffleman\baelor\Exceptions\UnauthorizedBaeException
     */
    public function createUser($username, $email, $password)
    {
        $this->prepareRequest('post', 'users');
        $this->attachPayload([
            'username'         => $username,
            'email_address'    => $email,
            'password'         => $password,
            'password_confirm' => $password
        ]);
        $user = $this->process();

        return $user;
    }

    /**
     * Quick accessor to the MagicMethod class.
     *
     * @param $methodName
     * @return \Duffleman\baelor\MagicMethod
     */
    private function convertName($methodName)
    {
        return new MagicMethod($methodName);
    }

    /**
     * Builds our Guzzle Request.
     * Loads auth if we have it.
     *
     * @param       $method
     * @param       $endpoint
     * @param array $headers
     * @return \GuzzleHttp\Message\Request|\GuzzleHttp\Message\RequestInterface
     */
    public function prepareRequest($method, $endpoint, $headers = [])
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

    /**
     * Attaches the body payload if needed. (Only really needed for login and admin functions)
     *
     * @param $payload
     */
    private function attachPayload($payload)
    {
        $body = Stream::factory(json_encode($payload));
        $this->currentRequest->setBody($body);
    }

    /**
     * Sends the actual request to baelor.io
     *
     * @throws \Duffleman\baelor\Exceptions\BaeAPIOverloadException
     * @throws \Duffleman\baelor\Exceptions\InvalidBaePIException
     * @throws \Duffleman\baelor\Exceptions\UnauthorizedBaeException
     */
    public function process()
    {
        try {
            $response = $this->guzzle->send($this->currentRequest);
        } catch (ClientException $exception) {
            if ($exception->getCode() == 403) {
                throw new UnauthorizedBaeException('API needs authentication.');
            }
            throw new InvalidBaePIException('Unknown API error.');
        } catch (Exception $exception) {
            throw new InvalidBaePIException('Unable to process request.');
        }

        $this->populateHeaders($response);

        $this->checkCallCount();

        $body = $response->getBody()->getContents();

        $result = json_decode($body);
        $result = new Generic($result);

        return $result;
    }

    /**
     * Checks to ensure we let the user know when we hit our limit.
     *
     * @throws \Duffleman\baelor\Exceptions\BaeAPIOverloadException
     */
    private function checkCallCount()
    {
        if ($this->remainingCalls <= 0) {
            throw new BaeAPIOverloadException('You have hit the limit for this API key.');
        }
    }

    /**
     * Populate the headers when we make a call.
     *
     * @param \GuzzleHttp\Message\Response $response
     */
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