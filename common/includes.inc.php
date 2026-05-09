<?php
set_include_path(
	get_include_path()
//	. PATH_SEPARATOR . ROOT.'admin/'
	. PATH_SEPARATOR . ROOT
	. PATH_SEPARATOR . ROOT.'cronjob/library/'
	. PATH_SEPARATOR . ROOT.'common/'
	. PATH_SEPARATOR . ROOT.'common/class/'
	. PATH_SEPARATOR . ROOT.'common/library/'
	. PATH_SEPARATOR . ROOT.'common/pear/'
	. PATH_SEPARATOR . ROOT.'common/views/'
);
