<?php   
//传入参数：$StuffIdList，$UniteIdList ,$ProductId 
Header("Content-type: image/png"); //输出一个PNG 图片文件
include "../../basic/parameter.inc";

include  "../../pChart/pChart/pChart.class";

$FontName="../../pChart/Fonts/simfang.ttf";
$FontSize=10;

//$BillNumber='201503189';
$SignResult=mysql_query("SELECT Signature FROM $DataIn.ck12_thsignature  WHERE BillNumber='$BillNumber'",$link_id);

$im_w=640;$im_h=255;
$im = new pchart($im_w,$im_h);
//$im->drawGraphAreaGradient(240,240,240,2,TARGET_BACKGROUND); //绘制背景颜色
$im->setLineStyle(3,0);

$scale=400.0/255.0;


if($SignRow=mysql_fetch_array($SignResult)){
    $Sign=$SignRow["Signature"];
    $allPoint=preg_replace('/[{}]/', '',$Sign);
    $allPoints=explode(',',preg_replace('/[?]/', ',',$allPoint));

    $maxValue= max($allPoints);
    $scale=$maxValue>960?600.0/255.0:400.0/255.0;
    
    $SignArray=explode("?", $Sign);
    $counts=count($SignArray);
    if ($counts>1){
        $prePoint=array();
        $Point=array();
        $prePoint=explode(',',preg_replace('/[{}]/', '', $SignArray[0]));
        $direction = ($prePoint[1]/$scale>255)?1:0;
	    for($i=1;$i<$counts;$i++){
	       $Point=explode(',',preg_replace('/[{}]/', '', $SignArray[$i]));
	       if ($direction==1){
		      $im->drawLine($prePoint[1]/$scale,$prePoint[0]/$scale,$Point[1]/$scale,$Point[0]/$scale,0,0,0);   
	       }
	       else{
		     $im->drawLine($prePoint[0]/$scale,$prePoint[1]/$scale,$Point[0]/$scale,$Point[1]/$scale,0,0,0);  
	       }
	        
	       $prePoint=$Point;
	    }
    }
}

$im->AntialiasQuality = 0;
$outFileName="../../download/th_signature/" . $BillNumber . ".png";
$im->Render($outFileName);
$im->Stroke();

?>