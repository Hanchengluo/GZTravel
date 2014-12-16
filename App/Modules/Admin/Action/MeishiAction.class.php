<?php 
/**
* 美食类
* @author  <[s@easycms.cc]>
*/
class MeishiAction extends CommonAction
{
		public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Meishi');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$model = M('Meishi');
		//$map['islock'] = 0;
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		$model1 = M('Chi');
		$data['islock'] = 0;
		$chilist = $model1->where($data)->select();
		$this->assign('chilist', $chilist);
		$this->display();
		return;
	}
	public function search() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Zuobiao');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$model = M('Zuobiao');
		$map['islock'] = 0;
		if (!empty($model)) {
			$this->_list($model, $map);
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
		$model1 = M('Chi');
		$data['islock'] = 0;
		$chilist = $model1->where($data)->select();
		$this->assign('chilist', $chilist);
		$this->display('add');
	}
	
	
	public function insert(){
		$model = D('Meishi');
		unset ( $_POST [$model->getPk()] );
		
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
		$model = M('Meishi');
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->find($id);
		$this->assign('vo', $vo);
		$model1 = M('Chi');
		$data['islock'] = 0;
		$chilist = $model1->where($data)->select();
		$this->assign('chilist', $chilist);
		$this->display('edit');
	}
	
	public function update() {
		$model = D('Meishi');		
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
	
	public function delete() {
		//删除指定记录
		$model = M('Meishi');
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
		$model = M('Meishi');
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
		$this->display('upload');
	}

	public function upload(){
		//设置上传目录		
		$upFilePath="./Uploads/Meishi/video/";
		$file_name = $_FILES['pic']['name'];
		$file_tmp_name = $_FILES['pic']['tmp_name'];
		if(!is_dir($upFilePath)){
			mkdir($upFilePath,0777,true);
		}
		$info = pathinfo($file_name);
        $extend = $info['extension'];
		$fileName = date("YmdHis").rand(100,999).'.'.$extend;
		$file=@move_uploaded_file($file_tmp_name,$upFilePath.$fileName);  

		if($file === FALSE){
			echo json_encode(array('code'=>'1','message'=>'上传失败','file_url'=>$upFilePath.$fileName));
		}else{
			echo json_encode(array('code'=>'0','message'=>'上传成功','file_url'=>$upFilePath.$fileName));
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

