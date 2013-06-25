<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>
<script type="text/javascript">
  $(document).ready(function(){
    $('#content').before($('.easyui-layout')).remove();

  })
</script>

<div class="easyui-layout" fit="true" style="width:100%;height:100%">
    <!--  style="width:700px;height:350px     style="width:100%;height:100%;background:#0A3DA4;"-->
    <div data-options="region:'north'" style="height:50px"></div>
    <div data-options="region:'south',split:true" style="height:50px;"></div>
    <div data-options="region:'east',split:true" title="East" style="width:180px;"></div>
    <div data-options="region:'west',split:true" title="West" style="width:100px;"></div>
    <div data-options="region:'center',title:'Main Title',iconCls:'icon-ok'">
      <table class="easyui-datagrid"
          data-options="url:'<?php echo Yii::app()->request->baseUrl; ?>/datagrid_data1.json',border:false,singleSelect:true,fit:true,fitColumns:true">
        <thead>
          <tr>
            <th data-options="field:'itemid'" width="80">Item ID</th>
            <th data-options="field:'productid'" width="100">Product ID</th>
            <th data-options="field:'listprice',align:'right'" width="80">List Price</th>
            <th data-options="field:'unitcost',align:'right'" width="80">Unit Cost</th>
            <th data-options="field:'attr'" width="150">Attribute</th>
            <th data-options="field:'status',align:'center'" width="50">Status</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>