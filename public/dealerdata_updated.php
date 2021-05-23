<?php 
/*电信-yang 20120801
$DataPublic.dealerdata
$DataIn.linkmandata
$DataIn.companyinfo
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="经销商或其它公司";		//需处理
$upDataSheet="$DataPublic.dealerdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$Ids="";
for($i=1;$i<=$IdCount;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:$Ids.",".$Id;
		}
	}
$x=1;
switch($ActionId){
	case 5:				//则联系人，联系人的帐号可用
		$Log_Funtion="可用";
		$upSql = "UPDATE $upDataSheet F 
		LEFT JOIN $DataIn.linkmandata L ON F.CompanyId=L.CompanyId  and L.Type='$Type' 
		SET F.Estate='1',F.Locks='0',L.Estate='1',L.Locks='0' WHERE F.Id IN ($Ids)";
		 $upResult = mysql_query($upSql);
		if($upResult){
			$Log="&nbsp;&nbsp;ID在( $Ids ) 的 $TitleSTR 成功!</br>";
			}
		else{
			$OperationResult="N";
			$Log="<div class='redB'>&nbsp;&nbsp;ID在( $Ids ) 的 $TitleSTR 失败!</div></br>";
			}
		break;
	case 6:
		$Log_Funtion="禁用";
		$upSql = "UPDATE $upDataSheet F 
		LEFT JOIN $DataIn.linkmandata L ON F.CompanyId=L.CompanyId and L.Type='$Type' 
		SET F.Estate='0',F.Locks='0',L.Estate='0',L.Locks='0' WHERE F.Id IN ($Ids)";
		 $upResult = mysql_query($upSql);
		if($upResult){
			$Log="&nbsp;&nbsp;ID在( $Ids ) 的 $TitleSTR 成功!</br>";
			}
		else{
			$OperationResult="N";
			$Log="<div class='redB'>&nbsp;&nbsp;ID在( $Ids ) 的 $TitleSTR 失败!</div></br>";
			}
		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "subprogram/updated_model_3b.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "subprogram/updated_model_3b.php";		break;
	default:
		//主表信息
		$Forshort=FormatSTR($Forshort);
		$Currency=$Currency;
		
		//从表信息		
		$Company=FormatSTR($Company);
		$Tel=FormatSTR($Tel);
		$Fax=FormatSTR($Fax);
		$Area=FormatSTR($Area);
		$Website=FormatSTR($Website);
		$Address=FormatSTR($Address);
		$ZIP=FormatSTR($ZIP);
		$Bank=FormatSTR($Bank);
		$Remark=FormatSTR($Remark);
		
		//默认联系人信息
		$Linkman=FormatSTR($Linkman);
		$Nickname=FormatSTR($Nickname);
		$Headship=FormatSTR($Headship);
		$Mobile=FormatSTR($Mobile);
		$Tel2==FormatSTR($Tel2);
		$Email=FormatSTR($Email);
		$Remark2=FormatSTR($Remark2);
		$Defaults=0;
		$Date=date("Y-m-d");
		
		$mainSql = "UPDATE $upDataSheet F,$DataIn.companyinfo I 
		SET F.Forshort='$Forshort',F.Currency='$Currency',
		I.Company='$Company',I.Tel='$Tel',I.Fax='$Fax',I.Area='$Area',I.Website='$Website',I.Address='$Address',I.ZIP='$ZIP',I.Bank='$Bank',I.Remark='$Remark'		
		WHERE F.Id='$Id' and F.CompanyId=I.CompanyId AND I.Type='$Type'";
		$Result = mysql_query($mainSql);
		if($Result){
			$Log="经销商或其它公司 $Forshort 的资料更新成功.<br>";
			//联系人更新，如果有记录则更新，如果没有则新增
			
			$checkLinkman=mysql_query("SELECT Id FROM $DataIn.linkmandata 
			WHERE Id='$LinkId' AND Defaults='0' AND Type='$Type' LIMIT 1",$link_id);
			if($checkRow=mysql_fetch_array($checkLinkman)){//更新
				if($Linkman!=""){
					$Linkman_recode="UPDATE $DataIn.linkmandata SET 
					Name='$Linkman',Sex='$Sex',Nickname='$Nickname',Headship='$Headship',
					Mobile='$Mobile',Tel='$Tel2',MSN='$MSN',SKYPE='$SKYPE',Email='$Email',
					Remark='$Remark2',Date='$Date',Operator='$Operator' WHERE Id='$LinkId' and Defaults='0'";
					$Linkman_res=mysql_query($Linkman_recode);
					if($Linkman_res){
						$Log.="&nbsp;&nbsp;经销商或其它公司的默认联系人更新成功! <br>";
						}
					else{
						$Log.="<div class=redB>&nbsp;&nbsp;经销商或其它公司的默认联系人更新失败!</div>";
						$OperationResult="N";
						}
					}
				else{
					//删除默认联系人
					$delSql="DELETE FROM $DataIn.linkmandata WHERE Id='$LinkId' and Defaults='0'";
					$delRresult = mysql_query($delSql);
					if ($delRresult && mysql_affected_rows()>0){
						//$OPTIMIZE=mysql_query("OPTIMIZE TABLE linkmandata");
						}
					}
				}
			else{//新增
				if($Linkman!=""){
					$LinkmanRecode="INSERT INTO $DataIn.linkmandata 
					(Id,CompanyId,Name,Sex,Nickname,Headship,Mobile,Tel,MSN,SKYPE,Email,Remark,Date,Defaults,Type,Estate,Locks,Operator) VALUES 
					(NULL,'$CompanyId','$Linkman','$Sex','$Nickname','$Headship','$Mobile','$Tel2','$MSN','$SKYPE','$Email','$Remark2','$Date','0','$Type','1','0','$Operator')";
					$LinkmanRes=@mysql_query($LinkmanRecode);
					if($LinkmanRes){
						$Log.="&nbsp;&nbsp; $Forshort 的默认联系人资料新增成功！";
						}
					else{
						$Log.="<div class=redB>&nbsp;&nbsp; $Forshort 的默认联系人资料新增失败！$LinkmanRecode</div>";
						$OperationResult="N";
						}
					}
				}				
			}//end if($Result)
		else{
			$Log="<div class=redB>经销商或其它公司 $Forshort 的资料更新失败!</div></br>";
			$OperationResult="N";
			}//end if($Result)*/
	break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>