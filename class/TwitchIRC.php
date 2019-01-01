<?php

class TwitchIRC
{
	private $socket;

	public function __construct()
	{
		$this->socket = new Socket();;
	}

	public function connect($address, $port)
	{
		$this->socket->connect($address, $port);
	}

	public function send($str)
	{
		$this->socket->send($str."\r\n");
	}

	public function read()
	{
		return $this->socket->read();
	}

	public function close()
	{
		$this->socket->close();
	}
}