<?
//公司简介
$ImagePath="../../download/Video/video_7.mov";
$img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
$jsonArray[]=array("Name"=>"video_7","Type"=>"mov","Align"=>"V","Date"=>"$img_mtime");

$ImagePath="../../download/Video/video_5.mov";
$img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
$jsonArray[]=array("Name"=>"video_5","Type"=>"mov","Align"=>"V","Date"=>"$img_mtime");

for ($i=1;$i<9;$i++){
    $ImagePath="about/image/info_$i.png";
    $img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
    $jsonArray[]=array("Name"=>"info_$i","Type"=>"png","Align"=>"V","Date"=>"$img_mtime","Top"=>"-30");
}

$ImagePath="about/image/info_9.png";
$img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
$jsonArray[]=array("Name"=>"info_9","Type"=>"png","Align"=>"H","Date"=>"$img_mtime","Top"=>"0");

$ImagePath="about/image/info_10.png";
$img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
$jsonArray[]=array("Name"=>"info_10","Type"=>"png","Align"=>"H1","Date"=>"$img_mtime","Top"=>"0");    

$ImagePath="about/image/info_11.png";
$img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
$jsonArray[]=array("Name"=>"info_11","Type"=>"png","Align"=>"H1","Date"=>"$img_mtime","Top"=>"0");  

?>