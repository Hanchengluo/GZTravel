<?php
/**
 * 后台酒店住宿类
 * @author   <[c@easycmz.cc]>
 */
class WeatherAction extends CommonAction{
	
	public function index($city) {
		header('Content-Type:application/json; charset=utf-8');
		Log::write(get_client_ip(0).',	请求城市city:'.$city,'当前请求IP:');
		$cityname=urlencode($city);// 这个作为变量传入函数吧
		$json_url="http://api.map.baidu.com/telematics/v3/weather?location=". $cityname ."&output=json&ak=qE8QVFXpHgumM2YyiQG7G1A4&qq-pf-to=pcqq.c2c";
		$data=fopen($json_url,"rb");
		$json_contents="";
		while (!feof($data))
		{
			$json_contents .=fread($data,10000);
		}
		fclose($data);
		$contents_json = json_decode($json_contents,true);

		$result=array();
		if ($contents_json["status"] == "success")
		{
			$result["city"]=$contents_json["results"][0]["currentCity"];
			$result["date"]=$contents_json["date"];
			$temp=$contents_json["results"][0];
			$result["dateString"]=$temp["weather_data"][0]["date"];
			$result["dayPictureUrl"]=$temp["weather_data"][0]["dayPictureUrl"];
			$result["nightPictureUrl"]=$temp["weather_data"][0]["nightPictureUrl"];
			$result["weather"]=$temp["weather_data"][0]["weather"];
			$result["wind"]=$temp["weather_data"][0]["wind"];
			$temperature = $temp["weather_data"][0]["temperature"];
			$result["_temperature"]= $temperature;
			$result["temperature"]= substr($temperature, 0, -3);
		}
		if ($result) {
			$res = array('code'=>200,'error'=>'','data'=>$result);
		}else{
			$res = array('code'=>0,'error'=>'无法获取天气信息','data'=>'');
		}
		echo json_encode($res);
		exit;
	}


}
