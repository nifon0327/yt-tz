<?php   
function EAN_13($code,$lw,$hi) { //$lw =1; //条码宽//$hi = 30; //条码高
  	//左资料码编码规则：根据国家不同有所不同
  	//									3法国	4 德国		5英国   6中国
  	$Guide = array(1=>'AAAAAA','AABBAB','AABBBA','ABAABB','ABBAAB','ABBBAA','ABABAB','ABABBA','ABBABA'); 
  	$Lstart ='101';//左边线 
  	//左侧编码格式，有两种码
 	 $Lencode = array("A" => array('0001101','0011001','0010011','0111101','0100011','0110001','0101111','0111011','0110111','0001011'), 
                   "B" => array('0100111','0110011','0011011','0100001','0011101','0111001','0000101','0010001','0001001','0010111')); 
  	//右边编码格式
 	$Rencode = array('1110010','1100110','1101100','1000010','1011100','1001110','1010000','1000100','1001000','1110100');
	$center = '01010';//中线    
	$ends = '101'; //右边线
	if ( strlen($code)!=13){//条码位数不是13，则条码有错
		die("条码必须是13位!");} 
	$lsum =0; 
	$rsum =0; 
  	for($i=0;$i<(strlen($code)-1);$i++){ 
    	if($i % 2){
			$lsum +=(int)$code[$i];}
		else{
			$rsum +=(int)$code[$i];} 
   		} 
	$tsum = $lsum*3 + $rsum; 
  	$barcode = $Lstart; 
	for($i=1;$i<=6;$i++){ 
		$barcode .= $Lencode [$Guide[$code[0]][($i-1)]] [$code[$i]];
		} 
	$barcode .= $center; 
	for($i=7;$i<13;$i++){
		$barcode .= $Rencode[$code[($i)]];
		} 
	$barcode .= $ends; 
	$img = ImageCreate($lw*95+20,$hi+20); //输出x*y的空白图像ImageCreate($lw*95+60,$hi+30)
	$fg = ImageColorAllocate($img,0,0,0); //给空白图象填色
	$bg = ImageColorAllocate($img, 255, 255, 255); //给空白图象填色
	//int imagefilledrectangle ( resource image, int x1, int y1, int x2, int y2, int color ). 
	ImageFilledRectangle($img, 0, 0, $lw*95+40, $hi+20, $bg); 
	$shift=10; 
	for ($x=0;$x<strlen($barcode);$x++) { 
		if(($x<4) || ($x>=45 && $x<50) || ($x >=92)){
			$sh=10;} 
		else{
			$sh=0; 
			} 
		if ($barcode[$x]=='1'){  
			$color = $fg;} 
		else{  
			$color = $bg;  
			} 
   		ImageFilledRectangle($img, ($x*$lw)+10,0,($x+1)*$lw+9,$hi+0+$sh,$color); 
		}
	/*
	$fontfile = 'c:/windows/fonts/Arial.ttf';
	imagettftext($img, 10, 0, 2, $hi+8, $fg, $fontfile,$code[0]);//图像对象，字体大小，字体角度，X，Y，字体颜色，字体，字符
  	for ($x=0;$x<6;$x++) { 
		imagettftext($img, 10, 0, 13+$x*7, $hi+14, $fg, $fontfile,$code[$x+1]);//图像对象，字体大小，字体角度，X，Y，字体颜色，字体，字符
		imagettftext($img, 10, 0, 60+$x*7, $hi+14, $fg, $fontfile,$code[$x+7]);//图像对象，字体大小，字体角度，X，Y，字体颜色，字体，字符
  		}  
	*/
	ImagePNG($img); 
	}

EAN_13($Code,$lw,$hi);
?>  
