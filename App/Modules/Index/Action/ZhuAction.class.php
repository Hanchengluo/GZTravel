<?php
/**
 * 后台酒店住宿类
 * @author   <[c@easycmz.cc]>
 */
class ZhuAction extends CommonAction{
	
	public function getZhu($zhuId) {
		header('Content-Type:application/json; charset=utf-8');
		if (isset($zhuId)) {
			$Model = new Model();
			$res = $Model->table('Zhu z')
			->where('z.islock = 0 and z.id ='.$zhuId)
			->field('z.id id, z.as_id asid, z.name name, z.tel tel, z.address address,
			 z.aveprice aveprice, z.address address, z.comment comment,
			 z.pointlng pointlng, z.pointlat pointlat, z.zbcy zbcy, z.zbjd zbjd, z.zbsd zbsd')
			->find();

			if (count($res)) {
				$temp = $res['comment'];
			    $resPic = D('Picture')->where(array('pid' => $zhuId, 'as_id' => $res['asid']))
					->field('picurl')
					->select();
				if ($resPic) {
					foreach ($resPic as $key => $value) {
			    		$resPic[$key] = $this->returnURLName().substr($value['picurl'], 1);
			    	}
					$zbcy = $this->getZbcy($res['zbcy']);
					$zbjd = $this->getZbjd($res['zbjd']);
					$zbsd = $this->getZbsd($res['zbsd']);
				} else {
					$resPic = array();
					$temp = array();
					$zbcy = array();
					$zbjd = array();
					$zbsd = array();
				}


				unset($res['zbjd']);
				unset($res['zbcy']);
				unset($res['zbsd']);
				unset($res['comment']);
				unset($res['asid']);
				$res['picurl'] =  $resPic;
		    	$resend['header'] = $res;
				$resend['comment'] = $temp;
				$resend['zbcy'] = $zbcy;
				$resend['zbjd'] = $zbjd;
				$resend['zbsd'] = $zbsd;

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

	/**
	 * @param int $currentPage
	 * @param int $pageCount
	 * @param string $zhuId
     */
	public function getAllZhus($currentPage = 1, $pageCount = 10, $asId = ''){
	    header('Content-Type:application/json; charset=utf-8');

		$Model = new Model();
		if ($asId != '') {
			$str = ' and z.as_id = '.$asId.' and p.as_id = '.$asId;

		} else {
			$str = ' and z.as_id = p.as_id';
		}

		$res = $Model->Distinct(true)->table('Zhu z')
			->join('Picture p on z.id = p.pid')
			->where('z.islock = 0 '.$str)
			->field('z.id id, z.name name, p.minurl picurl, z.address address, z.pointlng pointlng, z.pointlat pointlat')
			->group('z.id')->order('z.sort desc')->page($currentPage, $pageCount)->select();


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


	public function getZhuTypeList() {
		header('Content-Type:application/json; charset=utf-8');
		$asid = $this->getAreaServiceId("住");
		if ($asid) {
			$res = D('Areaservice')->where(array('pid' => $asid, 'islock' => 0))
				->field('id, name')->select();
		} else {
			$res =array();
		}

		if (count($res)) {
			$res = array('code'=>200,'error'=>'成功','data'=>$res);
		} else {
			$res = array('code'=>200,'error'=>'','data'=>[]);
		}
		echo json_encode($res);
		exit();
	}


}
