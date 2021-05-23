<?
//启动页显示

$baseUrl = "http://www.ashcloud.com/iphoneAPI/ManagerApp/";

for ($i=1;$i<3;$i++){
    $ImagePath="launch/image/home_page_$i.png";
    $url = $baseUrl.$ImagePath;
    $img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
             
	$dataArray=array("Name"=>"home_page_$i","Type"=>"png","Align"=>"V","Date"=>"$img_mtime","url"=>"$url");
    $jsonArray[]=$dataArray;
}

for ($i=3;$i<5;$i++){
    $ImagePath="../../download/Video/home_video_$i.mov";
    $img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
	$dataArray=array("Name"=>"home_video_$i","Type"=>"mov","Align"=>"V","Date"=>"$img_mtime");
    $jsonArray[]=$dataArray;
}

for ($i=5;$i<20;$i++){
	
    $ImagePath="launch/image/home_page_$i.png";
    $url = $baseUrl.$ImagePath;
    $img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
	$dataArray=array("Name"=>"home_page_$i","Type"=>"png","Align"=>"V","Date"=>"$img_mtime","url"=>"$url");
    $jsonArray[]=$dataArray;
}
//10.0.10.1/iphoneAPI/ManagerApp/main.php?ModuleId=123|main&ModuleType=&LoginNumber=11965&info=(null)&NextPage=0&AppVersion=3.3.8
if (versionToNumber($AppVersion) > 406) {
	$dataArray=array("Name"=>"home_page_20","Type"=>"html","Align"=>"V","Date"=>"$img_mtime","url"=>"http://www.middlecloud.com/show/ashcloud2016.html");
	$jsonArray[]=$dataArray;
}

if (versionToNumber($AppVersion) > 407) {
	for ($i=21;$i<22;$i++){
		
	    $ImagePath="launch/image/home_page_$i.png";
	    $url = $baseUrl.$ImagePath;
	    $img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
		$dataArray=array("Name"=>"home_page_$i","Type"=>"png","Align"=>"V","Date"=>"$img_mtime","url"=>"$url");
	    $jsonArray[]=$dataArray;
	}
}


?>