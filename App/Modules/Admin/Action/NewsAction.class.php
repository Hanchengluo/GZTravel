<?php
/**
 * 后台新闻类
 * @author   <[c@easycms.cc]>
 */
class NewsAction extends CommonAction{
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('news');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=0;
		$model = M('News');
		$m = M('Newstype');
		$newslist = $m->where($data)->select();
		$this->assign('newslist',$newslist);
		if (!empty($model)) {
			$this->_list($model, $map, 'sort');
		}
		$this->display();
		return;
	}
	
	public function rubbish() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search('news');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['islock']=1;
		$model = M('news');
		$m = M('Newstype');
		$newslist = $m->where($data)->select();
		$this->assign('newslist',$newslist);
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
			$map['title']=array("like","%{$_REQUEST['keyword']}%");
		}
	}	
	
	public function add(){
		$m=M('Newstype');
		$data['islock']=0;
		$aslist=$m->where($data)->select();
		$this->assign('aslist',$aslist);
		$this->display(add);
		return;
	}
	
	public function addPicture() {
		$model1 = M('Areaservice');
		$data['name'] = '新闻';
		$asid = $model1->where($data)->getField('id');
		$model = M('News');
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
		$model = M('News');
		$news=array(
				'title'=>I('title'),
				'comment'=>I('comment'),
				'nt_id'=>I('nt_id'),
				'jianjie'=>I('jianjie'),
				'username'=>$_SESSION[C('ADMIN_AUTH_KEY_B')],
				'updatetime'=>time()
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
		$model = M('News');
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->find($id);
		$this->assign('vo', $vo);
		$m=M('Newstype');
		$data['islock']=0;
		$aslist=$m->where($data)->select();
		$this->assign('aslist',$aslist);
		$this->display(edit);
	}
	

	public function update() {
		$model = M('News');
		if(false === $model->create()) {
			$this->error($model->getError());
		}
/*	    $data['id'] = I('id');
		$data['title'] = I('title');
		$data['comment'] = I('comment');
		$data['nt_id'] = I('nt_id');
		$data['jianjie'] = I('jianjie');
		$data['updatetime'] = time();*/
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
		$model = M('News');
		$pid = $_REQUEST[$model->getPk()];
		$model1 = M('Areaservice');
		$data['name'] = '新闻';
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
		$upFilePath = "./Uploads/News/".$type."/";
		$this -> uploadPic($upFilePath);
	}
	
	//xheditor上传文件保存
	public function uploadAll(){
 		header('Content-Type: text/html; charset=UTF-8');
		$inputName='filedata';//表单文件域name
		$attachDir='./Uploads/News';//上传文件保存路径，结尾不要带/
		$dirType=1;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
		$maxAttachSize=2097152;//最大上传大小，默认是2M
		$upExt='txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid';//上传扩展名
		$msgType=2;//返回上传参数的格式：1，只返回url，2，返回参数数组
		$immediate=isset($_GET['immediate'])?$_GET['immediate']:0;//立即上传模式，仅为演示用
		ini_set('date.timezone','Asia/Shanghai');//时区

		$err = "";
		$msg = "''";
		$tempPath=$attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';
		$localName='';

		if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){//HTML5上传
			file_put_contents($tempPath,file_get_contents("php://input"));
			$localName=urldecode($info[2]);
		}
		else{//标准表单式上传
			$upfile=@$_FILES[$inputName];
			if(!isset($upfile))$err='文件域的name错误';
			elseif(!empty($upfile['error'])){
				switch($upfile['error'])
				{
					case '1':
						$err = '文件大小超过了php.ini定义的upload_max_filesize值';
						break;
					case '2':
						$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
						break;
					case '3':
						$err = '文件上传不完全';
						break;
					case '4':
						$err = '无文件上传';
						break;
					case '6':
						$err = '缺少临时文件夹';
						break;
					case '7':
						$err = '写文件失败';
						break;
					case '8':
						$err = '上传被其它扩展中断';
						break;
					case '999':
					default:
						$err = '无有效错误代码';
				}
			}
			elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none')$err = '无文件上传';
			else{
				move_uploaded_file($upfile['tmp_name'],$tempPath);
				$localName=$upfile['name'];
			}
		}

		if($err==''){
			$fileInfo=pathinfo($localName);
			$extension=$fileInfo['extension'];
			if(preg_match('/^('.str_replace(',','|',$upExt).')$/i',$extension))
			{
				$bytes=filesize($tempPath);
				if($bytes > $maxAttachSize)$err='请不要上传大小超过'.$this->formatBytes($maxAttachSize).'的文件';
				else
				{
					switch($dirType)
					{
						case 1: $attachSubDir = 'day_'.date('ymd'); break;
						case 2: $attachSubDir = 'month_'.date('ym'); break;
						case 3: $attachSubDir = 'ext_'.$extension; break;
					}
					$attachDir = $attachDir.'/'.$attachSubDir;
					if(!is_dir($attachDir))
					{
						@mkdir($attachDir, 0777);
						@fclose(fopen($attachDir.'/index.htm', 'w'));
					}
					PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
					$newFilename=date("YmdHis").mt_rand(1000,9999).'.'.$extension;
					$targetPath = $attachDir.'/'.$newFilename;
					
					rename($tempPath,$targetPath);
					@chmod($targetPath,0755);
					$targetPath=$this->jsonString($targetPath);

					if($immediate=='1')$targetPath='!'.$targetPath;
					if($msgType==1)$msg="'$targetPath'";
					else $msg="{'url':'".$targetPath."','localname':'".$this->jsonString($localName)."','id':'1'}";//id参数固定不变，仅供演示，实际项目中可以是数据库ID
				}
			}
			else $err='上传文件扩展名必需为：'.$upExt;

			@unlink($tempPath);
		}

		echo "{'err':'".$this->jsonString($err)."','msg':".$msg."}";
	}

	function jsonString($str)
	{
		return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
	}
	
	function formatBytes($bytes) {
		if($bytes >= 1073741824) {
			$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
		} elseif($bytes >= 1048576) {
			$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
		} elseif($bytes >= 1024) {
			$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
		} else {
			$bytes = $bytes . 'Bytes';
		}
		return $bytes;
	}

   public function rubAll(){
    	$name='News';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$data1['islock']=1;
		$model->where($data)->save($data1);
		$this->success('更新成功');
	}

	public function delAll(){
    	$name='News';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$model->where($data)->delete();
		$this->success('更新成功');
	}

   public function recAll(){
    	$name='News';
		$model = M($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$data1['islock']=0;
		$model->where($data)->save($data1);
		$this->success('更新成功');
	}

	public function changeState() {
		$model = M("News"); // 实例化对象
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
		$model = D('News');
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
