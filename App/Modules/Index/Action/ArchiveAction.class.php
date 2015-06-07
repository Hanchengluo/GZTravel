<?php 
/**
* 资源打包
* @author  <[s@easycms.cc]>
*/
class ArchiveAction extends CommonAction
{
	public function archiveList(){
		header('Content-Type:application/json; charset=utf-8');
		$archive = M('archive');
		$data['isonline'] = 1;
		$archive_list = $archive->where($data)->select();

		if (count($archive_list)>0) {
			for ($i=0; $i < count($archive_list); $i++) { 
				$archive_list[$i]['resource_path'] = $this->returnURLName().substr($archive_list[$i]['resource_path'], 1);
			}
			$res = array('code'=>200,'error'=>'成功','data'=>$archive_list);
		}else {
			$res = array('code' =>0 ,'error' => '没有数据', 'data'=>'');
		}
		echo json_encode($res);
	    exit();
	}
}