<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<!--tree-->
<script type="text/javascript"> 
$(document).ready(function(){
var treeList = <?php echo $treeList;?>;
$('#tree').tree({
       			animate:true,
                //dnd:true,
                onClick:function(node){
                	if(node.attributes['sheetID'] !== ''){
                    	$('#dlg1').dialog('close');
                    	$('#dlg2').dialog('close');
                    	var dataGrid = $('#list');
                		var url = '<?php echo Yii::app()->createUrl('site/readsheet');?>';
               			$.ajax({
                      		type:'post',
                       		data:{id:node.attributes['sheetID']},
 					   		url:url,
 					   		success:function(data,textStatus){
 					      		$('#datagrid_view').html('').append(data);
 					      		dataGrid.datagrid('getPager').pagination('select', 1);
  					      		//更新dlg2
  					      		var colList = dataGrid.datagrid('getColumnFields');
  					      		var colStr = "";
  					      		for (var i in colList){
  	  					      		colStr += "<div class='fitem'><label>"+dataGrid.datagrid('getColumnOption',colList[i]).title+":</label><input style='float:right;' name=colData["+colList[i]+"] type='text' /></div>";
  					      		}
  					      		$('#datafm').html('').append(colStr);
                       		},
                		});  
                	}
                },
                onDblClick:function(node){
                    var c = $('#list').datagrid('getColumnOption','c0');
                    var a = $('#list').datagrid('getColumnFields');
                },
            });
	//动态加载树列表
	$('#tree').tree('loadData',treeList);
});

</script>
<center>
    <!--布局控件-->
	<div class="easyui-layout" style="width:1200px;height:500px;">
	
		<!--tree控件-->    
        <div data-options="region:'west',split:true" title="Excel文件结构" style="width:180px;">
            <ul id="tree"></ul>
        </div>  
         
        <!-- datagrid -->
        <div data-options="region:'center',title:'<?=$sheetTitle?>'" class="center"> 
        	
        		<div id="tb" style="padding:5px;height:auto">   
        			<div style="margin-bottom:5px">  
        				<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-reload" plain=true onclick="reloadSheet()">刷新</a>
        				<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-add" plain=true onclick="newItem()">新增条目</a> 
         				<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-edit" plain=true onclick="editItem()">编辑条目</a>  
                   	 	<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-remove" plain=true onclick="removeItem()">删除条目</a>
                    	<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-save" plain=true onclick="exportSheet()">导出当前工作薄</a>
                    	<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-edit" plain=true onclick="renameSheet()">重命名工作薄</a>
                    	<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-cancel" plain=true onclick="removeSheet()">删除当前工作薄</a>
        			</div> 	 
   				</div>
   				<table id= 'list' class="easyui-datagrid"></table>
   			
   			<div id="datagrid_view">
   				<?php $this->renderPartial('_index');?>
   			</div>
        </div> 
        <!--中部center结束-->     
    </div> 
    <!--layout结束-->
    
    <!--tree右键的编辑按钮-->
    <div id="dlg1" class="easyui-dialog" style="width:300px;height:180px;padding:10px 20px" closed="true" buttons="#dlg1-buttons" title="修改工作薄名称"> 
            <form id="treefm" method="post" action="">
                <div class="fitem">
                	<label>Old Name:</label>
                	<label id="oldSheetName"></label>
                </div>
                <div class="fitem">
                	<label>New Name:</label>
                	<input type="text" name="title" id="title" class="easyui-validatebox" required="true" />
                </div>
                <input type="hidden" name="type" value="sheet" id="type" />
                <input type="hidden" name="id" id="sheetid" value="" />
            </form>
    </div>   
    <!--dlg1结束-->
    
    <!--dlg1的操作按钮-->
    <div id="dlg1-buttons">  
        <a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveTreeForm()">Save</a>  
        <a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg1').dialog('close')">Cancel</a>  
    </div> 
    
    <!-- 数据更新表单 -->
    <div id="dlg2" class="easyui-dialog" style="width:300px;padding:10px 20px;" closed="true" buttons="#dlg2-buttons" >
    	<div class="ftitle">数据操作</div>
    	<form id="datafm" method="post" action="">
    		
    	</form>
    </div>
    <div id="dlg2-buttons">
    	<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveDataForm()">Save</a>
    	<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg2').dialog('close')">Cancel</a>
    </div>
