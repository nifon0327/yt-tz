<?php   
Header("Content-type: image/png"); //输出一个PNG 图片文件
$Label_image="images/label_6.png";
$im_info=getimagesize($Label_image); 
$im_w=$water_info[0]; //取得图片的宽 
$im_h=$water_info[1]; //取得图片的高
$im=imagecreatefrompng($Label_image);//装入背景图片
$black=imagecolorallocate($im,0,0,0); //定义黑色
$white=imagecolorallocate($im,255,255,255); //定义白色
$yellow=imagecolorallocate($im,255,255,0); //定义黄色
$font1="Fonts/ARIALBD.TTF";//定义字体
$font2="Fonts/OMB___WT.TTF";
$font3="Fonts/TIMESBD.TTF";
$font4="Fonts/simhei.ttf";
//输出标题
$font2_size=42;
$curFontSize=setFontSize($eCode,$font2,$font2_size,466,46);
$curX=12;$curY=50;
$curX=floor($curX+(466-$curFontSize[1])/2);
$curY=floor($curY-(46-$curFontSize[2])/2);
imagettftext($im,$curFontSize[0],0,$curX,$curY,$black,$font2,$eCode);

//输出SHIPTO
 $font1_size=12.12;
 $curX=40;$curY=80;
 $EndPlace=$EndPlace==""?"CELLULAR ITALIA SPA c/o TRANSMEC LOG SRL":$EndPlace;
 $curFontSize=setFontSize($EndPlace,$font1,$font1_size,418,18);
 $curX=floor($curX+(418-$curFontSize[1])/2);
 $curY=floor($curY-(18-$curFontSize[2])/2);
 imagettftext($im,$curFontSize[0],0,$curX,$curY,$black,$font1,$EndPlace);
 
//输出cName
//$cName = iconv("gb2312","UTF-8",$cName);
//$cName=preg_replace('/\xa3([\xa1-\xfe])/e', 'chr(ord(\1)-0x80)',$cName); 
$font4_size=10;
$curFontSize=setFontSize($cName,$font4,$font4_size,200,15);
$curX=20;$curY=113;
$curX=floor($curX+(200-$curFontSize[1])/2);
$curY=floor($curY-(15-$curFontSize[2])/2);
imagettftext($im,$curFontSize[0],0,$curX,$curY,$yellow,$font4,$cName);

//输出箱号
$font2_size=32;
$curFontSize=setFontSize($BoxNo,$font2,$font2_size,34,31);
$curX=322;$curY=125;
$curX=floor($curX+(34-$curFontSize[1])/2);
$curY=floor($curY-(31-$curFontSize[2])/2);
imagettftext($im,$curFontSize[0],0,$curX,$curY,$yellow,$font2,$BoxNo);
//输出总箱数
$font2_size=18;
$curFontSize=setFontSize($BoxTotal,$font2,$font2_size,76,22);
$curX=406;$curY=120;
$curX=floor($curX+(76-$curFontSize[1])/2);
$curY=floor($curY-(22-$curFontSize[2])/2);
imagettftext($im,$curFontSize[0],0,$curX,$curY,$black,$font2,$BoxTotal);

//输出日期
$mDate="";
$arrDate=explode(" ", $Udate);  //取得英文日期中日后面的英文字母
if (count($arrDate)==3){
	$Udate=preg_replace( '/[^\d]/ ', '',$arrDate[0]);
	$mDate=preg_replace( '/[\d]/ ', '',$arrDate[0]);
	$sDate=$arrDate[1] . " ". $arrDate[2];
}
$font1_size=12.12;
$curX=55;$curY=140;
if ($mDate==""){
   imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$Udate);
 }else{
   imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$Udate);
   $curX=65;
   if (strlen($Udate)==2) $curX=$curX+8;
   imagettftext($im,10,0,$curX,$curY-4,$black,$font1,$mDate);
   $curX=$curX+15;
   imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$sDate); 
}

$BoxSpec=preg_replace( "/\*/","×",$BoxSpec);
$font1_size=12.12;
$curX=123;$curY=162;
imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$BoxSpec); 
//输出NG WG
$font1_size=12.12;
$curX=55;$curY=183;
imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$WG); 
$curX=55;$curY=204;
imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$NG); 
//输出P/O NO INVOICE
$font1_size=12.12;
$curX=88;$curY=224;
imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$OrderPO); 
$curX=88;$curY=245;
imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$InvoiceNO); 

