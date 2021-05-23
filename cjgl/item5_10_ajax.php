<?php   
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="置换备料记录";			//需处理
$Log_Funtion="数据更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
// ActionId=1 新增记录 ,2 修改记录,3删除记录
switch($ActionId){
	case 1:
	if ($StuffId!="" && $TempValue!=""){
		//保存主单资料
		$inRecode="INSERT INTO $DataIn.sc_stuffrepmain (Id,StuffId,Remark,Estate,Locks,Date,Operator) VALUES (NULL,'$StuffId','$Remark','1','0','$Date','$Operator')";
        $inAction=@mysql_query($inRecode);
	    $Mid=mysql_insert_id(); 
	   
	    //生成主备料单
		$blinRecode="INSERT INTO $DataIn.yw9_blmain (Id,Estate,Locks,Date,Operator) VALUES (NULL,'1','0','$DateTime','$Operator')";
		  $blinAction=@mysql_query($blinRecode);
		  $Pid=mysql_insert_id();
			
	  if($Mid>0 &&  $Pid>0){ 
		$Field=explode(",",$TempValue);
		$Lens=count($Field);
		for($i=0;$i<$Lens;$i++){
			$tmpData=explode("|",$Field[$i]);
			//保存置换记录资料
			 $addRecodes="INSERT INTO $DataIn.sc_stuffrepsheet  (Id,Mid,StockId,oldQty,Qty,Locks,Estate) VALUES (NULL,'$Mid','$tmpData[1]','$tmpData[2]','$tmpData[3]','0','1')";
			$addAction=@mysql_query($addRecodes);
		   // 更新领料记录
		   	$upCk="UPDATE $DataIn.ck5_llsheet SET Qty='$tmpData[3]' WHERE Id='$tmpData[0]' AND StuffId='$StuffId'  LIMIT 1";
		    $upCkAction=mysql_query($upCk);		
			
			$tmpQty=$tmpData[3]-$tmpData[2];
	        //生成领料明细数据 
	        $POrderId=substr($tmpData[1], 0,12);
			 $llInSql="INSERT INTO $DataIn.ck5_llsheet (Id,Pid,Mid,POrderId,StockId,StuffId,Qty,Locks,Estate) VALUES  (NULL,'$Pid','0',$POrderId,'$tmpData[1]','$StuffId','$tmpQty','0','0')";
			 $llInAction=@mysql_query($llInSql);

			if($llInAction){
				//更新备料单状态
				if ($tmpData[2]>$tmpData[3]){
				   //取得配件ID号
				   $checkResult = mysql_query("SELECT POrderId  FROM $DataIn.cg1_stocksheet WHERE StockId='$tmpData[1]' LIMIT 1",$link_id); 
				   $POrderId=mysql_result($checkResult,0,"POrderId");	
				   $UpdateSql="DELETE  FROM  $DataIn.yw9_blsheet   WHERE POrderId='$POrderId'";
	               $UpdateResult = mysql_query($UpdateSql);
				}
				$Log.="&nbsp;&nbsp;流水号$tmpData[1]备料置换成功(数量:$tmpData[2]->$tmpData[3]).<br>";
			    }
			else{
			    $Log.="&nbsp;&nbsp;流水号$tmpData[1]备料置换失败(数量:$tmpData[2]->$tmpData[3]).<br>";
				$OperationResult="N";
			 } 	
		 }
	  }else { $Log.="保存主表记录失败.$TempValue <br>";$OperationResult="N";}
 }else{ $Log.="数据格式有误.$TempValue <br>";$OperationResult="N";}
	
  $alertLog=$Log_Item . "数据保存成功";$alertErrLog=$Log_Item . "数据保存失败";
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