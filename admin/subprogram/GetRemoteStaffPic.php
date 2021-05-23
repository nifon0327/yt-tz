<?php   
//$DataPublic.staffmain$DataIn.电信---yang 20120801
//二合一已更新

$count_Temp=mysql_query("SELECT EShortName,CShortName,IPaddress FROM $DataPublic.companys_group 
						 WHERE  EShortName='$GetSign' ",$link_id);  //
//echo "SELECT count( * ) AS counts FROM $DataIn.cg1_lockstock WHERE StockId='$StockId";
$EShortName=mysql_result($count_Temp,0,"EShortName");
$CShortName=mysql_result($count_Temp,0,"CShortName");
$IPaddress=mysql_result($count_Temp,0,"IPaddress");

$Passvalue="";  

$PreFileName1="P".$Number.".jpg";
$PreFileName1="http://$IPaddress/download/staffPhoto/".$PreFileName1;
//echo "$PreFileName1 <br>";
$uploadInfo1=Get_Remote_File($PreFileName1,$FilePath,$Passvalue);
$upValue1=$uploadInfo1=="-1"?"":",Photo='1'";	

$PreFileName2="C".$Number.".jpg";
$PreFileName2="http://$IPaddress/download/staffPhoto/".$PreFileName2;
$uploadInfo2=Get_Remote_File($PreFileName2,$FilePath,$Passvalue);
$upValue2=$uploadInfo2=="-1"?"":",IdcardPhoto='1'";

$PreFileName3="I".$Number.".pdf";
$PreFileName3="http://$IPaddress/download/staffPhoto/".$PreFileName3;
$uploadInfo3=Get_Remote_File($PreFileName3,$FilePath,$Passvalue);
$upValue3=$uploadInfo3=="-1"?"":",InFile='1'";

$PreFileName4="H".$Number.".jpg";
$PreFileName4="http://$IPaddress/download/staffPhoto/".$PreFileName4;
$uploadInfo4=Get_Remote_File($PreFileName4,$FilePath,$Passvalue);
$upValue4=$uploadInfo4=="-1"?"":",HealthPhoto='1'";

$sheetSql ="UPDATE $DataPublic.staffsheet SET  Number=$Number 
 $upValue1 $upValue2 $upValue3 $upValue4 WHERE Number=$Number";
 //echo "$sheetSql";

$sheetResult = mysql_query($sheetSql);
if($sheetResult){
	$Log.="&nbsp;&nbsp; $Name 的从 $CShortName 图片文获取成功!</br>";
	}
else{
	$Log.="&nbsp;&nbsp; $Name 的从 $CShortName 图片文获失败! $sheetSql </br>";
	$OperationResult="N";
	}

?>