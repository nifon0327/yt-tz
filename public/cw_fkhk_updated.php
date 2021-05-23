<?php 
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="供应商其它扣款";		//需处理
$upDataSheet="$DataIn.cw2_hksheet";	//需处理
$upDataMain="$DataIn.cw2_hkmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=$Date==""?date("Y-m-d"):$Date;
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$FileDir="/cwfkdj/";	
switch ($ActionId){
	case 7:		
		$Log_Funtion="锁定";		$SetStr="Locks=0";						include "../model/subprogram/updated_model_3d.php";		break;
	case 8:		
		$Log_Funtion="解锁";		$SetStr="Locks=1";						include "../model/subprogram/updated_model_3d.php";		break;
	case 14:	
		$Log_Funtion="请款";		$SetStr="Estate=2,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回OK
			$Log_Funtion="审核退回";			$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3b.php";			
			}
		else{							//财务退回
			if($Estate==3){					//未结付退回OK
				$Log_Funtion="未结付退回";		$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3b.php";
				}
			else{							//已结付退回，要处理现金流水帐
				$Log_Funtion="已结付退回";		$SetStr="Mid=0,Estate=1,Locks=1";		include "../model/subprogram/updated_model_3c.php";
				}			
			}
		break;
	case 16:
		$Log_Funtion="取消结付";	$SetStr="Mid=0,Estate=3,Locks=0";		include "../model/subprogram/updated_model_3c.php";		break;
	case 17:
		$Log_Funtion="审核";		$SetStr="Estate=3,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 18://结付
		$Log_Funtion="结付";		include "../model/subprogram/updated_model_3pay.php";
		$Estate=0;
		break;
	case 20://财务更新
		$Log_Funtion="主结付单资料更新";		
		$FileDir="cwhk";	
		include "../model/subprogram/updated_model_cw.php";
		$Estate=0;
		break;
	default://OK
        if($Attached!=""){//上传单据
		    $FileType=".jpg";
		    $OldFile=$Attached;
		    $FilePath="../download/cwhk/";
		    if(!file_exists($FilePath)){
			      makedir($FilePath);
			     }
		    $PreFileName="H".$Mid.$FileType;
		    $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		    if($Attached){
			      $Log.="&nbsp;&nbsp;$PreFileName单据上传成功！<br>";
			      $sql = "UPDATE $DataIn.cw2_hksheet SET Attached='1' WHERE Id=$Id";
			      $result = mysql_query($sql);
			    }
		  else{
			     $Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
			    $OperationResult="N";			
			   }
		   }
		$Remark=FormatSTR($Remark);		
		$SetStr="CompanyId='$CompanyId',Amount='$Amount',Remark='$Remark',Date='$Date'";
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