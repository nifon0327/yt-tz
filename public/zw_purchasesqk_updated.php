<?php 
//步骤1 $DataIn.zw3_purchaset 二合一已更新
//电信-joseph
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="总务采购请款";		//需处理
$upDataSheet="$DataIn.zw3_purchases";	//需处理
$upDataMain="$DataIn.zw3_purchasem";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$FileDir="zwbuy";
$x=1;
switch($ActionId){
	case 14:
		$Log_Funtion="请款";		$SetStr="Estate=2,Locks=0,qkDate='$DateTime'";				include "../model/subprogram/updated_model_3d.php";		break;
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回
			$Log_Funtion="审核退回";			$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";			
			}
		else{							//财务退回
			if($fromWebPage==$funFrom."_read"){
				$Log_Funtion="申购退回";		$SetStr="cgSign=1,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
				}
			else{
				if($Estate==3){					//未结付退回
					$Log_Funtion="未结付退回";		$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";
					}
				else{							//已结付退回，要处理现金流水帐
					$AmountSTR="Qty*Price";
					$Log_Funtion="已结付退回";		$SetStr="Mid=0,Estate=1,Locks=1";		include "../model/subprogram/updated_model_3c.php";
					}
				}
			}
		break;
	case 17:
		$Log_Funtion="审核通过";	$SetStr="Estate=3";		include "../model/subprogram/updated_model_3d.php";		break;
	case 16:
		$AmountSTR="Qty*Price";
		$Log_Funtion="取消结付";	$SetStr="Mid=0,Estate=3,Locks=0";		include "../model/subprogram/updated_model_3c.php";		
		break;
	case 18://结付
		$AmountValue="Qty*Price";
		$Log_Funtion="结付";		include "../model/subprogram/updated_model_3pay.php";
		break;
	case 20://财务更新
			//必选参数	:文件目录
			$Log_Funtion="主结付单资料更新";
			include "../model/subprogram/updated_model_cw.php";
			$ALType="Estate=0";

		break;
	default:
             if($Picture!=""){//有物品图片上传文件
		$FileType=".jpg";
		//$OldFile=$Picture;
		$FilePath="../download/zwwp/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="Z".$TypeId.$FileType;
		$upPicture=UploadFiles($Picture,$PreFileName,$FilePath);
		if($upPicture){
			$Log.="&nbsp;&nbsp;总务采购物品图片上传成功！ <br>";
			//更新刚才的记录
			$upsql = "UPDATE $DataIn.zw3_purchaset SET Attached='1' WHERE Id=$TypeId";
			$result = mysql_query($upsql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;总务采购物品图片上传失败！</div><br>";			
			}
		}
		//上传档案
		$FilePath="../download/$FileDir/";
		$PreFileName1="Z".$Id.".jpg";
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
		$SetStr="BuyerId='$BuyerId',Price='$Price' $BillSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>