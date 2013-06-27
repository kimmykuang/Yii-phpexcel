<?php 
$this->pageTitle = CHtml::encode(iconv('gbk','utf-8',Yii::app()->name)).' | '.$pageTitle;
?>
<center>
    <!--布局控件-->
	<div class="easyui-layout" style="width:1200px;height:500px;">
	
		<!--tree控件-->    
        <div data-options="region:'west',split:true" title="Excel文件结构" style="width:180px;">
            <ul id="tree"></ul>
        </div>  
        <!-- tree右键-sheet -->
        <div id="menu3" class="easyui-menu">
        	<div onclick="exportSheet()" data-options="iconCls:'icon-save'" >导出工作薄</div>
        	<div onclick="renameSheet()" data-options="iconCls:'icon-edit'">重命名工作薄</div>
        	<div onclick="removeit()" data-options="iconCls:'icon-cancel'">删除工作薄</div>
        </div>
        <!-- tree右键-file -->
        <div id="menu2" class="easyui-menu">
            <div onclick="" data-options="iconCls:'icon-add'">新建工作薄</div>
        	<div onclick="removeit()" data-options="iconCls:'icon-cancel'">删除文件</div>
        	<div onclick="" data-options="iconCls:'icon-remove'">批量删除工作薄</div>
        	<div class="menu-sep"></div>
        	<div onclick="expand()">展开</div>
        	<div onclick="collapse()">收起</div>
        </div>
        <!-- tree右键-root -->
        <div id="menu1" class="easyui-menu">
        	<div onclick="" data-options="iconCls:'icon-add'">新建文件</div>
        	<div onclick="removeit()" data-options="iconCls:'icon-cancel'">删除所有文件</div>
        	<div class="menu-sep"></div>
        	<div onclick="expand()">展开</div>
        	<div onclick="collapse()">收起</div>
        </div>
         
        <!-- datagrid -->
        <div data-options="region:'center',title:'<?=$sheetTitle?>'" class="center"> 
        	
        		<div id="tb" style="padding:5px;height:auto;display:none;">   
        			<div style="margin-bottom:5px">  
        				<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-reload" plain=true onclick="reloadSheet()">刷新</a>
        				<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-add" plain=true onclick="newItem()">新增</a> 
         				<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-edit" plain=true onclick="editItem()">编辑</a>  
                   	 	<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-remove" plain=true onclick="removeItem()">删除</a>
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
    
    <!--dlg1-->
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
    <div id="dlg1-buttons">  
        <a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveTreeForm(this)">Save</a>  
        <a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg1').dialog('close')">Cancel</a>  
    </div> 
    
    <!-- dlg2 -->
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

//展开节点
function expand(){
	var t = $("#tree");
	var node = t.tree('getSelected');
	t.tree('expand',node.target);
}

//收起节点
function collapse(){
	var t = $("#tree");
	var node = t.tree('getSelected');
	t.tree('collapse',node.target);	
}

//刷新当前工作薄
function reloadSheet(){
	$('#list').datagrid('reload');
}

//重命名工作薄
function renameSheet(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	if(node && node.attributes['lvl'] === 3){
		$('#treefm #title').val('');
		$('#dlg1').dialog('open');
		$('#sheetid').val(node.attributes['nodeid']);
		$('#oldSheetName').text(node.text);
		url = '<?=Yii::app()->createUrl('site/updatetitle')?>';
	}else{
		$.messager.alert('消息提示','请先选择一个工作薄');
	}
}

//删除文件
function removeit(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	if(node){
			var text = (node.text === 'All Documents')?'所有文件':node.text;
			$.messager.confirm('确认提示','确认删除:'+text+' 吗？如果您选择的是删除文件或者所有文件，那么文件下的所有工作薄也会被删除!',function(r){
				if(r){
					url = '<?=Yii::app()->createUrl('site/delete');?>';
					$.post(url,{id:node.attributes['nodeid'],lvl:node.attributes['lvl']},function(result){
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
		$.messager.alert('消息提示','请先选择一个文件');
	}
}

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
				$.messager.alert('消息提示','重命名工作薄出错，请刷新页面后重新操作');
			}
		},
	});
}

//新增数据
function newItem(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	if(node && node.attributes['lvl'] === 3){
		$('#dlg2').css('height','100%').children().children().css('height','100%');
		$('#datafm').form('clear').parents('#dlg2').dialog('open');
		url = '<?php echo Yii::app()->createUrl('site/crud');?>'+'?id=0&ac=insert&sheetID='+node.attributes['nodeid'];
	}else{
		$.messager.alert('消息提示','请先选择一个工作薄!');
	}
}

//编辑数据
function editItem(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	var row = $('#list').datagrid('getSelected');
	if(node && node.attributes['lvl'] === 3){
		if(row){
			var dlg2 = $('#dlg2');
			dlg2.css('height','100%').children().children().css('height','100%');
			$('#datafm').form('clear').parents('#dlg2').dialog('open');
			var opt = {};
			for (var i in row){
				opt['colData['+i+']'] = row[i];
			}
			$('#datafm').form('load',opt);
			url = '<?php echo Yii::app()->createUrl('site/crud');?>'+'?id='+row.ID+'&ac=update&sheetID='+node.attributes['nodeid'];
		}
	}else{
		$.messager.alert('消息提示','请先选择一个工作薄!');
	}
}

//删除一条数据
function removeItem(){
	var t = $('#tree');
	var node = t.tree('getSelected');
	var row = $('#list').datagrid('getSelected');
	if(node && node.attributes['lvl'] === 3){
		if(row){
			$.messager.confirm('确认提示','确定要删除该条数据吗？',function(r){
				if(r){
					url = '<?php echo Yii::app()->createUrl('site/crud');?>'+'?id='+row.ID+'&ac=delete&sheetID='+node.attributes['nodeid'];
					$.post(url,function(result){
						if(result){
							$('#list').datagrid('reload');
							$.messager.alert('消息提示','数据删除成功');
						}
					});
				}
			});
		}
	}else{
		$.messager.alert('消息提示','请先选择一个工作薄!');
	}
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

//导出当前工作薄
function exportSheet(obj){
	//alert(obj);
	var t = $('#tree');
	var node = t.tree('getSelected');
	if(node && node.attributes['lvl'] === 3){
		var id = node.attributes['nodeid'];
		url = '<?php echo Yii::app()->createUrl('site/download');?>'+'?id='+id+'&type=sheet';
		window.open(url,'newwindow');
	}
}

</script>

<script type="text/javascript"> 
$(document).ready(function(){
var treeList = <?php echo $treeList;?>;
$('#tree').tree({
       		animate:true,
       		lines:true,
            dnd:true,
            onClick:function(node){
            	if(node.attributes['lvl'] === 3){
                    $('#dlg1').dialog('close');
                    $('#dlg2').dialog('close');
                    var dataGrid = $('#list');
                	var url = '<?php echo Yii::app()->createUrl('site/readsheet');?>';
               		$.ajax({
                      	type:'post',
                       	data:{id:node.attributes['nodeid']},
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
            onContextMenu:function(e,node){
                e.preventDefault();
                $('#tree').tree('select',node.target);
                var menuIndex = node.attributes['lvl'];
                $('#menu'+menuIndex).menu('show',{left:e.pageX,top:e.pageY});
            },
        });
//动态加载树列表
$('#tree').tree('loadData',treeList);
});
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