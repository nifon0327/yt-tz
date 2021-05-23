<?php   
//电信-ZX  2012-08-01
/*//测试数据
$ProductId="83734";
$eCode="SILICONCIPHONE4";
$StartPlace="Ash Cloud Co, Ltd. Shenzhen";
$BoxNo="7";
$BoxTotal="100";
$Udate="12th May 2011";
$BoxPcs="68";
$PackingUnit="PCS";
$BoxSpec="38*32*22CM";
$WG="7.60";
$NG="7.80";
$OrderPO="3662";
$InvoiceNO="laz-May11-625";
$cName="黑色刺绣针袜";
$BoxCode="5019507123422|SKGS-M2-BLK1-BC";
$EndPlace="CELLULAR ITALIA SPA c/o TRANSMEC LOG SRL";
*/
header("Content-Type:text/html;charset=utf-8");
include "../../basic/parameter.inc";
$RType=$_GET["RT"];
$arrCode=explode("|",$_GET["CODE"]);
$BoxCode=$_GET["BCODE"];
if (count($arrCode)==7){$cName=$arrCode[0];$eCode=$arrCode[1];$OrderPO=$arrCode[2];$PackingUnit=$arrCode[3];$BoxSpec=$arrCode[4];$BoxPcs=$arrCode[5];$Qty=$arrCode[6];}
//初始化数据
$Label_image="images/label_1.png";
if ($BoxPcs>0 && $Qty>0) $BoxTotal=floor($Qty/$BoxPcs);
if ($BoxTotal>0){
	 $BoxNo=rand(1,$BoxTotal);
	 }
 else{
	 $BoxNo=rand(2,10);
	 }	
