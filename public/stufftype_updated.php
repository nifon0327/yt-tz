<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-17
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="配件分类";		//需处理
$upDataSheet="$DataIn.StuffType";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
	
	   $oldResult= mysql_fetch_array(mysql_query("SELECT T.TypeId,T.BuyerId,T.DevelopGroupId,T.DevelopNumber,T.Position  
	   FROM $DataIn.StuffType T  WHERE T.Id='$Id' LIMIT 1",$link_id));
		$TypeId=$oldResult["TypeId"];
		$oldBuyerId=$oldResult["BuyerId"];
		$oldDevelopGroupId=$oldResult["DevelopGroupId"];
		$oldDevelopNumber=$oldResult["DevelopNumber"];
		$oldPosition=$oldResult["Position"];
		$CheckSign=$oldResult["CheckSign"];
		
		$TypeName=FormatSTR($TypeName) ;
		$chinese=new chinese;
        $BlType=$BlType==""?0:$BlType;
		$Letter=substr($chinese->c($TypeName),0,1);
		$Date=date("Y-m-d");
		
		/*
		$JNField=explode("|",$PicJobid);	
		$PicJobid=$JNField[0];
		$PicNumber=$JNField[1];

		$GicField=explode("|",$GicJobid);	
		$GicJobid=$GicField[0];
		$GicNumber=$GicField[1];
		*/
		$DevelopField=explode("|",$DevelopId);	
		$DevelopGroupId=$DevelopField[0];
		$DevelopNumber=$DevelopField[1];
		
		$ActionId=$ActionId==""?0:$ActionId;
		$WorkShopId=$WorkShopId==""?0:$WorkShopId;
		//,jhDays='$jhDays',AQL='$AQL',PicJobid='$PicJobid',PicNumber='$PicNumber',GicJobid='$GicJobid',GicNumber='$GicNumber',
		$SetStr="mainType='$mainType',TypeName='$TypeName',ActionId='$ActionId',WorkShopId='$WorkShopId',AQL='$AQL',Letter='$Letter',NameRule='$NameRule',BlType='$BlType',Position='$Position',
		ForcePicSign='$ForcePicSign',BuyerId='$BuyerId',DevelopGroupId='$DevelopGroupId',DevelopNumber='$DevelopNumber',
		Date='$Date',Operator='$Operator',Locks='0'";
		include "../model/subprogram/updated_model_3a.php";
		
		if ($TypeId>0){
				if ($oldBuyerId!=$BuyerId){
					//更新相应类型的配件采购人员
					$upResult=mysql_query("UPDATE $DataIn.stuffdata  S INNER JOIN $DataIn.bps B ON B.StuffId=S.StuffId SET B.BuyerId='$BuyerId' WHERE  S.TypeId='$TypeId' AND S.Estate>0 ",$link_id);
					if (mysql_affected_rows()>0){
						  $Log.="更新配件资料所属分类($TypeId)的采购人员成功";
					}
					else{
						 $Log.="<div class='redB'>更新配件资料所属分类($TypeId)的采购人员失败</div>";
					}
				}
				
		       if ($oldDevelopGroupId!=$DevelopGroupId){
					//更新相应类型的配件开发小组
					$upSql="UPDATE $DataIn.stuffdata  S  SET S.Pjobid='$DevelopGroupId',S.JobId='$DevelopGroupId' WHERE  S.TypeId='$TypeId' AND S.Estate>0 ";
					$upResult=mysql_query($upSql,$link_id);
					if (mysql_affected_rows()>0){
						  $Log.="更新配件资料所属分类($TypeId)的开发小组成功";
					}
					else{
						 $Log.="<div class='redB'>更新配件资料所属分类($TypeId)的开发小组失败</div><br>$upSql<br>";
					}
				}
				
				if ($oldDevelopNumber!=$DevelopNumber){
					//更新相应类型的配件开发人员
					$upSql="UPDATE $DataIn.stuffdata  S  SET S.PicNumber='$DevelopNumber',S.GicNumber='$DevelopNumber' WHERE  S.TypeId='$TypeId' AND S.Estate>0 ";
					$upResult=mysql_query($upSql,$link_id);
					if (mysql_affected_rows()>0){
						  $Log.="更新配件资料所属分类($TypeId)的开发人员成功";
					}
					else{
						 $Log.="<div class='redB'>更新配件资料所属分类($TypeId)的开发人员失败</div><br>$upSql<br>";
					}
				}
				
				 if ($oldPosition!=$Position){
					//更新相应类型的配件送货楼层
					$checkSignResult=mysql_fetch_array(mysql_query("SELECT CheckSign FROM $DataIn.base_mposition WHERE Id='$Position' LIMIT 1",$link_id));
	                $CheckSign=$checkSignResult["CheckSign"];
	                
					$upSql="UPDATE $DataIn.stuffdata  S  SET S.SendFloor='$Position',S.CheckSign='$CheckSign' WHERE  S.TypeId='$TypeId' AND S.Estate>0 ";
					$upResult=mysql_query($upSql,$link_id);
					if (mysql_affected_rows()>0){
						  $Log.="更新配件资料所属分类($TypeId)的送货楼层成功";
					}
					else{
						 $Log.="<div class='redB'>更新配件资料所属分类($TypeId)的送货楼层失败</div><br>$upSql<br>";
					}
				}
       }
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>