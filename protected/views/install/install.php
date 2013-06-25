<div class="dtitle">
	<p>一键安装Yii-Phpexcel系统</p>
</div>
<div class="dseparator"></div>
<?php $form=$this->beginWidget('CActiveForm',array(
	'id'=>'config-form',
	//'class'=>'dform',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
	'htmlOptions'=>array('class'=>'dform'),
));?>
<?php echo $form->errorSummary($model);?>
	<table>
		<tr>
			<td class="tdleft"><?php echo $form->labelEx($model,'dbname');?></td>
			<td class="tdright"><?php echo $form->textField($model,'dbname',array('value'=>'phpexcel'));?></td>
			<td><?php echo $form->error($model,'dbname');?></td>
		</tr>
		<tr>
			<td class="tdleft"><?php echo $form->labelEx($model,'table_sheets');?></td>
			<td class="tdright"><?php echo $form->textField($model,'table_sheets',array('value'=>'excel_sheets'));?></td>
			<td><?php echo $form->error($model,'table_sheets');?></td>
		</tr>
		<tr>
			<td class="tdleft"><?php echo $form->labelEx($model,'table_files');?></td>
			<td class="tdright"><?php echo $form->textField($model,'table_files',array('value'=>'excel_files'));?></td>
			<td><?php echo $form->error($model,'table_files');?></td>
		</tr>
		<tr>
			<td class="tdleft"><?php echo $form->labelEx($model,'table_columns');?></td>
			<td class="tdright"><?php echo $form->textField($model,'table_columns',array('value'=>'excel_columns'));?></td>
			<td><?php echo $form->error($model,'table_columns');?></td>
		</tr>
		<tr><td></td><td><?php echo CHtml::submitButton('安装',array('class'=>'dsubmit'));?></td><td></td></tr>
	</table>
	
<?php $this->endWidget();?>
<style>
.dtitle{
	font-size:25px;
}
.dseparator{
	border:2px solid #ccc;
}
.dform{
	margin-top:30px;
	width:500px;
	margin-left:auto;
	margin-right:auto;
}
.tdleft{
	padding-right:40px;
}
.tdright{
	padding-right:40px;
}
.dsubmit{
	margin-top:10px;
	float:right;
	margin-right:40px;
}
</style>