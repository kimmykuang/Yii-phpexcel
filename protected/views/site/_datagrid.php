             			<script type="text/javascript">
		

			
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
            

	

</script> 