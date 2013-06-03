<?php

class SiteController extends Controller
{
	
	
	private $excel_db = 'phpexcel';
	private $excel_files = 'excel_files';
	private $excel_sheets = 'excel_sheets';
	private $excel_columns = 'excel_columns';
	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		//modify
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
			if($rows != null){
				var_dump($en_str = JSON_ENCODE($rows));
				$de_arr = JSON_DECODE($en_str,true);  //加上第二个参数true表示将JSON对象强制转化为关联数组
				var_dump($de_arr);
				exit;
			}
			//comment
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
	
	/*
	 * 处理上传excel文件
	 */
	public function actionUpload(){
		ini_set('display_errors', 1);
		error_reporting(E_ALL );
		//加载模型
		$model = new File();
		if(isset($_POST['File'])){
			
			//获取上传文件对象
			$tmpFile = CUploadedFile::getInstance($model,'excelfile');
			
			if(empty($tmpFile)){
				//这里需要使用一个更好地错误提示，同时前端也做一个检验用户是否提交文件
				$this->redirect('upload',array('model'=>$model));
				//exit;
			}
			
			//获取文件的基本信息
			$model->setAttribute('fileName',mb_convert_encoding($tmpFile->name,'gbk','UTF-8'));
			
			$model->setAttribute('fileType',$tmpFile->extensionName);

			$model->fileSize = $tmpFile->size;
			
			$baseUrl =Yii::app()->basePath;
			
			$model->setAttribute('filePath',$baseUrl.Yii::app()->params['uploadPath'].$model->getAttribute('fileName'));
			//echo $model->filePath;exit;
			
			//验证文件信息
			if($model->validate()){
				//将临时文件转存
				if($tmpFile->saveAs($model->filePath) && $model->save()){
					
					//引入application.vendors.PHPExcel第三方库
					Yii::import('application.vendors.*');
					spl_autoload_unregister(array('YiiBase','autoload'));
					require_once 'PHPExcel/PHPExcel.php';
					
					//对转存后的文件进行处理
					$file = $model->getAttribute('filePath');
					
					if(!file_exists($file)){
						//这里不能使用Yii的CException或者CHTTPException类，因为Yii的autoload已经被unregister了，会报找不到类的错误
						throw new Exception('file not exists!');
					}
					//页面调试输出头
					header("Content-Type:text/html;charset=utf-8");
					//兼容Excel5和Excel7
					$excelReader = new PHPExcel_Reader_Excel2007();
        			if(!$excelReader->canRead($file)) {
        				$excelReader = new PHPExcel_Reader_Excel5();
        			}
        			// true: 只读取数据，如果不对文件进行写操作，那么设置为只读模式可以提高读取效率
        			$excelReader->setReadDataOnly(true);
        			//设置读编码
        			//setlocale(LC_ALL, 'zh_CN');
        			//在load整个文件之前就读取所有worksheets的名字，使用了PHP_ZIP扩展
        			$sheetNames = $excelReader->listWorksheetNames($file);
					//读到的是UTF-8编码的字符串
        			
					//load整个文件，这里是一下子把文件读入到内存
					$objPHPExcel = $excelReader->load($file);
					$s = microtime(1);
					$sheetCount = count($sheetNames);
					
					for ($c=0;$c<=$sheetCount;$c++){
						$fields = $rows = $columns = array();
						$currentSheet = $objPHPExcel->getSheet($c);
						$row_num = $currentSheet->getHighestRow();
						$col_num = PHPExcel_Cell::columnIndexFromString($currentSheet->getHighestColumn());
						
						//读取每个worksheet的第一列，作为表的column
						//按数字读列column,是从0开始的
						for ($j = 0;$j<=$col_num;$j++){
							$columns[$j] = $currentSheet->getCellByColumnAndRow($j,1)->getValue();
						}	

						//清理列数组
						$columns = $this->trimArray($columns);
						//若列为空则表示空工作薄，跳过当前循环继续下一个循环
						if(empty($columns)){
							continue;
						}
						
						//var_dump(mb_detect_encoding($currentSheet->getCellByColumnAndRow(0,1)->getValue()));exit;
						//var_dump($columns);exit;
						//根据列名创建数据表
						
						foreach ($columns as $column){
							//汉字取首字母拼音需要gbk编码
							$fields[$this->pyInit($this->changeEncode('UTF-8','GBK',$column))] = $column;
						}
						//var_dump($fields);exit;
						
						
						//对每个worksheet，应该考虑大量数据对于内存的使用与释放
						for ($i=1;$i<=$row_num;$i++){
							for ($j=1;$j<=$col_num;$j++){
								$address = $j.$i;
								$rows[$i][$j] = $currentSheet->getCell($address)->getValue();
							}
						}
						
						//comment
						
						
						unset($rows);
						unset($currentSheet);
					}
					
					//re-register autoload in Yii
					spl_autoload_register(array('YiiBase','autoload'));
					
					//显示上传成功
					$this->redirect('upload',array('model',$model));
				}
			}
				
		}
		
		$this->render('upload',array('model'=>$model));
		
	}
	
