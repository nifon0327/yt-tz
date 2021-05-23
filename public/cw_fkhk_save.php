<?php 
include "../model/modelhead.php";
$Log_Item="供应商其它扣款";			//需处理
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
$inRecode="INSERT INTO $DataIn.cw2_hksheet (Id,Mid,Did,CompanyId,Amount,Rate,Attached,Remark,Date,Estate,Locks,Operator ) 
VALUES (NULL,'0', '0','$CompanyId','$Amount','$Rate','0','$Remark','$Date', '1', '1', '$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	   $Id=mysql_insert_id();
	   $Log="$TitleSTR 成功!<br>";
       if($Attached!=""){//记录保存成功，上传单据
		    $FileType=".jpg";
		    $OldFile=$Attached;
		    $FilePath="../download/cwhk/";
		    if(!file_exists($FilePath)){
			      makedir($FilePath);
			     }
		    $PreFileName="H".$Id.$FileType;
		    $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		    if($Attached){
			      $Log.="&nbsp;&nbsp;单据上传成功！<br>";
			      $sql = "UPDATE $DataIn.cw2_hksheet SET Attached='1' WHERE Id=$Id";
			      $result = mysql_query($sql);
			    }
		  else{
			     $Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
			    $OperationResult="N";			
			   }
		   }
	   } 
else{
	   $Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
	   $OperationResult="N";
	} 
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
