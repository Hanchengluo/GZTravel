<?php
/**
 * 后台新闻类
 * @author   <[c@easycms.cc]>
 */
class ShopAction extends CommonAction{
	
	public function getShop($shopId) {
		header('Content-Type:application/json; charset=utf-8');
		$asid = $this->getAreaServiceId("商店");

		if (isset($shopId) && isset($asid)) {
			$Model = new Model();
			$res = $Model->table('Shop s')
			->join('Jingqu jq on jq.id = s.jq_id')
			->where('s.islock = 0 and jq.islock = 0 and s.id ='.$shopId)
			->field('s.id id, s.name name, s.tel tel,
			 s.starttime starttime, s.endtime endtime, jq.address address,
			 s.pointlng pointlng, s.pointlat pointlat')
			->find();

			if (count($res)) {
			    $resPic = D('Picture')->where(array('pid' => $shopId, 'as_id' => $asid))
					->field('picurl')
					->select();
				if ($resPic) {
					foreach ($resPic as $key => $value) {
			    		$resPic[$key] = $this->returnURLName().substr($value['picurl'], 1);
			    	}
				} else {
					$resPic = array();
				}

				$asid = $this->getAreaServiceId("特产");
				$techan = $Model->Distinct(true)->table('Techan t')
					->join('Shop s on t.shop_id = s.id')
					->join('Picture p on t.id = p.pid and p.as_id = '.$asid)
					->where('s.islock = 0 and t.islock = 0 and t.shop_id = '.$shopId)
					->field('t.id id, t.name name, p.minurl picurl')
					->group('t.id')->select();

				if ($techan) {
					foreach ($techan as $key => $value) {
						if ($value['picurl']) {
							$techan[$key]['picurl'] = $this->returnURLName() . substr($value['picurl'], 1);
						} else {
							$techan[$key]['picurl'] = '';
						}
			    	}
				} else {
					$techan = array();
				}

				$res['picurl'] = $resPic;
		    	$resend['header'] = $res;
		    	$resend['techan'] = $techan;

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
	 * @param string $jingquId
     */
	public function getAllShops($currentPage = 1, $pageCount = 10, $jingquId = ''){
	    header('Content-Type:application/json; charset=utf-8');
	    $asid = $this->getAreaServiceId("商店");
		$Model = new Model();
		if ($jingquId != '') {
			$str = ' and s.jq_id = '.$jingquId;
		} else {
			$str = ' ';
		}

		$res = $Model->Distinct(true)->table('Shop s')
		->join('Jingqu jq on jq.id = s.jq_id')
		->join('Picture p on s.id = p.pid and p.as_id = '.$asid)
		->where('s.islock = 0 and p.islock = 0'.$str)
		->field('s.id id, s.name name, p.minurl picurl, jq.address address, s.pointlng pointlng, s.pointlat pointlat')
		->group('s.id')->order('s.sort desc')->page($currentPage, $pageCount)->select();

	    if (count($res)) {
	    	foreach ($res as $key => $value) {
		    	$res[$key]['picurl'] = $this->returnURLName().substr($value['picurl'], 1);
		    }
		    $res = array('code'=>200,'error'=>'成功','data'=>$res);
	    } else {
	    	$res = array('code'=>200,'error'=>'','data'=>[]);
	    }
	    echo json_encode($res);
	    exit();
	}


}
