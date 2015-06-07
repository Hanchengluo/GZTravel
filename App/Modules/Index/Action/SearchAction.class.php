<?php
/**
* 前台搜索
* @author  <[s@easycms.cc]>
*/
class SearchAction extends CommonAction
{

	public function search($search = '')
	{
		header('Content-Type:application/json; charset=utf-8');
		$Ids = array();
		if (isset($search)) {
			 $res = $this->getJingdian($search);
			if ($res) {
				foreach ($res as $key => $value) {
					$Ids[] = $value['jqId'];
					unset($res[$key]['jqId']);
				}
				$temp['jingdian'] = $res;
			} else {
				$temp['jingdian'] = array();
			}

			$resXing = $this->getXing($search);
			if ($resXing) {
				$temp['xing'] = $resXing;
			} else {
				$temp['xing'] = array();
			}

			$resChi = $this->getChi($Ids);
			if ($resChi) {
				$temp['chi'] = $resChi;
			} else {
				$temp['chi'] = array();
			}

			$resZhu = $this->getZhu($Ids);

			if ($resZhu) {
				$temp['zhu'] = $resZhu;
			} else {
				$temp['zhu'] = array();
			}

			$resShop = $this->getShop($Ids);

			if ($resShop) {
				$temp['shop'] = $resShop;
			} else {
				$temp['shop'] = array();
			}

			$res = array('code'=>200,'error'=>'','data'=>$temp);
		} else {
			$res = array('code'=>200,'error'=>'','data'=>[]);
		}

		echo json_encode($res);
		exit();
	}

	//搜索路线
	public function  getXing($search = '') {
		$asid = $this->getAreaServiceId('行');
		$Model = new Model();

		$res = $Model->Distinct(true)->table('Xing jd')
			->join('Picture p on jd.id = p.pid')
			->where('p.as_id = '.$asid.' and jd.islock = 0 and jd.title like \'%'.$search.'%\'')
			->field('jd.id id, jd.title title, p.picurl picurl, jd.num num')
			->group('jd.id')->order('jd.sort desc')->select();

		if (count($res)) {
			foreach ($res as $key => $value) {
				if ($value['picurl']) {
					$res[$key]['picurl'] = $this->returnURLName() . substr($value['picurl'], 1);
				} else {
					$res[$key]['picurl'] = '';
				}
			}
		} else {
			$res = array();
		}

		return $res;
	}

	//搜索美食
	public function  getChi($search = '') {
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
			->join('Picture p on c.id = p.pid and p.as_id in ('.implode(',', $Ids).')')
			->where('c.islock = 0 and c.jq_id in ('.implode(',', $search).')')
			->field('c.id id, c.name name, c.title title, p.picurl picurl, c.address address, c.pointlng pointlng, c.pointlat pointlat')
			->group('c.id')->order('c.sort desc')->select();


		if (count($res)) {
			foreach ($res as $key => $value) {
				if ($value['picurl']) {
					$res[$key]['picurl'] = $this->returnURLName() . substr($value['picurl'], 1);
				} else {
					$res[$key]['picurl'] = '';
				}
			}

		} else {
			$res = array();
		}

		return $res;
	}

	//搜索住宿
	public function  getZhu($search = '') {
		$Model = new Model();

		$res = $Model->Distinct(true)->table('Zhu z')
			->join('Picture p on z.id = p.pid')
			->where('z.islock = 0 '.' and z.as_id = p.as_id and z.jq_id in ('.implode(',', $search).')')
			->field('z.id id, z.name name, p.picurl picurl, z.address address, z.pointlng pointlng, z.pointlat pointlat')
			->group('z.id')->order('z.sort desc')->select();


		if (count($res)) {
			foreach ($res as $key => $value) {
				if ($value['picurl']) {
					$res[$key]['picurl'] = $this->returnURLName() . substr($value['picurl'], 1);
				} else {
					$res[$key]['picurl'] = '';
				}
			}
		} else {
			$res = array();
		}

		return $res;
	}

	//搜索景点
	public function  getJingdian($search = '') {

		$asid = $this->getAreaServiceId('景点');
		$Model = new Model();

		$res = $Model->Distinct(true)->table('Jingdian jd')
			->join('Jingqu jq on jq.id = jd.jq_id')
			->join('Picture p on jd.id = p.pid')
			->where('p.as_id = '.$asid.' and jd.islock = 0 and jd.name like \'%'.$search.'%\'')
			->field('jd.id id, jq.id jqId, jd.name name, p.picurl picurl, jq.address address, jd.pointlng pointlng, jd.pointlat pointlat')
			->group('jd.id')->order('jd.sort desc')->select();

		if (count($res)) {
			foreach ($res as $key => $value) {
				if ($value['picurl']) {
					$res[$key]['picurl'] = $this->returnURLName() . substr($value['picurl'], 1);
				} else {
					$res[$key]['picurl'] = '';
				}
			}
		} else {
			$res = array();
		}

		return $res;
	}

	//搜索购物
	public function  getShop($search = '') {
		$asid = $this->getAreaServiceId("商店");
		$Model = new Model();

		$res = $Model->Distinct(true)->table('Shop s')
			->join('Jingqu jq on jq.id = s.jq_id')
			->join('Picture p on s.id = p.pid and p.as_id = '.$asid)
			->where('s.islock = 0 and p.islock = 0 and s.jq_id in ('.implode(',', $search).')')
			->field('s.id id, s.name name, p.picurl picurl, jq.address address, s.pointlng pointlng, s.pointlat pointlat')
			->group('s.id')->order('s.sort desc')->select();

		if (count($res)) {
			foreach ($res as $key => $value) {
				if ($value['picurl']) {
					$res[$key]['picurl'] = $this->returnURLName() . substr($value['picurl'], 1);
				} else {
					$res[$key]['picurl'] = '';
				}
			}

		} else {
			$res = array();
		}

		return $res;
	}
}