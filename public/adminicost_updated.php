<?php 
//电信-EWEN
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="行政费用";		//需处理
$upDataSheet="$DataIn.hzqksheet";	//需处理
$upDataMain="$DataIn.hzqkmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=$Date==""?date("Y-m-d"):$Date;
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$FileDir="cwadminicost";
switch ($ActionId){
	case 7:
		$Log_Funtion="锁定";		$SetStr="Locks=0";						include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";		$SetStr="Locks=1";						include "../model/subprogram/updated_model_3d.php";		break;
	case 14:
		$Log_Funtion="请款";		$SetStr="Estate=2,Locks=0,OPdatetime='$DateTime' ";				
		include "../model/subprogram/updated_model_3d.php";		break;
	case 17:
		$Log_Funtion="审核";		$SetStr="ReturnReasons='',Estate=3,Auditor='$Operator',Locks=0";				
		include "../model/subprogram/updated_model_3d.php";		
		
		$inRecode="INSERT INTO $DataIn.cw_operation (Id,Name,Rec_ID,Estate,Locks,Date,Operator) 
		VALUES (NULL,'$Log_Item$Log_Funtion', '$Ids','1','0','$Date','$Operator')";
		$inAction=@mysql_query($inRecode);
		if ($inAction){
			$Log.="ID号为 $Ids 的记录插入操作表 $DataIn.cw_operation 成功! </br>";	
		}
		else {
			$Log.="ID号为 $Ids 的记录插入操作表 $DataIn.cw_operation 失败! </br>";	
		}
		break;
	case 16:
		$Log_Funtion="取消结付";	$SetStr="Mid=0,Estate=3,Locks=0";		
		include "../model/subprogram/updated_model_3c.php";		break;
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回
			$Log_Funtion="审核退回";			
			$SetStr="ReturnReasons='$ReturnReasons',Auditor='$Operator',Estate=1,Locks=1";		
			include "../model/subprogram/updated_model_3d.php";			
			}
		else{							//财务退回
			if($Estate==3){					//未结付退回
				$Log_Funtion="未结付退回";		$SetStr="Estate=1,Locks=1";		
				include "../model/subprogram/updated_model_3d.php";
				}
			else{							//已结付退回，要处理现金流水帐
				$Log_Funtion="已结付退回";		$SetStr="Mid=0,Estate=1,Locks=1";		
				include "../model/subprogram/updated_model_3c.php";
				}			
			}
		break;
	case 18://结付
		$Log_Funtion="结付";		include "../model/subprogram/updated_model_3pay.php";
		break;
	case 20://财务更新
			//必选参数	:文件目录
			$Log_Funtion="主结付单资料更新";
			include "../model/subprogram/updated_model_cw.php";
		break;
	default:
		$FilePath="../download/$FileDir/";
		$PreFileName1="H".$Id.".jpg";
		if($Attached!=""){
			$OldFile1=$Attached;
			
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);
			$BillSTR=$uploadInfo1==""?",Bill='0'":",Bill='1'";
			}
		if($BillSTR=="" && $oldAttached!=""){//没有上传文件并且已选取删除原文件
			$FilePath1=$FilePath."/$PreFileName1";
			if(file_exists($FilePath1)){
				unlink($FilePath1);
				}
			$BillSTR=",Bill='0'";
			}
       if($OtherId=="")$OtherId=0;
       if($Property=="")$Property=0;    
		$TypeIdArray=explode("|",$TypeId);
		$TypeId=$TypeIdArray[0];
		$Content=FormatSTR($Content);
		if($Estate==0){
			$SetStr="TypeId='$TypeId',Content='$Content',modifier='$Operator',modified='$DateTime' $BillSTR";
			}
		else{
			
			$SetStr="cSign = '$cSign',TypeId='$TypeId',Date='$theDate',Amount='$Amount',Currency='$Currency',Content='$Content' ,
			OtherId='$OtherId',Property='$Property',modifier='$Operator',modified='$DateTime' $BillSTR";
			}
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>