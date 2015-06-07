<?php
/**
 * 后台新闻类
 * @author   <[c@easycms.cc]>
 */
class TestAction extends CommonAction{
	/**
	* 说明：ThinkPHP文件上传测试函数
	* 作者：攀爬的蜗牛
	* 时间：2012-03-29
	* 版本：1.0
	* 网址：http://www.dutycode.com
	*/
	public function addChk(){
		    //导入图片上传类
            import("ORG.Net.UploadFile");
            //实例化上传类
            $upload = new UploadFile();
            $upload->maxSize = 3145728;
			//设置文件上传类型
            $upload->allowExts = array('jpg','gif','png','jpeg');
            //设置文件上传位置
            $upload->savePath = "./Uploads/Test/";//这里说明一下，由于ThinkPHP是有入口文件的，所以这里的./Public是指网站根目录下的Public文件夹
            //设置文件上传名(按照时间)
            $upload->saveRule = "time";
            if (!$upload->upload()){
                $this->error($upload->getErrorMsg());
            }else{
                //上传成功，获取上传信息
                $info = $upload->getUploadFileInfo();
                $this->success(L('新增成功'));
            }
}
}
