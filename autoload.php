<?php
define('TAB', "\n");

define('COLOR_DEFAULT', 'NORMAL');
define('COLOR_INFO', 'YELLOW');
define('COLOR_ERROR', 'RED');
define('COLOR_SUCCESS', 'GREEN');

// PHP Classes
require_once 'class/Log.php';
require_once 'class/Socket.php';
require_once 'class/TwitchIRC.php';

$log = new Log();
