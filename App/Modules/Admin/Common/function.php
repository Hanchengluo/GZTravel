<?php 
//测试dump方法
function P($array){
	dump($array,1,'<pre>',0);
}

//切换ok error的图片
function isok($num){
	if ($num) {
		return '<img src="../Public/Images/ok.gif" />';
	}else{
		return '<img src="../Public/Images/error.gif" />';
	}
}

//切换锁定和未锁定的状态值
function islock($num){
	if ($num) {
		return '<img src="../Public/Images/locked.gif" />';
	}else{
		return '<img src="../Public/Images/ok.gif" />';
	}
}

//节点合并
function node_merge($node,$access=null,$pid = 0){
	$arr=array();
	foreach ($node as $v) {
		if (is_array($access)) {
			$v['access']=in_array($v['id'],$access)?1:0;
		}

		if ($v['pid']==$pid) {
			$v['child']=node_merge($node,$access,$v['id']);
			$arr[]=$v;
		}
	}
	return $arr;
}

function showOnline($status, $id, $callback="") {
	switch ($status) {
		case 0 :
			$info = '<a href="__URL__/changeState/id/'.$id.'/isonline/0" target="ajaxTodo" callback="'.$callback.'">未上线</a>';
			break;
		case 1 :
			$info = '<a href="__URL__/changeState/id/'.$id.'/isonline/1" target="ajaxTodo" callback="'.$callback.'">已上线</a>';
			break;
	}
	return $info;
}
function resourceURL($url){
	list(,$temp)=explode('/',__URL__);
	return 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].'/'.$temp.substr($url, 1);
}

//获取数据库表前缀
function prefix(){
	return C('DB_PREFIX');
}