$StartPlace="Ash Cloud Co, Ltd. Shenzhen";
if ($RType==2){
	$POrderId=$_GET["ID"];
	$Box_Sql = mysql_query("SELECT M.InvoiceNO,M.Date,M.ModelId,M.PreSymbol,L.BoxPcs,L.BoxQty,L.WG,L.BoxSpec,D.CompanyId,D.StartPlace,D.EndPlace,D.LabelModel
FROM $DataIn.ch2_packinglist L
LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=L.Mid
LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId
WHERE L.POrderId='$POrderId'",$link_id);
    if($BoxRow=mysql_fetch_array($Box_Sql)){
	   $PreSymbol=$BoxRow["PreSymbol"];
	   //$BoxTotal=$BoxRow["BoxTotal"];
	   $CompanyId=$BoxRow["CompanyId"];
	   $StartPlace=$BoxRow["StartPlace"];
	   $EndPlace=$BoxRow["EndPlace"];
	   $ModelId=$BoxRow["ModelId"];
	   $LabelModel=$BoxRow["LabelModel"];
	   $InvoiceNO=$BoxRow["InvoiceNO"];	//Invoice编号  
	   $Udate=$BoxRow["Date"];			//出货日期
	   if (strlen($Udate)>5){
	       $Udate=toenglishdate($Udate);
	   }
	   $sBoxPcs=$BoxRow["BoxPcs"];		//装箱数量
	   $BoxPcs=$sBoxPcs==""?$BoxPcs:$sBoxPcs;
	   $sBoxQty=$BoxRow["BoxQty"];		//箱数
	   if ($sBoxQty>0){
		  $BoxTotal=$sBoxQty;
	      $BoxNo=rand(1,$BoxTotal);
		}
	   $BoxNo=$PreSymbol .$BoxNo;
	   $WG=$BoxRow["WG"];
	   if ($WG>1) $NG=sprintf("%.2f",$WG-1);
	   $sBoxSpec=$BoxRow["BoxSpec"];
	   $BoxSpec=$sBoxSpec==""?$BoxSpec:$sBoxSpec;
	}
	switch ($CompanyId){case 1049:$InvoiceNO="&nbsp;";$cName="&nbsp;";break;}
	if ($LabelModel=="" || $LabelModel=null){
		 $LabelResult = mysql_query("SELECT H.LabelModel FROM  $DataIn.yw1_ordersheet S  
		 LEFT JOIN	$DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
		 LEFT JOIN	$DataIn.ch8_shipmodel H ON H.CompanyId=M.CompanyId 
         WHERE S.POrderId='$POrderId'",$link_id);
         if($LabelRow = mysql_fetch_array($LabelResult)){
	           $LabelModel=$LabelRow["LabelModel"];
	      }
	   }
}

$cName = mb_convert_encoding($cName, "html-entities","UTF-8");
$StartPlace=$StartPlace==""?"Ash Cloud Co, Ltd. Shenzhen":$StartPlace;

switch($LabelModel){
		case 2:		//ECHO专用标签模板
		   include "labelFile/newLabel_2.php";
		   break;
		case 5:	//CG专用
		   include "labelFile/newLabel_5.php";
		   break;
	    case 6: // CEL专用模板
		  include "labelFile/newLabel_6.php";
		  break;
		default:	//标准标签模板
		   include "labelFile/newLabel.php";
		break;
}


function setFontSize($WriteText,$FontName,$FontSize,$setWidth,$setHeight){
   if (strlen($WriteText)>0){
      do{
	      $WriteText=str_replace(' ',',',$WriteText);
          $temp=imagettfbbox($FontSize,0,$FontName,$WriteText); //取得使用 TrueType 字体的文本的范围 
          $temp_w=$temp[2]-$temp[6]; 
          $temp_h=$temp[3]-$temp[7]; 
          unset($temp);
          $FontSize=$FontSize-0.5;
      }while(($setWidth<$temp_w)||($setHeight<$temp_h));
       $FontSize+=1;
   }
   else{$temp_w=0;$temp_h=0;}
   return array($FontSize,$temp_w,$temp_h);
}

//转英文日期
function toenglishdate($Date_temp){
	$OutDate=date("j-M-Y",strtotime($Date_temp));
	$DateStr=explode("-",$OutDate); //日
	$DayStr=$DateStr[0]."th"." ".$DateStr[1]." ".$DateStr[2];
	
	if(($DateStr[0]=="1") or($DateStr[0]=="21")or($DateStr[0]=="31")){
		$DayStr=$DateStr[0]."st"." ".$DateStr[1]." ".$DateStr[2];
		}
	if(($DateStr[0]=="2") or($DateStr[0]=="22")){
		$DayStr=$DateStr[0]."nd"." ".$DateStr[1]." ".$DateStr[2];
		}
	if(($DateStr[0]=="3") or($DateStr[0]=="23")){
		$DayStr=$DateStr[0]."rd"." ".$DateStr[1]." ".$DateStr[2];
		}
	return $DayStr;
}

function createCode($im,$code,$curX,$curY,$setWidth,$lw,$hi){
		$Guide = array(1=>'AAAAAA','AABBAB','AABBBA','ABAABB','ABBAAB','ABBBAA','ABABAB','ABABBA','ABBABA'); 
  	$Lstart ='101'; 
 	$Lencode = array("A" => array('0001101','0011001','0010011','0111101','0100011','0110001','0101111','0111011','0110111','0001011'), 
                   "B" => array('0100111','0110011','0011011','0100001','0011101','0111001','0000101','0010001','0001001','0010111')); 
 	$Rencode = array('1110010','1100110','1101100','1000010','1011100', 
                   '1001110','1010000','1000100','1001000','1110100');
	$center = '01010'; $ends = '101'; 
	$lsum =0; $rsum =0; 
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
	$bcLen=strlen($barcode);
	$curX=$curX+($setWidth-($bcLen+1)*$lw)/2;
	$fg=imagecolorallocate($im,0,0,0); 
    $bg=imagecolorallocate($im,255,255,0); 
	for ($x=0;$x<$bcLen;$x++) { 
		if(($x<4) || ($x>=45 && $x<50) || ($x >=92)){
			$sh=10;} 
		else{
			$sh=0; 
			} 
		if ($barcode[$x]=='1'){  
			$color = $fg;} 
		else{  
			$color = $bg;} 
   	    ImageFilledRectangle($im,$curX+($x*$lw)+10,$curY,$curX+($x+1)*$lw+9,$curY+$hi+$sh,$color); 
	} 
   	ImageString($im,$lw+2,$curX,$hi+3+$curY,$code[0],$fg); 
  	for ($x=0;$x<6;$x++) { 
   		ImageString($im,$lw+2,$lw*(8+$x*6)+10+$curX,$hi+3+$curY,$code[$x+1],$fg); 
   		ImageString($im,$lw+2,$lw*(53+$x*6)+10+$curX,$hi+3+$curY,$code[$x+7],$fg); 
  	}  
}
?>