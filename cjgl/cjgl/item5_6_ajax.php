<?php
session_start();
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="物料报废/损耗";			//需处理
$Log_Funtion="数据更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
// ActionId=1 新增记录 ,2 修改记录,3删除记
switch($ActionId){
	case 1://新增数据

	  $inRecode="INSERT INTO $DataIn.ck8_bfsheet  SELECT 
	  NULL,'$ProposerId',StuffId,'$Qty','$LocationId','$Remark','$Type','0','','',2,'$bfDate','1','0',
	  '$Operator','$DateTime','0','$Operator',NOW(),'$Operator',NOW()   
	  FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' and oStockQty>=$Qty and tStockQty>=$Qty";
     $inAction=@mysql_query($inRecode);
     if ($inAction && mysql_affected_rows()>0){
	       $Log=$Log_Item. "保存成功!<br>";
	       $Id=mysql_insert_id();
	       //上传文件
	       if($Attached!=""){//有上传文件
	       	       $FileType=".jpg";
	       	       $OldFile=$Attached;
	       	       $FilePath="../download/ckbf/";
	       if(!file_exists($FilePath)){
			       	makedir($FilePath);
	       }
	       $PreFileName="B".$Id.$FileType;
	       $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
	       if($Attached){
		       	       $Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
				       $Attached=1;
				       //更新刚才的记录
				       $sql = "UPDATE $DataIn.ck8_bfsheet SET Bill='1' WHERE Id=$Id";
				       $result = mysql_query($sql);
			  }
		     else{
			       $Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
			       $OperationResult="PN";
			   }
		  }
	 }
	 else{
		     $Log="<div class=redB>" .$Log_Item . "保存失败!</div><br>"  . $inRecode;
		     $OperationResult="N";
		 }
        $alertLog=$Log_Item . "数据保存成功";$alertErrLog=$Log_Item . "数据保存失败";
	 break;

   case 2: //更新记录


        if($Qty>0){
            $changeQty = $Qty*$Operators;
			$rkSTR=" AND K.tStockQty>=F.Qty+$changeQty AND K.oStockQty>=F.Qty+$changeQty";
		}
		$SetStr=" SET F.ProposerId='$ProposerId',F.Qty=F.Qty +$changeQty,F.Date='$bfDate',
		F.Remark='$Remark',F.Type='$Type',F.Estate='1',F.Locks='0',F.LocationId='$LocationId',
		F.ReturnReason=''";
		$upSql = "UPDATE $DataIn.ck8_bfsheet F 
		          LEFT JOIN $DataIn.ck9_stocksheet K ON F.StuffId=K.StuffId
			      $SetStr  WHERE F.Id=$Id AND F.Estate>0 AND F.Locks=0  $rkSTR";
	   // 只更当前，不更新库存
	   $upResult = mysql_query($upSql);
       if($upResult && mysql_affected_rows()>0){
			$Log=$Log_Item . "记录更新成功. <br>";
			}
		else{
			$Log="<div class='redB'>" . $upSql . "记录更新失败!</div><br>";
			$OperationResult="N";
			}
		$alertLog=$Log_Item . "数据更新成功";
		$alertErrLog=$Log_Item . "数据更新失败";
	   break;

   case 3://删除数据
	if ($Id!=""){

			//删除记录
			$delSql = "DELETE FROM $DataIn.ck8_bfsheet WHERE Id='$Id' AND Estate >0";
			$delResult = mysql_query($delSql);
			if($delResult && mysql_affected_rows()>0){
				$Log.="Id为" . $Id .  $Log_Item . "记录删除成功(相关配件库存已更新).<br>";
				}
			else{
				$Log.="<div class='redB'>Id为" . $Id .  $Log_Item . "记录删除失败. $delSql </div><br>";
				$OperationResult="N";
				}
	  }//end if ($Id!="")
	 $alertLog=$Log_Item . "数据已成功删除";$alertErrLog=$Log_Item . "数据删除失败";
	break;
}


//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Operator,Log,OperationResult,Estate,Locks) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Operator','$Log','$OperationResult','1','0')";
$IN_res=mysql_query($IN_recode);

if ($OperationResult=="N"){
       echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertErrLog');</script>";
      }
   else{
	  echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertLog');parent.closeWinDialog();parent.ResetPage(1,5);</script>";
   }
?>