<?php namespace Duffleman\baelor;

use GuzzleHttp\Client;

class BaelorAPI {

	protected $guzzle;
	protected $base_url = 'http://baelor.io/api/v0/';
	protected $key = null;

	public function __construct()
	{
		$this->guzzle = new Client;
	}

	public function login($identity, $password)
	{

	}




}