//输出条码
if($BoxCode!=""){
   if (strlen($BoxCode)==13){
	   $code=$BoxCode;
   }else{
     $Field=explode("|",$BoxCode);$code=$Field[1];$BoxCode1=$Field[0];
     $BoxCode1=eregi_replace(","," ",$BoxCode1);
   }
}
if(is_numeric($code) &&  strlen($code)==13){
	 $font1_size=10;
	 $curX=260;$curY=155;
     $curFontSize=setFontSize($BoxCode1,$font1,$font1_size,155,15);
     $curX=floor($curX+(155-$curFontSize[1])/2);
     $curY=floor($curY-(15-$curFontSize[2])/2);
     imagettftext($im,$curFontSize[0],0,$curX,$curY,$black,$font1,$BoxCode1);
   //生成条码//$code=$BoxCode;
    $curX=255;$curY=160;$setWidth=155;
    $lw=1.4;$hi=65;
	createCode($im,$code,$curX,$curY,$setWidth,$lw,$hi);
}

//输出装箱数量
if ($BoxPcs=="0") $BoxPcs=rand(10,99);
$font2_size=14;
$curFontSize=setFontSize($BoxPcs,$font2,$font2_size,26,15);
$curX=415;$curY=200;
$curX=floor($curX+(26-$curFontSize[1])/2);
$curY=floor($curY-(15-$curFontSize[2])/2);
imagettftext($im,$curFontSize[0],0,$curX,$curY,$yellow,$font2,$BoxPcs);
//输出装箱数量条码
if (is_numeric($BoxPcs) &&  $BoxPcs>0){
   $barcode = new BarCode128($im,432,245,$BoxPcs,$BoxPcs);
   $barcode->createBarCode();
}

// $outFile="../../download/labelFile/L" .$ProductId . ".png";
// imagepng($im,$outFile); //创建图形
 imagepng($im); 
 imagedestroy($im); //关闭图形
 
class BarCode128 { //生成条码类
	const STARTA = 103;
	const STARTB = 104;
	const STARTC = 105;
	const STOP = 106;
	private $unit_width = 1.5;	//单位宽度 缺省1个象素
	private $is_set_height = false;
	private $width = -1;
	private $height = 42;
	private $quiet_zone = 6;
	private $font_height = 15;
	private $font_type = 3;
	private $color =0x000000;
	private $bgcolor =0xFFFFFF;
	private $image = null;
	private $codes = array("212222","222122","222221","121223","121322","131222","122213","122312","132212","221213","221312","231212","112232","122132","122231","113222","123122","123221","223211","221132","221231","213212","223112","312131","311222","321122","321221","312212","322112","322211","212123","212321","232121","111323","131123","131321","112313","132113","132311","211313","231113","231311","112133","112331","132131","113123","113321","133121","313121","211331","231131","213113","213311","213131","311123","311321","331121","312113","312311","332111","314111","221411","431111","111224","111422","121124","121421","141122","141221","112214","112412","122114","122411","142112","142211","241211","221114","413111","241112","134111","111242","121142","121241","114212","124112","124211","411212","421112","421211","212141","214121","412121","111143","111341","131141","114113","114311","411113","411311","113141","114131","311141","411131","211412","211214","211412","2331112");
	private $valid_code = -1;
	private $type ='B';
	private $start_codes =array('A'=>self::STARTA,'B'=>self::STARTB,'C'=>self::STARTC);
	private $code ='';
	private $bin_code ='';
	private $text ='';
	
	public function __construct($image='',$poiX=0,$poiY=0,$code='',$text='',$type='B'){
		if (in_array($type,array('A','B','C')))
			$this->setType($type);
		else
			$this->setType('B');
	    if ($image !=='')
			$this->setImage($image);
		if ($poiX >0)
			$this->setpoiX($poiX);
       if ($poiY >0)
			$this->setpoiY($poiY);
		if ($code !=='')
			$this->setCode($code);
		if ($text !=='')
			$this->setText($text);
		}
	
	public function setUnitWidth($unit_width){
		$this->unit_width = $unit_width;
		$this->quiet_zone = $this->unit_width*6;
		$this->font_height = $this->unit_width*15;
		if (!$this->is_set_height){
			$this->height = $this->unit_width*35;
			}
		}

	public function setFontType($font_type){
		$this->font_type = $font_type;
		}
		
	public function setBgcolor($bgcoloe){
		$this->bgcolor = $bgcoloe;
		}
	
	public function setColor($color){
		$this->color = $color;
		}
		
	public function setCode($code){
		if ($code !=''){
			$this->code= $code;
			if ($this->text ==='')
				$this->text = $code;
			}
		}
		
	public function setImage($image){
		$this->image = $image;
		}	
		
	public function setText($text){
		$this->text = $text;
		}
	
	public function setType($type){
		$this->type = $type;
		}
	
	public function setpoiX($poiX){
		$this->poiX = $poiX;
		}
	
	public function setpoiY($poiY){
		$this->poiY = $poiY;
		}
		
