<?php

class Log
{
	private $colors = array(
		'LIGHT_RED'		=>	"[1;31m",
		'LIGHT_GREEN'	=>	"[1;32m",
		'YELLOW'		=>	"[1;33m",
		'LIGHT_BLUE'	=>	"[1;34m",
		'MAGENTA'		=>	"[1;35m",
		'LIGHT_CYAN'	=>	"[1;36m",
		'WHITE'			=>	"[1;37m",
		'NORMAL'		=>	"[0m",
		'BLACK'			=>	"[0;30m",
		'RED'			=>	"[0;31m",
		'GREEN'			=>	"[0;32m",
		'BROWN'			=>	"[0;33m",
		'BLUE'			=>	"[0;34m",
		'CYAN'			=>	"[0;36m",
		'BOLD'			=>	"[1m",
		'UNDERSCORE'	=>	"[4m",
		'REVERSE'		=>	"[7m"
	);

	public function print($message, $breakLine = true, $exit, $color = 'NORMAL')
	{
		$selectedColor = empty($this->colors[$color]) ? "[0m" : $this->colors[$color];
		$str = chr(27).$selectedColor.$message.chr(27).chr(27)."[0m".chr(27).($breakLine ? PHP_EOL : '');
		if($exit)
			echo $str;
		else
			exit($str);
	}
}