</center>
<script type="text/javascript">
var url;
//刷新当前工作薄
function reloadSheet(){
	$('#list').datagrid('reload');
}
//重命名工作薄
function renameSheet(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	if(node){
		if(node.attributes['sheetID'] !== ''){
			$('#treefm #title').val('');
			$('#dlg1').dialog('open');
			$('#sheetid').val(node.attributes['sheetID']);
			$('#oldSheetName').text(node.text);
			url = '<?=Yii::app()->createUrl('site/updatetitle')?>';
		}
	}else{
		$.messager.alert('消息提示','请先选择一个工作薄');
	}
}
//删除当前工作薄
function removeSheet(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	if(node && t.tree('isLeaf',node.target)){

			$.messager.confirm('确认提示','确认删除当前工作薄:'+node.text+' 吗？一旦删除，将无法恢复数据',function(r){
				if(r){
					 
					url = '<?=Yii::app()->createUrl('site/deletesheet');?>'+'?id='+node.attributes['sheetID'];
					$.post(url,function(result){
						if(result){
							t.tree('remove',node.target);
							$.messager.alert('消息提示','您已经删除了工作薄：'+node.text,'info',function(){
								$("<div class=\"datagrid-mask\"></div>").css({display:"block",width:"100%",height:$(window).height()}).appendTo("body"); 
								$("<div class=\"datagrid-mask-msg\"></div>").html("正在处理，请稍候。。。").appendTo("body").css({display:"block",left:($(document.body).outerWidth(true) - 190) / 2,top:($(window).height() - 45) / 2});
								location.reload();
							});
						}
					});
				}
			});
	}else{
		$.messager.alert('消息提示','请先选择一个工作薄');
	}
}
//待重构代码
function saveTreeForm(){
	$.ajax({
		url:url,
		data:$('#treefm').serialize(),
		type:'POST',
		success:function(data){
			if(data){
				$('#dlg1').dialog('close');
				var node = $('#tree').tree('getSelected');
				$('#tree').tree('update',{
					target:node.target,
					text:$('#title').val(),
				});
			}else{
				$.messager.alert('Error','rename sheet name fail,please try again.');
			}
		},
	});
}
//新增数据
function newItem(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	if(node && t.tree('isLeaf',node.target)){
		$('#dlg2').css('height','100%').children().children().css('height','100%');
		$('#datafm').form('clear').parents('#dlg2').dialog('open');
		url = '<?php echo Yii::app()->createUrl('site/crud');?>'+'?id=0&ac=insert&sheetID='+node.attributes['sheetID'];
	}else{
		$.messager.alert('消息提示','请先选择一个工作薄!');
	}
}
//编辑数据
function editItem(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	var row = $('#list').datagrid('getSelected');
	if(node && t.tree('isLeaf',node.target)){
		if(row){
			var dlg2 = $('#dlg2');
			dlg2.css('height','100%').children().children().css('height','100%');
			$('#datafm').form('clear').parents('#dlg2').dialog('open');
			var opt = {};
			for (var i in row){
				opt['colData['+i+']'] = row[i];
			}
			$('#datafm').form('load',opt);
			url = '<?php echo Yii::app()->createUrl('site/crud');?>'+'?id='+row.ID+'&ac=update&sheetID='+node.attributes['sheetID'];
		}
	}else{
		$.messager.alert('消息提示','请先选择一个工作薄!');
	}
}
//删除一条数据
function removeItem(){
	
}
//异步提交数据表单
function saveDataForm(){
	$('#datafm').form('submit',{
		url:url,
		success:function(result){
			if(result){
				$('#dlg2').dialog('close');
				$('#list').datagrid('reload');
			}
		},	
	});
}
function exportSheet(){
	
}
</script>
<style>
.fitem {
	margin-top:15px;
	font-size: 12px;
}
.ftitle {
	font-size: 14px;
	font-weight: bold;
	padding: 5px 0;
	margin-bottom: 10px;
	border-bottom: 1px solid #ccc;
}
</style>