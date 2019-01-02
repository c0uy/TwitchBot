<?php

require '../autoload.php';

$log = new Log();
$log->print('Welcome', true, 'YELLOW');
$log->print('Fail ', false, 'RED');
$log->print('Success', true, 'GREEN');
