<?php 
/**
* 资源打包
* @author  <[s@easycms.cc]>
*/
class ArchiveAction extends CommonAction
{
	public function index(){
		//列表过滤器，生成查询Map对象
		$map = $this->_search('Archive');
		if(method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		// $map['islock']=0;
		$model = M('archive');
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		// dump($model);
		// dump($model);
		$this->display();
		return;
	}	
	public function edit() {
		$model = M('archive');
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->find($id);
		$this->assign('vo', $vo);
		$this->display();
	}

	public function update(){
		$model = M('archive');
		if(false === $model->create()) {
			$this->error($model->getError());
		}
	    $data['id'] = I('id');
		$data['explain'] = I('explain');

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
	public function add(){
		$this->display();
	}
	// 修改上线状态
	public function changeState() {
		$model = M("archive"); // 实例化对象
		$pk = $model->getPk();
		$condition[$pk]=$_REQUEST[$pk];
		// 要修改的数据对象属性赋值
		$data['isonline'] = ($_REQUEST['isonline']==0)?1:0;
		$model->where($condition)->save($data); // 根据条件保存修改的数据
		if(false !== $model->where($condition)->save($data)) {
			//成功提示
			$this->success(L('更新成功'));
		} else {
			//错误提示
			$this->error(L('更新失败'));
		}
	}
	public function delete() {
		//删除指定记录
		$model = M('archive');
		if (!empty($model)) {
			$id = $_REQUEST['id'];
			if (isset($id)) {
				$data['id'] = $id;
				if (false !== $model->where($data)->delete()) {
					$this->success(L('删除成功'));
				} else {
					$this->error(L('删除失败'));
				}
			} else {
				$this->error('非法操作');
			}
		}
	}

	public function uploadify(){
	    if (!empty($_FILES)) {
	        import('ORG.Net.UploadFile');
	        $upload = new UploadFile();
	        $upload->allowExts = array('zip');
	        $upload->savePath = "./archive/";
	        $upload->saveRule = $this->pictureNameRule(); //上传规则
	        if(!$upload->upload()){
	            $this->error($upload->getErrorMsg());//获取失败信息
	        } else {
	            $info = $upload->getUploadFileInfo();//获取成功信息
	        }
	        echo $info[0]['savename'];    //返回文件名给JS作回调用
	    }
 	}

 	public function pictureNameRule(){
 		$model = M('archive');
        $data = $model->where('type=1')->order('archive_time desc')->find();
        if ($data) {
            $archive_version = $data['archive_version'] + 1;
        }else{
            $archive_version = 1;
        }
        $file_name = 'map_'.time().'_'.$archive_version;

        $new_archive['resource_path'] = './archive/'.$file_name.'.zip';
        $new_archive['archive_version'] = $archive_version;
        $new_archive['type'] = 1;
        $new_archive['archive_time'] = time();
		$model->add($new_archive);
        return $file_name;
	}
}