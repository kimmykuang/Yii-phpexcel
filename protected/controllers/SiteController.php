<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		set_time_limit(0);
		//ini_set('memory_limit', '512M');
		Yii::import('application.vendors.*');
		//echo Yii::getPathOfAlias('application.vendors');exit;
		//解除Yii的自动加载，与PHPExcel自带的autoload冲突
		spl_autoload_unregister(array('YiiBase','autoload'));
		require_once 'PHPExcel/PHPExcel.php';
		$filepath = Yii::getPathOfAlias('application.data').DIRECTORY_SEPARATOR.'test.xlsx';
		if(!file_exists($filepath)){
			throw new Exception('file not exists!');
		}
		$objPHPExcel = PHPExcel_IOFactory::load($filepath);
		$s = microtime(1);
		//$sheetData = $objPHPExcel->getSheet(2)->toArray(null,true,true,true);var_dump($sheetData);echo microtime(1)-$s;exit;
		$sheetCount = $objPHPExcel->getSheetCount();	
		for ($c=0;$c<=$sheetCount;$c++){
			ob_start();
			$currentSheetID = $c;
			$currentSheet = $objPHPExcel->getSheet($currentSheetID);
			$row_num = $currentSheet->getHighestRow();
			$col_num = $currentSheet->getHighestColumn();
			$rows = array();
			for ($i=1;$i<=$row_num;$i++){
				for ($j='A';$j<$col_num;$j++){
					$address = $j.$i;
					$rows[$i][$j] = $currentSheet->getCell($address)->getFormattedValue();
				}
			}
			echo $c,"<br/>";
			var_dump($rows);
			ob_end_flush();
			unset($rows);
			unset($currentSheet);
		}
		spl_autoload_register(array('YiiBase','autoload'));
		//var_dump($sheetArray);
		//var_dump($rows);echo microtime(1)-$s;
		//$sheets = $objPHPExcel->getSheetNames();
		exit;
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		
		//重新注册Yii的autoload
		spl_autoload_register(array('YiiBase','autoload'));
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}