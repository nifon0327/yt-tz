<?php 
//电信---yang 20120801
//代码、数据共享-EWEN 2012-08-17
include "../model/modelhead.php";
$Log_Item="配件属性";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Name=FormatSTR($Name);
$Remark=FormatSTR($Remark);
//增加新职位
//颜色#转换成RGB

function hex2rgb_c($hexColor) {
$color = str_replace('#', '', $hexColor);
if (strlen($color) > 3) {
$rgb = array(
'r' => hexdec(substr($color, 0, 2)),
'g' => hexdec(substr($color, 2, 2)),
'b' => hexdec(substr($color, 4, 2))
);
} else {
$color = str_replace('#', '', $hexColor);
$r = substr($color, 0, 1) . substr($color, 0, 1);
$g = substr($color, 1, 1) . substr($color, 1, 1);
$b = substr($color, 2, 1) . substr($color, 2, 1);
$rgb = array(
'r' => hexdec($r),
'g' => hexdec($g),
'b' => hexdec($b)
);
}
return $rgb;
}
$ActionId=$ActionId==""?0:$ActionId;
$RGB=hex2rgb_c($SelColor);
$inRecode="INSERT INTO $DataIn.stuffpropertytype (Id,TypeName,TypeColor,rColor,gColor,bColor,MainType,ActionId,Remark,Estate,Locks,Date,Operator) VALUES (NULL,'$Name','$SelColor','$RGB[r]','$RGB[g]','$RGB[b]','$MainType','$ActionId','$Remark','1','0','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
