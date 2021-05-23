<?php 
//define("ANGLE_STEP", 3); //定义画椭圆弧时的角度步长
//define("starX", 80); //在图的X位置
//define("starY", 40); //在图的Y位置
//global $font;
//global $Fontcolor;

define("FONT_USED", "c:/windows/fonts/simhei.ttf"); // 使用到的字体文件位置
function draw_getdarkcolor($img,$clr) //求$clr对应的暗色
{
	$rgb = imagecolorsforindex($img,$clr);
	return array($rgb["red"]/2,$rgb["green"]/2,$rgb["blue"]/2);
}
function draw_getexy($a, $b, $d) //求角度$d对应的椭圆上的点坐标
{
	$d = deg2rad($d);
	return array(round($a*Cos($d)), round($b*Sin($d)));
}
function draw_arc($img,$ox,$oy,$a,$b,$sd,$ed,$clr,$ANGLE_STEP) //椭圆弧函数
{
	$n = ceil(($ed-$sd)/$ANGLE_STEP);
	$d = $sd;
	list($x0,$y0) = draw_getexy($a,$b,$d);
	for($i=0; $i<$n; $i++)
	{
		$d = ($d+$ANGLE_STEP)>$ed?$ed:($d+$ANGLE_STEP);
		list($x, $y) = draw_getexy($a, $b, $d);
		imageline($img, $x0+$ox, $y0+$oy, $x+$ox, $y+$oy, $clr);
		$x0 = $x;
		$y0 = $y;
	}
}
function draw_sector($img, $ox, $oy, $a, $b, $sd, $ed, $clr,$ANGLE_STEP,$Fontcolor,$strvalue) //画扇面(img,x=0+200,y=0+90,长半轴=200,短半轴90,角度起点0,角度终点, 色彩)
{
	$n = ceil(($ed-$sd)/$ANGLE_STEP);
	$d = $sd;  //起点
	list($x0,$y0) = draw_getexy($a, $b, $d);  //求角度$d对应的椭圆上的点坐标	
	imageline($img, $x0+$ox, $y0+$oy, $ox, $oy, $clr);  //起如线直线，从圈外事到圆心
	for($i=0; $i<$n; $i++)
	{
		$d = ($d+$ANGLE_STEP)>$ed?$ed:($d+$ANGLE_STEP);
		list($x, $y) = draw_getexy($a, $b, $d);
		imageline($img, $x0+$ox, $y0+$oy, $x+$ox, $y+$oy, $clr);  //不断用一段段直线外圈线，形成圆的外圈线
		$x0 = $x;
		$y0 = $y;
	}
	imageline($img, $x0+$ox, $y0+$oy, $ox, $oy, $clr);  //终点线，从圆心到圈外
	list($x, $y) = draw_getexy($a/2, $b/2, ($d+$sd)/2);
	imagefill($img, $x+$ox, $y+$oy, $clr);  //对所封闭的区域进行填充色彩.也是
	//imagettftext($img, 10,-($sd+($d-$sd)/2), $x+$ox, $y+$oy, $Fontcolor, FONT_USED, "$sd+($d-$sd)/2");  //在饼图上输出数字
	//imagettftext($img, 12,-($sd+($d-$sd)/2), $ox, $oy, $Fontcolor, FONT_USED, $strvalue);  //在饼图上输出数字
    /*
	list($xT,$yT) = draw_getexy(60, 60*($b/$a), ($d+$sd)/2);  //求文字所放的位置，小椭圆的位置,用于存放文字
	$result=array();
    //$result[]=-$sd+($d-$sd)/2;
	$result[]=-$sd;
	$result[]=$xT+$ox;
	$result[]=$yT+$oy;
    */
	
	$result=array();
	$TextAngle=($sd+($d-$sd)/2);  //修正文字的角度	
	if($TextAngle>270){
	   $result[]=-($sd+($d-$sd)/2);
	   //$result[]=-$sd;
		$result[]=$x/1.5+$ox;
		$result[]=$y/(1.5*$a/$b)+$oy;   //font的高度更好
	   
	}
	else{
	    $result[]=-($sd+($d-$sd)/2);
		//$result[]=-$sd;
		$result[]=$x/1.5+$ox;
		$result[]=$y/1.5+$oy;
		 
	}
	//imagettftext($img, 10,$result[0], $result[1], $result[2], $Fontcolor, FONT_USED,$strvalue."　　　　");  //在饼图上输出数字
	return $result;
	
}
function draw_sector3d($img, $ox, $oy, $a, $b, $v, $sd, $ed, $clr,$ANGLE_STEP,$Fontcolor,$strvalue) //3d扇面
{
	$result=draw_sector($img, $ox, $oy, $a, $b, $sd, $ed, $clr,$ANGLE_STEP,$Fontcolor,$strvalue);
	if($sd<180)  //要填充下面的三维的效果
	{
		list($R, $G, $B) = draw_getdarkcolor($img, $clr);
		$clr=imagecolorallocate($img, $R, $G, $B);
		if($ed>180) $ed = 180;
		list($sx, $sy) = draw_getexy($a,$b,$sd);
		$sx += $ox;
		$sy += $oy;
		list($ex, $ey) = draw_getexy($a, $b, $ed);
		$ex += $ox;
		$ey += $oy;
		imageline($img, $sx, $sy, $sx, $sy+$v, $clr);
		imageline($img, $ex, $ey, $ex, $ey+$v, $clr);
		draw_arc($img, $ox, $oy+$v, $a, $b, $sd, $ed, $clr,$ANGLE_STEP);
		list($sx, $sy) = draw_getexy($a, $b, ($sd+$ed)/2);
		$sy += $oy+$v/2;
		$sx += $ox;
		imagefill($img, $sx, $sy, $clr);
	}
	
	//有个bug在这，上面要填充下面的三维的效果填充时，会冲突，有这个文字输出，某一块上面的填充会失败，原因，不明！！！,可能填充封闭性好，刚好破坏了它的封闭性条件，就出问题了
	//imagettftext($img, 8,$result[0], $result[1], $result[2], $Fontcolor, FONT_USED,$strvalue);  //在饼图上输出数字
	//imagettftext($img, 10,$result[0], $ox,$oy, $Fontcolor, FONT_USED, "　　　".$strvalue);  //在饼图上输出数字

}
function draw_getindexcolor($img, $clr) //RBG转索引色
{
	$R = ($clr>>16) & 0xff;
	$G = ($clr>>8)& 0xff;
	$B = ($clr) & 0xff;
	return imagecolorallocate($img, $R, $G, $B);
}
// 绘图主函数，并输出图片
// $datLst 为数据数组, $datLst 为标签数组, $datLst 为颜色数组
// 以上三个数组的维数应该相等
//$a=150;//椭圆长半轴
//$b=50;//椭圆段半轴
//$v=20;//圆饼高度
//$font=5;//字体


