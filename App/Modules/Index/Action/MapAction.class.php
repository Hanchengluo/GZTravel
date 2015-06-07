<?php
/**
 * 后台地图类
 * @author   <[c@easycmz.cc]>
 */
class MapAction extends CommonAction{

	/**
	 * @param int $currentPage
	 * @param int $pageCount
	 * @param string $MapId
	 */
	public function getAllPois($start, $end){
		header('Content-Type:application/json; charset=utf-8');
		$asid = $this->getAreaServiceId('景点');
		$startlng = explode(',', $start)[0];
		$startlat = explode(',', $start)[1];
		$endlng = explode(',', $end)[0];
		$endlat = explode(',', $end)[1];

		$model = new Model();

		$res1 = $model->Distinct(true)->table('jingdian jd')
			->join('jingqu jq on jd.jq_id = jq.id')
			->join('Picture p on jd.id = p.pid')
			->where('p.as_id = '.$asid.' and jd.islock = 0 and (jd.pointlng between '.$startlng.' and '.$endlng.')
			  and (jd.pointlat between '.$endlat.' and '.$startlat.')')
			->field('jd.id, jd.name, jq.address, p.minurl picurl, jd.pointlng, jd.pointlat')
			->group('jd.id')->select();

		$res1 = $this->setPictureUrl($res1, 1);


		$res2 = $model->Distinct(true)->table('Zhu z')
			->join('Picture p on z.id = p.pid and p.as_id = z.as_id')
			->where('z.islock = 0 and (z.pointlng between '.$startlng.' and '.$endlng.')
			  and (z.pointlat between '.$endlat.' and '.$startlat.')')
			->field('z.id id, z.name name, p.minurl picurl, z.address address, z.pointlng pointlng, z.pointlat pointlat')
			->group('z.id')->select();

		$res2 = $this->setPictureUrl($res2, 2);

		$res3 = $model->Distinct(true)->table('Chi c')
			->join('Picture p on c.id = p.pid and p.as_id = c.as_id')
			->where('c.islock = 0  and (c.pointlng between '.$startlng.' and '.$endlng.')
			  and (c.pointlat between '.$endlat.' and '.$startlat.')')
			->field('c.id id, c.name name, p.minurl picurl, c.address address, c.pointlng pointlng, c.pointlat pointlat')
			->group('c.id')->select();


		$res3 = $this->setPictureUrl($res3, 3);

		$asid = $this->getAreaServiceId('商店');
		$res4 = $model->Distinct(true)->table('Shop s')
			->join('Picture p on s.id = p.pid and p.as_id = '.$asid)
			->where('s.islock = 0 and (s.pointlng between '.$startlng.' and '.$endlng.')
			  and (s.pointlat between '.$endlat.' and '.$startlat.')')
			->field('s.id id, s.name name, p.minurl picurl, s.address address, s.pointlng pointlng, s.pointlat pointlat')
			->group('s.id')->select();
		$res4 = $this->setPictureUrl($res4, 4);

		$ids = D('Areaservice') ->where(array('level' => 3))->field('id, name')->select();
		foreach ($ids as $key => $value) {
			if ($value['name'] == '美食' || $value['name'] == '特产' || $value['name'] == 'POI') {
				continue;
			} else {
				$idRes[] = $value['id'];
			}

		}

		$res5 = $model->table('Zuobiao z')
			->where('z.as_id in ('.implode(',', $idRes).') and z.islock = 0 and (z.bpointlng between '.$startlng.' and '.$endlng.')
			  and (z.bpointlat between '.$endlat.' and '.$startlat.')')
			->field('z.id id, z.name name, z.picurl picurl, z.address address, z.bpointlng pointlng, z.bpointlat pointlat')
			->select();
		$res5 = $this->setPictureUrl($res5, 4);
		$res = array();
		if ($res1) {
			$res = array_merge($res, $res1);
		}
		if ($res2) {
			$res = array_merge($res, $res2);
		}
		if ($res3) {
			$res = array_merge($res, $res3);
		}
		if ($res4) {
			$res = array_merge($res, $res4);
		}
		if ($res5) {
			$res = array_merge($res, $res5);
		}

		if ($res){
			$res = array('code'=>200,'error'=>'成功','data'=>$res);
		} else {
			$res = array('code'=>200,'error'=>'','data'=>[]);
		}
		echo json_encode($res);
		exit();
	}

	function setPictureUrl($res, $type) {
		if (count($res)) {
			foreach ($res as $key => $value) {
				if ($value['picurl']) {
					$res[$key]['picurl'] = $this->returnURLName() . substr($value['picurl'], 1);
				} else {
					$res[$key]['picurl'] = '';
				}
				switch ($type) {
					case '1' :
						$res[$key]['type'] = 1;
						break;
					case '2' :
						$res[$key]['type'] = 2;
						break;
					case '3' :
						$res[$key]['type'] = 3;
						break;
					case '4' :
						$res[$key]['type'] = 4;
						break;
					default  :
						$res[$key]['type'] = 5;
						break;
				}
			}
		}
		return $res;
	}

}
