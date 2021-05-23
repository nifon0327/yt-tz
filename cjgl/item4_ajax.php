<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
//步骤2：
$Log_Item="生产订单设置";			//需处理
$Log_Funtion="状态更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
switch($ActionId){
	case 4:
       $UpdateSql="Update $DataIn.yw1_ordersheet SET scFrom=2 WHERE POrderId='$POrderId' AND scFrom='1' AND Estate='1'";
       $UpdateResult = mysql_query($UpdateSql);
       if($UpdateResult){
		  echo "Y";
	      $Log="<div class=greenB>订单:" . $POrderId . "设为生产状态成功!</div><br>";
	   } 
      else{
		  echo "N"; 
  	      $Log="<div class=redB>订单:" . $POrderId . "设为生产状态失败!</div><br>";
	      $OperationResult="N";
	   }
	   break;
	case 5://取消生产
       $UpdateSql="Update $DataIn.yw1_ordersheet SET scFrom=1 WHERE POrderId='$POrderId' AND scFrom='2' AND Estate='1'";
       $UpdateResult = mysql_query($UpdateSql);
       if($UpdateResult){
		  echo "Y";
	      $Log="<div class=greenB>订单:" . $POrderId . "取消生产成功!</div><br>";
	   } 
      else{
		  echo "N"; 
  	      $Log="<div class=redB>订单:" . $POrderId . "取消生产状态失败!</div><br>";
	      $OperationResult="N";
	   }
	   break;
	 case 21://设置生产日期
	  //检查生产日期是否已标记：
     $checkResult = mysql_query("SELECT Id FROM $DataIn.sc_workdate WHERE POrderId='$POrderId'  LIMIT 1",$link_id);
	 if($checkRow = mysql_fetch_array($checkResult)){
		 //更新生产日期
	    $UpdateSql="Update $DataIn.sc_workdate SET scDate='$sDate' WHERE POrderId='$POrderId'";
        $UpdateResult = mysql_query($UpdateSql);
        if($UpdateResult){
		      echo "Y";
	          $Log="<div class=greenB>订单:" . $POrderId . "生产日期更新成功!</div><br>";
	         } 
         else{
		       echo "N"; 
  	           $Log="<div class=redB>订单:" . $POrderId . "生产日期更新失败!</div><br>";
	           $OperationResult="N";
	          }
	     }
	 else{
		//新增生产日期
	   $inRecode="INSERT INTO $DataIn.sc_workdate(Id,POrderId,scDate,endDate,Estate,Date,Operator) VALUES (NULL,'$POrderId','$sDate',NULL,'1','$Date','$Operator')";
		$inResult=@mysql_query($inRecode);
		if($inResult){
			echo "Y";
			$Log.="&nbsp;&nbsp;订单:" . $POrderId . "生产日期标记成功.</br>";
			}
		else{
			 echo "N";
			$Log.="<div class='redB'>&nbsp;&nbsp;订单:" . $POrderId . "生产日期标记失败.$inRecode </div></br>";
			$OperationResult="N";
			}
	   }
///////////、更新备料主单
	   $checkResultB = mysql_query("SELECT Id FROM $DataIn.yw9_blsheet WHERE POrderId='$POrderId'  LIMIT 1",$link_id);
	  if($checkRowB = mysql_fetch_array($checkResultB)){
		  //更新备料日期
	      $UpdateSqlB="Update $DataIn.yw9_blsheet SET blDate='$sDate',Estate='1',Operator='$Operator' WHERE POrderId='$POrderId'";
          $UpdateResultB = mysql_query($UpdateSqlB);
		   if($UpdateResultB){
	          $Log.="<div class=greenB>订单:" . $POrderId . "备料日期更新成功!</div><br>";
	          } 
            else{
  	         $Log.="<div class=redB>订单:" . $POrderId . "备料日期更新失败!</div><br>";
	          }
	       }
	  else{
//////////新增备料主单信息
       //检查主ID
	     $checkNum=mysql_query("",$link_id);
		 $maxSql = mysql_query("SELECT IFNULL(MAX(Num),0) AS Num FROM $DataIn.yw9_blsheet",$link_id);
		 $Num=mysql_result($maxSql,0,"Num");
		 $Num+=1;
	     $inRecodeB="INSERT INTO $DataIn.yw9_blsheet (Id,Num,POrderId,blDate,Estate,Date,Operator) VALUES (NULL,'$Num','$POrderId','$sDate','1','$Date','$Operator')";
		$inResultB=@mysql_query($inRecodeB);
		if($inResultB){
			$Log.="&nbsp;&nbsp;订单:" . $POrderId . "备料主单生成成功.</br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;订单:" . $POrderId . "备料主单生成失败.$inRecode </div></br>";
			}
		} 
		break;
		
	 case 23://删除生产日期标记
	    $delSql="DELETE FROM $DataIn.sc_workdate WHERE POrderId='$POrderId'";
	    $delResult=mysql_query($delSql);	
		if($delResult){
		    echo "Y";
	        $Log.="<div class=greenB>订单:" . $POrderId . " 生产日期已取消!</div><br>";
	     } 
         else{
		     echo "N"; 
  	         $Log.="<div class=redB>订单:" . $POrderId . "生产日期取消失败!</div><br>";
	         $OperationResult="N";
	     }
		 //锁定备料主单
		  $UpdateSql="Update $DataIn.yw9_blsheet SET Estate='2' WHERE POrderId='$POrderId'"; 
		  $UpdateResult = mysql_query($UpdateSql);
        if($UpdateResult){
	          $Log.="<div class=greenB>订单:" . $POrderId . "备料主单锁定成功!</div><br>";
	        } 
         else{
  	           $Log.="<div class=redB>订单:" . $POrderId . "备料主单锁定失败!</div><br>";
		    }
	   break;
}

//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
?>
