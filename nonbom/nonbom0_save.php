<?php 
//EWEN 2013-03-29 OK
include "../model/modelhead.php";
//步骤2：
$Log_Item="非BOM供应商资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
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
$Tel=FormatSTR($Tel);
$Fax=FormatSTR($Fax);
$Area=FormatSTR($Area);
$Website=FormatSTR($Website);
$ZIP=FormatSTR($ZIP);
$Address=FormatSTR($Address);
$Bank=FormatSTR($Bank);
$Judge="";
$Remark=FormatSTR($Remark);
$Currency=$Currency;

$Legalperson=FormatSTR($Legalperson);
$Description=FormatSTR($Description);
$Aptitudes=FormatSTR($Aptitudes);
$EAQF=FormatSTR($EAQF);
$FscNo=FormatSTR($FscNo);
$Quality=0;
$Normative=0;
$Effect=0;
$Qos=0;
$Results=0;

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
//计算编号最大值
$maxSql = mysql_query("SELECT MAX(CompanyId) AS Mid FROM $DataPublic.nonbom3_retailermain ORDER BY CompanyId DESC LIMIT 1",$link_id);
$CompanyId=mysql_result($maxSql,0,"Mid");
if($CompanyId){
	$CompanyId=$CompanyId+1;
	}
else{
	$CompanyId=60001;
	}
	
$inRecode="INSERT INTO $DataPublic.nonbom3_retailermain (Id,cSign,CompanyId,FscNo,Letter,Forshort,PayMode,Judge,Currency,PackFile,AddTaxValue,Estate,Locks,Date,Operator) VALUES (NULL,'0','$CompanyId','$FscNo','$Letter','$Forshort','$PayMode','$Judge','$Currency','0','$AddTaxValue','1','0','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="主表 $TitleSTR 成功!<br>";
	$infoRecode="INSERT INTO $DataPublic.nonbom3_retailersheet (Id,CompanyId,Company,Tel,Fax,Area,Website,Address,ZIP,Bank,Remark) VALUES (NULL,'$CompanyId','$Company','$Tel','$Fax','$Area','$Website','$Address','$ZIP','$Bank','$Remark')";
	$infoAction=@mysql_query($infoRecode);
	$Log.="从表 $TitleSTR 成功!<br>";
	$sheetRecode="INSERT INTO $DataPublic.nonbom3_retailerother  (Id,CompanyId ,LegalPerson,BLdate ,TRCdate ,PLdate,Description,Aptitudes,EAQF,Quality,Normative,Effect,Qos,Results) VALUES (NULL,'$CompanyId','$LegalPerson','$BLdate','$TRCdate','$PLdate','$Description','$Aptitudes','$EAQF','$Quality','$Normative','$Effect','$Qos','$Results')";
	$sheetAction=@mysql_query($sheetRecode);
	$Log.="其它信息表 $TitleSTR 成功!<br>";
	if($Linkman!=""){
		$LinkmanRecode="INSERT INTO $DataPublic.nonbom3_retailerlink (Id,CompanyId,Name,Sex,Nickname,Headship,Mobile,Tel,MSN,SKYPE,Email,Remark,Date,Defaults,Estate,Locks,Operator) VALUES (NULL,'$CompanyId','$Linkman','$Sex','$Nickname','$Headship','$Mobile','$Tel2','$MSN','$SKYPE','$Email','$Remark2','$Date','0','1','0','$Operator')";
		$LinkmanRes=@mysql_query($LinkmanRecode);
		if($LinkmanRes){
			$Log.="&nbsp;&nbsp;供应商 $Forshort 的默认联系人资料新增成功！";
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;供应商 $Forshort 的默认联系人资料新增失败！$LinkmanRecode</div>";
			$OperationResult="N";
			}
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
else{
	$Log="<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
