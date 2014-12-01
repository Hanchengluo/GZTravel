<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>多彩贵州后台管理</title>

<link href="../Public/dwz/themes/default/style.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="../Public/dwz/themes/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="../Public/dwz/themes/css/print.css" rel="stylesheet" type="text/css" media="print"/>
<link href="../Public/dwz/uploadify/css/uploadify.css" rel="stylesheet" type="text/css" media="screen"/>
<!--[if IE]>
<link href="../Public/dwz/themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
<![endif]-->

<script src="../Public/dwz/js/speedup.js" type="text/javascript"></script>
<script src="../Public/dwz/js/jquery-1.7.1.js" type="text/javascript"></script>
<script src="../Public/dwz/js/jquery.cookie.js" type="text/javascript"></script>
<script src="../Public/dwz/js/jquery.validate.js" type="text/javascript"></script>
<script src="../Public/dwz/js/jquery.bgiframe.js" type="text/javascript"></script>

<script src="../Public/dwz/xheditor/xheditor-1.1.14-zh-cn.min.js" type="text/javascript"></script>
<script src="../Public/dwz/uploadify/scripts/swfobject.js" type="text/javascript"></script>
<script src="../Public/dwz/uploadify/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>


<script src="../Public/dwz/js/dwz.min.js" type="text/javascript"></script>
<script src="../Public/dwz/js/dwz.regional.zh.js" type="text/javascript"></script>

<script type="text/javascript">
$(function(){
	DWZ.init("../Public/dwz/dwz.frag.xml", {
		statusCode:{ ok:200, error:300, timeout:301}, //【可选】
		pageInfo:{ pageNum:"pageNum", numPerPage:"numPerPage", orderField:"_order", orderDirection:"_sort"}, //【可选】
		debug:false,	// 调试模式 【true|false】
		callback:function(){
			initEnv();
			$("#themeList").theme({ themeBase:"../Public/dwz/themes"}); // themeBase 相对于index页面的主题base路径
		}
	});
});



</script>
</head>

