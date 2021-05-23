<?php 
//电信-EWEN
include "../basic/parameter.inc";
$result = mysql_query("SELECT Id,Name,Price FROM $DataPublic.ct_menu WHERE CtId='$CtId' AND mType ='$mType' AND Estate=1 ",$link_id);
	
	if($result && $NameResult = mysql_fetch_array($result)){  //判断匹配与否
	//使用循环do{}while()进行对结果取值
	$DishNameStr="";
	  	do{
	  		$Id=$NameResult["Id"];
			$Name=$NameResult["Name"];
			$Price=$NameResult["Price"];
			$DishNameStr.=$DishNameStr==""?$Id ."|" . $Name."|" . $Price."|" . $CtId:"|" .$Id ."|" . $Name."|" . $Price."|" . $CtId;
			}while($NameResult = mysql_fetch_array($result));	
	   echo $DishNameStr;
	  }
?>