<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

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
                    //alert(node.text);
                    var sheetname = node.text;
                    alert(sheetname);
                    //alert($('#list').datagrid('options').url); //获取easyui控件的属性值

                }
                
            });
           
            //动态加载树列表
            $('#tree').tree('loadData',treeList);
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
	<div class="easyui-layout" style="width:1200px;height:600px;">
	
		<!-- 左边的树菜单 -->   
        <div data-options="region:'west',split:true" title="Excel文件结构" style="width:180px;">  
        	<!--tree控件-->
            <ul id="tree"></ul>
            <!--tree的节点右键操作--> 
            <div id="mm" class="easyui-menu" style="width:120px;">  
                <div onclick="check()" data-options="iconCls:'icon-search'">查看</div>
                <div onclick="edit()" data-options="iconCls:'icon-edit'">编辑</div> 
                <div onclick="remove()" data-options="iconCls:'icon-remove'">移除</div>  
                <div class="menu-sep"></div>  
                <div onclick="expand()">展开</div>  
                <div onclick="collapse()">收起</div>  
            </div>
        </div>  
         
        <!-- upload file -->
        <div data-options="region:'center',title:'<?php echo $sheetTitle;?>',iconCls:'icon-ok'">
			<?php echo CHtml::form('upload','POST',array('enctype'=>'multipart/form-data'));?>
			<?php echo CHtml::activeFileField($model,'excelfile');?>
			<?php echo CHtml::errorSummary($model);?>
			<?php echo CHtml::submitButton('Submit',array('value'=>"提交"));?>
			<?php echo CHtml::endForm();?>
        </div>   
        
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
