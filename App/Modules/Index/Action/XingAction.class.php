<?php
/**
 * @author   <[c@easycms.cc]>
 */
class XingAction extends CommonAction{
	
	public function getXing($xingId) {
		header('Content-Type:application/json; charset=utf-8');
		$asid = $this->getAreaServiceId('行');

		if (isset($xingId) && isset($asid)) {
			$Model = new Model();
			$res = $Model->table('Xing jd')
			->where('jd.islock = 0  and jd.id ='.$xingId)
			->field('jd.id id, jd.star star, jd.title title, jd.jianjie jianjie, jd.day day,
			 jd.licheng licheng, jd.num num, jd.zuobiao zuobiao, jd.zblg zblg, jd.zbcy zbcy,
			 jd.zbsd zbsd')
			->find();

			if (count($res)) {
				$resTemp = $res['jianjie'];
				unset($res['jianjie']);
			    $resPic = D('Picture')->where(array('pid' => $xingId, 'as_id' => $asid))
					->field('picurl')
					->select();
				if ($resPic) {
					foreach ($resPic as $key => $value) {
			    		$resPic[$key] = $this->returnURLName().substr($value['picurl'], 1);

			    	}
				} else {
					$resPic = array();
				}

				if ($res['zuobiao']) {
					$res['zuobiao'] = explode(';', $res['zuobiao']);
				}

				$resZblg = $this->getZblg($res['zblg']);
				unset($res['zblg']);

				$resZbcy = $this->getZbcy($res['zbcy']);
				unset($res['zbcy']);

				$resZbsd = $this->getZbsd($res['zbsd']);
				unset($res['zbsd']);

		    	$res['picurl'] = $resPic;
		    	$resend['header'] = $res;
		    	$resend['comment'] = $resTemp;
		    	$resend['zbgw'] = $resZbsd;
				$resend['zbzs'] = $resZblg;
				$resend['zbms'] = $resZbcy;

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

	public function getAllXings($currentPage = 1, $pageCount = 10){
	    header('Content-Type:application/json; charset=utf-8');
	    $asid = $this->getAreaServiceId('行');
		$Model = new Model();

		$res = $Model->Distinct(true)->table('Xing jd')
		->join('Picture p on jd.id = p.pid')
		->where('p.as_id = '.$asid.' and jd.islock = 0')
		->field('jd.id id, jd.title title, p.minurl picurl, jd.num num')
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

	public function getXings($currentPage = 1, $pageCount = 10){
		header('Content-Type:application/json; charset=utf-8');
		$asid = $this->getAreaServiceId('行');
		$Model = new Model();

		$res = $Model->Distinct(true)->table('Xing jd')
			->join('Picture p on jd.id = p.pid')
			->where('p.as_id = '.$asid.' and jd.islock = 0')
			->field('jd.id id, jd.recommend recommend, jd.title title, p.minurl picurl, p.picurl minurl, jd.num num')
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

	//推荐路线
	public function getRecommendXings(){
		header('Content-Type:application/json; charset=utf-8');
		$asid = $this->getAreaServiceId('行');
		$Model = new Model();

		$res = $Model->Distinct(true)->table('Xing jd')
			->where('jd.islock = 0')
			->field('jd.id id, jd.title title, jd.licheng licheng, jd.through through, jd.zuobiao zuobiao')
			->group('jd.id')->order('jd.sort desc')->select();

		if (count($res)) {
			$res = array('code'=>200,'error'=>'成功','data'=>$res);
		} else {
			$res = array('code'=>200,'error'=>'','data'=>[]);
		}
		echo json_encode($res);
		exit();
	}

}
