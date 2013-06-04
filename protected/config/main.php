<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'PHP Excel',
	'timeZone'=>'Asia/Shanghai',
	'language'=>'zh_cn',
	// preloading 'log' component
	'preload'=>array('log'),
	//yiiframework源码里写了'defaultController'=>'site',这里可以重写
	'defaultController'=>'site',
	//homeUrl
	'homeUrl'=>'',

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		//脚手架
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Enter Your Password Here',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		//url美化
		'urlManager'=>array(
			'caseSensitive'=>FALSE,
			'urlSuffix'=>'.html',
			'showScriptName'=>FALSE,
			'urlFormat'=>'path',
			//url匹配规则,这里可以根据自己的业务逻辑来设定url的规则
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		//uncomment the following to use a SqlLite database
		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		*/
		
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=phpexcel',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '123456',
			'charset' => 'gbk',
			'tablePrefix'=>'excel_',
			//让捆绑的变量显示出来
   			'enableProfiling' => YII_DEBUG,
   			'enableParamLogging' =>YII_DEBUG,
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				
				
				array(
					'class'=>'CWebLogRoute',
					//添加的内容
     				'levels' =>'trace',
     				'categories' => 'system.db*',
				),
				
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'uploadPath'=>'\data\\',
		'downloadPath'=>'\data\\', //Yii::app()->params['uploadPath']
	),
);