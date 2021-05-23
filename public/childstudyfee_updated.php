<?php 
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="员工小孩助学费用";		//需处理
$upDataSheet="$DataIn.cw19_studyfeesheet";	//需处理
$upDataMain="$DataIn.cw19_studyfeemain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$FileDir="childinfo";
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";		include "../model/subprogram/updated_model_3d.php";		break;
	case 14:
		$Log_Funtion="请款";	$SetStr="Estate=2,Locks=0,OPdatetime='$DateTime' ";		
		include "../model/subprogram/updated_model_3d.php";		break;
	case 17:
		$Log_Funtion="审核";	$SetStr="Estate=3,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 16:
		$Log_Funtion="取消结付";	$SetStr="Mid=0,Estate=3,Locks=0";	
		include "../model/subprogram/updated_model_3c.php";		break;
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回
			$Log_Funtion="审核退回";		$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";			
			}
		else{							//财务退回
			if($Estate==3){					//未结付退回
				$Log_Funtion="未结付退回";		$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";
				}
			else{							//已结付退回，要处理现金流水帐
				$Log_Funtion="已结付退回";		$SetStr="Mid=0,Estate=1,Locks=1";
				include "../model/subprogram/updated_model_3c.php";
				}			
			}
		break;
	case 18://结付
		//$AmountValue="Amount";
		$Log_Funtion="结付";		include "../model/subprogram/updated_model_3pay.php";
		break;
	case 20://财务更新
		$Date=date("Y-m-d");
		$Estate=0;
		$Log_Funtion="主结付单资料更新";
		include "../model/subprogram/updated_model_cw.php";
		break;
		

           case 127:
              $Log_Funtion="凭证上传";
		       //之前最后一个记录
		      $FilePath="../download/childinfo/";
		      if(!file_exists($FilePath)){
			      makedir($FilePath);
			    }
             if($Attached!=""){
                    $OldFile=$Attached;
					$FileType=substr("$Attached_name", -4, 4);
					$PreFileName=$Mid.$FileType;
                	$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
				    if($uploadInfo!=""){
						  $inRecode="Update $upDataSheet SET Attached='$uploadInfo' WHERE Id=$Mid";
						 $inAction=@mysql_query($inRecode);
						if($inAction){
							    $Log.="小孩助学费用记录凭证上传成功，备注更新成功.<br>";
							    $EndNumber++;
                               }
						else{
							    $Log.="<div class='redB'>小孩助学费用记录凭证上传成功，备注更新失败. </div><br>";
							    $OperationResult="N";
							 }
					 }

             }
             break;
	 case 128:
		 $SetStr="Remark='$Remark'";
		 include "../model/subprogram/updated_model_3a.php";
		 break;
	  }
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>