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
                
                //onClick:function(node){
                    //alert(node.text);
                   // var sheetname = node.text;
                   // alert(sheetname);
                    //alert($('#list').datagrid('options').url); //获取easyui控件的属性值

               // },
                onDblClick:function(node){
                
                    $.ajax({
                        type:'post',
                        data:{text:node.text},
						url:'<?=Yii::app()->createUrl('site/readsheet')?>',
						success:function(data,textStatus){
							$('.center').html('').append(data);
							},

                        });
                },
            });
//动态加载树列表
$('#tree').tree('loadData',treeList);
});
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
                <div onclick="check()" data-options="iconCls:'icon-search'">查看工作薄</div>
                <div onclick="edit()" data-options="iconCls:'icon-edit'">重命名工作薄</div> 
                <div onclick="remove()" data-options="iconCls:'icon-remove'">删除工作薄</div>  
                <div class="menu-sep"></div>  
                <div onclick="expand()">导出工作薄</div>  
                <div onclick="collapse()">收起</div>  
            </div><!--tree的节点右键操作--> 
        </div>  
         
        <!-- datagrid -->
        <div data-options="region:'center',title:'<?=$sheetTitle?>',iconCls:'icon-ok'" class="center"> 
        	<?php $this->renderPartial('_index')?>
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
