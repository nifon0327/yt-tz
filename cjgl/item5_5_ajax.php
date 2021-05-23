<?php   
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="备品转入数据";			//需处理
$Log_Funtion="数据更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch($ActionId){
	case 1://新增备品转入数据
	
	   $inRecode="INSERT INTO $DataIn.ck7_bprk (Id,StuffId,CompanyId,Qty,Price,Remark,LocationId,Date,Locks,Operator) VALUES (NULL,'$StuffId','$CompanyId','$Qty','$Price','$Remark','$Identifier','$rkDate','0','$Operator')";
	   $inAction=@mysql_query($inRecode);
	   if ($inAction){ 
		     $Log=$Log_Item. "保存成功!<br>";
	    } 
	   else{
		    echo "N" . $inRecode;
		    $Log="<div class=redB>" .$Log_Item . "保存失败!</div><br>"  . $inRecode;
		    $OperationResult="N";
		 } 
	   $alertLog=$Log_Item . "数据保存成功";$alertErrLog=$Log_Item . "数据保存失败";
	 break;
   
   case 2:
   
      $SetStr="Qty='$Qty',Remark='$Remark',Date='$Date',LocationId='$LocationId',
      Estate= 1,ReturnReason='',Operator='$Operator',Locks='0'";
      $updateSQL = "UPDATE $DataIn.ck7_bprk SET $SetStr WHERE Id='$Id' AND Estate>0";
      $updateResult = mysql_query($updateSQL);
      if ($updateResult && mysql_affected_rows()>0){
	         $Log.=$Log_Item. "ID号：" . $Id. "更新成功!<br>";
		
	   }else{
	        $Log.="<div class=redB>" .$Log_Item. "ID号：" . $Id. "更新失败!</div><br>";
	        $OperationResult="N";
	   }
	   $alertLog=$Log_Item . "数据更新成功";$alertErrLog=$Log_Item . "数据更新失败";
	   break;
	   
   case 3://删除备品转入数据
	  if ($Id!=""){		
			$delSql = "DELETE FROM $DataIn.ck7_bprk WHERE Id='$Id' AND Estate>0"; 
			$delResult = mysql_query($delSql);
			if($delResult && mysql_affected_rows()>0){
				$Log.="Id为" . $Id . "的备品入库记录删除成功(相关配件库存已更新).<br>";
				}
			else{
				$Log.="<div class='redB'>Id为" . $Id . "的备品入库记录删除失败. $delSql </div><br>";
				$OperationResult="N";
				}
		}
	   $alertLog=$Log_Item . "数据已成功删除";$alertErrLog=$Log_Item . "数据删除失败";
	break;
}

//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

if ($OperationResult=="N"){
       echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertErrLog');</script>";
      }
   else{
	  echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertLog');parent.closeWinDialog();parent.ResetPage(1,5);</script>";
   }

?>