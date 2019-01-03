<?php

require 'autoload.php';

$irc = new TwitchIRC();
$irc->connect($config['server']['address'], $config['server']['port']);

if ($irc->login($config['account']['nick'], $config['account']['oauth']) && $irc->join($config['server']['channel'])) {

	$irc->sendMessage('Bienvenue sur la chaine de '.$config['server']['channel']);

	while (true) {
		$buffer = $irc->read();

		if (!empty($buffer)) {
			if ($irc->isPing($buffer))
				$irc->sendPong();
			else {
				if ($irc->isMessage($buffer)) {
					$message = $irc->parseMessage($buffer);
					$isCMD = strpos($message['content'], CMD_PREFIX) === 0;

					if ($isCMD) {
						$message['content'] = ltrim($message['content'], CMD_PREFIX);

						$log->print(date('H:i:s', time()) . ' < ');
						$log->print($message['nick'] . ' : ', COLOR_USER_NICK);
						$log->println($message['content'], COLOR_USER_MESSAGE);

						switch ($message['content']) {
							case 'ping':
								$irc->sendMessage('pong');
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
