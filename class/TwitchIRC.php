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
		$this->close();
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
		global $log;

		$log->print('Logging as ');
		$log->print($username, COLOR_INFO);
		$log->print(' : ');

		$this->send('PASS oauth:'.$oauth);
		$this->send('NICK '.$username);

		$buffer = $this->read();
		$res = false;
		if(!empty($buffer)) {
			$bufferExp = explode(':', explode("\n", $buffer)[0]);
			if(!empty($bufferExp[2]) && trim($bufferExp[2]) !== 'Login authentication failed')
				$res = true;
		}

		$log->println($res ? 'success' : 'failed', $res ? COLOR_SUCCESS : COLOR_ERROR);

		return $res;
	}

	public function join($channel)
	{
		global $log;

		$log->print('Joining channel ');
		$log->print($channel, COLOR_INFO);
		$log->print(' : ');

		$this->send('JOIN #'.$channel);
		$res = !empty($this->read());
		if ($res)
			$this->channel = $channel;

		$log->println($res ? 'success' : 'failed', $res ? COLOR_SUCCESS : COLOR_ERROR);

		return $res;
	}

	public function part()
	{
		if(!empty($this->channel))
			$this->send('PART #'.$this->channel);
	}

	public function sendMessage($message)
	{
		if(!empty($this->channel))
			$this->send('PRIVMSG #'.$this->channel.' : '.$message);
		else
			echo '[ERROR] No channel were joined'.PHP_EOL;
	}
}