	public function setHeight($height){
		$this->height = $height;
		$this->is_set_height = true;
		}
	
	private function getValueFromChar($ch){
		$val = ord($ch);
		try{
			if ($this->type =='A'){
				if ($val > 95)
					throw new Exception(' illegal barcode character '.$ch.' for code128A in '.__FILE__.' on line '.__LINE__);
				if ($val < 32)
					$val += 64;
				else
					$val -=32;
				}
			else if ($this->type =='B'){
				if ($val < 32 || $val > 127)
					throw new Exception(' illegal barcode character '.$ch.' for code128B in '.__FILE__.' on line '.__LINE__);
				else
					$val -=32;
				}
			else{
				if (!is_numeric($ch) || (int)$ch < 0 || (int)($ch) > 99)
					throw new Exception(' illegal barcode character '.$ch.' for code128C in '.__FILE__.' on line '.__LINE__);
				else{
					if (strlen($ch) ==1)
						$ch .='0';
					$val = (int)($ch);
					}
				}
			}
		catch(Exception $ex){
			errorlog('die',$ex->getMessage());
			}
		return $val;
		}

	private function parseCode(){
		$this->type=='C'?$step=2:$step=1;
		$val_sum = $this->start_codes[$this->type];
		$this->width = 35;
		$this->bin_code = $this->codes[$val_sum];
		$j =1;
		for($i =0;$i<strlen($this->code);$i+=$step){
			$this->width +=11;
			$ch = substr($this->code,$i,$step);
			$val = $this->getValueFromChar($ch);
			$val_sum += $val*$j++;
			$this->bin_code .= $this->codes[$val];
			}
		$this->width *=$this->unit_width;
		$val_sum = $val_sum%103;
		$this->valid_code = $val_sum;
		$this->bin_code .= $this->codes[$this->valid_code];
		$this->bin_code .= $this->codes[self::STOP];
		}
		
	public function getValidCode(){
		if ($this->valid_code == -1)
			$this->parseCode();
		return $this->valid_code;
		}

	public function getWidth(){
		if ($this->width ==-1)
			$this->parseCode();
		return $this->width;
		}
	
	public function getHeight(){
		if ($this->width ==-1)
			$this->parseCode();
		return $this->height;
		}
		
	public function createBarCode($image_type ='png',$file_name=null){
		$this->parseCode();
		//$this->image = ImageCreate($this->width+2*$this->quiet_zone,$this->height + $this->font_height); 
		$this->bgcolor = imagecolorallocate($this->image,$this->bgcolor >> 16,($this->bgcolor >> 8)&0x00FF,$this->bgcolor & 0xFF);
		$this->color = imagecolorallocate($this->image,$this->color >> 16,($this->color >> 8)&0x00FF,$this->color & 0xFF);
		//ImageFilledRectangle($this->image, 0, 0, $this->width + 2*$this->quiet_zone,$this->height + $this->font_height, $this->bgcolor); 
		$sy = $this->quiet_zone;
		$sx = $this->font_height -1;
		$fw = 10; //編號為2或3的字體的寬度為10，為4或5的字體寬度為11
		if ($this->font_type >3){
			$sx++;
			$fw=11;
			}
		$ey = 0;
		$ex = $this->height + $this->font_height - 2;
		for($i=0;$i<strlen($this->bin_code);$i++){
			$ey = $sy + $this->unit_width*(int) $this->bin_code{$i} -1;
			if ($i%2==0)	
				//ImageFilledRectangle($this->image, $sx+$this->poiX, $sy+$this->poiY, $ex+$this->poiX,$ey+$this->poiY, $this->color); 
				ImageFilledRectangle($this->image, $sx+$this->poiX, $this->poiY-$sy, $ex+$this->poiX,$this->poiY-$ey, $this->color); 
			$sy =$ey + 1;
			}
	/*	$t_num = strlen($this->text);
		$t_x = $this->width/$t_num;
		$t_sx = ($t_x -$fw)/2;        //目的为了使文字居中平均分布
		for($i=0;$i<$t_num;$i++){	
			imagechar($this->image,$this->font_type,6*$this->unit_width +$t_sx +$i*$t_x,0,$this->text{$i},$this->color);
			}

		if (!$file_name){
			header("Content-Type: image/".$image_type); 
			}		
		switch ($image_type){
			case 'jpg':
			case 'jpeg':
				Imagejpeg($this->image,$file_name);
				break;
			case 'png':
				Imagepng($this->image,$file_name);
				break;
			case 'gif':
				break;
				Imagegif($this->image,$file_name);
			default:
				Imagepng($this->image,$file_name);
				break;
			}*/		
		}
	
	}
?>