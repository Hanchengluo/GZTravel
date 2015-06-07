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
		$res = $model->where($data)->field('picurl')->find();

		//header('location:'.$res['picurl']);
		if ($res) {
		    $this->assign('vo',$res);
			$this->display('index');
		} else {
			$this->display('index');
		}
	}

	public function getAllActivities($currentPage = 1, $pageCount = 10){
	    header('Content-Type:application/json; charset=utf-8');
	    $model1 = M('Areaservice');
		$map['name'] = '特色活动';
		$asid = $model1->where($map)->getField('id');
		$Model = new Model(); 
		$res = $Model->Distinct(true)->table('Activity a')->join('Picture p on a.id = p.pid')
		->where('p.as_id = '.$asid.' and a.islock = 0 and p.islock = 0')
		->field('a.id id, a.name name, a.jianjie jianjie, a.startdate stardate, a.enddate enddate, p.picurl picurl')
		->group('a.id')->order('a.sort desc')->page($currentPage, $pageCount)->select();

	    if (count($res)) {
	    	foreach ($res as $key => $value) {
		    	$res[$key]['picurl'] = $this->returnURLName().substr($value['picurl'], 1);
		    	$res[$key]['detailurl'] = $this->returnURLName().'/index.php?g=index&m=activity&a=index&id='.$res[$key]['id'];
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