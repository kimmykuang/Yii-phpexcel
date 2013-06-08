<?php
	
	$str = array( array('id' => 1,'text' => "hr", 'desc' => "文件描述，内容可有可无，但该字段不可少"),
				  array('id' => 2,'text' => "test", 'desc' => ""),
				  array('id' => 3,'text' => "测试", 'desc' => ""),
				  array('id' => 4,'text' => "开发需求书-与中国人力资源网-20121212",'desc' => "" )
				);
	$test = json_encode($str);
	echo $test;
?>