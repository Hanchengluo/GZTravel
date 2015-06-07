<?php
/**
 * 后台新闻类
 * @author   <[c@easycms.cc]>
 */
class JingdianAction extends CommonAction{
	
	public function getJingdian($jingdianId) {
		header('Content-Type:application/json; charset=utf-8');
		$asid = $this->getAreaServiceId('景点');

		if (isset($jingdianId) && isset($asid)) {
			$Model = new Model();
			$res = $Model->table('Jingdian jd')
			->join('Jingqu jq on jq.id = jd.jq_id')
			->where('jd.islock = 0 and jq.islock = 0 and jd.id ='.$jingdianId)
			->field('jd.id id, jd.star star, jd.name name, jd.comment comment, jd.pay pay,
			 jd.usetime usetime, jd.videourl videourl, jq.address address, 
			 jd.pointlng pointlng, jd.pointlat pointlat, jd.zbjd zbjd, jd.star star')
			->find();

			if (count($res)) {
				$resTemp = $res['comment'];
				unset($res['comment']);
			    $resPic = D('Picture')->where(array('pid' => $jingdianId, 'as_id' => $asid))
					->field('picurl')
					->select();
				if ($resPic) {
					foreach ($resPic as $key => $value) {
			    		$resPic[$key] = $this->returnURLName().substr($value['picurl'], 1);
			    	}
				} else {
					$resPic = array();
				}
				

				$resZbjd = $this->getZbjd($res['zbjd']);
				unset($res['zbjd']);
				if ($res['videourl']) {
					$res['videourl'] = $this->returnURLName().substr($res['videourl'], 1);
				} else {
					$res['videourl'] = '';
				}
				$res['picurl'] = $resPic;
		    	$resend['header'] = $res;
		    	$resend['comment'] = $resTemp;
		    	$resend['JdRecommend'] = $resZbjd;

			    $res = array('code'=>200,'error'=>'成功','data'=>$resend);
		    } else {
		    	$res = array('code'=>200,'error'=>'','data'=>[]);
		    }
		} else {
			$res = array('code'=>200,'error'=>'','data'=>[]);
		}
	    echo json_encode($res);
	    exit();
	}

	public function getAllJingdians($currentPage = 1, $pageCount = 10, $jingquId = ''){
	    header('Content-Type:application/json; charset=utf-8');
	    $asid = $this->getAreaServiceId('景点');
		$Model = new Model();
		if ($jingquId != '') {
			$str = ' and jd.jq_id = '.$jingquId;
		} else {
			$str = ' ';
		}

		$res = $Model->Distinct(true)->table('Jingdian jd')
		->join('Jingqu jq on jq.id = jd.jq_id')
		->join('Picture p on jd.id = p.pid')
		->where('p.as_id = '.$asid.''.$str.' and jd.islock = 0')
		->field('jd.id id, jd.name name, p.minurl picurl, jq.address address, jd.pointlng pointlng, jd.pointlat pointlat')
		->group('jd.id')->order('jd.sort desc')->page($currentPage, $pageCount)->select();

	    if (count($res)) {
	    	foreach ($res as $key => $value) {
				if ($value['picurl']) {
					$res[$key]['picurl'] = $this->returnURLName() . substr($value['picurl'], 1);
				} else {
					$res[$key]['picurl'] = '';
				}
			}
		    $res = array('code'=>200,'error'=>'成功','data'=>$res);
	    } else {
	    	$res = array('code'=>200,'error'=>'','data'=>[]);
	    }
	    echo json_encode($res);
	    exit();
	}

	public function getJingdians($currentPage = 1, $pageCount = 10, $jingquId = ''){
		header('Content-Type:application/json; charset=utf-8');
		$asid = $this->getAreaServiceId('景点');
		$Model = new Model();
		if ($jingquId != '') {
			$str = ' and jd.jq_id = '.$jingquId;
		} else {
			$str = ' ';
		}

		$res = $Model->Distinct(true)->table('Jingdian jd')
			->join('Jingqu jq on jq.id = jd.jq_id')
			->join('Picture p on jd.id = p.pid')
			->where('p.as_id = '.$asid.''.$str.' and jd.islock = 0')
			->field('jd.id id, jd.recommend recommend, jd.name name, p.minurl picurl, p.picurl minurl,
			jq.address address, jd.pointlng pointlng, jd.pointlat pointlat')
			->group('jd.id')->order('jd.sort desc')->page($currentPage, $pageCount)->select();

		$header = array();
		if (count($res)) {
			foreach ($res as $key => $value) {
				if ($value['picurl']) {
					$res[$key]['picurl'] = $this->returnURLName() . substr($value['picurl'], 1);
				} else {
					$res[$key]['picurl'] = '';
				}

				if ($res[$key]['recommend']) {
					if ($value['minurl']) {
						$res[$key]['picurl'] = $this->returnURLName() . substr($value['minurl'], 1);
					} else {
						$res[$key]['picurl'] = '';
					}
					$header = $res[$key];
					unset($header['recommend']);
					unset($res[$key]);
				} else {
					unset($res[$key]['recommend']);
				}
				unset($res[$key]['minurl']);

			}

			$result = array('header' =>	$header, 'data' => array_values($res));
			$res = array('code'=>200,'error'=>'成功','data'=>$result);
		} else {
			echo '{"code":200,"error":"","data":{}}';
			exit();
		}
		echo json_encode($res);
		exit();
	}

	//获取景点对应景区分类
	public function getJingquList(){
	    header('Content-Type:application/json; charset=utf-8');
	    $Model = new Model();
		$res = $Model->Distinct(true)->table('Jingdian jd')
		->join('Jingqu jq on jq.id = jd.jq_id')
		->where('jq.islock = 0 and jd.islock = 0')
		->field('jq.id id, jq.name name')
		->group('jq.id')->select();
		
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
