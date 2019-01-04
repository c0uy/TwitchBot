<?php

require 'autoload.php';

define('FILE_MESSAGES', 'messages.txt');
define('AUTOMSG_INTERVAL', 1);
define('CMD_PREFIX', '!');

$irc = new TwitchIRC();
$irc->connect($config['server']['address'], $config['server']['port']);

// Automatic messages parameters
$autoMessages = is_file(FILE_MESSAGES) && is_readable(FILE_MESSAGES) ? array_map('trim', array_filter(file(FILE_MESSAGES))) : array();
$actualMessageIndex = 0;
$interval = 60 * AUTOMSG_INTERVAL;
$nextTimestamp = time() + $interval;

if ($irc->login($config['account']['nick'], $config['account']['oauth']) && $irc->join($config['server']['channel'])) {

	while (true) {
		$actualTime = time();
		$buffer = $irc->read();

		// Automatic messages
		if (!empty($autoMessages) && $actualTime >= $nextTimestamp) {
			$irc->sendMessage($autoMessages[$actualMessageIndex]);
			$actualMessageIndex++;
			if ($actualMessageIndex > count($autoMessages))
				$actualMessageIndex = 0;
			$nextTimestamp = $actualTime + $interval;
		}

		// At message reception
		if (!empty($buffer)) {
			// Ping server
			if ($irc->isPing($buffer))
				$irc->sendPong();
			else {

				if ($irc->isMessage($buffer)) {
					$message = $irc->parseMessage($buffer);
					$isCMD = strpos($message['content'], CMD_PREFIX) === 0;

					// Chat command
					if ($isCMD) {
						$message['content'] = ltrim($message['content'], CMD_PREFIX);

						$log->print(date('H:i:s', $actualTime) . ' < ');
						$log->print($message['nick'] . ' : ', COLOR_USER_NICK);
						$log->println($message['content'], COLOR_USER_MESSAGE);

						switch ($message['content']) {
							case 'ping':
								$irc->sendMessage('pong');
								break;
							case 'datetime':
								$irc->sendMessage(date('d/m/Y H:i:s', $actualTime));
								break;
							case 'date':
								$irc->sendMessage(date('d/m/Y', $actualTime));
								break;
							case 'time':
								$irc->sendMessage(date('H:i:s', $actualTime));
								break;
						}
					}
				}

			}
		}

		usleep(100);
	}

	$irc->sendMessage('Bye !');
	$irc->part();

}

$irc->close();