	/**
	 * 初始化数据库
	 */
	public function actionInitDB(){
		//header("Content-Type:text/html;charset=utf-8");
		//这里可以用yii dao来初始化数据库
		//yii dao 允许一条sql语句执行多次query
		
		$conn = Yii::app()->db; //继承自CDbConnection类，connectString来自配置文件/config/main.php
		
		$sql = "DROP DATABASE IF EXISTS `$this->excel_db`;
				CREATE DATABASE IF NOT EXISTS `$this->excel_db` DEFAULT CHARACTER SET gbk COLLATE gbk_chinese_ci;
				CREATE TABLE `$this->excel_db`.`$this->excel_files` (
  				`ID` int(10) NOT NULL auto_increment,
  				`fileName` nvarchar(50) NOT NULL,
  				`filePath` nvarchar(100) NOT NULL,
  				`uploadTime` varchar(22) NOT NULL default '0000-00-00 00:00',
  				`userIp` varchar(16) NOT NULL default '0.0.0.0',
  				`fileType` varchar(5) NOT NULL default 'xlsx',
  				`lastModifyTime` varchar(22) NOT NULL default '0000-00-00 00:00',
  				`lastModifyUserIp` varchar(16) NOT NULL default '0.0.0.0',
  				PRIMARY KEY  (`ID`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET gbk COLLATE gbk_chinese_ci;
				CREATE TABLE `$this->excel_db`.`$this->excel_sheets` (
  				`ID` int(10) NOT NULL auto_increment,
  				`fileID` int(10) NOT NULL,
  				`sheetTitle` nvarchar(50) NOT NULL,
  				`sheetTableName` varchar(25) NOT NULL,
  				PRIMARY KEY  (`ID`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET gbk COLLATE gbk_chinese_ci;
				CREATE TABLE `$this->excel_db`.`$this->excel_columns` (
  				`ID` int(10) NOT NULL auto_increment,
  				`tableID` int(10) NOT NULL,
  				`columnTitle` nvarchar(50) NOT NULL,
  				`columnName` varchar(25) NOT NULL,
  				PRIMARY KEY  (`ID`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET gbk COLLATE gbk_chinese_ci;";
		
		try {
			$command = $conn->createCommand($sql);  //继承自CDbCommand,准备执行sql语句的命令
			$command->execute();  //执行no-query sql
		} catch (Exception $e) {
			echo "初始化数据库出错:","<br />";
			print_r($e->getMessage());
			exit();
		}
		
		
		//$result = $command->queryAll();  //执行会返回若干行数据的sql语句，成功返回一个CDbDataReader实例，就是一个结果集
		//var_dump($result);
		
	}
	
	/**
	 * PHPExcel取excel文件的列时会取到空列，使用这个函数清理下
	 * @return Array():返回不为空的那些列名
	 * 当返回的数组是一个空时，说明当前这个worksheet是空的
	 */
	public function trimArray($columns){
		if(empty($columns)){
			return $columns;
		}
		if(is_array($columns)){
			foreach ($columns as $key=>$value){
				if($value == null)
					unset($columns[$key]);
			}
			return $columns;
		}else return array();
	}

	//获取汉字的首字母，除了汉字以外则直接返回（字母、数字、特殊字符）
	//$str必须是gbk编码的!
	function pyInit($str){
		$restr = '';
		$i=0;
		$count=strlen($str);
		while($i<$count) {
			$tmp=bin2hex(substr($str,$i,1));
			if($tmp>='B0'){ //汉字的开始
				$t=$this->getLetter(hexdec(bin2hex(substr($str,$i,2))));
				$restr .= sprintf("%c",$t==-1 ? '*' : $t );
			$i+=2;
			}
			else{
				$restr .= sprintf("%s",substr($str,$i,1));
				$i++;
			}
		}
		return $restr;
	}
	
	//获取字符ascii码
	function getLetter($num){
		$limit = array( //gb2312 拼音排序
					array(45217,45252), //A
					array(45253,45760), //B
					array(45761,46317), //C
					array(46318,46825), //D
					array(46826,47009), //E
					array(47010,47296), //F
					array(47297,47613), //G
					array(47614,48118), //H
					array(0,0),//I
					array(48119,49061), //J
					array(49062,49323), //K
					array(49324,49895), //L
					array(49896,50370), //M
					array(50371,50613), //N
					array(50614,50621), //O
					array(50622,50905), //P
					array(50906,51386), //Q
					array(51387,51445), //R
					array(51446,52217), //S
					array(52218,52697), //T
					array(0,0), //U
					array(0,0),//V
					array(52698,52979), //W
					array(52980,53688), //X
					array(53689,54480), //Y
					array(54481,55289), //Z
				);
		$char_index=65;
		foreach($limit as $k=>$v){
			if($num>=$v[0] && $num<=$v[1]){
				$char_index+=$k;
			return $char_index;
			}
 		}
 		return -1;
	}
	
	//字符集编码转换 utf8<=>gbk
	function changeEncode($inCode,$outCode,$input){
		$outCode=strtolower($outCode);
		if($outCode == 'gbk' || $outCode == 'gb2312')
			$outCode='gb2312//IGNORE'; //防止转码出错,忽略不可转字符
		if(is_array($input)){
			foreach ($input as $key=>$val){
				$key=iconv($inCode,$outCode,$key);
				$output[$key]=$this->changeEncode($inCode,$outCode,$val);
			}
			return $output;
		}else{
			return iconv($inCode,$outCode,$input);
		}
	}
	
}