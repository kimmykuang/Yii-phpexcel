<?php $this->pageTitle=Yii::app()->name; ?>
<?php echo CHtml::form('','POST',array('enctype'=>'multipart/form-data'));?>
<?php echo CHtml::activeFileField($model,'excelfile');?>
<?php echo CHtml::errorSummary($model);?>
<?php echo CHtml::submitButton('Submit',array('value'=>"提交"));?>
<?php echo CHtml::endForm();?>
<script type="text/javascript">
//这里检测是否有文件提交
//onbeforesubmit()
</script>
