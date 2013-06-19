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
                onContextMenu :function(e,node){
                    e.preventDefault();
                    $(this).tree('select',node.target);
                    $('#mm').menu('show',{  
                    left: e.pageX,  
                    top: e.pageY  
                    });
                },
                onClick:function(node){
                	if(node.attributes['sheetID'] !== ''){
                    	
                		var url = '<?php echo Yii::app()->createUrl('site/readsheet');?>';
               			$.ajax({
                      		type:'post',
                       		data:{id:node.attributes['sheetID']},
 					   		url:url,
 					   		success:function(data,textStatus){
 					      		$('#datagrid_view').html('').append(data);
  					      		$('#list').datagrid('getPager').pagination('select', 1);	// select the second page
                       		},
                		});  
                	}
                },
                onDblClick:function(node){
                    $('#list').datagrid('reload');  //reload the datagrid after data changed
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
            <!--tree的节点右键操作--> 
            <div id="mm" class="easyui-menu" style="width:120px;">  
                <div onclick="check()" data-options="iconCls:'icon-search'">查看工作薄</div>
                <div onclick="edit()" data-options="iconCls:'icon-edit'">重命名工作薄</div> 
                <div onclick="remove()" data-options="iconCls:'icon-remove'">删除工作薄</div>  
                <div class="menu-sep"></div>
            </div>
        </div>  
         
        <!-- datagrid -->
        <div data-options="region:'center',title:'<?=$sheetTitle?>'" class="center"> 
        	
        		<div id="tb" style="padding:5px;height:auto">   
        			<div style="margin-bottom:5px">  
        				<a href="#" class="easyui-linkbutton" iconCls="icon-add" plain=true onclick="newItem()">新增</a> 
         				<a href="#" class="easyui-linkbutton" iconCls="icon-edit" plain=true onclick="editItem()">编辑</a>  
                   	 <a href="#" class="easyui-linkbutton" iconCls="icon-remove" plain=true onclick="removeItem()">删除</a>
                    	<a href="#" class="easyui-linkbutton" iconCls="icon-save" plain=true onclick="exportSheet()">导出当前工作薄</a>
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
    <div id="dlg1" class="easyui-dialog" style="width:300px;height:180px;padding:10px 20px" closed="true" buttons="#dlg-buttons" title="修改工作薄名称"> 
            <form id="treefm" method="post" action="">
                <div class="treefmItem">
                	<label>Old Name:</label>
                	<label id="oldSheetName"></label>
                </div>
                <div class="treefmItem">
                	<label>New Name:</label>
                	<input type="text" name="title" class="easyui-validatebox" required="true" />
                </div>
                <input type="hidden" name="type" id="type" value="sheet" />
                <input type="hidden" name="id" id="sheetid" value="" />
            </form>
    </div>   
    <!--dlg1结束-->
    
    <!--dlg1的操作按钮-->
    <div id="dlg-buttons">  
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveTreeForm()">Save</a>  
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg1').dialog('close')">Cancel</a>  
    </div> 
</center>
<script type="text/javascript">
var url;
function check(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	if(node.attributes['sheetID'] !== ''){
    	
			url = '<?php echo Yii::app()->createUrl('site/readsheet');?>';
			$.ajax({
      		type:'post',
       		data:{id:node.attributes['sheetID']},
		   		url:url,
		   		success:function(data,textStatus){
		      		$('#datagrid_view').html('').append(data);
		      		$('#list').datagrid('getPager').pagination('select', 1);	// select the second page
       		},
		});  
	}
}
        
function edit(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	if(node.attributes['sheetID'] !== ''){
		$('#dlg1').dialog('open').children('#treefm').form('clear');
		$('#sheetid').val(node.attributes['sheetID']);
		$('#oldSheetName').text(node.text);
		url = '<?=Yii::app()->createUrl('site/updatetitle')?>';
	}else{
		$.messager.alert('提示','只能重命名工作薄');
	}
}

function remove(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	if (confirm("你真的确定要删除吗?")) {
		t.tree('remove', node.target);
	}
}

function saveTreeForm(){
	$.ajax({
		url:url,
		data:$('#treefm').serialize(),
		type:'POST',
		success:function(data){
			//alert(data);
			//var data = eval('('+data+')');
			//alert(data);
			if(data == 'success'){
				$('#dlg1').dialog('close');
				//$('#tree').tree('reload');  //tree可以reload的条件是有url提供数据源或者直接重写reload方法，异步去后台再次读取数据后loadData
				
			}else{
				$.messager.alert('Error','rename sheet name fail,please try again.');
			}
		},
	});
}

function newItem(){
	
}

function editItem(){
	
}

function removeItem(){
	
}

function exportSheet(){
	
}
</script>
<style>
.treefmItem {
	margin-top:15px;
	
}
</style>