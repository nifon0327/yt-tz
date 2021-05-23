<?php
//EWEN 2013-03-29 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM供应商资料";		//需处理
$upDataSheet="$DataPublic.nonbom3_retailermain";	//需处理
$Type=3;
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
switch($ActionId){
	case 5:				//则联系人，联系人的帐号可用
		$Log_Funtion="可用";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$sql = "UPDATE $upDataSheet A LEFT JOIN $DataPublic.nonbom3_retailerlink B ON B.CompanyId=A.CompanyId SET A.Estate='1',A.Locks='0',B.Estate='1',B.Locks='0' WHERE A.Id IN ($Ids)";
		$result = mysql_query($sql);
		if($result){
			$Log.="ID号在 $Ids 的 $Log_Item 设为可用成功!</br>";
			}
		else{
			$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 设为可用失败!</div></br>";
			$OperationResult="N";
			}
		break;
	case 6:
		$Log_Funtion="禁用";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$sql = "UPDATE $upDataSheet A LEFT JOIN $DataPublic.nonbom3_retailerlink B ON B.CompanyId=A.CompanyId SET A.Estate='0',A.Locks='0',B.Estate='0',B.Locks='0' WHERE A.Id IN ($Ids)";
		$result = mysql_query($sql);
		if($result){
			$Log.="ID号在 $Ids 的 $Log_Item 设为禁用成功!</br>";
			}
		else{
			$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 设为禁用失败! $sql </div></br>";
			$OperationResult="N";
			}
		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		if($Prov!=$City){
			$Area=$Prov."|".$City;}
		else{
			$Area=$City;
			}
		//记录字段值
		$chinese=new chinese;
		$Letter=substr($chinese->c($Forshort),0,1);
		$Company=FormatSTR($Company);
		$Forshort=FormatSTR($Forshort);
		$FscNo=FormatSTR($FscNo);
		$Tel=FormatSTR($Tel);
		$Fax=FormatSTR($Fax);
		$Area=FormatSTR($Area);
		$Website=FormatSTR($Website);
		$ZIP=FormatSTR($ZIP);
		$Address=FormatSTR($Address);
		$Bank=FormatSTR($Bank);
		$Judge=FormatSTR($Judge);
		$Remark=FormatSTR($Remark);
		$Currency=$Currency;
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
		$CompanyIdResult = mysql_query("SELECT CompanyId FROM $upDataSheet A  WHERE A.Id='$Id'",$link_id);
		if($CompanyIdRow= mysql_fetch_array($CompanyIdResult)) {
			$CompanyId=$CompanyIdRow["CompanyId"];
			$mainSql = "UPDATE $upDataSheet A SET A.FscNo='$FscNo',A.Letter='$Letter', 
			A.Forshort='$Forshort', A.Currency='$Currency', A.PayMode='$PayMode', A.AddTaxValue = '$AddTaxValue',
			A.Date='$Date', A.Operator='$Operator', A.Locks='0',A.Judge='$Judge'
			WHERE A.Id='$Id'";
			$Result = mysql_query($mainSql);
			if($Result){
				$Log.="非BOM供应商 $Forshort 的主资料更新成功.<br>";
				}
			else{
				$Log.="<div class=redB>非BOM供应商 $Forshort 的主资料更新失败! $mainSql </div></br>";
				$OperationResult="N";
				}		
			$mainSql = "UPDATE $DataPublic.nonbom3_retailersheet B SET B.Company='$Company',B.Tel='$Tel',B.Fax='$Fax',B.Area='$Area',B.Website='$Website',B.ZIP='$ZIP',B.Address='$Address',B.Bank='$Bank',B.Remark='$Remark' WHERE B.CompanyId='$CompanyId'";
			$Result = mysql_query($mainSql);
			if($Result){
				$Log.="非BOM供应商 $Forshort 的公司资料更新成功.<br>";
				}
			else{
				$Log.="<div class=redB>非BOM供应商 $Forshort 的公司资料更新失败! $mainSql </div></br>";
				$OperationResult="N";
				}	
			$mainSql = "UPDATE $DataPublic.nonbom3_retailerlink C SET C.Name='$Linkman',C.Sex='$Sex',C.Nickname='$Nickname',C.Headship='$Headship',C.Mobile='$Mobile',C.Tel='$Tel2',C.MSN='$MSN',C.SKYPE='$SKYPE',C.Email='$Email',C.Remark='$Remark2',C.Date='$Date',C.Operator='$Operator' WHERE C.CompanyId='$CompanyId' AND C.Defaults='0'";
			$Result = mysql_query($mainSql);
			if($Result){
				$Log.="非BOM供应商 $Forshort 的联系人资料更新成功.<br>";
				}
			else{
				$Log.="<div class=redB>非BOM供应商 $Forshort 的联系人资料更新失败! $mainSql </div></br>";
				$OperationResult="N";
				}						
			//其他资料
			$Quality=0;
			$Normative=0;
			$Effect=0;
			$Qos=0;
			$Results=0;
			$mainSql = "UPDATE $DataPublic.nonbom3_retailerother D SET D.LegalPerson='$LegalPerson',D.BLdate='$BLdate',D.TRCdate='$TRCdate',D.PLdate='$PLdate',D.Description='$Description',D.Aptitudes='$Aptitudes',D.EAQF='$EAQF',D.Quality='$Quality',D.Normative='$Normative',D.Effect='$Effect',D.Qos='$Qos',D.Results='$Results' WHERE D.CompanyId='$CompanyId'";
			$Result = mysql_query($mainSql);
			if($Result){
				$Log.="非BOM供应商 $Forshort 的附加资料更新成功.<br>";
				}
			else{
				$Log.="<div class=redB>非BOM供应商 $Forshort 的附加资料更新失败! $mainSql </div></br>";
				$OperationResult="N";
				}
           //供应商和子分类的联系表
              $DelSql="DELETE  FROM $DataPublic.nonbom3_link WHERE CompanyId=$CompanyId";
               $DelResult=@mysql_query($DelSql);
              if($TypeId!=""){
                       $TypeArray=explode("@",$TypeId);
                        $TypeCount=count($TypeArray);
                       for($k=0;$k<$TypeCount;$k++){
                               $IN_recode1="INSERT INTO $DataPublic.nonbom3_link(Id,CompanyId,TypeId)VALUES(NULL,'$CompanyId','$TypeArray[$k]')";
                               $IN_res1=@mysql_query($IN_recode1);
                          }
                    }
			}		
	break;
	}
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page&Orderby=$Orderby";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>