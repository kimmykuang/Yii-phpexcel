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
                onDblClick:function(node){
                  if(node.text.indexOf('.xlsx')){
                    //只有点击sheet节点才现出工作条
                  }
                  else{
                    $('#tb').css('display','block');//一旦用户单击sheet节点，则显示工作条
                  }
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
                /*
                onDblClick:function(node){
                    $('#list').datagrid('reload');  //reload the datagrid after data changed
                 },
                 */
            });
	//动态加载树列表
	$('#tree').tree('loadData',treeList);

});

</script>
<!--在线情况右键js代码开始-->
<script type="text/javascript">
  $(function(){
    $('#dg1').datagrid({
      url: '<?php echo Yii::app()->request->baseUrl; ?>/protected/data/online.json',
      fitColumns: false,
      columns:[[
        {field:'name',title:'登录名',width:100},
        {field:'time',title:'登录时间',width:100},
        {field:'ip',title:'IP',width:100}
      ]],
      onHeaderContextMenu: function(e, field){
        e.preventDefault();
        if (!cmenu){
          createColumnMenu();
        }
        cmenu.menu('show', {
          left:e.pageX,
          top:e.pageY
        });
      }
    });
  });
  var cmenu;
  function createColumnMenu(){
    cmenu = $('<div/>').appendTo('body');
    cmenu.menu({
      onClick: function(item){
        if (item.iconCls == 'icon-ok'){
          $('#dg1').datagrid('hideColumn', item.name);
          cmenu.menu('setIcon', {
            target: item.target,
            iconCls: 'icon-empty'
          });
        } else {
          $('#dg1').datagrid('showColumn', item.name);
          cmenu.menu('setIcon', {
            target: item.target,
            iconCls: 'icon-ok'
          });
        }
      }
    });
    var fields = $('#dg1').datagrid('getColumnFields');
    for(var i=0; i<fields.length; i++){
      var field = fields[i];
      var col = $('#dg1').datagrid('getColumnOption', field);
      cmenu.menu('appendItem', {
        text: col.title,
        name: field,
        iconCls: 'icon-ok'
      });
    }
  }
</script>
<!--在线情况右键js代码开始-->
<center>
    <!--布局控件-->
	<div class="easyui-layout" style="width:100%;height:500px;">
	
		<!--tree控件-->    
        <div data-options="region:'west',split:true" title="Excel文件结构" style="width:200px;">
            <ul id="tree"></ul>
            <!--tree的节点右键操作--> 
            <div id="mm" class="easyui-menu" style="width:120px;">  
                <div onclick="check()" data-options="iconCls:'icon-search'">查看工作薄</div>
                <div onclick="edit()" data-options="iconCls:'icon-edit'">重命名工作薄</div> 
                <div onclick="remove()" data-options="iconCls:'icon-remove'">删除工作薄</div>  
                <div class="menu-sep"></div>
            </div>
        </div>
        <!--东部east开始-->
        <div data-options="region:'east',split:false" style="width:200px;">  
            <div class="easyui-accordion" data-options="fit:false,border:false,height:240">  
                <div title="日历" style="padding-top:20px">  
                    <div id="cc" class="easyui-calendar" data-options="width:180" style="margin:auto;"></div>
                </div>
                <!--  
                <div title="在线情况" data-options="selected:true" style="padding:10px;">  
                    content2  
                </div>  
                <div title="Title3" style="padding:10px">  
                    content3  
                </div>  
              -->
            </div>
            <div class="easyui-accordion" data-options="fit:false,border:false,height:250">
                <div title="在线情况" >
                    <table id="dg1" class="easyui-datagrid"  >  
                    </table>
                </div>
            </div>
        </div>
        <!-- datagrid -->
        <div data-options="region:'center',title:'<?=$sheetTitle?>'" class="center"> 
        	
        		<div id="tb" style="padding:5px;height:auto;display:none">   
        			<div style="margin-bottom:5px">  
        				<a href="#" class="easyui-linkbutton" iconCls="icon-add" plain=true onclick="newItem()">新增</a> 
         				<a href="#" class="easyui-linkbutton" iconCls="icon-edit" plain=true onclick="editItem()">编辑</a>  
                   	 <a href="#" class="easyui-linkbutton" iconCls="icon-remove" plain=true onclick="removeItem()">删除</a>
                    	<a href="#" class="easyui-linkbutton" iconCls="icon-save" plain=true onclick="exportSheet()">导出当前工作薄</a>
        			</div> 	 
   				</div>
   				<table id= 'list' class="easyui-datagrid"></table>
   			<div id="datagrid_view" style="width:100%,height:100%">
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
                	<input type="text" name="title" id="title" class="easyui-validatebox" required="true" />
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