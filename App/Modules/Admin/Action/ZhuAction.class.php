<?php 
/**
* 住地类
* @author  <[s@easycms.cc]>
*/
class ZhuAction extends CommonAction
{
		public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Zhu');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$model = M('Zhu');
		//$map['islock'] = 0;
		if (!empty($model)) {
			$this->_list($model, $map, 'sort');
		}
		$this->display();
		return;
	}

	public function search() {
		//列表过滤器，生成查询Map对象
		$model1 = M('Areaservice');
		$data['name'] = '住';
		$id = $model1->where($data)->getField('id');
		$data1['pid'] = $id;
		$ids = $model1->where($data1)->getField('id',true);
		$model = M('Zuobiao');
		$map = $this->_search('Zuobiao');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock'] = 0;
		$map['as_id'] = array ('in',$ids);
		if (!empty($model)) {
			$this->_list($model, $map, 'sort');
		}
		$this->display(zuobiao);
	}

	//添加搜索方法
	public function _filter(&$map){
		//判断是否有搜索条件
		if(!empty($_REQUEST['keyword'])){
			$map['name']=array("like","%{$_REQUEST['keyword']}%");
		}
	}	
	
	public function add() {
		$model1 = M('Jingqu');
		$data['islock'] = 0;
		$jqlist = $model1->where($data)->select();
		$this->assign('jqlist', $jqlist);
		$model2 = M('Areaservice');
		$data['name'] = '住';
		$zhuid = $model2->where($data)->getField('id');
		$map['pid'] = $zhuid;
		$map['islock'] = '0';
		$aslist = $model2->where($map)->select();
		$this->assign('aslist', $aslist);
		unset($data['name']);
		$zbcylist = M('Chi')->where($data)->getField('id,name');
		$this->assign('zbcylist',$zbcylist);
		$zbsdlist = M('Shop')->where($data)->getField('id,name');
		$this->assign('zbsdlist',$zbsdlist);
		$zbjdlist = M('Jingdian')->where($data)->getField('id,name');
		$this->assign('zbjdlist',$zbjdlist);
		$this->display('add');
	}
	
	public function addPicture() {
		$ids = I('id');
		$id = explode(',', $ids);
		$this->assign('pid', $id[0]);
		$this->assign('asid', $id[1]);
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
	
	public function yinyue() {
		$model = M('Zhu');
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->find($id);
		$this->assign('vo', $vo);
		$this->display('Public/yinyue');
	}

	public function photos() {
		$ids = I('id');
		$id = explode(',', $ids);
		$model2 = M('Picture');
		$map['pid'] = $id[0];
		$map['as_id'] = $id[1];
        $volist = $model2->where($map)->select();
		$this->assign('volist', $volist);
		$this->display('Public/photo');
	}

	
	public function zuobiao() {
		$model1 = M('Areaservice');
		$data['name'] = '住';
		$id = $model1->where($data)->getField('id');
		$data1['pid'] = $id;
		$ids = $model1->where($data1)->getField('id',true);
		$model = M('Zuobiao');
		$map['islock'] = 0;
		$map['as_id'] = array ('in',$ids);
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		$this->display(zuobiao);
	}
	
	public function insert(){
		$model = D('Zhu');
		unset ( $_POST [$model->getPk()] );
		$_POST['zbcy'] = implode(",",I('zbcy'));
		$_POST['zbsd'] = implode(",",I('zbsd'));
		$_POST['zbjd'] = implode(",",I('zbjd'));
		$_POST['comment'] = strip_tags($_POST['comment']);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		//保存当前数据对象
		if ($result = $model->add()) { //保存成功
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
		$model = M('Zhu');
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->find($id);
		$this->assign('vo', $vo);
		$model1 = M('Jingqu');
		$data['islock'] = 0;
		$jqlist = $model1->where($data)->select();
		$this->assign('jqlist', $jqlist);
		$model2 = M('Areaservice');
		$data['name'] = '住';
		$Zhuid = $model2->where($data)->getField('id');
		$map['pid'] = $Zhuid;
		$map['islock'] = '0';
		$aslist = $model2->where($map)->select();
		$this->assign('aslist', $aslist);
		unset($data['name']);
		$zbcylist = M('Chi')->where($data)->getField('id,name');
		$this->assign('zbcylist',$zbcylist);
		$zbsdlist = M('Shop')->where($data)->getField('id,name');
		$this->assign('zbsdlist',$zbsdlist);
		$zbjdlist = M('Jingdian')->where($data)->getField('id,name');
		$this->assign('zbjdlist',$zbjdlist);
		$this->display('edit');
	}
	
	public function update() {
		$_POST['zbcy'] = implode(",",I('zbcy'));
		$_POST['zbsd'] = implode(",",I('zbsd'));
		$_POST['zbjd'] = implode(",",I('zbjd'));
		$_POST['comment'] = strip_tags($_POST['comment']);
		$model = D('Zhu');

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

	public function delete() {
		//删除指定记录
		$model = M('Zhu');
		if (!empty($model)) {
			$pk = $model->getPk();
			$id = $_REQUEST[$pk];
			if (isset($id)) {
				$condition = array($pk => array('in', explode(',', $id)));
				if (false !== $model->where($condition)->delete()) {
					$this->success(L('删除成功'));
				} else {
					$this->error(L('删除失败'));
				}
			} else {
				$this->error('非法操作');
			}
		}
	}
	
	public function lock(){
		$model = M('Zhu');
		$data['id']=(I('id'));
		$data['islock']=I('islock');

		if(false === $model->create($data)) {
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
	
	public function uploadAdd() {
		$type = $_REQUEST['type'];
		$this->assign('type', $type);
		$this->display('Public/upload');
	}


	public function upload(){
		//设置上传目录
		$type = $_POST['type'];		
		$upFilePath = "./Uploads/Zhu/".$type."/";
		$this -> uploadPic($upFilePath);
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
		$model = M($name);
		$map = array();
		//$map['islock'] = 0;
		foreach ($model->getDbFields() as $key => $val) {
			if (substr($key, 0, 1) == '_')
				continue;
			if (isset($_REQUEST[$val]) && $_REQUEST[$val] != '') {
				$map[$val] = $_REQUEST[$val];
			}
		}
		//$map['islock'] = 0;
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

