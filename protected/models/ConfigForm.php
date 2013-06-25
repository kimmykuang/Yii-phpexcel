<?php
class ConfigForm extends CFormModel
{
	public $dbname;              //default:phpexcel
	public $table_sheets;        //default:excel_sheets
	public $table_files;         //default:excel_files
	public $table_columns;       //default:excel_columns

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('dbname, table_sheets, table_files, table_columns', 'required'),
			array('dbname', 'length', 'max'=>20),
			array('table_sheets, table_files, table_columns', 'length', 'max'=>25),
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
			'dbname'       => '数据库名',
			'table_files'  => '文件表',
			'table_sheets' => '工作薄表',
			'table_columns'=> '列表',
		);
	}
}