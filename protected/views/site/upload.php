<?php
$this->pageTitle = CHtml::encode(iconv('gbk','utf-8',Yii::app()->name)).' | '.$pageTitle;
?>
<style>
.hide {
	display:none;
}
</style>
<script type="text/javascript">
	function search1 () {
		$('#upload').click();
	}
	function change(){
		$('#text1').val($('#upload').val());
	}
</script>

        <!-- upload file -->
        <center><!--使panel居中-->
        	<div  class="easyui-panel" title="文件上传" style="width:500px;height:auto;padding:10px;text-align:center;">
        		<div style="margin-top: 5%;margin-bottom:5%">
					<?php echo CHtml::form('upload','POST',array('enctype'=>'multipart/form-data'));?>
					<?php echo CHtml::activeFileField($model,'excelfile',array('id'=>"upload",'class'=>"hide",'onchange'=>"change()"));?><!--隐藏activeFileField
				-->
					<input type="text" id="text1" name="text1" onclick="search1()"/>
					<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-search" onclick="search1()">浏览</a>
					<?php echo CHtml::linkButton('提交',array('value'=>"1",'class'=>"easyui-linkbutton",'iconCls'=>"icon-redo"));?>
					<?php echo CHtml::errorSummary($model);?>
					<?php echo CHtml::endForm();?>
        		</div>
  			</div>
  		</center>