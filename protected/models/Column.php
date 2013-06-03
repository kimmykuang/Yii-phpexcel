<?php
/**
 * ActiveRecord for upload file model
 */

class Column extends CActiveRecord{

	public $ID;
	public $tableID;
	public $columnTitle;
	public $columnName;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Spacefield the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		//@return prefix_tablename
		return '{{columns}}';
	}
	
	/**
	 * verify rules
	 * @return validation rules array when function validate() is called
	 */
	public function rules(){
		return array(
			
		);
	}
	
	/**
	 * do before  $model->validate()
	 * @see CModel::beforeValidate()
	 */
	public function beforeValidate(){
		return true;
	}
	
	
	/**
	 * do after $model->validate()
	 * @see CModel::afterValidate()
	 */
	public function afterValidate(){
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
			'ID'=>'字段ID',
			'tableID'=>'工作薄表ID',
			'columnTitle'=>'列名',
			'columnName'=>'字段名',
		);
	}
	
	/**
	 * before save action this function will be execed
	 */
	public function beforeSave()
	{
		parent::beforeSave();
		//do something here
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