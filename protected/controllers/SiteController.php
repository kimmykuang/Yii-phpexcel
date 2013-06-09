<?php

class SiteController extends Controller
{
	
	
	private $excel_db = 'phpexcel';
	private $excel_files = 'excel_files';
	private $excel_sheets = 'excel_sheets';
	private $excel_columns = 'excel_columns';
	
	/**
	 * 
	 * default action 
	 * @return $treeList:树形列表
	 * 
	 */
	public function actionIndex()
	{
		$dyData = $dyCols = $treeArray = array();
		$files = File::model()->findAll();
		
		// <!-- start tree list -->
		//treeList只允许有一个root节点，不允许几个同级的root节点
		$treeArray['id'] = 1;
		$treeArray['text'] = 'All Documents';
		$i = 0;
		foreach ($files as $file){
			$i++;
			$j = 0;
			$children_file = array();
			$children_file['id'] = $treeArray['id'].$i;
			$children_file['text'] = $file->fileTitle;
			$children_file['attributes'] = array('data-id'=>$file->ID);
			foreach ($file->sheets as $sheet){
				$j++;
				$children_file['children'][] = array(
					'id'=>$children_file['id'].$j,
					'text'=>$sheet->sheetTitle,
					'attributes'=>array('data-id'=>$sheet->ID),
				);
			}
			$treeArray['children'][] = $children_file;
		}
		$treeList = '['.json_encode($treeArray).']';
		// <!-- end tree list -->
		
		// <!-- start dyCols -->
		$worksheet = $files[0]->sheets[1];
		$dyCols = array(
			array('fieldName'=>'productid','fieldText'=>'产品编号','fieldStyle'=>array('width'=>'80px')),
			array('fieldName'=>'productname','fieldText'=>'产品名字','fieldStyle'=>array('width'=>'80px')),
			array('fieldName'=>'listprice','fieldText'=>'价格','fieldStyle'=>array('width'=>'80px')),
			array('fieldName'=>'status','fieldText'=>'库存状态','fieldStyle'=>array('width'=>'80px')),
			);
		// <!-- end dyCols -->
		
		// <!-- start dyData -->
		$dyData = array(
			
		);
		// <!-- end dyData -->
		
		$this->render('index',array(
			'treeList'=>$treeList,
			'dyCols'=>$dyCols,
			'dyData'=>$dyData,
		));
	}
	
