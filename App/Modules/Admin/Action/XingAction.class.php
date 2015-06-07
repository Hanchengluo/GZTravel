<?php
/**
 * 后台行程类
 * @author   <[c@easycms.cc]>
 */
class XingAction extends CommonAction{
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Xing');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=0;
		$model = M('Xing');
		$m = M('Xingtype');
		$xinglist = $m->where($map)->select();
		$this->assign('xinglist',$xinglist);
		if (!empty($model)) {
			$this->_list($model, $map, 'sort');
		}
		$this->display();
		return;
	}
	
	public function rubbish() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Xing');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=1;
		$data['islock']=0;
		$model = M('Xing');
		$m = M('Xingtype');
		$xinglist = $m->where($data)->select();
		$this->assign('xinglist',$xinglist);
		if (!empty($model)) {
			$this->_list($model, $map,'sort');
		}
		$this->display();
		return;
	}

	//添加搜索方法
	public function _filter(&$map){
		//判断是否有搜索条件
		if(!empty($_REQUEST['keyword'])){
			$map['title']=array("like","%{$_REQUEST['keyword']}%");
		}
	}	
	
	public function add(){
		$m=M('Xingtype');
		$data['islock']=0;
		$aslist=$m->where($data)->select();
		$this->assign('aslist',$aslist);
		$zblglist = M('Zhu')->where($data)->getField('id,name');
		$this->assign('zblglist',$zblglist);
		$zbcylist = M('Chi')->where($data)->getField('id,name');
		$this->assign('zbcylist',$zbcylist);
		$zbsdlist = M('Shop')->where($data)->getField('id,name');
		$this->assign('zbsdlist',$zbsdlist);
		$zbjqlist = M('Jingqu')->where($data)->getField('id,name');
		$this->assign('zbjqlist',$zbjqlist);
		$this->display(add);
		return;
	}
	
	public function addPicture() {
		$model1 = M('Areaservice');
		$data['name'] = '行';
		$asid = $model1->where($data)->getField('id');
		$model = M('Xing');
		$pid = $_REQUEST[$model->getPk()];
		$this->assign('pid', $pid);
		$this->assign('asid', $asid);
		$this->display('Public/addPhoto');
	}

	public function savePic() {
		$model = M('Picture');
		unset ( $_POST [$model->getPk()] );
		
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$total = $_REQUEST['total'];
		$as_id = $_REQUEST['as_id'];
		$pid = $_REQUEST['pid'];

		for ($i = 0; $i < $total; $i++) 
		{ 
			$data[$i]['pid'] = $pid;
			$data[$i]['as_id'] = $as_id;
			$data[$i]['picurl'] = $_REQUEST['picurl'.$i.''];
		} 

		//保存当前数据对象
		if ($result = $model->addAll($data)) { //保存成功
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


	public function savePhotos() {
		$model = M('Picture');
		unset ( $_POST [$model->getPk()] );

		$data['pid'] = $_POST['pid'];
		$data['as_id'] = $_POST['as_id'];

		//保存当前数据对象
		if ($_POST['picurl']) {
			$res1 = $model->where(array('pid' => $_POST['pid'], 'as_id' => $_POST['as_id']))->find();
			if ($res1['id']) {
				$data['picurl'] = $_POST['picurl'];
				$result = $model->where(array('id' => $res1['id']))->save($data);
			} else {
				$data['picurl'] = $_POST['picurl'];
				$result = $model->add($data);
			}
		}

		if ($_POST['minurl']) {
			$res2 = $model->where(array('pid' => $_POST['pid'], 'as_id' => $_POST['as_id']))->find();
			if ($res2['id']) {
				$data['minurl'] = $_POST['minurl'];
				$result = $model->where(array('id' => $res2['id']))->save($data);
			} else {
				$data['minurl'] = $_POST['minurl'];
				$result = $model->add($data);
			}
		}

		if ($result) { //保存成功
			//成功提示
			$this->success(L('更新成功'));
		} else {
			//失败提示
			$this->error(L('更新失败').$model->getLastSql());
		}
	}

	
	public function insert(){

		$_POST['updatetime'] = time();
		$_POST['username'] = $_SESSION[C('ADMIN_AUTH_KEY_B')];
		$_POST['zblg'] = implode(",",$_POST['zblg']);
		$_POST['zbcy'] = implode(",",$_POST['zbcy']);
		$_POST['zbsd'] = implode(",",$_POST['zbsd']);
		$_POST['zbjq'] = implode(",",$_POST['zbjq']);
		$_POST['jianjie'] = strip_tags($_POST['jianjie']);
		$model = M('Xing');
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		//保存当前数据对象
		if ($result = $model->add()){ //保存成功
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
		$model = M('Xing');
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->find($id);
		$this->assign('vo', $vo);
		$m=M('Xingtype');
		$data['islock']=0;
		$aslist=$m->where($data)->select();
		$this->assign('aslist',$aslist);
		$zblglist = M('Zhu')->where($data)->getField('id,name');
		$this->assign('zblglist',$zblglist);
		$zbcylist = M('Chi')->where($data)->getField('id,name');
		$this->assign('zbcylist',$zbcylist);
		$zbsdlist = M('Shop')->where($data)->getField('id,name');
		$this->assign('zbsdlist',$zbsdlist);
		$zbjqlist = M('Jingqu')->where($data)->getField('id,name');
		$this->assign('zbjqlist',$zbjqlist);
		$this->display(edit);
	}
	

	public function update() {
		$_POST['updatetime'] = time();
		$_POST['username'] = $_SESSION[C('ADMIN_AUTH_KEY_B')];
		$_POST['zblg'] = implode(",",$_POST['zblg']);
		$_POST['zbcy'] = implode(",",$_POST['zbcy']);
		$_POST['zbsd'] = implode(",",$_POST['zbsd']);
		$_POST['zbjq'] = implode(",",$_POST['zbjq']);
		$_POST['jianjie'] = strip_tags($_POST['jianjie']);
		$model = M('Xing');

		if(false === $model->create()) {
			$this->error($model->getError());
		}

		// 更新数据
		if(false !== $model->save()) {
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

	public function photos() {
		$model = M('Xing');
		$pid = $_REQUEST[$model->getPk()];
		$model1 = M('Areaservice');
		$data['name'] = '行';
		$asid = $model1->where($data)->getField('id');
		$model2 = M('Picture');
		$map['pid'] = $pid;
		$map['as_id'] = $asid;
        $volist = $model2->where($map)->select();
		$this->assign('volist', $volist);
		$this->display('Public/photo');
	}

	public function deletePic() {
		//删除指定记录
		$model = M('Picture');
		if (!empty($model)) {
			$id = $_POST['id'];
			if (isset($id)) {
				$res = substr($id, strlen($id) - 1);
				$data['id'] = $id;
				if ($res == 's') {
					$picurl = 'minurl';
				} else {
					$picurl = 'picurl';
				}
				$data['id'] = $id;
				$url = $model->where($data)->getField($picurl);
				if (false !== $model->where($data)->setField($picurl,null)) {
					chmod($url,0777);
					@unlink($url);
					$this->success(L('删除成功'));
				} else {
					$this->error(L('删除失败'));
				}
			} else {
				$this->error('非法操作');
			}
		}
	}

	public function uploadAdd() {
		$type = $_REQUEST['type'];
		$this->assign('type', $type);
		$this->display('Public/upload');
	}


	public function upload(){
		//设置上传目录
		$type = $_POST['type'];		
		$upFilePath = "./Uploads/Xing/".$type."/";
		$this -> uploadPic($upFilePath);
	}
	
   public function rubAll(){
    	$name='Xing';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$data['islock']=1;
		$model->where($data)->save($data);
		$this->success('更新成功');
	}

	public function delAll(){
    	$name='Xing';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$model->where($data)->delete();
		$this->success('更新成功');
	}

   public function recAll(){
    	$name='Xing';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$data1['islock']=0;
		$model->where($data)->save($data1);
		$this->success('更新成功');
	}

	public function changeState() {
		$model = M("Xing"); // 实例化对象
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
		$model = D('Xing');
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

		$this->assign("values",			$_POST['values']);	//搜索输入框内容
		$this->assign("totalCount",		$count);			//总条数
		$this->assign("numPerPage",		$p->listRows);		//每页显多少条
		$this->assign("currentPage",	$nowPage);			//当前页码
	}
}
