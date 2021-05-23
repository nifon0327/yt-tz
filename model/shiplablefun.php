<?php 
function ToLable($PreWord,$LableID,$LableSUM,$i,$Udate,$BoxTotal,$InvoiceNO,$OrderPO,$Description,$WG,$BoxPcs,$eCode,$BoxSpec,$BoxCode,$PackingUnit,$PackingRemark,$StartPlace,$EndPlace,$LabelModel,$cName,$NG='',$FromCounty='',$InBoxPcs='',$ProductId=''){
	//初始化
	//echo "FromCounty1:$FromCounty <br>";
	//echo $LabelModel;
	$TrackingNO="&nbsp";
	//$NG=sprintf("%.2f",$WG-1);
	$SpecStr=substr($BoxSpec,0,-2);$Spec=explode("*",$SpecStr);	
	//echo "$BoxCode <br>";
	$codeClass="codebig";
	switch($LabelModel){
	  case 6:
            
            if($BoxCode!=""){
				$Field=explode("|",$BoxCode);
              $BoxCode0=$Field[0];
              $BoxCode1=$Field[1];
             
			$FieldCount=count($Field); 
			$barcode39="";
			if($FieldCount>=3){
				$barcode39=$Field[2];  
                //echo "code39:$barcode39";
			}
			$barcode14="";
			if($FieldCount>=4){
				$barcode14=$Field[3]; 
			}
			
				if(is_numeric($BoxCode0)){	
					$BoxCode1=preg_replace("/,/","<br>",$BoxCode1);
					$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='bottom'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling='no' width='160' height='80'  src='../model/ean_13code.php?Code=$BoxCode0&lw=1.5&hi=60'></iframe></td></tr><tr><td height='32' valign='top' scope='col'><div align='center' class='code_title'>$BoxCode1</div></td></tr></table>";
					}
				else{
					$BoxCode0=preg_replace("/,/","<br>",$BoxCode0);
					if(is_numeric($BoxCode1)){
										if (strlen($BoxCode0)>20) $codestyle="code_title0"; else $codestyle="code_title";
						$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td height='32' valign='bottom' scope='col'><div align='center' class='$codestyle'>$BoxCode0</div></td></tr><tr><td align='center' valign='top'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling='no' width='160' height='80'  src='../model/ean_13code.php?Code=$BoxCode1&lw=1.5&hi=60'></iframe></td></tr></table>";
					  }
					 else{
						$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td height='32' valign='bottom' scope='col'></td></tr><tr><td align='center' valign='top'></td></tr></table>"; 
					 }
					}
				}
			else{
				$BoxCodeTable="&nbsp;";
				}

            

		break;
	  case 16:  //diesel 
	  	$EndPlace=$Description;	
	  	break;
	 case 27:
	 case 30:
	 case 35:
		  if($BoxCode!=""){
			$Field=explode("|",$BoxCode);$BoxCode0=$Field[0];$BoxCode1=$Field[1];
			
			if(is_numeric($BoxCode0) && strlen($BoxCode1)<10 && strpos($BoxCode0,".")===false){	
				if ( strpos($BoxCode1, ",") !== false ) $BoxCode1=preg_match(",","<br>",$BoxCode1);
				$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='bottom'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling='no' width='120' height='40'  src='../model/ean_13code.php?Code=$BoxCode0&lw=1&hi=25'></iframe></td></tr><tr><td height='32' valign='top' scope='col'><div align='center' class='code_title'>$BoxCode1</div></td></tr></table>";
				}
			else{
				if ( strpos($BoxCode0, ",") !== false ) $BoxCode0=preg_match(",","<br>",$BoxCode0);
				if (strlen($BoxCode0)>20) $codestyle="code_title0"; else $codestyle="code_title";
				if(is_numeric($BoxCode1)){
					
					$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='top'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling='no' width='240' height='80'  src='../model/ean_13code.php?Code=$BoxCode1&lw=2&hi=50'></iframe></td></tr></table>";
				  }
				 else{
					$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td height='32' valign='bottom' scope='col'></td></tr><tr><td align='center' valign='top'></td></tr></table>"; 
				 }
				}
			}
		else{
			$BoxCodeTable="&nbsp;";
			}

	 	break;
	  case 39:
	     if($BoxCode!=""){
			$Field=explode("|",$BoxCode);$BoxCode0=$Field[0];$BoxCode1=$Field[1];
			$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='top'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling='no' width='240' height='80'  src='../model/ean_13code.php?Code=$BoxCode1&lw=1.5&hi=50'></iframe></td></tr></table>";
			}
		else{
			$BoxCodeTable="&nbsp;";
			}
	     
	    break;
	  default:	
	      //echo "BoxCode" . $BoxCode;
		  if($BoxCode!=""){
			$Field=explode("|",$BoxCode);$BoxCode0=$Field[0];$BoxCode1=$Field[1];
			
			$FieldCount=count($Field); 
			$barcode39="";
			if($FieldCount>=3){
				$barcode39=$Field[2];  //如case 31://鸿昌顺出CEL(Code39)
			}
			$barcode14="";
			if($FieldCount>=4){
				$barcode14=$Field[3];  //如case 31://鸿昌顺出CEL(Code39)
				echo "barcode14:$barcode14";
			}
			
			if(is_numeric($BoxCode0) && strlen($BoxCode1)<10 && strpos($BoxCode0,".")===false){	
				if ( strpos($BoxCode1, ",") !== false ) $BoxCode1=preg_match(",","<br>",$BoxCode1);
				$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='bottom'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='120' height='40'  src='../model/ean_13code.php?Code=$BoxCode0&lw=1&hi=25'></iframe></td></tr><tr><td height='32' valign='top' scope='col'><div align='center' class='code_title'>$BoxCode1</div></td></tr></table>";
				}
			else{
				if ( strpos($BoxCode0, ",") !== false ) $BoxCode0=preg_match(",","<br>",$BoxCode0);
				if (strlen($BoxCode0)>20) $codestyle="code_title0"; else $codestyle="code_title";
				if(is_numeric($BoxCode1)){
					
					$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td height='20' valign='bottom' scope='col'><div align='center' class='$codestyle'>$BoxCode0</div></td></tr><tr><td align='center' valign='top'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='120' height='45'  src='../model/ean_13code.php?Code=$BoxCode1&lw=1&hi=25'></iframe></td></tr></table>";
				  }
				 else{
					$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td height='32' valign='bottom' scope='col'></td></tr><tr><td align='center' valign='top'></td></tr></table>"; 
				 }
				}
			}
		else{
			$BoxCodeTable="&nbsp;";}
			}
		if ($LabelModel!=2){
			$arrDate=explode(" ", $Udate);  //取得英文日期中日后面的英文字母
			if (count($arrDate)==3){
			   $Udate=preg_replace( '/[^\d]/ ', '',$arrDate[0]);
			   $mDate=preg_replace( '/[\d]/ ', '',$arrDate[0]);
			   $sDate=$arrDate[1] . " ". $arrDate[2];
			   }
		
	    	$eCode=trim($eCode);//判断标题的长度,长度超过时自动缩小字体
			$eLen=strlen($eCode); 
		  if ($eLen>13){
			$sumLen=0;
			$sCount=0;
			$LettersArry=array("I","J","(",")");
			$iCount=0;
			while(list($key,$str) = each($LettersArry))
			{
			  $iCount=$iCount+substr_count($eCode,$str);
			}
			$sumLen=$sumLen+$iCount*6.25;
			$sCount=$sCount+$iCount;
			
			$mCount=0;
			$mCount=$mCount+substr_count($eCode, "M");
			$mCount=$mCount+substr_count($eCode, "W");
			$sumLen=$sumLen+$mCount*13;
			$sCount=$sCount+$mCount;
			
			$tempCode=preg_replace( '/\d/', '',$eCode);
			$tempLen=strlen($tempCode);
			$dCount=$eLen-$tempLen;
			$sumLen=$sumLen+$dCount*8.25;
			$sCount=$sCount+$dCount;
			$nCount=$eLen-$sCount;
			$sumLen=$sumLen+($nCount)*11;
			if ($sumLen>145){
				$n=0;$sumLen=$sumLen-$eLen*0.5;
				$eSize=42.18;
			  do {
				  $eSize=$eSize-1.5;
				  $sumLen=$sumLen-$iCount*0.175-$mCount*0.225-$dCount*0.18-$nCount*0.25;
				 }while($sumLen>145);
				 //判断字母大小写
			    $tempCode=preg_replace( '/[a-z]/', '',$eCode);
			    $tempLen=$eLen-strlen($tempCode);
			    if ($tempLen>20){
				    $eSize=$eSize+$tempLen*0.80;//0.15
			    }
			    else{
			       if ($tempLen>15){
				        $eSize=$eSize+$tempLen*0.75;//0.15
			       }
			       else{
				       $eSize=$eSize+$tempLen*0.13;//0.15
			       }
				}
			}
			else{$eSize=42.18;}
		 }
		 else{$eSize=42.18;} 
		  
			  $boxLen=strlen($PreWord)+strlen($i);
			  $Box_width=38+($boxLen-1)*25;
			  if ($boxLen>3){
                             $BoxSize=28;
			  }
			  else{
			      $BoxSize=32;
			  }
			if ($LabelModel!=6){
			  $qtyLen=strlen($BoxPcs);
			  $qty_width=32+($qtyLen-1)*12;
              if ($qtyLen>4) $qty_width+=10;
			  $qty_tdwidth= $qty_width+10;
			  $qty_nextwidth=184-$qty_width;
			  $cNameLen=strlen($cName);
			  if ($cNameLen>28){
				  $cNameSize=10;
			  }
			  else{
			      $cNameSize=12;
			  }
			}
			   $spLen=strlen($StartPlace);
			  if ($spLen>27){
				  $spSize=13;
			  }
			  else{
			      $spSize=15.5;
			  } 
			  if (strlen($BoxSpec)<15){$BoxSpec="&nbsp;" . $BoxSpec;}
			  $BoxSpec=preg_replace( "/\*/","<span class='Font_val9'>×</span>",$BoxSpec);
	   }
	
	//模板:标准	2-ECHO专用
	//echo $LabelModel;
	switch($LabelModel){
		case 0:  //临时用，随便改
		case 2: //ECHO专用标签模板
		case 5:	//CG专用
	    case 6: // CEL专用模板	
        case 7:
        case 8: //FORCE 专用
        case 9: //Afiseo 专用
        case 10: //Infinity 专用    
        case 11://Mline 专用 
		case 12://Mline 专用 
		case 13: //AVENIR  专用
		case 14: //MCA DC  专用
		case 15: //TLF  专用
		case 16: //Diesel
		case 17: //bigben
		case 18: //Cookies
		case 19: //Cookies
		case 20://VOG专用
		case 21://Skech专用
		case 22://QTUSD专用
		case 23://鸿昌顺专用
		case 24://WT专用标签
		case 25://CG中文标签
		case 26://鸿昌顺PURO
		case 27://CG_ASIA出MCA
		case 28://CG出CEL
		case 29://出仁清
		case 30://出QT-Puro
		case 31://鸿昌顺出CEL(Code39)
		case 32://出Rob
		case 35://出CG_ASIA出air-J
		case 36://出CG_ASIA出Bigben
		case 3602://出CG_ASIA出Bigben 侧面
		case 37://出富士康
		case 38://QT(USD)出Gooey 
		case 39://DCAsia
		case 40://hama
		case 41://GHC To Ascendeo
		case 42: //GHC TO TK-MAX
		case 43: //hama
		case 4302: //hama
		case 44: //AsiaxessQ
		case 4402: //AsiaxessQ
		case 45: //GHC To Hamas
		case 46: //DCAsia with Delivery NO
		case 4602: //
		case 3902: //DCAsia with Delivery NO
		case 48: //DCAsia 
		case 4802: //hama
		case 49: //V-IT 
		case 50://hama=>Telekom
		    //echo "FromCounty: $FromCounty";
        	include "shipLabel/shiplabel_" . $LabelModel . ".php";
            break;
		default://标准标签模板    
			include "shipLabel/shiplabel_default.php";
			break;
		}
	   if($LableID<$LableSUM){echo"<div style='PAGE-BREAK-AFTER: always'></div>";}
	}
