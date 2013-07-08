<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	
	<!-- blueprint CSS framework -->
	
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/protected/vendors/easyui/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/protected/vendors/easyui/js/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/protected/vendors/easyui/js/easyui-lang-zh_CN.js"></script>
	<title><?php echo isset($this->pageTitle)?$this->pageTitle:CHtml::encode(iconv('gbk','utf-8',Yii::app()->name)); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/protected/vendors/easyui/css/easyui.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/protected/vendors/easyui/css/icon.css" /> 
	
</head>

<style>
.mainmenu1 {
	background-image:url(<?php echo Yii::app()->request->baseUrl; ?>/css/header.jpg);
	background-repeat: repeat-x;
}
#mainmenu ul li a:hover, #mainmenu ul li.active a
{
	background-image:url(<?php echo Yii::app()->request->baseUrl; ?>/css/ulbg.jpg);
	text-decoration:none;
}
.panel-header {
  background-color: #ff3d00;
  background: -webkit-linear-gradient(top,#ff3d00 0,#ff3d00 100%);
  background: -moz-linear-gradient(top,#ff3d00 0,#ff3d00 100%);
  background: -o-linear-gradient(top,#ff3d00 0,#ff3d00 100%);
  background: linear-gradient(to bottom,#ff3d00 0,#ff3d00 100%);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#EFF5FF,endColorstr=#ff3d00,GradientType=0);
}

#layout .layout-panel-west .panel-header{
	-moz-border-radius: 5px 5px 0px  0px ;
    -webkit-border-radius: 5px 5px 0px  0px;
    border-radius:5px 5px 0px  0px;
}
#layout .layout-panel-west .panel-body{
	-moz-border-radius: 0px 0px 5px  5px ;
    -webkit-border-radius: 0px 0px 5px  5px;
    border-radius:0px 0px 5px  5px;
}

#east{
	-moz-border-radius: 5px 5px 0px  0px ;
    -webkit-border-radius: 5px 5px 0px  0px;
    border-radius:5px 5px 0px  0px;
}


#content .panel {
	-moz-border-radius: 5px 5px 5px  5px ;
    -webkit-border-radius: 5px 5px 5px  5px;
    border-radius:5px 5px 5px  5px;
}
.panel-title {
	color:white;
}
#content .panel .easyui-panel{
	-moz-border-radius: 0px 0px 5px  5px ;
    -webkit-border-radius: 0px 0px 5px  5px;
    border-radius:0px 0px 5px  5px;
}
.accordion .accordion-header{
	background-image: url(<?php echo Yii::app()->request->baseUrl; ?>/css/header.jpg);
}
.accordion .accordion-header-selected{
	background-image: url(<?php echo Yii::app()->request->baseUrl; ?>/css/header.jpg);
	background-repeat: repeat-x;
}


.accordion .accordion-header-selected  {
	color: white;
}

.window {
    background: linear-gradient(to bottom, rgb(255, 61, 0) 0px, rgb(255, 61, 0) 100%) repeat-x scroll 0% 0% transparent;
}

.layout-expand {
    background-color: #ff3d00;
}
.panel-title {
    font-size: 12px;
    font-weight: bold;
    height: 16px;
    line-height: 16px;
}

#footer {
	margin:0 0 0 0;
}
</style>
<body>
<!--style="background-image:url(<?php echo Yii::app()->request->baseUrl; ?>/css/header.jpg);background-repeat: repeat-x;"   --background-image:url(/phpExcel/css/header.jpg);-->
	<div id="mainmenu" style="margin-bottom: 10px;">
		
		<?php $this->widget('zii.widgets.CMenu',array(
			'htmlOptions'=>array('class'=>'mainmenu1'),
			'activeCssClass'=>'selected', 
			'items'=>array(
				array('label'=>'首页', 'url'=>array('/site/index')),
				//array('label'=>'上传', 'url'=>array('/site/upload')),
				//array('label'=>'Contact', 'url'=>array('/install/index')),
				//array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				//array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'文件上传', 'url'=>array('/site/upload')),
				array('label'=>'关于', 'url'=>array('/site/about')),
				//array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				//array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer" style="background-image:url(<?php echo Yii::app()->request->baseUrl; ?>/css/footer.jpg);background-repeat: repeat-x;width:auto">
		Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->



</body>
</html>
