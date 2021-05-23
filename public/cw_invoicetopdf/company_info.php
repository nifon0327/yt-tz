<?php 
//电信-zxq 2012-08-01
 if ($Ship==0 && $FromFunPos=="CH"){
    $Priceterm="Price term:FCA HONG KONG AIRPORT "; 
 }else{
    $Priceterm="Price term:FOB HK <br>"; //公用 放在terms
 }
$Commoditycode="";
$StableNote="";
$special_Pic="";
switch($CompanyId){
	case 1031:  //Elite 的Note要显示Commodity code:8517709000
		$Commoditycode="Commodity code:8517709000 <br>";  //note里
		break;
	case 1049:  //　　CG
		$StableNote="NO.TVA Intracommunautaire:FR355 1263 0393 <br>";   //欧州消费者增值税,note里
		break;
	case 1018:  //　　EUR
		//$Commoditycode="Commodity code:8517709000 <br>";  //note里
		//$StableNote="N°TVA Intracommunautaire:FR355 1263 0393 <br>";   //欧州消费者增值税,note里
		break;	
	case 1004: //CEL-A
	    $StableNote="EORI Number:IT0174934035 <br>";
           // if ($Ship==0 && $FromFunPos=="CH") $Priceterm="FCA HONG KONG AIRPORT";
	    break;
	case 1059://CEL-B
	    $StableNote="EORI Number:IT0174934035 <br>";
            //if ($Ship==0 && $FromFunPos=="CH") $Priceterm="FCA HONG KONG AIRPORT";
	    break;
	case 1064://AF  add by zx 2011-11-14  需要专用图片，文本会乱码
	case 1071://AF  add by zx 2011-11-14  需要专用图片，文本会乱码
		$special_Pic="../images/1064_PIC.jpg";
	    break;		
	}
?>