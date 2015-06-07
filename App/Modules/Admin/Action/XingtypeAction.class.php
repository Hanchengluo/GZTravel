<?php
/**
 * 后台行程类型类
 * @author   <[c@easycms.cc]>
 */
class XingtypeAction extends CommonAction{
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Xingtype');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=0;
		$model = M('Xingtype');
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		$this->display();
		return;
	}
	
	public function rubbish() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Xingtype');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=1;
		$model = M('Xingtype');
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
		$m=M('Xingtype');
		$aslist=$m->where($data)->select();
		$this->assign('aslist',$aslist);
		$this->display(add);
	}
	
	public function insert(){
		$model = M('Xingtype');
		$news=array(
				'name'=>I('name')
			);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		//保存当前数据对象
		if ($result = $model->add($news)){ //保存成功
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
		$model = M('Xingtype');
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->find($id);
		$this->assign('vo', $vo);
		$this->display(edit);
	}
	

	public function update() {
		$model = M('Xingtype');
		if(false === $model->create()) {
			$this->error($model->getError());
		}
	    $data['id'] = I('id');
		$data['name'] = I('name');
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
    	$name='Xingtype';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$data1['islock']=1;
		$model->where($data)->save($data1);
		$this->success('更新成功');
	}

	public function delAll(){
    	$name='Xingtype';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$data1['xt_id'] = array('in',$_POST['ids']);
		$count = M('Xing')->where($data1)->count('id');
		if ($count > 0) {
			$this->error(L('该类型的行程已存在，请先删除该类型的行程后，再删除！'));
		} else {
			$model->where($data)->delete();
			$this->success('删除类型成功');	
		}
	}

   public function recAll(){
    	$name='Xingtype';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$data1['islock']=0;
		$model->where($data)->save($data1);
		$this->success('更新成功');
	}

	public function changeState() {
		$model = M("Xingtype"); // 实例化对象
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
		$model = D('Xingtype');
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
