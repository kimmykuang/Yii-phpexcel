<?php
/**
 * ActiveRecord for upload file model
 */

//建立好数据库后使用CActiveRecord
//需要在某个action中初始化数据库
class UpFile extends CActiveRecord{
	
	public $fileName;
	public $fileId;
	public $uploadTime;
	public $filePath;
	public $userIp;
	public $lastModifyTime;
	public $lastModifyUserIp;
	public $fileType;
	public $fileSize;
	const MAX_FILE_SIZE = 52428800;  //50MB,这里是字节数
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Spacefield the static model class
	 */
	public static function model($className=__CLASS__)
	{
		//use like this : Example::model()->xxx
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		//@return prefix_tablename
		return '{{tables}}';
	}
	
	/**
	 * verify rules
	 * @return validation rules array when function validate() is called
	 */
	public function rules(){
		return array(
			//required attributes
			array('fileName,fileType,fileSize','required'),
			//file extension should be xls or xlsx
			array('fileType','in','range'=>array('xls','xlsx'),'message'=>'只接受.xls或者.xlsx为后缀名的Excel文件'),
			//uplaod file size should not bigger than MAX_FILE_SIZE
			array('fileSize','validateFileSize','maxsize'=>self::MAX_FILE_SIZE),
		);
	}
	
	/**
	 * do before  $model->validate()
	 * @see CModel::beforeValidate()
	 */
	public function beforeValidate(){
		//var_dump($this->attributes);exit;
		return true;
	}
	
	
	/**
	 * do after $model->validate()
	 * @see CModel::afterValidate()
	 */
	public function afterValidate(){
		//var_dump($this->attributes);exit;
		return true;
	}
	
	/**
	 * 验证规则：文件的大小
	 */
	public function validateFileSize($attribute,$params){
		if($this->fileSize > $params['maxsize']){
			$this->addError($attribute, '文件大小不能超过'.self::MAX_FILE_SIZE/1024/1024);
		}
		return true;
	}
	
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}
	
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'fileName'=>'文件名',
			'fileId'=>'文件ID',
			'uploadTime'=>'上传时间',
			'filePath'=>'文件路径',
			'userIp'=>'上传者的IP',
			'fileType'=>'文件后缀名',
			'lastModifyTime'=>'最近一次修改时间',
			'lastModifyUserIp'=>'最近一次修改的用户IP',
		);
	}
	
	/**
	 * before save action this function will be execed
	 */
	public function beforeSave()
	{
		parent::beforeSave();
		$this->lastModifyTime = $this->uploadTime = date('Y-m-d H:i:s',time());
		$this->lastModifyUserIp = $this->userIp = Yii::app()->request->userHostAddress;
		var_dump($this->attributes);exit;
		return true;
	}
	
	/**
	 * after save action this function will be execed
	 */
	public function afterSave()
	{
		parent::afterSave();
		
		//do something
		
		return true;
	}
}