<?php
/**
 * 后台新闻类
 * @author   <[c@easycms.cc]>
 */
class ZuobiaoAction extends CommonAction{
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Zuobiao');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=0;
		$model = M('Zuobiao');
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		$this->display();
		return;
	}
	
	
	public function rubbish() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Zuobiao');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=1;
		$model = M('Zuobiao');
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		$this->display();
		return;
	}

	//添加搜索方法
	public function _filter(&$map){
		//判断是否有搜索条件
		if(!empty($_REQUEST['keyword'])){
			$map['name']=array("like","%{$_REQUEST['keyword']}%");
		}
	}	
	
	public function add(){
		$m=M('Areaservice');
		$data['level']=2;
		$data['islock']=0;
		$aslist=$m->where($data)->select();
		$this->assign('aslist',$aslist);
		$this->display(add);
	}

	public function getLevel(){
		$m=M('Areaservice');
		$data['level']=$_REQUEST['level'];
		$data['islock']=0;
		$aslist = $m->where($data)->getField('id,name',true);
		$list = "[";
		foreach ($aslist as $key => $value) {
			if ($value == "美食" ||  $value == "特产") {
				continue;
			}
			$list = $list."[\"".$key."\",\"".$value."\"],";
		}
		$list = substr($list, 0,strlen($list)-1);
		$list = $list."]";
		echo $list;
		exit;
	}
	
	public function insert(){
		$model = M('Zuobiao');
		$Zuobiao=array(
				'name'=>I('name'),
				'pointlng'=>I('pointlng'),
				'pointlat'=>I('pointlat'),
				'bpointlng'=>I('bpointlng'),
				'bpointlat'=>I('bpointlat'),
				'address'=>I('address'),
				'picurl' => I('picurl'),
				'as_id'=>I('as_id')
			);
		
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		//保存当前数据对象
		if ($result = $model->add($Zuobiao)){ //保存成功
			// 回调接口
			if (method_exists($this, '_tigger_insert')) {
				$model->id = $result;
				$this->_tigger_insert($model);
			}
			
			//成功提示
			$this->success(L('新增成功'));
		} else {
			//失败提示
			$this->error(L('新增失败').$model->getLastSql());
		}
		
	
	}
	
	public function edit() {
		$model = M('Zuobiao');
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->find($id);
		$m=M('Areaservice');
		$vo['level']=$m->where(array('id' => $vo['as_id']))->getField('level');
		$this->assign('vo', $vo);
		
		$data['level']=$vo['level'];
		$data['islock']=0;
		$aslist=$m->where($data)->select();
		$this->assign('aslist',$aslist);
		$this->display(edit);
	}
	

	public function update() {
		$model = M('Zuobiao');
		if(false === $model->create()) {
			$this->error($model->getError());
		}
	    $data['id'] = I('id');
		$data['name'] = I('name');
		$data['pointlng'] = I('pointlng');
		$data['pointlat'] = I('pointlat');
		$data['bpointlng'] = I('bpointlng');
		$data['bpointlat'] = I('bpointlat');
		$data['address'] = I('address');
		$data['picurl'] = I('picurl');
		$data['as_id'] = I('as_id');
		// 更新数据
		if(false !== $model->save($data)) {
			// 回调接口
			if (method_exists($this, '_tigger_update')) {
				$this->_tigger_update($model);
			}
			//成功提示
			$this->success(L('更新成功'));
		} else {
			//错误提示
			$this->error(L('更新失败'));
		}
	}
	
   public function rubAll(){
    	$name='Zuobiao';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$data1['islock']=1;
		$model->where($data)->save($data1);
		$this->success('更新成功');
	}

	public function delAll(){
    	$name='Zuobiao';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$model->where($data)->delete();
		$this->success('更新成功');
	}

   public function recAll(){
    	$name='Zuobiao';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$data1['islock']=0;
		$model->where($data)->save($data1);
		$this->success('更新成功');
	}

	public function changeState() {
		$model = M("Zuobiao"); // 实例化对象
		$pk = $model->getPk();
		$condition[$pk]=$_REQUEST[$pk];
		// 要修改的数据对象属性赋值
		$data['islock'] = ($_REQUEST['islock']==0)?1:0;
		$model->where($condition)->save($data); // 根据条件保存修改的数据
		if(false !== $model->where($condition)->save($data)) {
			//成功提示
			$this->success(L('更新成功'));
		} else {
			//错误提示
			$this->error(L('更新失败'));
		}
	}

	public function uploadAdd() {
		$type = $_REQUEST['type'];
		$this->assign('type', $type);
		$this->display('Public/upload');
	}

	//上传图片
	public function upload(){
		//设置上传目录
		$type = $_POST['type'];
		$upFilePath = "./Uploads/Zuobiao/".$type."/";
		$this -> uploadPic($upFilePath);
	}

	public function photos() {
		$model = M('Zuobiao');
		$map['id'] = $_GET['id'];

		$map['islock'] = 0;
		$volist = $model->where($map)->select();
		$this->assign('volist', $volist);
		$this->display('Public/photo');
	}
	/**
	 * 根据表单生成查询条件
	 * 进行列表过滤
	 * @param string $name 数据对象名称
	 * @return HashMap
	 */
	protected function _search($name='') {
		//生成查询条件
		if (empty($name)) {
			$name = $this->getActionName();
		}
		$model = D('Zuobiao');
		$map = array();
	    foreach ($model->getDbFields() as $key => $val) {
		 	if (substr($key, 0, 1) == '_')
		 		continue;
			if (isset($_REQUEST[$val]) && $_REQUEST[$val] != '') {
				$map[$val] = $_REQUEST[$val];
		 	}
		 }
		return $map;
	}
	
	/**
	 * 根据表单生成查询条件
	 * 进行列表过滤
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
	 */
	protected function _list($model, $map = array(), $sortBy = '', $asc = false) {
		//排序字段 默认为主键名
		if (!empty($_REQUEST['_order'])) {
			$order = $_REQUEST['_order'];
		} else {
			$order = !empty($sortBy)?$sortBy:$model->getPk();
		}
		
		//排序方式默认按照倒序排列
		//接受 sort参数 0 表示倒序 非0都 表示正序
		if (!empty($_REQUEST['_sort'])) {
			$sort = $_REQUEST['_sort'] == 'asc'?'asc':'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		
		//取得满足条件的记录数
		$count = $model->where($map)->count();
		
		//每页显示的记录数
		if (!empty($_REQUEST['numPerPage'])) {
			$listRows = $_REQUEST['numPerPage'];
		} else {
			$listRows = '10';
		}
		
		
		//设置当前页码
		if(!empty($_REQUEST['pageNum'])) {
			$nowPage=$_REQUEST['pageNum'];
		}else{
			$nowPage=1;
		}
		$_GET['p']=$nowPage;
		
		//创建分页对象
		import("ORG.Util.Page");
		$p = new Page($count, $listRows);
		
		
		//分页查询数据
		//$list = $model->where($map)->order($order . ' ' . $sort)->select();
		$list = $model->where($map)->order($order.' '.$sort)
						->limit($p->firstRow.','.$p->listRows)
						->select();			
		//回调函数，用于数据加工，如将用户id，替换成用户名称
		if (method_exists($this, '_tigger_list')) {
			$this->_tigger_list($list);
		}
		//分页跳转的时候保证查询条件
		foreach ($map as $key => $val) {
			if (!is_array($val)) {
				$p->parameter .= "$key=" . urlencode($val) . "&";
			}
		}
	
		//分页显示
		$page = $p->show();
		
		//列表排序显示
		$sortImg = $sort;                                 //排序图标
		$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列';   //排序提示
		$sort = $sort == 'desc' ? 1 : 0;                  //排序方式
		
		
		//模板赋值显示
		$this->assign('list', $list);
		$this->assign('sort', $sort);
		$this->assign('order', $order);
		$this->assign('sortImg', $sortImg);
		$this->assign('sortType', $sortAlt);
		$this->assign("page", $page);
		
		$this->assign("search",			$search);			//搜索类型
		$this->assign("values",			$_POST['values']);	//搜索输入框内容
		$this->assign("totalCount",		$count);			//总条数
		$this->assign("numPerPage",		$p->listRows);		//每页显多少条
		$this->assign("currentPage",	$nowPage);			//当前页码
	}
}
