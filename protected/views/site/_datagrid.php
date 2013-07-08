<script type="text/javascript">
			//接收动态数据
			
			var columns = <?php echo $columns;?>;
			//datagrid绑定列
            $('#list').datagrid({
                
				 iconCls:'icon-ok',
				 collapsible:false,//是否可折叠的 
				 fit: true,//自动大小
				 pagination:true,
				 rownumbers:false,
				 toolbar:'#tb',
                 singleSelect:true,
                 pageNumber:1,
                 fitColumns:true,
                 striped:true,
                 nowrap:true,
                 columns:eval(columns),
                 url:'<?php echo Yii::app()->createUrl('site/dataprovider',array('id'=>$id));?>',
                 onLoadSuccess:function(data){
                     //var data = eval('('+data+')');
                     if(eval(data.total) == 0){
                         $.messager.alert('错误提示',data.errorMsg);
                     }
                 },
            	
               
			});
            
            //分页
            var p = $('#list').datagrid('getPager');
            $(p).pagination({
            	pageSize: 10, //每页显示的记录条数，默认为10
            	pageList: [5,10,15,20],//可以设置每页记录条数的列表
            	beforePageText: '第',//页数文本框前显示的汉字
            	afterPageText:'页 	共{pages}页',
            	displayMsg: '当前显示 {from} - {to} 条记录		共{total}条记录',
            });

            
</script> 