function draw_img($image,$HeadTitle,$datLst,$labLst,$clrLsts,$ox,$oy,$a=320,$b=140,$v=16,$ANGLE_STEP=2,$font=12)
{
	
	$starL=0;//40;
	$starR=0;//80;
	$starT=0;//;
	$starB=0;//80;
    
	if($image=="") { //表示要创建一个以x轴为$a,Y轴为$b，计算宽度及高度的图片,
		$ox = $starL+$a;  //5+$a;
		$oy = $starT+$b;  //5+$b;
		$fw = imagefontwidth($font);
		$fh = imagefontheight($font);
		$n = count($datLst);//数据项个数,如果单独显示文字色彩对应的，则要$n
		$w = $starL+10+$a*2+$starR;  //图片的宽度
		$h = $starT+10+$b*2+$v+($fh+2)*$n+$starB;  //图片的高度
		$img = imagecreate($w, $h);	
		$ly = 10+$b*2+$v;  //文字起始位置
	}
	else{  //否则，以
		$fw = imagefontwidth($font);
		$fh = imagefontheight($font);
		//$Tfh= imagefontheight($font);  //标题是1.5倍
	    $n = count($datLst);
		$img=$image;
		//$fw=$ox/2;
		$ly = $oy+$b+$fh;
		/*
		if($HeadTitle!=""){
			$ly = $ly+2*$fh;
		}  //加上标题
		*/
	}
	$Fontcolor=imagecolorallocate($img,0,0,0);  //字体色彩
	//转RGB为索引色
	for($i=0; $i<$n; $i++)
	$clrLst[$i] = draw_getindexcolor($img,$clrLsts[$i]);
	$clrbk = imagecolorallocate($img, 0xff, 0xff, 0xff);
	$clrt = imagecolorallocate($img, 0x00, 0x00, 0x00);
	//填充背景色
	//imagefill($img, 0, 0, $clrbk);
	//输出标题
	if($HeadTitle!=""){   //输出标题
	    $TFontSize=ceil(1.5*$font);//是1.5倍大小
		$TfH =imagefontwidth($TFontSize);
		$fontareaHeadTitle = ImageTTFBBox($TFontSize,0,FONT_USED,$HeadTitle);  //获取文字的宽度
		$HeadTitle_width = $fontareaHeadTitle[2]-$fontareaHeadTitle[0];//256
		imagettftext($img, $TFontSize,0,$ox-$HeadTitle_width/2, $ly+3*$TfH,$clrt,FONT_USED,$HeadTitle);
		$ly = $ly+3*$TfH;  
		}
	
	//求和
	$tot = 0;
	for($i=0; $i<$n; $i++)
		$tot += $datLst[$i];  //求总数
	$sd = 0;
	$ed = 0;
	//$ly = 10+$b*2+$v;

	for($i=0; $i<$n; $i++)
	{
		$sd = $ed;
		$ed += $datLst[$i]/$tot*360;
		//画圆饼
		$perValue=round(10000*($datLst[$i]/$tot))/100;
		$strvalue=$labLst[$i].":".$datLst[$i]."(".$perValue."%)";
		$Tstrvalue=$labLst[$i].":"."(".$perValue."%)";
		if($perValue<1){
			draw_sector3d($img, $ox, $oy, $a, $b, $v, $sd, $ed, $clrLst[$i],$ANGLE_STEP,$Fontcolor,""); //$sd,$ed,$clrLst[$i]);
		}
		else{
			draw_sector3d($img, $ox, $oy, $a, $b, $v, $sd, $ed, $clrLst[$i],$ANGLE_STEP,$Fontcolor,$Tstrvalue); //$sd,$ed,$clrLst[$i]);
		}
		
		//$colorBorder=imagecolorallocate($img,0,0,0);  //字体色彩
		//imagestring($img,$font,floor($ox+$sd ),floor($oy+$sd),$labLst[$i]."%",$colorBorder);
		//画标签
		imagefilledrectangle($img, $ox-$a+5, $ly+16, $ox-$a+5+$fw, $ly+$fh+16, $clrLst[$i]);  //色彩
		imagerectangle($img, $ox-$a+5, $ly+16, $ox-$a+5+$fw, $ly+$fh+16, $clrt);   //色彩外框
		//imagestring($img, $font, $ox-$a+5+2*$fw, $ly, $labLst[$i].":".$datLst[$i]."(".(round(10000*($datLst[$i]/$tot))/100)."%)", $clrt);
		imagettftext($img, $font,0, $ox-$a+5+2*$fw, $ly+29,$clrt,FONT_USED,  $labLst[$i].":".$datLst[$i]."(".(round(10000*($datLst[$i]/$tot))/100)."%)");
				
		//imagettftext($img, $font, 0, 5+2*$fw, $ly+13+$starT, $clrt, FONT_USED, $strvalue);  //输出数字

		$ly += $fh+2;
	}
	//输出图形
	if($image=="") { 
		header("Content-type: image/png");
		//输出生成的图片
		imagepng($img);
	}
}

// 以上生成图片,可以放在一个文件内

?>