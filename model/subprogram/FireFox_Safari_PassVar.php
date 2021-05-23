<?php  
/*
echo  "StuffId[0]:$StuffId0 <br>";
echo  "Price[0]:$Price0 <br>";
echo  "FactualQty[0]:$FactualQty0 <br>";
echo  "Company[0]:$Company0 <br>";
echo  "AddRemark[0]:$AddRemark0 <br>";
///专为safari设计,不对持javascript添加的变量，
if ($RecordCount>0 && $StuffId[1]=="") {  //说是是safari，FirFox 无值传过来
     echo "safari <br>";
     $StuffId=explode("^",$StuffId0);
	 $Price=explode("^",$Price0);
	 $FactualQty=explode("^",$FactualQty0);
	 $Company=explode("^",$Company0);
	 $AddRemark=explode("^",$AddRemark0);
}

echo "0:".$StuffId[0]."  1:".$StuffId[1]."  2:".$StuffId[2]."  3:".$StuffId[3]."<br>";
echo "0:".$FactualQty[0]."  1:".$FactualQty[1]."  2:".$FactualQty[2]."  3:".$StuffId[3]."<br>" ;
return false;
*/
//echo "Safaripassvars:$Safaripassvars <br>";
if($Safaripassvars!=""){  //此变量不为空，说明是新版的 add by zx 2011-05-06  
    $passvars=explode("|",$Safaripassvars); //全部变量取出来
	$varsCount=count($passvars);
    
	$tempvar=$passvars[0];  //获取第一个变量
 
	if (count($$tempvar)==0){ //说明如果来自IE,则>0，其它是，否则==0 ,需要处理分拆变量	,当然，全部可以用此方支>=0，不分浏览器
		//echo count($$tempvar);
		//echo "Hear! <br>";
		for($v=0;$v<$varsCount;$v++){
			$tempstr=$passvars[$v]."0"; 
			$$passvars[$v]=explode("^",$$tempstr);
			//echo "$$passvars[$v]=$$tempstr;";
		}
		$v=""; //以免别的地方用到它
	}
}
/*
for($i=1;$i<=$RecordCount;$i++){
	//echo "Here2";
	$newStuffId=$StuffId[$i];	
	echo "$i:$newStuffId <br>";
}
*/
?>