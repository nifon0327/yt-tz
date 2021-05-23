<?php  
 if ($Ship==0 && $FromFunPos=="CH"){
    $Priceterm=$Priceterm==""?"Price term:FCA HONG KONG AIRPORT ":"Price term:".$Priceterm; 
 }else{
	 switch ($CompanyId){
		 case 1079:
		 	$Priceterm="Price term: <br>";
		 break;
		 case 1088://Dicsel
		 case 1090:
		 case 1091://Skech
		 	$Priceterm="Price term: ";
		 break;
		 case 1059://CEL-B
		 case 1004://CEL-A
		 	$Priceterm="Price term: ";
		    if($Ship==1 ) {
				$Priceterm="Price term: "; 
			}
		    if($Ship==28 ) {
				$Priceterm="Price term:FOB HONGKONG  "; 
			}
		 break;
		 default:
		 	$Priceterm=$Priceterm==""?"Price term:FOB HK <br>":"Price term:".$Priceterm . " <br>";
			break;	 
	 }
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
	    $StableNote="EORI Number:IT08026760960 <br>";
	    break;
	case 1059://CEL-B
	    $StableNote="EORI Number:IT08026760960 <br>";
	    break;
	case 1064://AF  add by zx 2011-11-14  需要专用图片，文本会乱码
	case 1071://AF  add by zx 2011-11-14  需要专用图片，文本会乱码
		$special_Pic="../images/1064_PIC.jpg";
	    break;				
}

?>