<body scroll="no">
	<div id="layout">
		<div id="header">
			<div class="headerNav">
				<a class="logo" href="#">标志</a>
				<ul class="nav">
					<li><a href="__APP__/" target="_blank">进入首页</a></li>	
					<li><a href="#" target="_blank">欢迎您：<?php echo (session('adminuser')); ?></a></li>
					<li><a href="__GROUP__/Login/logout">退出</a></li>
				</ul>
				
				<ul class="themeList" id="themeList">
					<li theme="default"><div class="selected">蓝色</div></li>
					<li theme="green"><div>绿色</div></li>
					<li theme="purple"><div>紫色</div></li>
					<li theme="silver"><div>银色</div></li>
					<li theme="azure"><div>天蓝</div></li>
				</ul>
			</div>
		</div>

		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse"><div></div></div>
				</div>
			</div>
			<div id="sidebar">
				<div class="toggleCollapse"><h2>主菜单</h2><div>收缩</div></div>
				<div class="accordion" fillSpace="sidebar">
					<!-- 游管理 -->
					<div class="accordionHeader">
						<h2><span>Folder</span>游管理</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a href="<?php echo U('Jingqu/index');?>" target="navTab" rel="jingqu" title="景区列表" >景区列表</a></li>
							<li><a href="<?php echo U('Jingdian/index');?>" target="dialog" width="600" height="300" title="景点列表" >景点列表</a></li>
						</ul>
					</div>
					<!--吃管理-->
					<div class="accordionHeader">
						<h2><span>Folder</span>吃管理</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a href="<?php echo U('Chi/index');?>" target="navTab" rel="listcomment" title="吃地列表" >吃地列表</a></li>
							<li><a href="<?php echo U('Meishi/rubbish');?>" target="navTab" rel="listcomment1" title="美食列表" >美食列表</a></li>
						</ul>
					</div>
					<!--住管理-->
					<div class="accordionHeader">
						<h2><span>Folder</span>住管理</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a href="<?php echo U('Zhu/index');?>" target="navTab" rel="listcomment" title="住地管理" >住地管理</a></li>
						</ul>
					</div>
					<!--拍管理-->
					<div class="accordionHeader">
						<h2><span>Folder</span>拍管理</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a href="<?php echo U('Picture/index');?>" target="navTab" rel="listcomment" title="照片管理" >照片管理</a></li>
						</ul>
					</div>
					<!-- 商店管理 -->
					<div class="accordionHeader">
						<h2><span>Folder</span>商店管理</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a href="<?php echo U('Shop/index');?>" target="navTab" rel="listcate" title="商店列表" >商店列表</a></li>
							<li><a href="<?php echo U('Techan/index');?>" target="navTab" rel="listarticle" title="特产列表" >特产列表</a></li>
						</ul>
					</div>
					<!-- 特色活动管理 -->
					<div class="accordionHeader">
						<h2><span>Folder</span>特色活动管理</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a href="<?php echo U('Activitis/index');?>" target="navTab" rel="news" title="特色活动列表" >特色活动列表</a></li>
							<li><a href="<?php echo U('Activitis/rubbish');?>" target="navTab" rel="rubbish" title="特色活动回收站" >特色活动回收站</a></li>
						</ul>
					</div>
					<!-- 新闻管理 -->
					<div class="accordionHeader">
						<h2><span>Folder</span>新闻管理</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a href="<?php echo U('News/index');?>" target="navTab" rel="news" title="新闻列表" >新闻列表</a></li>
							<li><a href="<?php echo U('News/rubbish');?>" target="navTab" rel="rubbish" title="新闻回收站" >新闻回收站</a></li>
						</ul>
					</div>
					<!-- 服务管理 -->
					<div class="accordionHeader">
						<h2><span>Folder</span>服务管理</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a href="<?php echo U('Admin/index');?>" target="navTab" rel="admin" title="用户管理" >用户管理</a></li>
							<li><a href="<?php echo U('Service/index');?>" target="navTab" rel="service" title="服务管理" >服务管理</a></li>
						</ul>
					</div>
					<!-- 系统管理 -->
					<div class="accordionHeader">
						<h2><span>Folder</span>系统管理</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a href="<?php echo U('Database/index',array('type'=>'export'));?>" target="navTab" rel="listdb" title="数据库备份" >数据库备份</a></li>
							<li><a href="<?php echo U('Database/index',array('type'=>'import'));?>" target="navTab" rel="listdb" title="数据库还原" >数据库还原</a></li>
							<li><a href="<?php echo U('System/updateSystemCache');?>" target="dialog" rel="listsystem" title="更新系统缓存" >更新系统缓存</a></li>
							<li><a href="<?php echo U('System/updateTplCache');?>" target="dialog" rel="listsystem" title="更新模版缓存" >更新模版缓存</a></li>
						</ul>
					</div>
				</div>

			</div>
		</div>
		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader">
					<div class="tabsPageHeaderContent">
						<ul class="navTab-tab">
							<li tabid="main" class="main"><a href="javascript:;"><span><span class="home_icon">我的主页</span></span></a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div>
					<div class="tabsRight">right</div>
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList">
					<li><a href="javascript:;">主页</a></li>
				</ul>
				<div class="navTab-panel tabsPageContent layoutBox">
					<div class="page unitBox">
						<div class="accountInfo">
							<p>多彩贵州管理系统
								<a href="#" target="_blank"></a>
							</p>
							<p>今天是 <?php echo (date('Y-m-d g:i a',time())); ?> </p>
						</div>
						<div class="pageFormContent"  style="">			 
						<table class="list"  width="100%" layoutH="112">		
							<tr class="row" ><th colspan="3" class="space">系统信息</th></tr>
							<?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr class="row" ><td width="50%"><?php echo ($key); ?></td><td width="50%"><?php echo ($v); ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?>
						</table>
						
					</div>
					
					
				</div>
			</div>
		</div>

	</div>

	<div id="footer">Copyright &copy; 2014 <a href="demo_page2.html" target="dialog">贵州旅游开发团队</a><!-- Tel：--></div>
</body>
</html>