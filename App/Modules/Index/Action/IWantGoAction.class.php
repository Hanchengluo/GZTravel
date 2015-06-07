<?php
/**
 * 后台类
 * @author   <[c@easycms.cc]>
 */
class IWantGoAction extends CommonAction{
	

	public function search($type, $currentPage = 1, $pageCount = 10) {

		if (isset($type) && is_numeric($currentPage) && is_numeric($pageCount)) {
			$pager = array('currentPage' => $currentPage, 'pageCount' => $pageCount);
			switch ($type) {
				case '美食' :
					R('Chi/getAllChis', $pager);
					break;
				case '住宿' :
					R('Zhu/getAllZhus', $pager);
					break;
				case '景点' :
					R('Jingdian/getAllJingdians', $pager);
					break;
				case '购物' :
					R('Shop/getAllShops', $pager);
					break;
				default :
					$this->getNull();
					break;
			}

		} else {
			$this->getNull();
		}
	}

	public function getNull(){
	    header('Content-Type:application/json; charset=utf-8');
	    $res = array('code'=>200,'error'=>'','data'=>[]);
	    echo json_encode($res);
	    exit();
	}

}
?>