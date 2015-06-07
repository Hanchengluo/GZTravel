<?php 
/**
* 拍类
* @author  <[s@easycms.cc]>
*/
class PaiAction extends CommonAction
{
	//获取图集列表
	public function getPailist($currentPage = 1, $pageCount = 10){
	    header('Content-Type:application/json; charset=utf-8');
		$model = M('Pai');
	    $data['islock']=0;
		$data['state']=1;
	    $count = $model->where($data)->count();
	    if ($count > 0 ) {
	    	$res = $model->join('photos ON pai.id=photos.pai_id')->where('pai.islock=0 AND pai.state=1')
				->field('pai.id,pai.title,photos.picurl ,photos.width,photos.height,pai.updatetime')
				->group('pai.id')->page($currentPage, $pageCount)->select();

	    	for ($i=0; $i < count($res) ; $i++) { 
	    		$url = $res[$i]['picurl'];
	    		$res[$i]['picurl'] = $this->returnURLName().substr($url, 1);
				$res[$i]['updatetime'] = date("Y-m-d", $res[$i]['updatetime']);
	    	}
		    $res = array('code'=>200,'error'=>'成功','data'=>$res);
	    } else {
	    	$res = array('code'=>0,'error'=>'没有数据','data'=>'');
	    }
	    echo json_encode($res);
	    exit();
	}

	//获取图集内相片列表
	public function getPhotolist($id, $currentPage = 1, $pageCount = 10){
	    header('Content-Type:application/json; charset=utf-8');
		$model = M('photos');
	    // $data['islock']=0;
	    $data['pai_id']=$id;
	    // $data['state']=1;
	    $count = $model->where($data)->count();
	    if ($count > 0 ) {
		    $res = $model->where($data)->field('id,picurl,width,height')
				->page($currentPage, $pageCount)->select();
		    for ($i=0; $i < count($res) ; $i++) { 
	    		$url = $res[$i]['picurl'];
	    		$res[$i]['picurl'] = $this->returnURLName().substr($url, 1);
	    	}
		    $res = array('code'=>200,'error'=>'成功','data'=>$res);
	    } else {
	    	$res = array('code'=>0,'error'=>'没有数据','data'=>'');
	    }
	    echo json_encode($res);
	    exit();
	}
	// 生成图集 ID，已经弃用了
	public function newAlbum(){
		header('Content-Type:application/json; charset=utf-8');
		$data['title'] = $this->_param('title');
		$data['comment'] = $this->_param('comment');
		$data['updatetime'] = time();
		$pai_model = M('pai');
		$key_id = $pai_model->add($data);
		$line_data = $pai_model->where('id='.$key_id)->find();

		$res = array('code'=>200,'error'=>'成功','data'=>$line_data);
		echo json_encode($res);
	}
	// 上传图片，支持多图上传
	public function uploadPicture(){
		header('Content-Type:application/json; charset=utf-8');
		import('ORG.Net.UploadFile');

		$upload = new UploadFile();// 实例化上传类
		// $upload->maxSize  = 3145728 ;// 设置附件上传大小
		//$upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		//设置需要生成缩略图，仅对图像文件有效
		$upload->thumb = true;
		//设置需要生成缩略图的文件后缀
		$upload->thumbPrefix = 'm_,s_';  //生产2张缩略图
		//设置缩略图最大宽度
		$upload->thumbMaxWidth = '100,320';
		//设置缩略图最大高度
		$upload->thumbMaxHeight = '100,240';

		$upload->savePath =  './Uploads/Pai/picture/';// 设置附件上传目录
		$upload->saveRule = $this->pictureNameRule();
		//删除原图
		$upload->thumbRemoveOrigin = true;

		if(!$upload->upload()) {// 上传错误提示错误信息
			$error = $upload->getErrorMsg();
			// dump($error);
			echo json_encode(array('code'=>'0','message'=>$error,'data'=>''));
			exit();
		}else{// 上传成功 获取上传文件信息
			$pai_model = M('pai');

			if ($this->_param('pai_id')) {// 上传图片到指定图集
				$pai_id = $this->_param('pai_id');
			}elseif ($this->_param('title')) {// 新建图集并上传图片
				$data['title'] = $this->_param('title');
				$data['comment'] = $this->_param('comment');
				$data['updatetime'] = time();
				$data['state'] = 0;
				$pai_id = $pai_model->add($data);
			}else{
				$res = array('code'=>0,'error'=>'参数错误','data'=>'');
				echo json_encode($res);
				exit();
			}

			$info =  $upload->getUploadFileInfo();
			$photo = M('photos');
			foreach ($info as $key => $value) {
				$image_size_info = getimagesize ('./Uploads/Pai/picture/'.$value['savename']);
				$data['pai_id'] = $pai_id;
				$data['picurl'] = './Uploads/Pai/picture/'.$value['savename'];
				$data['state'] = 1;
				$data['width'] = $image_size_info[0];
				$data['height'] = $image_size_info[1];
				$photo->add($data);
			}

			$line_data = $pai_model->where('id='.$pai_id)->find();
			$res = array('code'=>200,'error'=>'成功','data'=>$line_data);
			echo json_encode($res);
			exit();
		}
	}

	public function pictureNameRule(){
		return date("YmdHis").rand(100,999);
	}
}

