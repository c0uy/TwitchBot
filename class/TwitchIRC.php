<?php

class TwitchIRC
{
	private $socket;
	private $channel;

	public function __construct()
	{
		$this->socket = new Socket();
	}

	/**
	 * Connects to server (closes previous connexion)
	 * @param string $address
	 * @param integer $port
	 */
	public function connect($address, $port)
	{
		$this->close();
		$this->socket->connect($address, $port);
	}

	/**
	 * Sends string to server
	 * @param string $str
	 */
	public function send($str)
	{
		$this->socket->send($str . "\r\n");
	}

	/**
	 * Reads received message
	 * @return string
	 */
	public function read()
	{
		return $this->socket->read();
	}

	/**
	 * Closes connexion
	 */
	public function close()
	{
		$this->socket->close();
	}

	/**
	 * Checks if received message is a PING string
	 * @param string $str
	 * @return bool
	 */
	public function isPing($str)
	{
		return strpos($str, 'PING') === 0;
	}

	/**
	 * Sends PONG answer to PING query
	 */
	public function sendPong()
	{
		global $log;

		$log->println('Ping/Pong', 'BROWN');
		$this->send('PONG :tmi.twitch.tv');
	}

	/**
	 * Authenticates user
	 * @param string $username
	 * @param string $oauth
	 * @return bool
	 */
	public function login($username, $oauth)
	{
		global $log;

		$log->print('Logging as ');
		$log->print($username, COLOR_INFO);
		$log->print(' : ');

		$this->send('PASS oauth:' . $oauth);
		$this->send('NICK ' . $username);

		$buffer = $this->read();
		$res = false;
		if (!empty($buffer)) {
			$bufferExp = explode(':', explode("\n", $buffer)[0]);
			if (!empty($bufferExp[2]) && trim($bufferExp[2]) !== 'Login authentication failed')
				$res = true;
		}

		$log->println($res ? 'success' : 'failed', $res ? COLOR_SUCCESS : COLOR_ERROR);

		return $res;
	}

	/**
	 * Sends JOIN command
	 * @param string $channel
	 * @return bool
	 */
	public function join($channel)
	{
		global $log;

		$log->print('Joining channel ');
		$log->print($channel, COLOR_INFO);
		$log->print(' : ');

		$this->send('JOIN #' . $channel);
		$res = !empty($this->read());
		if ($res)
			$this->channel = $channel;

		$log->println($res ? 'success' : 'failed', $res ? COLOR_SUCCESS : COLOR_ERROR);

		return $res;
	}

	/**
	 * Leaves joined channel
	 */
	public function part()
	{
		if (!empty($this->channel))
			$this->send('PART #' . $this->channel);
	}

	/**
	 * Sends chat message to joined channel
	 * @param string $message
	 */
	public function sendMessage($message)
	{
		global $log;

		if (!empty($this->channel)) {
			$log->print(date('H:i:s', time()));
			$log->println(' > ' . $message, COLOR_BOT_MESSAGE);
			$this->send('PRIVMSG #' . $this->channel . ' : ' . $message);
		} else
			echo '[ERROR] No channel were joined' . PHP_EOL;
	}

	/**
	 * Checks if string is a chat message
	 * @param string $str
	 * @return bool
	 */
	public function isMessage($str)
	{
		return strpos($str, 'PRIVMSG') !== false;
	}

	/**
	 * Parses chat message to extract nick and message content
	 * @param string $str
	 * @return array
	 */
	public function parseMessage($str)
	{
		$strExp = explode(':', $str);

		$nick = explode('!', $strExp[1])[0];
		unset($strExp[0]);
		unset($strExp[1]);
		$message = implode(':', $strExp);

		return array('nick' => $nick, 'content' => $message);
	}
}