<?php   
     /**
     * 通过Mysql创建SQlite
     * $db 数据库
     * $tbname 表名
     * return SQL的数组形式
     * @author 沈振龙
     */

class MysqltoSqliteAction extends CommonAction
{  

    public function index(){

    	//游的景点，景区列表
    	$dsn = "mysql:host=".C('DB_HOST').":".C('DB_PORT').";dbname=".C('DB_NAME');
        $db = new PDO($dsn, C('DB_USER'), C('DB_PWD')); 
        $db->query('set names utf8');
        $runsql = $db->query("select id from areaservice WHERE name = '景点'");
        $res = $runsql->fetch();
    	$tbname = 'jingdian';
    	$sql = "SELECT tb1.*, tb2.name 'jq_name' FROM `jingdian` 
    	tb1 LEFT JOIN  `jingqu` tb2 ON tb1.jq_id = tb2.id  WHERE tb1.islock = 0";

    	$res = $this->toSqlite1($sql, $tbname, $res['id']);

        $runsql = $db->query("select id from areaservice WHERE name = '景区'");
        $res = $runsql->fetch();
    	$tbname = 'jingqu';
    	$sql = "SELECT * FROM `jingqu` WHERE islock = 0";
    	$res = $this->toSqlite1($sql, $tbname, $res['id']);

    	//吃的吃地，美食列表
        $runsql = $db->query("select id from areaservice WHERE name = '美食'");
        $res = $runsql->fetch();
    	$tbname = 'meishi';

    	$sql = "SELECT tb1.*, tb2.name 'chi_name' FROM `meishi` 
    	tb1 LEFT JOIN  `chi` tb2 ON tb1.chi_id = tb2.id WHERE tb1.islock = 0";
    	$res = $this->toSqlite1($sql, $tbname, $res['id']);

    	$tbname = 'chi';
    	$sql = "SELECT tb1.*, tb2.name 'as_name' FROM `chi` 
    	tb1 LEFT JOIN  `areaservice` tb2 ON tb1.as_id = tb2.id WHERE tb1.islock = 0";
    	$res = $this->toSqlite($sql, $tbname);

    	//住列表
    	$tbname = 'zhu';
    	$sql = "SELECT tb1.*, tb2.name 'as_name' FROM `zhu` 
    	tb1 LEFT JOIN  `areaservice` tb2 ON tb1.as_id = tb2.id WHERE tb1.islock = 0";
    	$res = $this->toSqlite($sql, $tbname);

    	//行程
        $runsql = $db->query("select id from areaservice WHERE name = '行'");
        $res = $runsql->fetch();
    	$tbname = 'xing';
    	$sql = "SELECT tb1.*, tb2.name 'xing_name' FROM `xing` 
    	tb1 LEFT JOIN  `xingtype` tb2 ON tb1.xt_id = tb2.id WHERE tb1.islock = 0";
    	$res = $this->toSqlite1($sql, $tbname, $res['id']);
    	
        //首页
        $runsql = $db->query("select id from areaservice WHERE name = '首页'");
        $res = $runsql->fetch();
        $tbname = 'banner';
        $sql = "SELECT * FROM `banner` WHERE islock = 0";
        $res = $this->toSqlite1($sql, $tbname, $res['id']);

    	//商店列表，特产列表
        $runsql = $db->query("select id from areaservice WHERE name = '特产'");
        $res = $runsql->fetch();
    	$tbname = 'techan';
    	$sql = "SELECT tb1.*, tb2.name 'shop_name' FROM `techan` 
    	tb1 LEFT JOIN  `shop` tb2 ON tb1.shop_id = tb2.id WHERE tb1.islock = 0";
    	$res = $this->toSqlite1($sql, $tbname, $res['id']);
    	
        $runsql = $db->query("select id from areaservice WHERE name = '商店'");
        $res = $runsql->fetch();
    	$tbname = 'shop';
    	$sql = "SELECT tb1.*, tb2.name 'jq_name' FROM `shop` 
    	tb1 LEFT JOIN  `jingqu` tb2 ON tb1.jq_id = tb2.id WHERE tb1.islock = 0";
    	$res = $this->toSqlite1($sql, $tbname, $res['id']);

    	//图片列表
    	$tbname = 'picture';
    	$sql = "SELECT * FROM `picture` WHERE islock = 0";
    	$res = $this->toSqlite($sql, $tbname);

    	//服务列表
    	$tbname = 'zuobiao';
    	$sql = "SELECT tb1.*, tb2.name 'as_name' FROM `zuobiao` 
    	tb1 INNER JOIN  `areaservice` tb2 ON tb1.as_id = tb2.id 
    	WHERE tb1.islock = 0 AND tb2.level = 3";
    	$res = $this->toSqlite($sql, $tbname);
		if($res == 1 || $res == null){
			echo "资源包数据库更新成功";
		}else{
			echo "资源包数据库更新失败";
		}

    }

}
?>