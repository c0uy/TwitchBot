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

// PHP Classes
require_once 'class/Log.php';
require_once 'class/Socket.php';
require_once 'class/TwitchIRC.php';

$log = new Log();
