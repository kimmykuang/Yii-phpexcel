<?php

class SiteController extends Controller
{	
	
	/**
	 * 
	 * default action 
	 * 
	 */
	public function actionIndex()
	{
		$dyData = $dyCols = $treeArray = array();
		$files = File::model()->findAll();
		$sheetTitle = 'Yii-PHPExcel首页';
		// <!-- start tree list -->
		$treeArray['id'] = 1;
		$treeArray['text'] = 'All Documents';
		$treeArray['attributes'] = array('sheetID'=>'');
		$i = 0;
		foreach ($files as $file){
			$i++;
			$j = 0;
			$children_file = array();
			$children_file['id'] = $treeArray['id'].$i;
			$children_file['text'] = $file->fileTitle;
			
			foreach ($file->sheets as $sheet){
				$j++;
				$children_file['children'][] = array(
					'id'=>$children_file['id'].$j,
					'text'=>$sheet->sheetTitle,
					'attributes'=>array('sheetID'=>$sheet->ID),
				);
			}
			$children_file['attributes'] = array('sheetID'=>'');
			$treeArray['children'][] = $children_file;
		}
		$treeList = '['.json_encode($treeArray).']';
		// <!-- end tree list -->

		$this->render('index',array(
			'treeList'=>$treeList,
			'sheetTitle'=>$sheetTitle,
		));
	}
	/**
	 * 测试布局用的action:index1
	 */
	public function actionIndex1()
	{
		$dyData = $dyCols = $treeArray = array();
		$files = File::model()->findAll();
		$sheetTitle = 'Yii-PHPExcel首页';
		// <!-- start tree list -->
		$treeArray['id'] = 1;
		$treeArray['text'] = 'All Documents';
		$treeArray['attributes'] = array('sheetID'=>'');
		$i = 0;
		foreach ($files as $file){
			$i++;
			$j = 0;
			$children_file = array();
			$children_file['id'] = $treeArray['id'].$i;
			$children_file['text'] = $file->fileTitle;
			
			foreach ($file->sheets as $sheet){
				$j++;
				$children_file['children'][] = array(
					'id'=>$children_file['id'].$j,
					'text'=>$sheet->sheetTitle,
					'attributes'=>array('sheetID'=>$sheet->ID),
				);
			}
			$children_file['attributes'] = array('sheetID'=>'');
			$treeArray['children'][] = $children_file;
		}
		$treeList = '['.json_encode($treeArray).']';
		// <!-- end tree list -->

		$this->render('index1',array(
			'treeList'=>$treeList,
			'sheetTitle'=>$sheetTitle,
		));
	}

	/**
	 * Ajax读取sheet数据
	 */
	public function actionReadSheet(){
		
		if(Yii::app()->request->isAjaxRequest){
			$id = intval($_POST['id']);
			$sheet = $this->loadSheetModel($id);
			$table = $sheet->sheetTableName;
			$dyCols = array();
			// <!-- start dyCols -->
			foreach ($sheet->columns as $column){
				$dyCols[] = array(
					'field' => $column->columnTitle,
					'title' => $column->columnName,
				);
			}
			$columns = '['.json_encode($dyCols).']';
			// <!-- end dyCols -->
			$this->renderPartial('_datagrid',array('columns'=>$columns,'id'=>$id));
			//exit;
		}else{
			$this->redirect(array('site/index'));
		}
	}
	
	/**
	 * 为datagrid提供数据源
	 */
	public function actionDataProvider($id){
		$id = intval($id);
		$sheet = $this->loadSheetModel($id);
		$page = isset($_POST['page'])?intval($_POST['page']):1;
		$rows = isset($_POST['rows'])?intval($_POST['rows']):10;
		$dyData = array();
		$dyData['rows'] = array();
		// <!-- start dyData -->
		$conn = Yii::app()->db;
		$count = $conn->createCommand()->select('COUNT(*)')->from($sheet->sheetTableName)->queryScalar();
		$startIndex = ($page-1)*$rows;
		$dataReader = $conn->createCommand()->select()->from($sheet->sheetTableName)->limit($rows,$startIndex)->query();
		//$dataReader->readAll() //返回所有结果集到数组
		
		while (($row=$dataReader->read()) !== FALSE){
			$dyData['rows'][] = $row;
		}
		$dyData['total'] = intval($count);
		$dyData = json_encode($dyData);
		// <!-- end dyData -->
		echo $dyData;
		exit;
	}
	