//外箱条码输出
function EAN_13($code,$lw,$hi) { 
	
	//$lw =1; //条码宽
  	//$hi = 30; //条码高
  	//左资料码编码规则：根据国家不同有所不同
  	//									3法国	4 德国		5英国   6中国
  	$Guide = array(1=>'AAAAAA','AABBAB','AABBBA','ABAABB','ABBAAB','ABBBAA','ABABAB','ABABBA','ABBABA'); 
  	//左边线
  	$Lstart ='101'; 
  	//左侧编码格式，有两种码
 	 $Lencode = array("A" => array('0001101','0011001','0010011','0111101','0100011','0110001','0101111','0111011','0110111','0001011'), 
                   "B" => array('0100111','0110011','0011011','0100001','0011101','0111001','0000101','0010001','0001001','0010111')); 
  	//右边编码格式
 	$Rencode = array('1110010','1100110','1101100','1000010','1011100', 
                   '1001110','1010000','1000100','1001000','1110100');
	//中线
	$center = '01010';    
	//右边线
	$ends = '101'; 
        $codelen=strlen($code);
	if (($codelen!=13) || ($codelen==13 && substr($code,0,1)=='0')){//条码位数不是13，则条码有错
			if($codelen==13) {
				$code=substr($code,1);
				UPCAbarcode13($code,$lw,$hi);
			    return true;
				}
            if ($codelen==12 || $codelen==11) {
                UPCAbarcode($code,$lw,$hi);
                return true;
            }else{
		die("条码必须是11-13位!");
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
	/*
	//条码校准码
    if($code[12] != (10-($tsum % 10))){ 
		die("校检码不正确!"); 
    	}  
	*/
  	$barcode = $Lstart; 
	for($i=1;$i<=6;$i++){ 
		$barcode .= $Lencode [$Guide[$code[0]][($i-1)]] [$code[$i]];
		} 
	$barcode .= $center; 
	for($i=7;$i<13;$i++){
		$barcode .= $Rencode[$code[($i)]];
		} 
	$barcode .= $ends; 
	$img = ImageCreate($lw*95+10,$hi+15); //输出x*y的空白图像ImageCreate($lw*95+60,$hi+30)
	$fg = ImageColorAllocate($img,0,0,0); //给空白图象填色
	$bg = ImageColorAllocate($img, 255, 255, 255); //给空白图象填色
	//int imagefilledrectangle ( resource image, int x1, int y1, int x2, int y2, int color ). 
	//ImageFilledRectangle($img, 0, 0, $lw*95+60, $hi+30, $bg)
	ImageFilledRectangle($img, 0, 0, $lw*95+40, $hi+15, $bg); 
	//在image图像中画一个用color颜色填充了的矩形，其左上角坐标为x1\y1,右下角坐标为x2/y2。0/0是图像的最左上角。
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
		// ImageFilledRectangle($img, ($x*$lw)+30,5,($x+1)*$lw+29,$hi+5+$sh,$color); 
   		ImageFilledRectangle($img, ($x*$lw)+10,0,($x+1)*$lw+9,$hi+0+$sh,$color); 
		} 
	/* Add the Human Readable Label */
  	//
  	ImageString($img,$lw+1,0,$hi+3,$code[0],$fg); 
  	for ($x=0;$x<6;$x++) { 
 		// int imagestring(int im, int font, int x, int y, string s, int col);
 		//ImageString($img,5,$lw*(8+$x*6)+30,$hi+5,$code[$x+1],$fg); 
   		ImageString($img,$lw+1,$lw*(8+$x*6)+10,$hi+3,$code[$x+1],$fg); 
   		ImageString($img,$lw+1,$lw*(53+$x*6)+10,$hi+3,$code[$x+7],$fg); 
  		}  
		//header("Content-Type: image/png"); 
	ImagePNG($img); 
	}
  
 //生成12位UPC-A码       
 function UPCAbarcode($code,$lw,$hi){
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
        if (($x<10) || ($x>=45 && $x<50) || ($x >=85)) { $sh=10; } else { $sh=0; }
        if ($bars[$x] == '1') { $color = $fg; } else { $color = $bg; }
        ImageFilledRectangle($img, ($x*$lw)+15,5,($x+1)*$lw+14,$hi+5+$sh,$color);
    }
    
  /* Add the Human Readable Label */
    ImageString($img,4,5,$hi-5,$code[0],$fg);
    for ($x=0;$x<5;$x++) {
        ImageString($img,5,$lw*(13+$x*6)+15,$hi+5,$code[$x+1],$fg);
        ImageString($img,5,$lw*(53+$x*6)+15,$hi+5,$code[$x+6],$fg);
    }
    ImageString($img,4,$lw*95+17,$hi-5,$code[11],$fg);
    /* Output the Header and Content. */
    header("Content-Type: image/png");
    ImagePNG($img);
}

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

