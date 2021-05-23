<?php   
//流程图函数

//颜色#转换成RGB
function hexToRGB($hexColor) {
		$color = str_replace('#', '', $hexColor);
		if (strlen($color) > 3) {
				$rgb = array(
				'r' => hexdec(substr($color, 0, 2)),
				'g' => hexdec(substr($color, 2, 2)),
				'b' => hexdec(substr($color, 4, 2))
				);
		} 
		else {
				$color = str_replace('#', '', $hexColor);
				$r = substr($color, 0, 1) . substr($color, 0, 1);
				$g = substr($color, 1, 1) . substr($color, 1, 1);
				$b = substr($color, 2, 1) . substr($color, 2, 1);
				$rgb = array(
						'r' => hexdec($r),
						'g' => hexdec($g),
						'b' => hexdec($b)
				);
      }
      return $rgb;
}

//取得使用 TrueType 字体的文本的范围 
function getFontSize($text,$font,$fontsize){
	 $textsize=imagettfbbox($fontsize,0,$font,$text); 
     $text_w=$textsize[2]-$textsize[6]; 
     $text_h=$textsize[3]-$textsize[7];
     $size=array('w'=>$text_w,'h'=>$text_h);
     return $size;
}

//文本
function drawWithText(&$im,$x,$y,$text,$font,$fontsize,$textcolor)
{
     $textcolor=strlen($textcolor)==7?$textcolor:"#000000";
     $textRGB=hexToRGB($textcolor);
     $im->setFontProperties($font,$fontsize);
     
     $im->drawTitle($x,$y,$text,$textRGB['r'],$textRGB['g'],$textRGB['b'],-1,-1,false);
}
//画线
function drawLineArrow(&$im,$x,$y,$len,$linecolor,$style='h',$width=1,$dotsize=0,$arrow=false)
{
     $im->setLineStyle($width,$dotsize);
     $linecolor=strlen($linecolor)==7?$linecolor:"#000000";
     $lineRGB=hexToRGB($linecolor);
     if ($style=='v'){
	    $x2=$x;
	    $y2=$y+$len;   
     }
     else{
        $x2=$x+$len;
	    $y2=$y;   
     }
     $im->drawLine($x,$y,$x2,$y2,$lineRGB['r'],$lineRGB['g'],$lineRGB['b']);  
     if ($arrow){
         $offsetY=($width-1)/2;
         $arrowX=array($x2-6,$y2-3-$offsetY,$x2,$y2,$x2-6,$y2+3);
         $red =$im->AllocateColor($im->Picture,$lineRGB['r'],$lineRGB['g'],$lineRGB['b']);
	     imagefilledpolygon($im->Picture,$arrowX,3,$red);
     }  
}
//画矩形
function RoundedRectangle(&$im,$x,$y,$text,$font,$fontsize,$textcolor,$bgcolor,$linecolor,$radius=3)
{
    // $radius=3;//圆角半径
    $im->setLineStyle(1,0);
     $offset=8;//位移
      if (strlen($text)==0) return array('x'=>$x,'y'=>$y);
      
     $textsize=getFontSize($text,$font,$fontsize); //取得使用 TrueType 字体的文本的范围 
     $x2=$x+$textsize['w']+$offset;
     $y2=$y+$textsize['h']+$offset;
          
     if (strlen($bgcolor)==7){
	     $bgRGB=hexToRGB($bgcolor);
	     if ($radius<=0){
		      $im->drawFilledRectangle($x,$y,$x2,$y2,$bgRGB['r'],$bgRGB['g'],$bgRGB['b']); 
	     }
	     else{
	        $im->drawFilledRoundedRectangle($x,$y,$x2,$y2,$radius,$bgRGB['r'],$bgRGB['g'],$bgRGB['b']); 
	     }
     }
     if (strlen($linecolor)==7){ 
	     $lineRGB=hexToRGB($linecolor);
	      if ($radius<=0){
	           $im->drawRectangle($x,$y,$x2,$y2,$lineRGB['r'],$lineRGB['g'],$lineRGB['b']); 
	      }
	      else{
		       $im->drawRoundedRectangle($x,$y,$x2,$y2,$radius,$lineRGB['r'],$lineRGB['g'],$lineRGB['b']); 
	      }
     }
     
     $textcolor=strlen($textcolor)==7?$textcolor:"#000000";
     $textRGB=hexToRGB($textcolor);
     $im->setFontProperties($font,$fontsize);
     
     $im->drawTitle($x+$offset/2,$y+ceil($textsize['h']+$offset/4),$text,$textRGB['r'],$textRGB['g'],$textRGB['b'],-1,-1,false);
     
     return array('x'=>$x2,'y'=>$y2-ceil(($textsize['h']+$offset)/2));
}
?>