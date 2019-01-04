<?php

require 'autoload.php';

$irc = new TwitchIRC();
$irc->connect($config['server']['address'], $config['server']['port']);

$messagesFile = 'messages.txt';
$autoMessages = is_file($messagesFile) && is_readable($messagesFile) ? array_map('trim', array_filter(file($messagesFile))) : array();
$actualMessageIndex = 0;
$interval = 60 * $config['autoMessages']['interval'];
$nextTimestamp = time() + $interval;

if ($irc->login($config['account']['nick'], $config['account']['oauth']) && $irc->join($config['server']['channel'])) {

	while (true) {
		$actualTime = time();
		$buffer = $irc->read();

		if (!empty($autoMessages) && $actualTime >= $nextTimestamp) {
			$irc->sendMessage($autoMessages[$actualMessageIndex]);
			$actualMessageIndex++;
			if ($actualMessageIndex > count($autoMessages))
				$actualMessageIndex = 0;
			$nextTimestamp = $actualTime + $interval;
		}

		if (!empty($buffer)) {
			if ($irc->isPing($buffer))
				$irc->sendPong(); else {
				if ($irc->isMessage($buffer)) {
					$message = $irc->parseMessage($buffer);
					$isCMD = strpos($message['content'], $config['cmdPrefix']) === 0;

					if ($isCMD) {
						$message['content'] = ltrim($message['content'], $config['cmdPrefix']);

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
