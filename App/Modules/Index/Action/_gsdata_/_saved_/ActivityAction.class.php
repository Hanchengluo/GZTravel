<?php
/**
 * 后台新闻类
 * @author   <[c@easycms.cc]>
 */
class ActivityAction extends CommonAction{
	
	public function index() {
		//列表过滤器，生成查询Map对象
		C('SHOW_PAGE_TRACE','');
		$model = M('Activity');
		$data['id'] = $_REQUEST['id'];
		$res = $model->where($data)->getField('id');
		if (null !== $res) {
			$data['islock']=0;
			$list=$model->where($data)->find();
			$this->assign('vo',$list);
			$map['name'] = '特色活动';
			$asid = D('Areaservice')->where($map)->getField('id');
			$picvo= M('Picture')
		    	->where(array('as_id'=>$asid,'pid'=>$data['id']))
		    	->field('picurl')->select();
		    $this->assign('picvo',$picvo);
			$this->display('index');
		} else {
			$list['name'] = '没有该项特色活动信息';
			$list['special'] = '没有该项特色活动信息';
			$this->assign('vo',$list);
			$this->display('index');
		}
	}

	public function getAllActivities(){
	    header('Content-Type:application/json; charset=utf-8');
	    $model1 = M('Areaservice');
		$map['name'] = '特色活动';
		$asid = $model1->where($map)->getField('id');
		$model = M('Activity');
	    $data['islock']=0;
	    $count = $model->where($data)->count();
	    if ($count > 0 ) {
		    $res = $model->where($data)->field('id,name,startdate,enddate')->select();
		   	foreach ($res as $key => $value) {
		    	$url= M('Picture')
		    	->where(array('as_id'=>$asid,'pid'=>$res[$key]['id']))
		    	->getField('picurl');
		    	$res[$key]['picurl'] = $this->returnURLName().substr($url, 1);
		    	$res[$key]['detailurl'] = $this->returnURL().'/index/id/'.$res[$key]['id'];
		    }
		    $res = array('code'=>200,'error'=>'成功','data'=>$res);
	    } else {
	    	$res = array('code'=>200,'error'=>'','data'=>[]);
	    }
	    echo json_encode($res);
	    exit();
	}
}
?>