function ITF14Code($code,$lw,$hi){//错误的
   //$lw = 2; $hi = 100;
   $code='8018080202230';
   $Lencode = array('00110','10001','01001','11000','00101','10100','01100','00011','10010','01010');
  // $Rencode = array('1110010','1100110','1101100','1000010','1011100','1001110','1010000','1000100','1001000','1110100');
   $starts='0000';$ends = '100';// $center = '01010';

/* UPC-A Must be 11 digits, we compute the checksum. */
if ( strlen($code) == 13 ) $code=substr($code,0,12);
if ( strlen($code) != 12 ) { die(" ITF14Code Must be 12 digits."); }
/* Compute the EAN-13 Checksum digit */
$code= '4'.$code;

$ncode ='0'.$code;
$even = 0; $odd = 0;
for ($x=0;$x<14;$x++)
{
if ($x % 2) { $odd += $ncode[$x]; } else { $even += $ncode[$x]; }
}
$sumValue=$odd*3 +$even;
$lastCode=substr($sumValue, -1,1);
$lastCode=10-$lastCode;
$code.=$lastCode;
/* Create the bar encoding using a binary string */
$bars=$starts;
//$bars.=$Lencode[$code[0]];
for($x=0;$x<14;$x++)
{
$bars.=$Lencode[$code[$x]];
}
/*
$bars.=$center;

for($x=6;$x<12;$x++)
{
$bars.=$Rencode[$code[$x]];
}
*/
$bars.=$ends;

/* Generate the Barcode Image */
$img = ImageCreate($lw*95+30,$hi+30);
$fg = ImageColorAllocate($img, 0, 0, 0);
$bg = ImageColorAllocate($img, 255, 255, 255);
ImageFilledRectangle($img, 0, 0, $lw*95+30, $hi+30, $bg);

$shift=10;
 for ($x=0;$x<strlen($bars);$x++) {
        //if (($x<10) || ($x>=45 && $x<50) || ($x >=85)) { $sh=10; } else { $sh=0; }
		//if (($x<4) || ($x>=45 && $x<50) || ($x >=92)) { $sh=10; } else { $sh=0; }
		$sh=0;
        if ($bars[$x] == '1') { $color = $fg; } else { $color = $bg; }
        ImageFilledRectangle($img, ($x*$lw)+15,5,($x+1)*$lw+14,$hi+5+$sh,$color);
    }
    
  /* Add the Human Readable Label */
  //  ImageString($img,4,5,$hi,$code[0],$fg);
	//ImageString($img,4,5,$hi-5,'4',$fg);
    //for ($x=0;$x<5;$x++) {
	 for ($x=0;$x<7;$x++) {	
        ImageString($img,$lw+1,$lw*(5+$x*6)+6,$hi+5,$code[$x],$fg);
        ImageString($img,$lw+1,$lw*(51+$x*6)+15,$hi+5,$code[$x+7],$fg);
		
    }
    //ImageString($img,4,$lw*95+17,$hi-5,$code[11],$fg);
    /* Output the Header and Content. */
    header("Content-Type: image/png");
    ImagePNG($img);
}


