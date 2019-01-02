<?php

class Socket
{
	private $socket;
	private $address;
	private $port;
	private $version = null;
	private $domains = array('4' => AF_INET, '6' => AF_INET6);

	/**
	 * Connect to the address & port specified
	 * Address can be IPv4, IPv6 or a domain name
	 * Port can be a number between 0-65535 or a protocol name
	 * @param string $address
	 * @param integer|string $port
	 */
	public function connect($address, $port)
	{
		global $log;

		$log->print('Connecting to ');
		$log->println($address.':'.$port, COLOR_INFO);

		// Address & Port
		if ($this->setAddress($address) === false)
			$log->println('[ERROR] Invalid address : ' . $address, COLOR_ERROR, true);
		if ($this->setPort($port) === false)
			$log->println('[ERROR] Invalid port : ' . $port, COLOR_ERROR, true);

		if(!empty($this->address) && !empty($this->port)) {
			// Socket Creation
			$this->socket = socket_create($this->domains[$this->version], SOCK_STREAM, SOL_TCP);
			if ($this->socket === false)
				$log->println('[ERROR] Socket creation failed : ' . $this->getLastSocketError(), COLOR_ERROR, true);

			// Socket Connection
			$result = socket_connect($this->socket, $this->address, $this->port);
			if ($result === false)
				$log->println('[ERROR] Connexion failed : ' . $this->getLastSocketError(), COLOR_ERROR, true);

			if(!socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 1, "usec" => 50000)))
				$log->println('[ERROR] Socket Option Failed : couldn\'t set reception timeout', COLOR_ERROR, true);
		}
	}

	/**
	 * Close the connection
	 */
	public function close()
	{
		global $log;

		if (!empty($this->socket)) {
			$log->println('Closing connection ...');
			socket_close($this->socket);
		}
	}

	/**
	 * Send data to the socket
	 * @param string $str
	 */
	public function send($str)
	{
		socket_write($this->socket, $str, strlen($str));
	}

	/**
	 * Read data from the socket
	 * @return string
	 */
	public function read()
	{
		$buffer = '';
		while ($data = socket_read($this->socket, 2048))
			$buffer .= $data;
		return $buffer;
	}

	public function getLastSocketError() {
		return socket_strerror(socket_last_error($this->socket));
	}

	/**
	 * @return mixed
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * Validate & Record Port into memory
	 * @param string $address
	 * @return bool
	 */
	public function setAddress($address)
	{
		// IP Validation
		$this->address = null;
		if (filter_var($address, FILTER_VALIDATE_IP)) {
			$this->address = $address;
			$this->version = filter_var($address, FILTER_FLAG_IPV6) ? 6 : 4;
		} else {
			$ipResolv = gethostbyname($address);
			if ($ipResolv !== $address) {
				$this->address = $ipResolv;
				$this->version = 4;
			}
		}
		return !empty($this->address);
	}

	/**
	 * @return mixed
	 */
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * Validate & Record Port into memory
	 * @param integer $port
	 * @return bool
	 */
	public function setPort($port)
	{
		// PORT Validation
		$this->port = null;
		$isInt = intval($port);
		if ($isInt && $port >= 0 && $port <= 65535)
			$this->port = $port;
		else if (!$isInt) {
			$portResolv = getservbyname($port, 'tcp');
			if ($portResolv !== false)
				$this->port = $portResolv;
		}
		return !empty($this->port);
	}
}
