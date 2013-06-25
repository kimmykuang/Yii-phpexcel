<?php 
class InstallController extends Controller{
	
	public $defaultAction = 'index';
	
	public function actionIndex(){
		$configFilePath = Yii::getPathOfAlias('ext').DIRECTORY_SEPARATOR.'phpexcel_config.php';
		//检查config
		if(file_exists($configFilePath)){
			$this->redirect(array('site/index'));
		}
		//提交配置信息
		$model = new ConfigForm();
		if(isset($_POST['ConfigForm'])){
			$configs = array();
			$model->attributes = $_POST['ConfigForm'];
			foreach($model->attributes as $k=>$v){
				$configs[$k] = $v;
			}
			
			$this->InitDB($configs,$configFilePath);
		}
		$this->render('install',array(
			'model'=>$model,
		));
	}
	
	public function InitDB($configs,$configFilePath){
		$db = $configs['dbname'];
		$table_files = $configs['table_files'];
		$table_sheets = $configs['table_sheets'];
		$table_columns = $configs['table_columns'];
		$conn = Yii::app()->db;
		$username = $conn->username;
		$password = $conn->password;
		$dsn = 'mysql:host=localhost;dbname=INFORMATION_SCHEMA';
		$conn->active = FALSE;
		try {
			$conn = new CDbConnection($dsn,$username,$password); //继承自CDbConnection类，connectString来自配置文件/config/main.php
			$conn->active = TRUE;  //激活连接
			$sql = "DROP DATABASE IF EXISTS `$db`;
				CREATE DATABASE IF NOT EXISTS `$db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
				CREATE TABLE `$db`.`$table_files` (
  				`ID` int(10) NOT NULL auto_increment,
  				`fileTitle` nvarchar(50) NOT NULL,
  				`filePath` nvarchar(100) NOT NULL,
  				`uploadTime` varchar(22) NOT NULL default '0000-00-00 00:00',
  				`userIp` varchar(16) NOT NULL default '0.0.0.0',
  				`fileType` varchar(5) NOT NULL default 'xlsx',
  				`lastModifyTime` varchar(22) NOT NULL default '0000-00-00 00:00',
  				`lastModifyUserIp` varchar(16) NOT NULL default '0.0.0.0',
  				PRIMARY KEY  (`ID`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
				CREATE TABLE `$db`.`$table_sheets` (
  				`ID` int(10) NOT NULL auto_increment,
  				`fileID` int(10) NOT NULL,
  				`sheetTitle` nvarchar(70) NOT NULL,
  				`sheetTableName` varchar(50) NOT NULL,
  				PRIMARY KEY  (`ID`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
				CREATE TABLE `$db`.`$table_columns` (
  				`ID` int(10) NOT NULL auto_increment,
  				`sheetID` int(10) NOT NULL,
  				`columnTitle` nvarchar(50) NOT NULL,
  				`columnName` varchar(25) NOT NULL,
  				PRIMARY KEY  (`ID`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
			
			//继承自CDbCommand,准备执行sql语句的命令
			$command = $conn->createCommand($sql);  
			//执行no-query sql
			if($command->execute()){
				echo "init DB success","<br/>","executed sql statement:","<br/>";  
				echo "<pre>";
				print_r($command->text);
				echo "</pre>";
			}
			//关闭连接
			$conn->active = FALSE;
			$conn = Yii::app()->db;
			$conn->active = TRUE;
			//生成配置文件
			$configStr = '<?php $configs=';
			$configStr .= var_export($configs,TRUE);
			$configStr .= ';?>';
			@file_put_contents($configFilePath,$configStr);
			
		} catch (Exception $e) {
			echo "初始化数据库出错:","<br />";
			print_r($e->getMessage());
			exit();
		}
		$this->redirect(array('site/index'));
	}
}
?>