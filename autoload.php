<?php
define('TAB', "\n");

define('COLOR_DEFAULT', 'NORMAL');
define('COLOR_INFO', 'YELLOW');
define('COLOR_ERROR', 'RED');
define('COLOR_SUCCESS', 'GREEN');
define('COLOR_BOT_PING', 'BROWN');
define('COLOR_BOT_MESSAGE', 'LIGHT_BLUE');
define('COLOR_USER_NICK', 'CYAN');
define('COLOR_USER_MESSAGE', 'NORMAL');

define('FILE_CONF', 'config.json');

define('CMD_PREFIX', '!');

// PHP Classes
require_once 'class/Log.php';
require_once 'class/Socket.php';
require_once 'class/TwitchIRC.php';

$log = new Log();

$config = null;
if(file_exists(FILE_CONF) && is_readable(FILE_CONF))
	$config = json_decode(file_get_contents(FILE_CONF), JSON_OBJECT_AS_ARRAY);
else
	$log->println('[ERROR] Config file ' . FILE_CONF . ' does not exists or not readable.', COLOR_ERROR, true);