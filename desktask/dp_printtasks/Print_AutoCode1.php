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
		
    	if(substr($code,0,1)=='0') {	
			$code=substr($code,1);
			
			//if ($codelen==12 || $codelen==11) {
			UPCAbarcode13($code,$lw,$hi);
			return true;
		}
	
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
		/* Add the Human Readable Label */
		//
		$font1="Fonts/ARIALBD.TTF";//定义字体
		$font2="Fonts/OMB___WT.TTF";
		$font3="Fonts/TIMESBD.TTF";
		$font4="Fonts/simhei.ttf";
		//$font5="Fonts/comic.ttf";
		$font6="Fonts/simsun.ttc";
		ImageString($img,$lw+1,0,$hi+3,$code[0],$fg);
		//imagettftext($img,8,0,0,$hi+8,$fg,$font5,$code[0]); 
		for ($x=0;$x<6;$x++) { 
			// int imagestring(int im, int font, int x, int y, string s, int col);
			//ImageString($img,5,$lw*(8+$x*6)+30,$hi+5,$code[$x+1],$fg); 
			ImageString($img,$lw+1,$lw*(8+$x*6)+10,$hi+3,$code[$x+1],$fg); 
			ImageString($img,$lw+1,$lw*(53+$x*6)+10,$hi+3,$code[$x+7],$fg); 
			//imagettftext($img,8,0,$lw*(8+$x*6)+10,$hi+10,$fg,$font5,$code[$x+1]);
			//imagettftext($img,8,0,$lw*(53+$x*6)+10,$hi+10,$fg,$font5,$code[$x+7]);
			}  
			header("Content-Type: image/png"); 
			ImagePNG($img); 
		}

	

function EAN_12_13($code,$lw,$hi) { //$lw =1; //条码宽//$hi = 30; //条码高
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
	$tmpcode=$code; 
	if ( strlen($code)!=13){//条码位数不是13，则条码有错
		die("条码必须是13位!");} 
	$is12=0;
	if(substr($code,0,1)=='0') {	
	    $code=substr($code,1);
		$is12=1;
		
		if (strlen($code) == 12) {  
			// 计算校验位  
			$lsum = 0;  
			$rsum = 0;  
			for($i=1; $i<=strlen($code); $i++) {  
				if($i % 2) {  
					$lsum += (int)$code[$i-1];  
				}else{  
					$rsum += (int)$code[$i-1];  
				}  
			}  
			$tsum = $lsum + $rsum * 3;  
			$chkdig = 10 - ($tsum % 10);  
			if ($chkdig == 10) $chkdig = 0;  
			$code .= $chkdig;  
		}
		
		
	}
	
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
    if($is12==1) {
		for ($x=0;$x<strlen($barcode);$x++) { 
			if(($x<4) || ($x>=45 && $x<50) || ($x >=85)){
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
		
		
	}
	else {
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
	}
	
  	//
	$font1="Fonts/ARIALBD.TTF";//定义字体
    $font2="Fonts/OMB___WT.TTF";
    $font3="Fonts/TIMESBD.TTF";
    $font4="Fonts/simhei.ttf";
    //$font5="Fonts/comic.ttf";
	$font6="Fonts/simsun.ttc";
	$code=$tmpcode;
  	ImageString($img,$lw+1,0,$hi+3,$code[0],$fg);
	
	//imagettftext($img,8,0,0,$hi+8,$fg,$font5,$code[0]); 
  	for ($x=0;$x<6;$x++) { 
 		// int imagestring(int im, int font, int x, int y, string s, int col);
 		//ImageString($img,5,$lw*(8+$x*6)+30,$hi+5,$code[$x+1],$fg); 
   	    ImageString($img,$lw+1,$lw*(8+$x*6)+10,$hi+3,$code[$x+1],$fg); 
   		ImageString($img,$lw+1,$lw*(53+$x*6)+10,$hi+3,$code[$x+7],$fg); 
		//imagettftext($img,8,0,$lw*(8+$x*6)+10,$hi+10,$fg,$font5,$code[$x+1]);
		//imagettftext($img,8,0,$lw*(53+$x*6)+10,$hi+10,$fg,$font5,$code[$x+7]);
  		}  
		header("Content-Type: image/png"); 
	    ImagePNG($img); 
	}
	
  
 //生成12位UPC-A码       
 function UPCAbarcode13($code,$lw,$hi){
   //$lw = 2; $hi = 100;
   $Lencode = array('0001101','0011001','0010011','0111101','0100011','0110001','0101111','0111011','0110111','0001011');
   $Rencode = array('1110010','1100110','1101100','1000010','1011100','1001110','1010000','1000100','1001000','1110100');
   $ends = '101'; $center = '01010';

/* UPC-A Must be 11 digits, we compute the checksum. */
if ( strlen($code) == 12 ) $code=substr($code,0,11);
if ( strlen($code) != 11 ) { die(" UPC-A Must be 11 digits."); }
/* Compute the EAN-13 Checksum digit */
$ncode = '0'.$code;
$even = 0; $odd = 0;
for ($x=0;$x<12;$x++)
{
if ($x % 2) { $odd += $ncode[$x]; } else { $even += $ncode[$x]; }
}

$code.=(10 - (($odd * 3 + $even) % 10)) % 10;

/* Create the bar encoding using a binary string */
$bars=$ends;
$bars.=$Lencode[$code[0]];
for($x=1;$x<6;$x++)
{
$bars.=$Lencode[$code[$x]];
}

$bars.=$center;

for($x=6;$x<12;$x++)
{
$bars.=$Rencode[$code[$x]];
}

$bars.=$ends;

/* Generate the Barcode Image */
$img = ImageCreate($lw*95+30,$hi+30);
$fg = ImageColorAllocate($img, 0, 0, 0);
$bg = ImageColorAllocate($img, 255, 255, 255);
ImageFilledRectangle($img, 0, 0, $lw*95+30, $hi+30, $bg);

$shift=10;
 for ($x=0;$x<strlen($bars);$x++) {
        //if (($x<10) || ($x>=45 && $x<50) || ($x >=85)) { $sh=10; } else { $sh=0; }
		if (($x<4) || ($x>=45 && $x<50) || ($x >=92)) { $sh=10; } else { $sh=0; }
        if ($bars[$x] == '1') { $color = $fg; } else { $color = $bg; }
        ImageFilledRectangle($img, ($x*$lw)+15,5,($x+1)*$lw+14,$hi+5+$sh,$color);
    }
    
  /* Add the Human Readable Label */
    //ImageString($img,4,5,$hi-5,$code[0],$fg);
	ImageString($img,4,5,$hi-5,'0',$fg);
    //for ($x=0;$x<5;$x++) {
	 for ($x=0;$x<6;$x++) {	
        ImageString($img,$lw+1,$lw*(5+$x*6)+15,$hi+5,$code[$x],$fg);
        ImageString($img,$lw+1,$lw*(51+$x*6)+15,$hi+5,$code[$x+6],$fg);
		
    }
    //ImageString($img,4,$lw*95+17,$hi-5,$code[11],$fg);
    /* Output the Header and Content. */
    header("Content-Type: image/png");
    ImagePNG($img);
}
		
EAN_13($Code,$lw,$hi);
//EAN_12_13($Code,$lw,$hi);
?>  
