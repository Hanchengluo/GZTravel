<?php
/**
 * 后台新闻类
 * @author   <[c@easycms.cc]>
 */
class NewsAction extends CommonAction{
	
	public function index() {
		//列表过滤器，生成查询Map对象
		C('SHOW_PAGE_TRACE','');
		$model = M('news');
		$data['id'] = $_REQUEST['id'];
		$res = $model->where($data)->getField('id');
		if (null !== $res) {
			$data['islock']=0;
			$list=$model->where($data)->find();
			$this->assign('vo',$list);
			$map['name'] = '新闻';
			$asid = D('Areaservice')->where($map)->getField('id');
			$picvo= M('Picture')
		    	->where(array('as_id'=>$asid,'pid'=>$data['id']))
		    	->field('picurl')->select();
		    $this->assign('picvo',$picvo);
			$this->display('index');
		} else {
			$list['title'] = '没有该项新闻';
			$list['comment'] = '没有该项新闻';
			$this->assign('vo',$list);
			$this->display('index');
		}
	}

	public function getAllNews($currentPage = 1, $pageCount = 10, $newstype = ''){
	    header('Content-Type:application/json; charset=utf-8');
	    $model1 = M('Areaservice');
		$map['name'] = '新闻';
		$asid = $model1->where($map)->getField('id');
		$Model = new Model();
		if ($newstype != '') {
			$str = ' and n.nt_id = '.$newstype;
		} else {
			$str = ' ';
		}
		  
		$res = $Model->Distinct(true)->table('News n')->join('Picture p on n.id = p.pid')
		->where('p.as_id = '.$asid.''.$str.' and n.islock = 0')
		->field('n.id id, n.title title, n.jianjie jianjie, p.picurl picurl, n.updatetime updatetime')
		->group('n.id')->order('n.sort desc')->page($currentPage, $pageCount)->select();

	    if (count($res)) {
	    	foreach ($res as $key => $value) {

				if ($value['picurl']) {
					$res[$key]['picurl'] = $this->returnURLName() . substr($value['picurl'], 1);
				} else {
					$res[$key]['picurl'] = '';
				}

		    	$res[$key]['detailurl'] = $this->returnURLName().'/index.php?g=index&m=news&a=index&id='.$res[$key]['id'];
		    	$res[$key]['updatetime'] = date("Y/m/d", $res[$key]['updatetime']);
		    }
		    $res = array('code'=>200,'error'=>'成功','data'=>$res);
	    } else {
	    	$res = array('code'=>200,'error'=>'','data'=>[]);
	    }
	    echo json_encode($res);
	    exit();
	}

	public function getAllNewstype(){
	    header('Content-Type:application/json; charset=utf-8');
		$res = D('newstype')->where(array('islock' => 0))->getField('id, id, name');
	    if (count($res)) {
	    	foreach ($res as $key => $value) {
		    	$resValue[] = $value;
		    }
		    $res = array('code'=>200,'error'=>'成功','data'=>$resValue);
	    } else {
	    	$res = array('code'=>200,'error'=>'','data'=>[]);
	    }
	    echo json_encode($res);
	    exit();
	}
}