	/**
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
				$this->redirect(array('site/upload','model'=>$model));
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
								$configFilePath = Yii::getPathOfAlias('ext').'phpexcel_config.php';
								require($configFilePath);
								
								$table_sheets = $configs['excel_sheets'];	
								$table_columns = $configs['excel_columns'];	
								//插入excel_sheets表
								$sql1 = "INSERT INTO `$table_sheets` VALUES (null,'$fileID','$currentSheetTitle','$currentSheetTableName');";
								//echo $sql1,"<br/>";
								$conn->createCommand($sql1)->execute();
								
								//插入excel_columns表
								$sheetID = $conn->lastInsertID;
								$sql2 = $this->insertCol($sheetID,$fields,$table_columns);
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
					//$this->actionIndex();
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
	 * 更新title，如果首字母重复则自动更改，同时update 表
	 * type in (filetitle,sheettitle,columntitle)
	 * ajax
	 */
	public function actionUpdateTitle(){
		if(Yii::app()->request->isAjaxRequest){
			$id = intval($_POST['id']);
			$title = $_POST['title'];  //这里需要做一下后台check，返回errorMsg
			$type = $_POST['type'];
			
			$types = array('file','sheet','column');
			if(!in_array($type, $types)){
				throw new CHttpException(404,'The requested page does not exist.');
				exit;
			}
			
			$field = $type.'Title';
			//$model = $this->loadSheetModel($id);
			
			if(method_exists($this, $method_name='load'.ucfirst($type).'Model')){
				$model = call_user_func(array($this,$method_name),$id);
			}
			
			
			$model->$field = $title;
			//$model->sheetTitle = $title;
			if($model->validate() && $model->save()){
				echo json_encode(array('flag'=>TRUE));
			}else{
				echo json_encode(array('flag'=>FALSE));
			}
			exit;
		}else{
			$this->redirect(array('site/index'));
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
	public function actionDownload($id,$type='file'){
		//header("Content-Type:text/html;charset=utf-8");
		$id = intval($id);
		if($type == 'file'){
			$file = $this->loadFileModel($id);
			$filename = $file->fileTitle;
			$filepath = $file->filePath;
			$sheets = $file->sheets;
		}elseif ($type == 'sheet'){
			$sheet = $this->loadSheetModel($id);
			$sheets[0] = $sheet;
			$ext = 'xlsx';
			$filename = $sheet->sheetTitle.'.'.$ext;
			$filepath = FILE_BASE_PATH.$filename;
			
			//$filepath = Yii::getPathOfAlias('application.data').DIRECTORY_SEPARATOR.$filename;
			//preg_replace的参数不允许是单独一个反斜杠\，所以要是 /\/
			//$filepath = preg_replace('/\\\\/', '/', $filepath); // '\\\\' 是 php 的字符串, 经过转义后, 是两个反斜杠, 再经过正则表达式引擎后才被认为是一个原文反斜线
			//echo $filepath;exit;
		}
		//兼容win环境
		if(strtolower(substr(PHP_OS,0,3)) === 'win'){
			$fileNameCharset = 'GBK';
			$filepath = $this->changeEncode('UTF-8', $fileNameCharset, $filepath);
		}
		$this->actionCreateFile($sheets,$filepath);
    	//清空输出缓存
		ob_clean();
		//输出到浏览器 
		$this->sendFile($filename, $filepath,$fileNameCharset);		
		exit;
	}
	
	/**
	 * 
	 * 在服务器目录下创建文件
	 * @param numeric $id
	 */
	public function actionCreateFile($sheets,$filepath){
		$conn = Yii::app()->db;
		$phpexcelFilePath = Yii::getPathOfAlias('application.vendors.PHPExcel');
		require_once $phpexcelFilePath.DIRECTORY_SEPARATOR.'PHPExcel.php';
			
		$objExcel = new PHPExcel();
		$objWriter = new PHPExcel_Writer_Excel2007($objExcel); // 用于 2007 格式 
		$objWriter->setOffice2003Compatibility(true); //向下兼容excel2005
						
		//组装数据
		foreach ($sheets as $sheetIndex => $sheet){
			try {
				spl_autoload_register(array('YiiBase','autoload'));
				$data = $columnArray = array();
				$selectstr = $comma = "";
				foreach ($sheet->columns as $column){
					$columnArray[] = $column->columnName;
					$selectstr .= $comma.$column->columnTitle;
					$comma = ",";
				}
				//var_dump($columnArray);exit;
				//从数据表中获取数据
				$data = $conn->createCommand()->select($selectstr)->from($sheet->sheetTableName)->queryAll();
				//var_dump($data);exit;
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
			catch (Exception $e){
				var_dump($e->getMessage());
				exit;
			}
		}
		//清空输出缓存
		ob_clean(); 
		//覆盖文件 
		$objWriter->save($filepath);
	}
	
	/**
	 * 数据表的增，删，改
	 * 数据表的数据操作必须都是事务型的
	 * @param int $id:data id
	 * @param int $sheetID:sheet id
	 * @param string @ac:array('insert','update','delete')
	 */
	public function actionCRUD($id,$sheetID,$ac){
		
		$actions = array('insert','update','delete');
		if(!in_array($ac,$actions)){
			throw new CHttpException(404,'The requested page does not exist.');
			exit;
		}
		$id = intval($id);
		$sheetID = intval($sheetID);
		$sheet = $this->loadSheetModel($sheetID);
		$conn = Yii::app()->db;
		$transaction = $conn->beginTransaction();
		switch ($ac){
			case 'delete':	
				try {
					$conn->createCommand()->delete($sheet->sheetTableName,'ID=:id',array(':id'=>$id));
					$transaction->commit();
					echo json_encode(array('flag'=>true));
				} catch (CDbException $e) {
					$transaction->rollback();
					echo "<pre>";
					print_r($e->getMessage());
					echo "</pre>";
				}
				break;
			case 'insert':
				if(isset($_POST['colData'])){
					try {
						$conn->createCommand()->insert($sheet->sheetTableName, $_POST['colData']);
						$transaction->commit();
						echo json_encode(array('flag'=>true));
					} catch (CDbException $e) {
						$transaction->rollback();
						echo "<pre>";
						print_r($e->getMessage());
						echo "</pre>";
					}
				}
				break;
			case 'update':
				if(isset($_POST['colData'])){
					try {
						$conn->createCommand()->update($sheet->sheetTableName, $_POST['colData'],'ID=:id',array(':id'=>$id));
						$transaction->commit();
						echo json_encode(array('flag'=>true));
					} catch (CDbException $e) {
						$transaction->rollback();
						echo "<pre>";
						print_r($e->getMessage());
						echo "</pre>";
					}
				}
				break;
		}
		exit;
	}
	
	/**
	 * 
	 * 删除worksheet
	 * 事务控制删除流程:删除关联columns->删除数据表->删除关联sheets表中记录
	 * @param int $id (sheetID)
	 */
	public function actionDeleteSheet($id){
		if(Yii::app()->request->isAjaxRequest){
			$configFilePath = Yii::getPathOfAlias('ext').'phpexcel_config.php';
			require($configFilePath);					
			$table_sheets = $configs['excel_sheets'];	
			$table_columns = $configs['excel_columns'];	
			$id = intval($id);
			$conn = Yii::app()->db;
			$sheet = $this->loadSheetModel($id);
			$transaction = $conn->beginTransaction();
			try {
				$command = $conn->createCommand();
				$command->delete(($table_columns),'sheetID=:sheetID',array(':sheetID'=>$sheet->ID));
				$command->dropTable($sheet->sheetTableName);
				$command->delete($table_sheets,'ID=:ID',array(':ID'=>$id));
				$transaction->commit();
			} catch (CDbException $e) {
				$transaction->rollback();
				echo "<pre>";
				print_r($e->getMessage());
				echo "</pre>";
				exit;
			}
			echo true;exit;
		}else{
			$this->redirect(array('site/index'));
		}
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
	function sendFile($filename,$filepath,$charset = 'UTF-8',$mimeType = 'application/octet-stream'){
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
		
		//mod_xsendfile
		$xsend = $this->ckApacheModule('mod_xsendfile');
		if($xsend){
			header("X-Sendfile:".$filepath);
		}else {
			header("Content-Length: {$filesize}");
			readfile($filepath);
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