<?php   
//获取对应产品的分类颜色电信---yang 20120801

$rColor=0;
$gColor=0;
$bColor=0;
$mycolor="SELECT  M.rColor,M.gColor,M.bColor 
FROM $DataIn.productdata P 
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
LEFT JOIN $DataIn.productmaintype M ON M.id=T.mainType
WHERE 1 AND P.TypeId=$Id LIMIT 1";
//echo "$mySql";
$myResult = mysql_query($mycolor,$link_id);
if ($myResult ){
	if($myRow = mysql_fetch_array($myResult)){
		//do{
			$rColor=$myRow["rColor"];
			$gColor=$myRow["gColor"];
			$bColor=$myRow["bColor"];  		
		//}  while ($myRow = mysql_fetch_array($myResult));
	}
}

?>