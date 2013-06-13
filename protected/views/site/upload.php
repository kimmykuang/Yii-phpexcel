<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>


        <!-- upload file -->
        
			<?php echo CHtml::form('upload','POST',array('enctype'=>'multipart/form-data'));?>
			<?php echo CHtml::activeFileField($model,'excelfile');?>
			<?php echo CHtml::errorSummary($model);?>
			<?php echo CHtml::submitButton('Submit',array('value'=>"提交"));?>
			<?php echo CHtml::endForm();?>
        
  