//add by zx 2014-04-25 ecode 39码
class pattenclass
{
    var $color;
    var $width;
    function pattenclass($color,$width)
    {
        $this->color=$color;
        $this->width=$width;
    }
}

class code39
{
    var $zoom;
    var $height;
    var $patten=array();
    function code39($zoom, $height)
    {
        $zoom=intval($zoom);
        $this->zoom=$zoom<1||$zoom>20?3:$zoom;
        $height=intval($height);
        $this->height=$height<1||$height>80?30:$height;
        $this->patten[]=new pattenclass("#FFFFFF",1*$this->zoom);
        $this->patten[]=new pattenclass("#FFFFFF",3*$this->zoom);
        $this->patten[]=new pattenclass("#000000",1*$this->zoom);
        $this->patten[]=new pattenclass("#000000",3*$this->zoom);
		//echo $this->zoom ."<br>";  
		//echo $this->height;
		
    }
    function makecode($code)//code39解码
    {
        switch ($code)
        {
            case "0":return ("202130302");
            case "1":return ("302120203");
            case "2":return ("203120203");
            case "3":return ("303120202");
            case "4":return ("202130203");
            case "5":return ("302130202");
            case "6":return ("203130202");
            case "7":return ("202120303");
            case "8":return ("302120302");
            case "9":return ("203120302");
            case "A":return ("302021203");
            case "B":return ("203021203");
            case "C":return ("303021202");
            case "D":return ("202031203");
            case "E":return ("302031202");
            case "F":return ("203031202");
            case "G":return ("202021303");
            case "H":return ("302021302");
            case "I":return ("203021300");
            case "J":return ("202031302");
            case "K":return ("302020213");
            case "L":return ("203020213");
            case "M":return ("303020212");
            case "N":return ("202030213");
            case "O":return ("302030212");
            case "P":return ("203030212");
            case "Q":return ("202020313");
            case "R":return ("302020312");
            case "S":return ("203020312");
            case "T":return ("202030312");
            case "U":return ("312020203");
            case "V":return ("213020203");
            case "W":return ("313020202");
            case "X":return ("212030203");
            case "Y":return ("312030202");
            case "Z":return ("213030202");
            case "-":return ("212020303");
            case ".":return ("312020302");
            case " ":return ("213020302");
            case "*":return ("212030302");
            case "$":return ("212121202");
            case "/":return ("212120212");
            case "+":return ("212021212");
            case "%":return ("202121212");
        }
        return ("212030302");
    }
    function display($code)//输出单个字符
    {
        $output="";
        for ($i=0;$i<9;$i++)
            $output.="<td height=".$this->height." bgcolor=".$this->patten[$code[$i]]->color." width=".$this->patten[$code[$i]]->width."></td>";
        return $output;
    }
    function decode($code)//全部输出
    {
        $output="<table width=".($this->height/2*(strlen($code)+2)*$this->zoom)." height=".$this->height." border=0 cellspacing=0 cellpadding=0 align=\"center\"><tr>";
        $output.=$this->display($this->makecode("*"));
        $output.="<td height=".$this->height." bgcolor=".$this->patten[0]->color." width=".$this->patten[0]->width."></td>";
        $length=strlen($code);
        for ($i=0;$i<$length;$i++)
        {
            $output.=$this->display($this->makecode($code[$i]));
            $output.="<td height=".$this->height." bgcolor=".$this->patten[0]->color." width=".$this->patten[0]->width."></td>";
        }
        $output.=$this->display($this->makecode("*"));
        $output.="</tr></table>";
        return $output;
    }
}


