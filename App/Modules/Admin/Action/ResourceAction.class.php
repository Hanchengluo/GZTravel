<?php
/**
 * 后台首页环境配置显示
 * @author  <[c@easycms.cc]>
 */
class ResourceAction extends CommonAction {
	public function index(){
		//导入类库
		import('ORG.Util.PclZip');
        $model = M('archive');
        $data = $model->where('type=0')->order('archive_time desc')->find();
        if ($data) {
            $archive_version = $data['archive_version'] + 1;
        }else{
            $archive_version = 1;
        }
        $file_name = '/resource_'.time().'_'.$archive_version.'.zip';

        $this->createDir('./archive');
		//生成的压缩包位置
		$zipurl = './archive'.$file_name;
		//实例化类库
		$zip = new PclZip($zipurl);
		//打包具体的目录先文件后文件夹
		$res = $zip->create('./gztravel.db,Uploads');
		// $this->moveFile('/resource.zip');
		$url = $this->returnURL();
		if ($res[0]['status'] == 'ok') {
			$new_archive['resource_path'] = './archive'.$file_name;
            $new_archive['archive_version'] = $archive_version;
            $new_archive['type'] = 0;
            $new_archive['archive_time'] = time();
            if ($model->add($new_archive)) {
                echo "资源包打包成功,URL:<a href=".$url.$file_name.">".$url.$file_name."</a>";
            }else{
                echo "资源包更新失败";
            }
		}else{
			echo "资源包打包失败，请重新打包";
		}
		//上面的意思是具体打包 Public/Config,Admin,User,Web,Core,文件夹 index.php,admin.php,user.php文件
		//ok，执行这个php脚本，你将会在根目录下看到名为data.zip的压缩包。
		//------下面是解压缩操作示范----
		//将刚才打包的内容压缩到data目录下
		//$zip->extract(PCLZIP_OPT_PATH,'./');
    }

    /**
     * 建立文件夹
     *
     * @param string $aimUrl
     * @return viod
     */
    function createDir($aimUrl) {
        $aimUrl = str_replace('', '/', $aimUrl);
        $aimDir = '';
        $arr = explode('/', $aimUrl);
        $result = true;
        foreach ($arr as $str) {
            $aimDir .= $str . '/';
            if (!file_exists($aimDir)) {
                $result = mkdir($aimDir);
            }
        }
        return $result;
    }

    /**
     * 移动文件
     *
     * @param string $fileUrl
     * @param string $aimUrl
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
    function moveFile($fileUrl, $aimUrl, $overWrite = false) {
        if (!file_exists($fileUrl)) {
            return false;
        }
        if (file_exists($aimUrl) && $overWrite = false) {
            return false;
        } elseif (file_exists($aimUrl) && $overWrite = true) {
            $this->unlinkFile($aimUrl);
        }
        $aimDir = dirname($aimUrl);
        $this->createDir($aimDir);
        rename($fileUrl, $aimUrl);
        return true;
    }
	/**
     * 删除文件夹
     *
     * @param string $aimDir
     * @return boolean
     */
    function unlinkDir($aimDir) {
        $aimDir = str_replace('', '/', $aimDir);
        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
        if (!is_dir($aimDir)) {
            return false;
        }
        $dirHandle = opendir($aimDir);
        while (false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!is_dir($aimDir . $file)) {
                $this->unlinkFile($aimDir . $file);
            } else {
                $this->unlinkDir($aimDir . $file);
            }
        }
        closedir($dirHandle);
        return rmdir($aimDir);
    }
    /**
     * 删除文件
     *
     * @param string $aimUrl
     * @return boolean
     */
    function unlinkFile($aimUrl) {
        if (file_exists($aimUrl)) {
            unlink($aimUrl);
            return true;
        } else {
            return false;
        }
    }

}
