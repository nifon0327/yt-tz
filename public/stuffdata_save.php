<?php 
//步骤1：
include "../model/modelhead.php";
//步骤2：
$Log_Item="配件资料";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Spec=FormatSTR($Spec);
$Remark=FormatSTR($Remark);
$Date=date("Y-m-d");
$maxSql = mysql_query("SELECT MAX(StuffId) AS Mid FROM $DataIn.stuffdata",$link_id);
$StuffId=mysql_result($maxSql,0,"Mid");
if($StuffId){
	$StuffId=$StuffId+1;
	}
else{
	$StuffId=90001;
	}
//新增操作
//写入记录
$checkResult=mysql_fetch_array(mysql_query("SELECT T.BuyerId,T.DevelopGroupId,T.DevelopNumber,T.Position,M.CheckSign  FROM $DataIn.StuffType T 
LEFT JOIN $DataIn.base_mposition M ON M.Id=T.Position WHERE T.TypeId='$TypeId' LIMIT 1",$link_id));
$DevelopGroupId=$checkResult["DevelopGroupId"];
$DevelopNumber=$checkResult["DevelopNumber"];
//$SendFloor=$checkResult["Position"];
//$CheckSign=$checkResult["CheckSign"];

$SendFloor=$SendFloor==""?0:$SendFloor;
$CheckSign=$CheckSign==""?0:$CheckSign;
$DevelopGroupId=$DevelopGroupId==""?0:$DevelopGroupId;
$DevelopNumber=$DevelopNumber==""?0:$DevelopNumber;

$Pjobid=	$GicJobid=$DevelopGroupId;
$PicNumber=	$GicNumber=$DevelopNumber;
	
$GCField=explode("|",$GcheckNumber);	
$GCjobid=$GCField[0];
$GcheckNumber=$GCField[1];

$jhDays=is_numeric($jhDays)?$jhDays:0;
$jhDays=0;

//计算配件成本价，采购价格/(1+增值税率)
$checkTaxRow = mysql_fetch_array(mysql_query("SELECT T.Value FROM $DataIn.providersheet P 
LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
WHERE P.CompanyId = '$CompanyId'",$link_id));
$AddValue = $checkTaxRow["Value"];
$AddValue=$AddValue==""?0:$AddValue;
$CostPrice = sprintf("%.4f", $Price/(1+$AddValue));
$NoTaxPrice = $NoTaxPrice==""?"0.000":$NoTaxPrice;
$PriceDetermined = $PriceDetermined ==""?0:$PriceDetermined;
if($Price>0){
	$PriceDetermined =0;
}

$inRecode="INSERT INTO $DataIn.stuffdata (Id,StuffId,StuffCname,StuffEname,TypeId,Spec,Weight,NoTaxPrice,Price,CostPrice,PriceDetermined,Unit,BoxPcs,Remark,Gfile,Gstate,Gremark,Picture,Pjobid,PicNumber,Jobid,GicNumber,GcheckNumber,SendFloor,CheckSign,ForcePicSpe,DevelopState,jhDays,Estate,Locks,Date,GfileDate,Operator,SeatId	) VALUES 
(NULL,'$StuffId','$StuffCname','$StuffEname','$TypeId','$Spec','$Weight','$NoTaxPrice','$Price','$CostPrice','$PriceDetermined','$Unit','$BoxPcs','$Remark','','0','','0','$Pjobid','$PicNumber','$GicJobid','$GicNumber','$GcheckNumber','$SendFloor','$CheckSign','$ForcePicSpe','$DevelopState','$jhDays','2','0','$Date',NULL,'$Operator','$SeatId')";
$inAction=@mysql_query($inRecode);
//解锁表
if($inAction){ 
     //配件属性
       $tempCount=count($Property);
       for($k=0;$k<$tempCount;$k++){
            if($Property[$k]>0){
                   $inSql3="INSERT INTO $DataIn.stuffproperty(Id,StuffId,Property)VALUES(NULL,'$StuffId','$Property[$k]')";
                   $inRes3=@mysql_query($inSql3);
                  }
           }
	$inRecode1="INSERT INTO $DataIn.bps (Id,StuffId,BuyerId,CompanyId,Locks) VALUES (NULL,'$StuffId','$BuyerId','$CompanyId','0')";
	$inRres1=@mysql_query($inRecode1);
	if ($inRres1){ 
		$Log="<br>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $StuffCname 的配件资料新增成功!配件采购供应商关系设定成功!";
		}
	else{
		$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $StuffCname 的配件资料新增成功!但配件采购供应商关系设定不成功!</div>";
		$OperationResult="N";
		}
	$inRecode2="INSERT INTO $DataIn.ck9_stocksheet (Id,StuffId,dStockQty,tStockQty,oStockQty,mStockQty,Date) VALUES (NULL,'$StuffId','0','0','0','0','$Date')";
	$inRes2=@mysql_query($inRecode2);
	if($inRes2){
		$Log.="<br>&nbsp;&nbsp;&nbsp;&nbsp;库存资料设定成功!!!";
		}
	else{
		$Log.="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;库存资料设定失败!!!</div>";
		$OperationResult="N";
		}
		
		//添加开发信息
		if ($DevelopState==1){
		      $checkTypeResult= mysql_fetch_array(mysql_query("SELECT G.GroupId  AS DevelopGroupId,T.DevelopNumber
		        FROM  $DataIn.StuffType T  
		        LEFT JOIN $DataIn.staffgroup G ON G.Id=T.DevelopGroupId
	          WHERE T.TypeId='$TypeId' LIMIT 1",$link_id));
		     $DevelopNumber=$checkTypeResult["DevelopNumber"];
		     $GroupId=$checkTypeResult["DevelopGroupId"];
		     
		     $Targetdate=$Targetdate==""?"0000-00-00":$Targetdate;
			  $addRecodes="INSERT INTO $DataIn.stuffdevelop (Id,StuffId,GroupId,Number,Targetdate,Finishdate,CompanyId,KfRemark,Remark,dFile,ReturnReasons,Estate,Date,Operator) VALUES (NULL, '$StuffId',  '$GroupId', '$DevelopNumber', '$Targetdate','0000-00-00 00:00:00','$ClientCompanyId','','$developRemark','','','1','$Date', '$Operator')";
			 // echo $addRecodes;
			$inRres4=@mysql_query($addRecodes);
	        if($inRres4){
				    $Log.="配件 $StuffId 设置开发信息成功.<br>";  
					$developFilePath="../download/Stuffdevelopfile/";  
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
							  $Log.="$StuffId 开发文件存档成功!<br>";
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
			     //新增推送信息
			     include "../iphoneAPI/subpush/develop_push.php";
			}
			else{
					  $Log.="<div class='redB'>配件 $StuffId 设置开发失败信息.</div><br>";  
			   }
		}
	} 
else{
	//失败后的处理,删除已经上传的文件
	$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $StuffCname 的配件资料新增失败! $inRecode</div>";
	$OperationResult="N";
	}//end if($res){  

//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
