<?php 
//电信-zxq 2012-08-01
/*
MC、DP共享代码
*/
//步骤1
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="供应商税款";		//需处理
$upDataSheet="$DataIn.cw2_gyssksheet";	//需处理
$upDataMain="$DataIn.cw2_gysskmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=$Date==""?date("Y-m-d"):$Date;
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$FileDir="cwgyssk";
switch ($ActionId){
	case 7:
		$Log_Funtion="锁定";		$SetStr="Locks=0";						include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";		$SetStr="Locks=1";						include "../model/subprogram/updated_model_3d.php";		break;
	case 14:
		$Log_Funtion="请款";		$SetStr="Estate=2,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 17:
		$Log_Funtion="审核";		$SetStr="Estate=3,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 16:
		$Log_Funtion="取消结付";	$SetStr="Mid=0,Estate=3,Locks=0";		include "../model/subprogram/updated_model_3c.php";		break;
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回
			$Log_Funtion="审核退回";			$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";			
			}
		else{							//财务退回
			if($Estate==3){					//未结付退回
				$Log_Funtion="未结付退回";		$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";
				}
			else{							//已结付退回，要处理现金流水帐
				$Log_Funtion="已结付退回";		$SetStr="Mid=0,Estate=1,Locks=1";		include "../model/subprogram/updated_model_3c.php";
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
     case 84://补传发票文件
         $FilePath="../download/$FileDir/";
		$PreFileName1="S".$Id.".pdf";
		if($Attached!=""){
			$OldFile1=$Attached;
			
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);
			$InvoiceFileSTR=$uploadInfo1==""?",InvoiceFile='0'":",InvoiceFile='1',`Getdate`='$Getdate'";
		}
                $InvoiceNUM=FormatSTR($InvoiceNUM);
		$SetStr="`InvoiceNUM`='$InvoiceNUM' $InvoiceFileSTR";
		include "../model/subprogram/updated_model_3a.php";   
      break;
	default:
		$FilePath="../download/$FileDir/";
		$PreFileName1="S".$Id.".pdf";
		if($Attached!=""){
			$OldFile1=$Attached;
			
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);
			$InvoiceFileSTR=$uploadInfo1==""?",InvoiceFile='0'":",InvoiceFile='1',Getdate='$Getdate' ";
			}
		if($InvoiceFileSTR=="" && $oldAttached!=""){//没有上传文件并且已选取删除原文件
			$FilePath1=$FilePath."/$PreFileName1";
			if(file_exists($FilePath1)){
				unlink($FilePath1);
				}
			$InvoiceFileSTR=",InvoiceFile='0',Getdate='0000-00-00'";
			}
		$Forshort=FormatSTR($Forshort);
		$Remark=FormatSTR($Remark);
		$InvoiceNUM=FormatSTR($InvoiceNUM);
		$SetStr="Forshort='$Forshort',PayMonth='$PayMonth',Currency='$Currency',InvoiceNUM='$InvoiceNUM',Amount='$Amount',Rate='$Rate',Fpamount='$Fpamount',
		Remark='$Remark',Date='$theDate' $InvoiceFileSTR";
		include "../model/subprogram/updated_model_3a.php";
		
		if($_POST['ListId']){//如果指定了操作对象
			$Counts=count($_POST['ListId']);
			if($Counts>0) {
				$addRecodes="DELETE FROM $DataIn.cw2_gysskrelation WHERE Mid='$Id' ";
				//echo "addRecodes:$addRecodes";
				$addAction=@mysql_query($addRecodes);
				
			}
			for($i=0;$i<$Counts;$i++){
				$thisId=$_POST[ListId][$i];
				$addRecodes="INSERT INTO $DataIn.cw2_gysskrelation (Id,Mid,nonbom6_sID,Date,Operator) VALUES (NULL,'$Id','$thisId','$Date','$Operator')";
				//echo ($addRecodes);
				$addAction=@mysql_query($addRecodes);
				if($addAction){
					$Log.="$Id 关联申购单成功: $thisId.<br>";
				}
				else {
					$Log.="$Id 关联申购单失败: $thisId.<br>";
				}
			}
		}
		
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