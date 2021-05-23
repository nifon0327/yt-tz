<?php 
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="Forward杂费";		//需处理
$upDataSheet="$DataIn.ch3_forward";	//需处理
$upDataMain="$DataIn.cw3_forward";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$FileDir="cwforward";
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";			include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";			include "../model/subprogram/updated_model_3d.php";		break;
	case 14:
		$Log_Funtion="请款";		$SetStr="Estate=2,Locks=0,modifier='$Operator',modified='$DateTime'";				
		include "../model/subprogram/updated_model_3d.php";		break;
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回
			$Log_Funtion="审核退回";			$SetStr="ReturnReasons='$ReturnReasons',Estate=1,Locks=1,modifier='$Operator',modified='$DateTime'";		include "../model/subprogram/updated_model_3d.php";			
			}
		else{							//财务退回
			if($Estate==3){					//未结付退回
				$Log_Funtion="未结付退回";		$SetStr="Estate=1,Locks=1,modifier='$Operator',modified='$DateTime'";		include "../model/subprogram/updated_model_3d.php";
				}
			else{							//已结付退回，要处理现金流水帐
				$Log_Funtion="已结付退回";		$SetStr="Mid=0,Estate=1,Locks=1,modifier='$Operator',modified='$DateTime'";		include "../model/subprogram/updated_model_3c.php";
				}			
			}
		break;
	case 16:
		$Log_Funtion="取消结付";	$SetStr="Mid=0,Estate=3,Locks=0,modifier='$Operator',modified='$DateTime'";		include "../model/subprogram/updated_model_3c.php";		break;
	case 17:
		$Log_Funtion="审核";		$SetStr="Estate=3,Locks=0,modifier='$Operator',modified='$DateTime'";				include "../model/subprogram/updated_model_3d.php";		break;
	case 18://结付
		$Log_Funtion="结付";		include "../model/subprogram/updated_model_3pay.php";
		break;
	case 20://财务更新
			$Log_Funtion="主结付单资料更新";
			$Date=date("Y-m-d");
			$Estate=0;
			include "../model/subprogram/updated_model_cw.php";
		break;
	default:
		$Date=date("Y-m-d");
		if($ShipType ==1 ){
			$CFSCharge = $CFSCharge1==""?0.00:$CFSCharge1;
			$THCCharge = $THCCharge1==""?0.00:$THCCharge1;
			$WJCharge  = $WJCharge1==""?0.00:$WJCharge1;
			$SXCharge  = $SXCharge1==""?0.00:$SXCharge1;
			$ENSCharge = $ENSCharge1==""?0.00:$ENSCharge1;
			$BXCharge  = 0.00;
			$GQCharge  = $GQCharge1==""?0.00:$GQCharge1;
			$DFCharge  = 0.00;
			$TDCharge  = $TDCharge1==""?0.00:$TDCharge1;
			$OtherCharge = $OtherCharge1==""?0.00:$OtherCharge1;
		}else if ($ShipType ==2){
			$CFSCharge = $CFSCharge2==""?0.00:$CFSCharge2;
			$THCCharge = 0.00;
			$WJCharge  = $WJCharge2==""?0.00:$WJCharge2;
			$SXCharge  = $SXCharge2==""?0.00:$SXCharge2;
			$ENSCharge = $ENSCharge2==""?0.00:$ENSCharge2;
			$BXCharge  = $BXCharge2==""?0.00:$BXCharge2;
			$GQCharge  = 0.00;
			$DFCharge  = $DFCharge2==""?0.00:$DFCharge2;
			$TDCharge  = $TDCharge2==""?0.00:$TDCharge2;
			$OtherCharge = $OtherCharge2==""?0.00:$OtherCharge2;
		}
		
		
		$SetStr="CompanyId='$CompanyId',PayType='$PayType',HoldNO='$HoldNO',ForwardNO='$ForwardNO',BoxQty='$BoxQty',mcWG='$mcWG',
		forwardWG='$forwardWG',Volume='$Volume',HKVolume='$HKVolume',VolumeKG='$VolumeKG',HKVolumeKG='$HKVolumeKG',
		CFSCharge='$CFSCharge',THCCharge='$THCCharge',WJCharge='$WJCharge',SXCharge='$SXCharge',ENSCharge='$ENSCharge',
		BXCharge='$BXCharge',GQCharge='$GQCharge',DFCharge='$DFCharge',TDCharge='$TDCharge',OtherCharge='$OtherCharge',
		Amount='$Amount',InvoiceDate='$InvoiceDate',ETD='$ETD',Remark='$Remark',Locks='0',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

