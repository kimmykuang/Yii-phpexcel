<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
		$(document).ready(function(){

			var treeList = <?php echo $treeList;?>;
			var data = <?php echo $dyData;?>;
			
            $('#list').datagrid({
				 iconCls:'icon-ok',
				 collapsible:false,//是否可折叠的 
				 fit: true,//自动大小
				 pagination:true,
				 rownumbers:true,
				 toolbar:'#tb',
                 singleSelect:true,
                 fitColumns:true,
                 striped:true,
                 nowrap:true,
                 columns:eval(<?php echo $columns;?>),
			});

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
                    //alert(node.text);
                    var sheetname = node.text;
                    alert(sheetname);
                    //alert($('#list').datagrid('options').url); //获取easyui控件的属性值

                }
                
            });
           
            //动态加载树列表
            $('#tree').tree('loadData',treeList);
            //动态加载datagrid数据
            $('#list').datagrid('loadData',eval(data.rows));
            
            
			var p = $('#list').datagrid('getPager');
			$(p).pagination({
				pageSize: 10,//每页显示的记录条数，默认为10
				pageList: [5,10,15,20],//可以设置每页记录条数的列表
				beforePageText: '第',//页数文本框前显示的汉字
				afterPageText:'页 	共{pages}页',
				displayMsg: '当前显示 {form} - {to} 条记录		共{total}条记录',
			});
            

		});

</script>
<!--tree节点右键方法-->
<script type="text/javascript"> 
        function check(){
            var t = $('#tree');
            var node = t.tree('getSelected');

        }
        function edit(){
            var t = $('#tree');
            var node = t.tree('getSelected');
            $('#dlg1').dialog('open').dialog('setTitle','XXX信息');
        }

        function remove(){
            var node = $('#tree').tree('getSelected');
            alert(node);
            if (confirm("你真的确定要删除吗?")) {
                  $('#tree').tree('remove', node.target);
            }; 
        }
        function collapse(){  
            var node = $('#tree').tree('getSelected');  
            $('#tree').tree('collapse',node.target);  
        }  
        function expand(){  
            var node = $('#tree').tree('getSelected');  
            $('#tree').tree('expand',node.target);  
        } 
</script>
<center>
    <!--布局控件-->
	<div class="easyui-layout" style="width:1200px;height:800px;">   
        <div data-options="region:'west',split:true" title="Excel文件结构" style="width:180px;">  
            <ul id="tree"></ul><!--tree控件-->
            <div id="mm" class="easyui-menu" style="width:120px;">  
                <div onclick="check()" data-options="iconCls:'icon-search'">查看</div>
                <div onclick="edit()" data-options="iconCls:'icon-edit'">编辑</div> 
                <div onclick="remove()" data-options="iconCls:'icon-remove'">移除</div>  
                <div class="menu-sep"></div>  
                <div onclick="expand()">展开</div>  
                <div onclick="collapse()">收起</div>  
            </div><!--tree的节点右键操作--> 
        </div>  
         
        <!-- datagrid -->
        <div data-options="region:'center',title:'Sheet数据',iconCls:'icon-ok'"> 
        	<table id= 'list' class="easyui-datagrid"></table> 
            <div id="tb" style="padding:5px;height:auto">   
        		<div style="margin-bottom:5px">  
        			<a href="#" class="easyui-linkbutton" iconCls="icon-add" plain="false">新增</a> 
         			<a href="#" class="easyui-linkbutton" iconCls="icon-edit" plain="false">编辑</a>  
                    <a href="#" class="easyui-linkbutton" iconCls="icon-remove" plain="false">删除</a>
                    <a href="#" class="easyui-linkbutton" iconCls="icon-save" plain="false">导出当前工作薄</a>
        		</div> 	 
        		<div>  
          			SheetName: <input style="width:80px">  
        			<a href="javascript:alert('search');" class="easyui-linkbutton" iconCls="icon-search">Search</a>  
    			</div>  
   			</div> 
        </div> 
        <!--中部center结束-->     
    </div> 
    <!--layout结束-->
    
    <!--tree右键的编辑按钮-->
    <div id="dlg1" class="easyui-dialog" style="width:300px;height:180px;padding:10px 20px" closed="true" buttons="#dlg-buttons"> 
            <center>
                <table>
                    <tr>
                        <td><label>File Name:</label></td>
                        <td><input name="filename" class="easyui-validatebox" required="true"></td>
                    </tr>
                    <tr>
                        <td><label>Create Time:</label></td>
                        <td><input name="createtime" class="easyui-validatebox" required="true"></td>
                    </tr>
                </table> 
            </center>
    </div>   
    <!--dlg1结束-->
    
    <!--dlg1的操作按钮-->
    <div id="dlg-buttons">  
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveNodeInfo()">Save</a>  
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg1').dialog('close')">Cancel</a>  
    </div> 
</center>
