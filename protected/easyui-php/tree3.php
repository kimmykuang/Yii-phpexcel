<?php
	$str = array('id' => 1,
				 'text' => "根",
				 'children' => array(
				 	array(
				 		'id' => 11,
				 		'text' => "hr",
				 		'state' => "closed",
				 		'children' => array(
				 			'id' => 111,
				 			'text' => "Sheet1",
				 			)
						),
				 	array(
				 		'id' => 12,
				 		'text' => "test",
				 		'children' => array(
				 			array(
				 				'id' => 121,
				 				'text' => "Sheet1"
				 				),
				 			array(
				 				'id' => 122,
				 				'text' => "上海浦东康桥工业区"
				 				),
				 			array(
				 				'id' => 123,
				 				'text' =>	 "上海张江高科技园"
				 				)
				 			)
				 		),
				 	array(
				 		'id' => 13,
				 		'text' => "测试",
				 		'state' => "closed",
				 		'children' => array(
				 			'id' => 131,
				 			'text' => "测试Sheet"
				 			)
				 		),
				 	array(
				 		'id' => 14,
				 		'text' => "开发需求书-与中国人力资源网-20121212",
				 		'state' => "closed",
				 		'children' => array(
				 			'id' => 141,
				 			'text' => "开发需求书"
				 			)
				 		)
				)
			);

	$test = '['.json_encode($str).']';
	echo $test;
?>