function getOrderBoxCode($POrderId,$DataIn,$link_id){
	$outboxCode = '';
	
	$codeResult = mysql_query("SELECT D.StuffId, D.StuffCname
	                          FROM $DataIn.cg1_stocksheet P 
	                          INNER JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId  
	                          INNER JOIN $DataIn.stufftype T On T.TypeId = D.TypeId
	                          WHERE P.POrderId = '$POrderId'
	                          AND D.TypeId in (9124, 9033)
	                   		  ",$link_id);
	                   		
	//AND T.mainType = 5
	switch(mysql_num_rows($codeResult)){
	    case 1: //只有一个的默认为外箱条码
	        $codeRow = mysql_fetch_assoc($codeResult);
	        $stuffCname = explode('-', $codeRow['StuffCname']);
	        $outboxCode = $stuffCname[count($stuffCname)-1];
	    break;
	    default :
	        while($codeRow = mysql_fetch_assoc($codeResult)){
	            if(strpos($codeRow['StuffCname'], '(ITF)')){
	                continue;
	            }
	            
	            $stuffCname = explode('-', $codeRow['StuffCname']);
	            if(strpos($codeRow['StuffCname'], '(外箱)')){
	                $outboxCode = $stuffCname[count($stuffCname)-1];
	            }
	        }
	    break;
	}
	$outboxCode = preg_replace("/[^\s\d]/", "", $outboxCode);
	return trim($outboxCode);
}

?>