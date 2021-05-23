<?php 
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的远程IP

$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="配件开发";		//需处理
$upDataSheet="$DataIn.stuffdevelop";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 15: 
	
		$Log_Funtion="审核退回";			
		$SetStr="ReturnReasons='$ReturnReasons',Estate=1";		
		include "../model/subprogram/updated_model_3d.php";
		$fromWebPage=$funFrom."_m";	
		break;
	
	case 17://审核通过
	
		$Log_Funtion="审核开发";
		$SetStr="Estate=3";
		include "../model/subprogram/updated_model_3d.php";
		//echo $sql;
		$fromWebPage=$funFrom."_m";	
		break;

    case "develop":
        $checkResult=mysql_query("SELECT * FROM $DataIn.stuffdevelop WHERE  StuffId='$StuffId' LIMIT 1",$link_id);
        if (mysql_num_rows($checkResult)>0){
	        $addRecodes="UPDATE $DataIn.stuffdevelop SET GroupId='$GroupId',Number='$DevelopNumber',Targetdate='$Targetdate',KfRemark='$KfRemark' WHERE StuffId='$StuffId' ";
        }
        else{
	        $addRecodes="INSERT INTO $DataIn.stuffdevelop (Id,StuffId,GroupId,Number,Targetdate,Finishdate,KfRemark,Remark,dFile,ReturnReasons,Estate,Date,Operator) VALUES (NULL, '$StuffId',  '$GroupId', '$DevelopNumber', '$Targetdate','0000-00-00 00:00:00','','$Remark','','','1','$Date', '$Operator')";
	        
        }
      //echo $addRecodes;
     $inRres=@mysql_query($addRecodes);
	  if($inRres){
		$Log.="配件 $StuffId 设置开发信息成功.<br>";
            /*
			$developFilePath="../download/Stuffdevelopfile/";  // add by zx 2014-10-15
			if(!file_exists($developFilePath)){
				   makedir($developFilePath);
			   }
		 if($developfile!=""){

				  $FType=substr("$developfile_name", -4, 4);
				  $Ohycfile=$developfile;
				  $PreFileName=$StuffId.$FType;
				  $Attached=UploadFiles($Ohycfile,$PreFileName,$developFilePath);
			  if($Attached!=""){		
				   $inRecode="UPDATE $DataIn.stuffdevelop SET dFile='$PreFileName' WHERE StuffId='$StuffId'";
				   $inAction=@mysql_query($inRecode);
				   if($inAction){ 
					  $Log.="$StuffId 开发文存档成功!<br>";
					   } 
				   else{
					  $Log.="<div class=redB>开发文件存档失败! $inRecode </div><br>";
					  $OperationResult="N";
					  }
					  $Log.="ID为 $StuffId 的开发文件上传上传成功<br>";
				   }
			  else{
					 $Log.="<div class='redB'>ID为 $StuffId 的开发文件上传失败</div><br>";
					 $OperationResult="N";
				  }
			   }
		  else{
			   $Log.="<div class='redB'>ID为 $StuffId 的开发文件没有上传！</div><br>";
			 }
			 */
		
		}
	else{
		$Log.="<div class='redB'>配件 $StuffId 设置开发信息失败.</div>";
		}
	  $fromWebPage=$funFrom."_m";	
      break;

	}
//$ALType="From=$From&Pagination=$Pagination&Page=$Page&StuffType=$StuffType";
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
  ?>