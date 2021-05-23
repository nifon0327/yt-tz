<?php
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="物料退换";			//需处理
$Log_Funtion="数据更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
// ActionId=1 新增记录 ,2 修改记录,3删除记录
switch($ActionId){
	case 1:
	     $EstateSTR=1;
         include "item5_7_th_ajax.php";
         $alertLog=$Log_Item . "数据保存成功";$alertErrLog=$Log_Item . "数据保存失败";
        break;
  case 2:
  		$Log_Funtion="退换数据更新";
		$thSTR="";
        $UpdateAttachStr="";
	       if($Picture!=""){//有上传文件
	       	       $FileType=".jpg";
	       	       $OldFile=$Picture;
	       	       $FilePath="../download/thimg/";
	       if(!file_exists($FilePath)){
			       	makedir($FilePath);
	       }
	       $PreFileName="T".$Id.$FileType;
	       $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
	       if($Attached){
				       $UpdateAttachStr= " ,T.Picture=1";
				 }
		    }

		if($Operators>0){	//增加退换数量的条件 在库>=增加的数量
			$thSTR=" and K.tStockQty>=$changeQty";
			}
		$upSql = "UPDATE $DataIn.ck2_thsheet T 
		       LEFT JOIN $DataIn.ck9_stocksheet K ON T.StuffId=K.StuffId 
		       SET T.Qty=T.Qty+$changeQty*$Operators,T.Estate=1  $UpdateAttachStr 
		       WHERE T.Id=$Id AND T.Estate>0 $thSTR";
		$upResult = mysql_query($upSql);
		if($upResult && mysql_affected_rows()>0){
			$Log="退换数据更新成功.<br>";
			}
		else{
			$Log="<div class='redB'>退换数据更新失败!</div><br>";
			$OperationResult="N";
			}
		$alertLog=$Log_Item . "数据更新成功";$alertErrLog=$Log_Item . "数据更新失败";
		break;

  case 3:
		//只能删除未审核的
	   	$thStateSql = "SELECT T.Estate,D.StuffCname FROM $DataIn.ck2_thsheet T 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = T.StuffId 
		WHERE T.Id = $Id";
		$thStateResult = mysql_query($thStateSql);
		$thStateRows = mysql_fetch_assoc($thStateResult);
		$thState = $thStateRows['Estate'];
		$StuffCname= $thStateRows['StuffCname'];
		if($thState == '0'){

		    $OperationResult="N";
			$Log.="<div class='redB'>配件名为: $StuffCname 的退货记录已审核，不能删除 </div><br>";
		}else if ($thState>0){
			//删除记录
			$delSql = "DELETE FROM $DataIn.ck2_thsheet WHERE Id='$Id' AND Estate>0";
			$delResult = mysql_query($delSql);
			if($delResult && mysql_affected_rows()>0){
				$Log.="配件名为: $StuffCname 的退货记录删除成功.<br>";
				}
			else{
				$Log.="<div class='redB'>配件名为: $StuffCname 的退货记录删除失败. $delSql </div><br>";
				$OperationResult="N";
				}
			}
	   //删除全部没有明细的主单
       $delMainSql = "DELETE  M FROM $DataIn.ck2_thmain M 
	    LEFT JOIN $DataIn.ck2_thsheet S ON M.Id=S.Mid
	    WHERE S.Id IS NULL";
       $delMianRresult = mysql_query($delMainSql);
	   $alertLog=$Log_Item . "数据已成功删除";$alertErrLog=$Log_Item . "数据删除失败";

	break;

    case 5:
          $Log_Funtion="退换主单数据更新";
         $UpdateAttachStr="";
	       if($Attached!=""){//有上传文件
	       	       $FileType=".jpg";
	       	       $OldFile=$Attached;
	       	       $FilePath="../download/thimg/";
	       if(!file_exists($FilePath)){
			       	makedir($FilePath);
	       }
	       $PreFileName="M".$Id.$FileType;
	       $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
	       if($Attached){
				       $UpdateAttachStr= " ,Attached=1";
				 }
		    }
          $UpdateSql="UPDATE $DataIn.ck2_thmain SET Date='$thDate' $UpdateAttachStr WHERE Id=$Id";
          $UpdateResult=@mysql_query($UpdateSql);
           if($UpdateResult && mysql_affected_rows()>0){
                 $OperationResult="Y";
              }
             else{
                 $OperationResult="N";
           }
	      $alertLog=$Log_Item . "数据更新成功";$alertErrLog=$Log_Item . "数据更新失败";
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