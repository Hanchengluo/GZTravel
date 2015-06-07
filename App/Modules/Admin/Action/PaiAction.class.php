<?php 
/**
* 拍类
* @author  <[s@easycms.cc]>
*/
class PaiAction extends CommonAction
{

	public function index() {

		//列表过滤器，生成查询Map对象
		$map = $this->_search('Pai');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=0;
		$map['state'] = 0;
		$model = M('Pai');
		if (!empty($model)) {
			$this->_list($model, $map);
		}

		$this->display('index');
		return;
	}


	// 审核通过
	public function pass() {
		//列表过滤器，生成查询Map对象

		$map = $this->_search('Pai');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=0;
		$map['state'] = 1;
		$model = M('Pai');
		if (!empty($model)) {
			$this->_list($model, $map);
		}

		$this->display('pass');
		return;

	}

	// 审核未过
	public function nopass() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Pai');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=0;
		$map['state'] = 2;
		$model = M('Pai');
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		$this->display('nopass');
		return;
	}

	// 加入未审核
	public function shenHeState0(){
		$model = M('pai');

		$data["id"] = $_GET["id"];
		$data["state"] = 0;

		if(false !== $model->save($data)) {
			//成功提示
			$this->success(L('更新成功'));
		} else {
			//错误提示
			$this->error(L('更新失败'));
		}
	}

	// 加入审核通过
	public function shenHeState1(){
		$model = M('pai');

		$data["id"] = $_GET["id"];
		$data["state"] = 1;
		
		if(false !== $model->save($data)) {
			//成功提示
			$this->success(L('更新成功'));
		} else {
			//错误提示
			$this->error(L('更新失败'));
		}
	}



	// 加入审核未通过
	public function shenHeState2(){
		$model = M('pai');
		$data["id"] = $_GET["id"];
		$data["state"] = 2;
		if(false !== $model->save($data)) {
			//成功提示
			$this->success(L('更新成功'));
		} else {
			//错误提示
			$this->error(L('更新失败'));
		}
	}






	public function rubbish() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Pai');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=1;
		$model = M('Pai');
		if (!empty($model)) {
			$this->_list($model, $map);
		}

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
			$map['title']=array("like","%{$_REQUEST['keyword']}%");
		}
	}	
	
	public function add() {

		$this->display();
	}
	
	
	public function insert(){
		$model = D('Pai');
		$pai=array(
				'title'=>strip_tags(I('title')),
				'comment'=>strip_tags(I('comment')),
				'updatetime'=>time()
			);
		
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		//保存当前数据对象
		if ($result = $model->add($pai)){ //保存成功
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
		$model = M('Pai');
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->find($id);
		$this->assign('vo', $vo);
		$this->display('edit');
	}
	
	public function update() {
		$model = D('Pai');
		$_POST['title'] = strip_tags($_POST['title']);
		$_POST['comment'] = strip_tags($_POST['comment']);

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
		$model = M('Pai');
		$model1 = M('Photos');
		if (!empty($model) && !empty($model1)) {
			$pk = $model->getPk();
			$id = $_REQUEST[$pk];
			if (isset($id)) {
				$condition1 = array($pk => array('in', explode(',', $id)));
				$model->startTrans();
				$data['id'] = $id;
				$flag1 = $model->where($condition1)->delete();
				$condition2 = array('pai_id' => array('in', explode(',', $id)));
				$flag2 = $model1->where($condition2)->delete();
				if ((false !== $flag1) && (false !== $flag2)) {
					$model->commit();
					$this->success(L('删除成功'));
				} else {
					$model->rollback();
					$this->error(L('删除失败'));
				}
			} else {
				$this->error('非法操作');
			}
		}
	}

    public function addPicture() {
		$model = M('Pai');
		if (!empty($model)) {
			$pid = $_REQUEST[$model->getPk()];
			$this->assign('pid', $pid);
		}
		$this->display('Public/addPhotos');
	}

	public function savePic() {
		$model = M('Photos');
		unset ( $_POST [$model->getPk()] );
		
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$total = $_REQUEST['total'];
		$pid = $_REQUEST['pid'];

		for ($i = 0; $i < $total; $i++) 
		{ 
			$data[$i]['pai_id'] = $pid;
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

	public function shenhe() {
		$model = M('Pai');
		if (!empty($model)) {
			$pid = $_REQUEST[$model->getPk()];
		}
		$model1 = M('Photos');
		if (!empty($model1)) {
			$map['pai_id'] = $pid;
			// $map['islock'] = 0;
			// $map['state'] = 0;
        	$volist = $model1->where($map)->select();
			$this->assign('volist', $volist);
		}
		$this->display('shenhephoto');
	}
/*	public function photo() {
		$model = M('Pai');
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->find($id);
		$this->assign('vo', $vo);
		$this->display('Public/photos');
	}*/

	public function photos() {
		$model = M('Pai');
		if (!empty($model)) {
			$pid = $_REQUEST[$model->getPk()];
		}
		$model1 = M('Photos');
		if (!empty($model1)) {
			$map['pai_id'] = $pid;
			// $map['state'] = 1;
			// $map['islock'] = 0;
        	$volist = $model1->where($map)->select();
			$this->assign('volist', $volist);
		}
		$this->display('Public/photo1');
	}

	public function todophotos() {
		$model = M('Pai');
		if (!empty($model)) {
			$pid = $_REQUEST[$model->getPk()];
		}
		$model1 = M('Photos');
		if (!empty($model1)) {
			// $map['islock'] = 0;
			// $map['state'] = 0;
			$map['pai_id'] = $pid;
        	$volist = $model1->where($map)->select();
			$this->assign('volist', $volist);
		}
		$this->display('Public/photo');
	}

	public function donephotos() {
		$model = M('Pai');
		if (!empty($model)) {
			$pid = $_REQUEST[$model->getPk()];
		}
		$model1 = M('Photos');
		if (!empty($model1)) {
			$ids = array('1','2');
			// $map['islock'] = 0;
			// $map['state'] = array ('in',$ids);
			$map['pai_id'] = $pid;
        	$volist = $model1->where($map)->select();
			$this->assign('volist', $volist);
		}
		$this->display('Public/photo');
	}

	public function uploadAdd() {
		$type = $_REQUEST['type'];
		$this->assign('type', $type);
		$this->display('Public/upload');
	}


	public function upload(){
		//设置上传目录
		$type = $_POST['type'];		
		$upFilePath = "./Uploads/Pai/".$type."/";
		$this -> uploadPic($upFilePath);
	}
	
	public function rubAll(){
    	$name='Pai';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$data1['islock']=1;
		$model->where($data)->save($data1);
		$this->success('更新成功');
	}

	public function delAll(){
    	$name='Pai';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$model->where($data)->delete();
		$this->success('更新成功');
	}

   public function recAll(){
    	$name='Pai';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$data1['islock']=0;
		$model->where($data)->save($data1);
		$this->success('更新成功');
	}

	public function changeState() {
		$model = M("Pai"); // 实例化对象
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

	/*//审核图集
	public function shenhetjState() {
		$model = M("Pai"); // 实例化对象
		// 要修改的数据对象属性赋值
		$data['id'] = I('id');
		$data['state'] = I('state');
		$model->save($data); // 根据条件保存修改的数据
		if(false !== $model->save($data)) {
			//成功提示
			$this->success(L('审核成功'));
		} else {
			//错误提示
			$this->error(L('审核失败'));
		}
	}
    
    //审核图片
	public function shenhetpState() {
		$model = M("Photos"); // 实例化对象
		// 要修改的数据对象属性赋值
		$data['id'] = $_POST['id'];
		$data['state'] = $_REQUEST['state'];
		$model->save($data); // 根据条件保存修改的数据
		if(false !== $model->save($data)) {
			//成功提示
			if($_REQUEST['state'] == 1) {
				$this->success(L('审核通过'));
			} else {
				$this->error(L('审核未过'));
			}
			
		} else {
			//错误提示
			$this->error(L('审核失败'));
		}
	}*/

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
						->limit($p->firstRow.','.$p->listRows)->select();
						
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

