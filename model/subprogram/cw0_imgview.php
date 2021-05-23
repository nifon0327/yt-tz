<?php 
//输出结付图片 分开已更新$DataIn.电信---yang 20120801
$Dir=anmaIn($ImgDir,$SinkOrder,$motherSTR);
if($Checksheet==1){
	$Checksheet="C".$Mid.".jpg";
	$Checksheet=anmaIn($Checksheet,$SinkOrder,$motherSTR);
	$Checksheet="<span onClick='OpenOrLoad(\"$Dir\",\"$Checksheet\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
	}
else{
	$Checksheet="-";
	}
		
if($Payee==1){
	$Payee="P".$Mid.".jpg";
	$Payee=anmaIn($Payee,$SinkOrder,$motherSTR);
	$Payee="<span onClick='OpenOrLoad(\"$Dir\",\"$Payee\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
	}
else{
	$Payee="-";
	}
		
if($Receipt==1){
	$Receipt="R".$Mid.".jpg";
	$Receipt=anmaIn($Receipt,$SinkOrder,$motherSTR);
	$Receipt="<span onClick='OpenOrLoad(\"$Dir\",\"$Receipt\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
	}
else{
	$Receipt="-";
	}
?>