	/*
	 * 处理上传excel文件
	 */
	public function actionUpload(){
		
		//设置读编码
        //setlocale(LC_ALL, 'zh_CN');
		//页面调试输出头
		header("Content-Type:text/html;charset=utf-8");
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
			$newName = time().rand(1,10000).'.'.$tmpFile->extensionName;
			//获取文件的基本信息
			$model->setAttribute('fileTitle',$tmpFile->name);
			
			$model->setAttribute('fileType',$tmpFile->extensionName);

			$model->fileSize = $tmpFile->size;
			
			$model->setAttribute('filePath',FILE_BASE_PATH.$newName);
			//echo $model->filePath;exit;
			
			//验证文件信息
			if($model->validate()){
				//将临时文件转存
				if($tmpFile->saveAs($model->filePath) && $model->save()){
					//yii dao
					$conn = Yii::app()->db;
					//tableprefix
					$prefix = $conn->tablePrefix;
					//fileID
					$fileID = $conn->lastInsertID;
					//file path
					$file = $model->getAttribute('filePath');
					//引入application.vendors.PHPExcel第三方库
					Yii::import('application.vendors.*');
					spl_autoload_unregister(array('YiiBase','autoload'));
					require_once 'PHPExcel/PHPExcel.php';
					
					// <!-- start file process -->
					if(!file_exists($file)){
						//这里不能使用Yii的CException或者CHTTPException类，因为Yii的autoload已经被unregister了，会报找不到类的错误
						throw new Exception('file not exists!');
					}
					
					//兼容Excel5和Excel7
					$excelReader = new PHPExcel_Reader_Excel2007();
        			if(!$excelReader->canRead($file)) {
        				$excelReader = new PHPExcel_Reader_Excel5();
        			}
        			
        			// true: 只读取数据，如果不对文件进行写操作，那么设置为只读模式可以提高读取效率
        			$excelReader->setReadDataOnly(true);

        			//在load整个文件之前就读取所有worksheets的名字，使用了PHP_ZIP扩展
        			$sheetNames = $excelReader->listWorksheetNames($file);
        			
					//load整个文件，这里是一下子把文件读入到内存
					$objPHPExcel = $excelReader->load($file);
					$sheetCount = count($sheetNames);
					
					// <!-- start worksheets loop -->
					for ($c=0;$c<$sheetCount;$c++){
						try {
							$fields = $rows = $columns = array();
							//当前worksheet
							$currentSheet = $objPHPExcel->getSheet($c);
							//当前worksheet的标题
							$currentSheetTitle = $currentSheet->getTitle();
							$currentSheetTableName = $prefix.'f'.$fileID.'_s'.$c;
							//行数
							$row_num = $currentSheet->getHighestRow();
							//列数
							$col_num = PHPExcel_Cell::columnIndexFromString($currentSheet->getHighestColumn());
						
							//读取每个worksheet的第一列，作为表的column
							//按数字读列column,是从0开始的
							for ($j = 0;$j < $col_num;$j++){
								$columns[$j] = $currentSheet->getCellByColumnAndRow($j,1)->getValue();
							}
							
							//清理列数组
							$columns = $this->trimArray($columns);
							$col_num = count($columns);
							
							//若列为空则表示空工作薄，跳过当前循环继续下一个循环
							if(empty($columns)){
								continue;
							}
							//列字段
							foreach ($columns as $key=>$column){
								$fields['c'.$key] = $column;
							}
							//var_dump($fields);exit;
							
							// <!-- start transaction -->
							spl_autoload_register(array('YiiBase','autoload'));
							$transaction = $conn->beginTransaction();					
							try {
																
								//插入excel_sheets表
								$sql1 = "INSERT INTO `$this->excel_sheets` VALUES (null,'$fileID','$currentSheetTitle','$currentSheetTableName');";
								//echo $sql1,"<br/>";
								$conn->createCommand($sql1)->execute();
								
								//插入excel_columns表
								$sheetID = $conn->lastInsertID;
								$sql2 = $this->insertCol($sheetID,$fields,$this->excel_columns);
								//echo $sql2,"<br/>";
								$conn->createCommand($sql2)->execute();
															
								//创建数据表	
								$sql3 = $this->createDataTable($fields, $currentSheetTableName);
								//echo $sql3,"<br/>";
								$conn->createCommand($sql3)->execute();

								// <!-- start worksheet data process -->
								//如果行数比较多的话，2000行一处理
								$count = ($row_num>=2000)?2000:$row_num;
								$i = 0;
								while ($i <= $row_num){
									// 从第二行开始读取数据，第一行是标题
									for ($i=2;$i<=$count;$i++){
										for ($j=0;$j<$col_num;$j++){
											$rows[$i][$j] = $currentSheet->getCellByColumnAndRow($j,$i)->getValue();
										}
									}
									$sql4 = $this->insertData($currentSheetTableName,$rows);
									$conn->createCommand($sql4)->execute();
								}
								// <!-- end worksheet data process -->
								
								//提交
								$transaction->commit();
								
							} catch (Exception $e) {
								$transaction->rollback();
								echo "事务出错:","<br />";
								print_r($e->getMessage());
								exit();
							}
							// <!-- end transaction -->
							
						} catch (Exception $e) {
							var_dump($e->getMessage());
							exit;
						}
						
						unset($rows);
						unset($fields);
						unset($columns);
						unset($currentSheet);
					}
					// <!-- end worksheets loop -->
					// <!-- end file process -->
					
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
	 * 展示所有的excel文件及其下属的worksheets
	 */
	public function actionView(){
		$files = File::model()->with('sheets')->findAll();
		echo "here is files:","<br/>";
		foreach ($files as $m){
			var_dump($m->attributes);
			echo "here is sheets of this file:","<br/>";
			foreach ($m->sheets as $sheet){
				var_dump($sheet->attributes);
			}
		}
	}
	
	/**
	 * 初始化数据库
	 */
	public function actionInitDB(){
		//header("Content-Type:text/html;charset=utf-8");
		//这里可以用yii dao来初始化数据库
		//yii dao 允许一条sql语句执行多次query
		
		$dsn = 'mysql:host=localhost;dbname=INFORMATION_SCHEMA';
		$username = 'root';
		$password = 'xiucai5880';
		try {
			$conn = new CDbConnection($dsn,$username,$password); //继承自CDbConnection类，connectString来自配置文件/config/main.php
			$conn->active = TRUE;  //激活连接
			$sql = "DROP DATABASE IF EXISTS `$this->excel_db`;
				CREATE DATABASE IF NOT EXISTS `$this->excel_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
				CREATE TABLE `$this->excel_db`.`$this->excel_files` (
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
				CREATE TABLE `$this->excel_db`.`$this->excel_sheets` (
  				`ID` int(10) NOT NULL auto_increment,
  				`fileID` int(10) NOT NULL,
  				`sheetTitle` nvarchar(70) NOT NULL,
  				`sheetTableName` varchar(50) NOT NULL,
  				PRIMARY KEY  (`ID`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
				CREATE TABLE `$this->excel_db`.`$this->excel_columns` (
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
		} catch (Exception $e) {
			echo "初始化数据库出错:","<br />";
			print_r($e->getMessage());
			exit();
		}
		//$result = $command->queryAll();  //执行会返回若干行数据的sql语句，成功返回一个CDbDataReader实例，就是一个结果集
		//var_dump($result);
		
	}
	
	/**
	 * 更新title，如果首字母重复则自动更改，同时update 表
	 * type in (filetitle,sheettitle,columntitle)
	 * ajax
	 */
	public function actionUpdateTitle($id,$title,$type='file'){
		$types = array('file','sheet','column');
		if(!in_array($type, $types)){
			throw new CHttpException(404,'The requested page does not exist.');
			exit;
		}
		$title = mb_convert_encoding($title, "UTF-8","GBK,UTF-8");
		$field = $type.'Title';
		$model = call_user_func(array($this,'load'.ucfirst($type).'Model'),$id);
		$model->$field = $title;
		if($model->validate() && $model->save()){
			//更名成功，重新加载datagrid，可以使用在前段使用reload，这里返回flag
			//$this->redirect(array('view'));
			echo true;
		}else{
			return false;
		}
	}
	
	/**
	 * 加载File模型类
	 */
	public function loadFileModel($id){
		$model = File::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	/**
	 * 加载Sheet模型类
	 */
	public function loadSheetModel($id){
		$model = Sheet::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	/**
	 * 加载Column模型类
	 */
	public function loadColumnModel($id){
		$model = Column::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	
	/*
	public function loadModel($id,$modelname){
		//$modelname::func()的形式php5.3以后才支持
		$model = $modelname::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}*/
	
	/**
	 * 
	 * 文件下载
	 * @param numeric $id
	 */
	public function actionDownload($id){
	
		$file_info = $this->actionCreateFile($id);
		$filename = $file_info[0];
		$filepath = $file_info[1];
    	//清空输出缓存
		ob_clean();
		//输出到浏览器 
		$xsend = $this->ckApacheModule('mod_xsendfile');
		$this->sendFile($filename, $filepath,'UTF-8',$xsend);
		/*
   		header("Content-Type: application/force-download"); 
   		header("Content-Type: application/octet-stream;charset=UTF-8"); 
  	 	header("Content-Type: application/download"); 
   		header('Content-Disposition:inline;filename="'.$filename.'"'); 
   		header("Content-Transfer-Encoding: binary"); 
  		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
   		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
  		header("Pragma: no-cache"); 
   		$objWriter->save('php://output'); 
   		*/
		
		exit;
	}
	
	/**
	 * 
	 * 在服务器目录下创建文件
	 * @param numeric $id
	 */
	public function actionCreateFile($id){
		
		//获取文件信息
		$file = $this->loadFileModel($id);
		$filename = $file->fileTitle;
		$filepath = $file->filePath;
		//yii dao
		$conn = Yii::app()->db;
		
		//判断是否需要重新生成文件
		if($file->uploadTime !== $file->lastModifyTime || !file_exists($filepath)){
			
			Yii::import('application.vendors.*');
			spl_autoload_unregister(array('YiiBase','autoload'));
			require_once 'PHPExcel/PHPExcel.php';
			
			$objExcel = new PHPExcel();
			$objWriter = new PHPExcel_Writer_Excel2007($objExcel); // 用于 2007 格式 
			$objWriter->setOffice2003Compatibility(true); //向下兼容excel2005
			spl_autoload_register(array('YiiBase','autoload'));
			
			try {
				//组装数据
				foreach ($file->sheets as $sheetIndex => $sheet){
					//echo $sheetIndex;
					$data = $columnArray = array();
					$selectstr = $comma = "";
					foreach ($sheet->columns as $column){
						$columnArray[] = $column->columnName;
						$selectstr .= $comma.$column->columnTitle;
						$comma = ",";
					}
					//var_dump($title);exit;
					//从数据表中获取数据
					$data = $conn->createCommand()->select($selectstr)->from($sheet->sheetTableName)->queryAll();
					//var_dump($data);exit;
					spl_autoload_unregister(array('YiiBase','autoload'));
			
					//添加一个新的worksheet 
   					$objActSheet = $objExcel->createSheet($sheetIndex); 
					//设置当前活动sheet的名称 
    				$objActSheet->setTitle($sheet->sheetTitle); 
    		
    				//对每个worksheet，设置第一行的列标题
    				foreach ($columnArray as $k=>$c){
    					$objActSheet->setCellValueByColumnAndRow($k,1,$c);
    				}
    		
    				//设置单元格内容
    				$count = count($columnArray);
					foreach ($data as $k=>$v){
						for ($i=0;$i<$count;$i++){
							$objActSheet->setCellValueByColumnAndRow($i,$k+2,$v['c'.$i]);
						}
					}
				}
			}
			catch (Exception $e){
				var_dump($e->getMessage());
				exit;
			}
			//清空输出缓存
			ob_clean(); 
			//覆盖文件 
			$objWriter->save($filepath);
		}
		spl_autoload_register(array('YiiBase','autoload'));
		return array($filename,$filepath);
	}
	
	/**
	 * 
	 * datagrid对数据的CRUD操作，Ajax方式
	 * 操作数据后，需要更新File的lastModifyTime和lastModifyUserIp
	 */
	public function actionCRUD(){
		
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
	
	//PHP查看APACHE是否支持某个模块扩展
	public function ckApacheModule($module_name){
		$modules = apache_get_modules();
		if(in_array($module_name,$modules)){
			return true;
		}else{
			return false;
		}
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
	
	//发送文件
	function sendFile($filename,$filepath,$charset = 'UTF-8',$xsend = true,$mimeType = 'application/octet-stream'){
		// 文件名乱码问题
		$ua = $_SERVER["HTTP_USER_AGENT"];
		if (preg_match("/MSIE/", $ua)) 
		{
			$filename = urlencode($filename);
			$filename = str_replace("+", "%20", $filename);// 替换空格
			$attachmentHeader = "Content-Disposition: attachment; filename=\"{$filename}\"; charset={$charset}";
	
		} else if (preg_match("/Firefox/", $ua)) {
	
			$attachmentHeader = 'Content-Disposition: attachment; filename*="utf8\'\'' . $filename. '"' ;
	
		} else {
	
			$attachmentHeader = "Content-Disposition: attachment; filename=\"{$filename}\"; charset={$charset}";
	
		}

		$filesize = filesize($filepath);

		//header("Pragma: public"); header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: {$mimeType}");

		header($attachmentHeader);
		header('Pragma: cache');
		header('Cache-Control: public, must-revalidate, max-age=0');

		if($xsend){
			//mod_xsendfile模块
			header("X-Sendfile:".$filepath);
			exit;
		}else {
			header("Content-Length: {$filesize}");
			readfile($filepath);
			exit;
		}
	}
	
	//组装$sql3,将列信息插入kcsv_columns表中
	public function insertCol($sheetID,$fields,$table){
		$sheetID = intval($sheetID);
		$colstr= $comma = "";
		foreach ($fields as $key=>$val){
			$colstr .= $comma."(NULL,'$sheetID','$key','$val')";
			$comma = ",";
		}
		//$colstr = substr($colstr,0,-1);
		//$colstr = rtrim($colstr,",");
		return "INSERT INTO `$table` VALUES ".$colstr;
	}
	
	//创建数据表
	public function createDataTable($fields,$table){
		$colstr = "";
		//$cstr = '';
		foreach ($fields as $key=>$val){
			//$cstr .= '`'.$val.'`,';
			$colstr .= " ,`".$key."` nvarchar(200)";
		}
		//$cstr = rtrim($cstr,',');
		$sql = "CREATE TABLE IF NOT EXISTS `".$table."` ( `ID` int(10) not null primary key auto_increment ".$colstr." ) ENGINE=InnoDB AUTO_INCREMENT=1 CHARACTER SET utf8 COLLATE utf8_general_ci;";
		return $sql;
	}
	
	//向数据表中插入数据
	public function insertData($table,$dataArray){
		if(empty($dataArray)){
			return false;
		}
		$comma = "";
		$sql="INSERT INTO `$table` VALUES ";
		foreach ($dataArray as $dArray){
			$istr = $comma."(NULL";
			foreach ($dArray as $data){
				$istr .= ",'$data'";
			}
			$istr .= ")";
			$sql .= $istr;
			$comma = ",";
			$istr = "";
		}
		return $sql;
	}
								
}