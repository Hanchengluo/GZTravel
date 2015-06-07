<?php
/**
* 前台公共类
* @author  <[c@easycms.cc]>
*/
class CommonAction extends Action
{
	Public function _initialize(){
		if (ismobile()) {
            C('DEFAULT_THEME','mobile');
        }
		//全局首页，用户个人中心导航分类展示
		$cats=M('Category')->where('isverify=1 and isshow=1')->order('sort desc')->select();
		$this->assign('cats',$cats);
	}
	
	//空操作
	public function _empty(){
		$this->redirect(__ROOT__);
	}

	//正则内容
	public function returnContent($str){
		$temp = null;
		if (strlen(trim(strip_tags($str))) > 300) {
			$temp = mb_substr(trim(strip_tags($str)), 0, 300, 'utf-8').'...';
		} else {
			$temp = trim(strip_tags($str));
		}
		if ($temp == '') {
			$temp = null;
		}
		return $temp;
	}

	//当前URL
	public function returnURL(){
		return 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].__URL__;
	}

	//当前URL1
	public function returnURLName(){
		list(,$temp)=explode('/',__URL__);
		return 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].'/'.$temp;
	}

	//获取区域ID
	public function getAreaServiceId($name){
		$model = M('Areaservice');
		$map['name'] = $name;
		$asid = $model->where($map)->getField('id');
		return $asid;
	}

	//获取区域IDS
	public function getAreaServiceIds($pid){
		$model = M('Areaservice');
		$map['pid'] = $pid;
		$asids = $model->where($map)->field('id')->select();
		return $asids;
	}

	//周边景点
	public function getZbjd($zbjd){
		$asid = $this->getAreaServiceId('景点');
		$Model = new Model();
		$resZbjd = $Model->Distinct(true)->table('jingdian jd')
			->join('Jingqu jq on jq.id = jd.jq_id')
			->join('Picture p on jd.id = p.pid and p.as_id='.$asid.'')
			->where('jd.islock = 0 and p.islock = 0 and jd.id in ('.$zbjd.')')
			->field('jd.id id, jd.name name, jq.address address, jd.pointlng pointlng,
			 jd.pointlat pointlat, p.minurl picurl')
			->group('jd.id')->select();

		if ($resZbjd) {
			foreach ($resZbjd as $key => $value) {
				$resZbjd[$key]['picurl'] = $this->returnURLName().substr($value['picurl'], 1);
			}
		} else {
			$resZbjd = array();
		}
		return $resZbjd;
	}

	//周边餐饮

	public function getZbcy($zbcy) {
		$zbcy = D('chi')->Distinct(true)->join('Picture p on chi.id = p.pid AND chi.as_id = p.as_id')
			->where(array('chi.islock' => 0, 'chi.id' =>array('in',
				explode(',', $zbcy))))
			->field('chi.id id, chi.name name, chi.address address,
						 chi.pointlng pointlng, chi.pointlat pointlat, p.minurl picurl')
			->group('chi.id')->select();
		if (count($zbcy)) {
			foreach ($zbcy as $key => $value) {
				$zbcy[$key]['picurl'] = $this->returnURLName().substr($value['picurl'], 1);
			}
		} else {
			$zbcy = array();
		}
		return $zbcy;
	}

	//周边商店
	public function getZbsd($zbsd){
		$asid = $this->getAreaServiceId('商店');
		$Model = new Model();
		$resZbsd = $Model->Distinct(true)->table('shop s')
			->join('Picture p on s.id = p.pid and p.as_id='.$asid.'')
			->where('s.islock = 0 and p.islock = 0 and s.id in ('.$zbsd.')')
			->field('s.id id, s.name name, s.address address, s.pointlng pointlng,
			 s.pointlat pointlat, p.minurl picurl')
			->group('s.id')->select();

		if ($resZbsd) {
			foreach ($resZbsd as $key => $value) {
				$resZbsd[$key]['picurl'] = $this->returnURLName().substr($value['picurl'], 1);
			}
		} else {
			$resZbsd = array();
		}
		return $resZbsd;
	}

	//周边旅馆

	public function getZblg($zbzs) {
		$zblg = D('zhu')->Distinct(true)->join('Picture p on zhu.id = p.pid AND zhu.as_id = p.as_id')
			->where(array('zhu.islock' => 0, 'zhu.id' =>array('in',
				explode(',', $zbzs))))
			->field('zhu.id id, zhu.name name, zhu.address address,
						 zhu.pointlng pointlng, zhu.pointlat pointlat, p.minurl picurl')
			->group('zhu.id')->select();
		if (count($zblg)) {
			foreach ($zblg as $key => $value) {
				$zblg[$key]['picurl'] = $this->returnURLName().substr($value['picurl'], 1);
			}
		} else {
			$zblg = array();
		}
		return $zblg;
	}

	public function  getPictureUrl($url) {

		$temp = './' . substr($url, strpos($url,'Uploads'));
		$picurl = D('Picture')->where(array('minurl' => $temp))->field('picurl')->find();

		if (file_exists($picurl['picurl'])) {
			header('location:'.$picurl['picurl']);
		} else {
			header('location:'.$url);
		}
	}

}
