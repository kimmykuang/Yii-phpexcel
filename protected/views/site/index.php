<?php 
$this->pageTitle = CHtml::encode(iconv('gbk','utf-8',Yii::app()->name)).' | '.$pageTitle;
?>
<!--tree-->
<script type="text/javascript"> 
$(document).ready(function(){
	/**加载完页面后也要约束layout的高度
	*/
    //var height = $(window).height();
    //height = height * 0.8;
    //$('#layout').height(height);

	$('#content').before($('.easyui-layout')).remove();
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
</script> <!--在线情况右键js代码结束-->

<script type="text/javascript">
	$(window).resize(function() {
		//$('#list').datagrid('resize');
    	var width = $(this).width();
    	var height = $(this).height();
    	//alert(height);
    	height = height * 0.8;
    	//alert(height);
    	//alert($(this).width());
    	//alert($(this).height());
    	$('#layout').height(height);
    	
	});
	$(window).load(function (){
		var width = $(this).width();
    	//var height = $(this).height();
    	//height = height * 0.8;
    	//alert('浏览器width:'+ $(this).width());
    	//alert('浏览器height:'+ $(this).height());
    	//$('#layout').height(height);
    	$('#layout').width(width);
    	//alert('west的width:' + $('#west').width());
    	//alert('east的width:' + $('#east').width());
    	//alert('center的width:' + $('#center').width());
    	//alert('west + east + center:' + ($('#west').width() + $('#east').width() + $('#center').width()));
    	
    	//var hight = $('#center').width();
    	//var width = $('#center').height();

    	//$('#panel1').height(hight);
    	//$('#panel1').width(width);
	}); 
	//table下td各宽度 tdW

	//宽度相加获得整体宽  tableW

	//计算出td对于table的比例%

	//table父级width boxW

	//比较boxW和tableW

	//boxW>tableW 按照比例导入td宽度
</script>
<center>
    <!--布局控件-->
	<div id="layout" class="easyui-layout" fit="true" style="width:100%;height:200px;">
	
		<!--tree控件-->    
        <div id="west" data-options="region:'west',split:true" title="Excel文件结构" style="width:180px;">
            <ul id="tree"></ul>
        </div> 
        <!--东部east开始-->
        <div id="east" data-options="region:'east',split:true" style="width:196px;">  
            <div class="easyui-accordion" data-options="fit:false,border:false,height:240">  
                <div title="日历" >  
                    <div id="cc" class="easyui-calendar"  style="margin:auto;width:194px;height:212px"></div>
                </div>
                <!--  data-options="width:180"
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
        <div id="center" data-options="region:'center',split:true,title:'<?=$sheetTitle?>'" class="center"> 
        		<div id="tb" style="padding:5px;height:auto;display:none;">   
        			<div style="margin-bottom:5px">  
        				<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-reload" plain=true onclick="reloadSheet()">刷新</a>
        				<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-add" plain=true onclick="newItem()">新增</a> 
         				<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-edit" plain=true onclick="editItem()">编辑</a>  
                   	 	<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-remove" plain=true onclick="removeItem()">删除</a>
        			</div> 	 
   				</div>
   				<table id= 'list' class="easyui-datagrid"  style="minwidth:500px"></table>
   			
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