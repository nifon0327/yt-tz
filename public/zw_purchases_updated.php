<?php 
//步骤1 $DataIn.zw3_purchaset 二合一已更新
//电信-joseph
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="总务采购记录";		//需处理
$upDataSheet="$DataIn.zw3_purchases";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="审核退回";$SetStr="cgSign=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";		break;
	case 17:
		$Log_Funtion="审核";   $SetStr="cgSign=3,Locks=1";		include "../model/subprogram/updated_model_3d.php";		break;
	case 52:
		$Log_Funtion="申购"; $EstateStr="AND cgSign=1";	$SetStr="cgSign=2,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
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
                
            switch($cgSign){
	     case 1:
		$TypeName=FormatSTR($TypeName) ;
		//$Date=date("Y-m-d");
		$SetStr="Date='$PDate',Operator='$BuyerId',WorkAdd='$WorkAdd',TypeId='$TypeId',Unit='$Unit',Qty='$Qty',Remark='$Remark',Locks='0'";
		include "../model/subprogram/updated_model_3a.php";
                break;
            
             case 0:
             case 3:
                if ($cNameCheck){
                   $NewcName=FormatSTR($NewcName);
                   $addRecode="INSERT INTO $DataIn.retailerdata (Id, cName, Linkman, Tel, Estate, Locks, Date, Operator) VALUES (NULL,'$NewcName','$NewLinkmen','$NewTel','1','0','$Date','$Operator')";
                   $addAction=@mysql_query($addRecode);  
                   $cName=mysql_insert_id();
                   if ($cName>0){
                      $Log.="新增供应商资料成功!<br>";
                     }
                  else{
                      $Log.="<div class=redB>新增供应商资料失败! $addRecode </div><br>";
                     }
                   }
                   
                 //上传档案
                      $FileDir="zwbuy";
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
			 $WorkAddSTR=$WorkAdd>0?",WorkAdd='$WorkAdd'":"";
              $SetStr="Cid='$cName',BuyerId='$BuyerId',Price='$Price',cgSign='0' $BillSTR $WorkAddSTR";
		     include "../model/subprogram/updated_model_3a.php";  
                     break;
               }
	  break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>