<?php
/**
 * 后台酒店住宿类
 * @author   <[c@easycmz.cc]>
 */
class ChiAction extends CommonAction{

	public function getChi($chiId) {
		header('Content-Type:application/json; charset=utf-8');
		if (isset($chiId)) {
			$Model = new Model();
			$res = $Model->table('Chi z')
				->where('z.islock = 0 and z.id ='.$chiId)
				->field('z.id id, z.as_id asid, z.name name, z.address address,
					     z.aveprice aveprice, z.starttime starttime, z.endtime endtime,
						 z.pointlng pointlng, z.pointlat pointlat')
				->find();

			if (count($res)) {
				$resPic = D('Picture')->where(array('pid' => $chiId, 'as_id' => $res['asid']))
					->field('picurl')
					->select();
				if ($resPic) {
					foreach ($resPic as $key => $value) {
						$resPic[$key] = $this->returnURLName() . substr($value['picurl'], 1);
					}
				} else {
					$resPic = array();
				}

				$asid = $this->getAreaServiceId("美食");
				$meishi = $Model->Distinct(true)->table('Meishi m')
					->join('Chi c on m.chi_id = c.id')
					->join('Picture p on m.id = p.pid and p.as_id = '.$asid)
					->where('m.islock = 0 and c.islock = 0 and m.chi_id = '.$chiId)
					->field('m.id id, m.name name,  m.title title, p.minurl picurl')
					->group('m.id')->select();
				if ($meishi) {
					foreach ($meishi as $key => $value) {
						if ($value['picurl']) {
							$meishi[$key]['picurl'] = $this->returnURLName() . substr($value['picurl'], 1);
						} else {
							$meishi[$key]['picurl'] = '';
						}
					}
				} else {
					$meishi = array();
				}


				unset($res['asid']);
				$res['picurl'] =  $resPic;
				$resend['header'] = $res;
				$resend['meishi'] = $meishi;

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
	 * @param string $chiId
	 */
	public function getAllChis($currentPage = 1, $pageCount = 10){
		header('Content-Type:application/json; charset=utf-8');

		$Model = new Model();
		$asId = $this->getAreaServiceId('吃');
		if ($asId) {
			$asIds = $this->getAreaServiceIds($asId);
		}

		if($asIds) {
			foreach ($asIds as $key => $value) {
				$Ids[] = $value['id'];
			}
		}

		$res = $Model->Distinct(true)->table('Chi c')
			->join('Picture p on c.id = p.pid and p.as_id = c.as_id')
			->where('c.islock = 0')
			->field('c.id id, c.name name, c.title title, p.minurl picurl, c.address address, c.pointlng pointlng, c.pointlat pointlat')
			->group('c.id')->order('c.sort desc')->page($currentPage, $pageCount)->select();


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


}
