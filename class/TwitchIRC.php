<?php

class TwitchIRC
{
	private $socket;
	private $channel;

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

	public function login($username, $oauth)
	{
		$this->send('PASS oauth:'.$oauth);
		$this->send('NICK '.$username);

		$buffer = $this->read();
		$res = false;
		if(!empty($buffer)) {
			$bufferExp = explode(':', explode("\n", $buffer)[0]);
			if(!empty($bufferExp[2]) && trim($bufferExp[2]) !== 'Login authentication failed')
				$res = true;
		}
		return $res;
	}

	public function join($channel)
	{
		$this->send('JOIN #'.$channel);
		$isJoined = !empty($this->read());
		if ($isJoined)
			$this->channel = $channel;
		return $isJoined;
	}
}