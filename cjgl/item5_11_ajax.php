<?php   
//电信-zxq 2012-08-01
if($ActionId ==4){
	session_start();
	$MyPDOEnabled=1;
	include "../basic/parameter.inc";
	header("Content-Type: text/html; charset=utf-8");
	header("expires:mon,26jul199705:00:00gmt");
	header("cache-control:no-cache,must-revalidate");
	header("pragma:no-cache");	
    $Log_Item="车间退料数据";			//需处理
	$Log_Funtion="审核";
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	switch($ActionId){
	    case 4:
	     $myResult=null;$myRow=null;   
	         
		 $myResult=$myPDO->query("CALL proc_sc_tlsheet_updatedestate('$Id',$Operator);");
		 $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
		 $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;            
	     echo $OperationResult;
	    break;
	}
	
}else{
	session_start();
	include "../basic/parameter.inc";
	header("Content-Type: text/html; charset=utf-8");
	header("expires:mon,26jul199705:00:00gmt");
	header("cache-control:no-cache,must-revalidate");
	header("pragma:no-cache");
	$Log_Item="车间退料数据";			//需处理
	$Log_Funtion="数据更新";
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	// ActionId=1 新增记录 ,2 修改记录,3删除记录
	switch($ActionId){
		case 1:
		if ($TempValue!=""){
			$Field=explode(",",$TempValue);
			$Lens=count($Field);
			$myData=explode("|",$Field[$i]);
			//先判断该产品是否已经生产完毕
			$scRow = mysql_fetch_array(mysql_query("SELECT SUM( A.Qty ) AS scQty, A.Estate, Y.Qty AS OrderQty
					 FROM $DataIn.sc1_cjtj A
					 LEFT JOIN $DataIn.yw1_scsheet  Y ON Y.sPOrderId = A.sPOrderId
					 WHERE A.sPOrderId = '$sPOrderId' GROUP BY A.sPOrderId",$link_id));
			$scQty   = $scRow['scQty'];
			$scState = $scRow['Estate'];
			$OrderQty= $scRow['OrderQty'];
	
			if($scState === '0' || $scQty ==$OrderQty ){
		 		//$OperationResult="N";
		 		//$Log.="&nbsp;&nbsp;流水号StockId 退料添加失败，工单已生产，不能退料.<br>";
		 		//break;
			}
	
			for($i=0;$i<$Lens;$i++){
				$tmpData=explode("|",$Field[$i]);
				$sPOrderId = $tmpData[0];
				$StockId = $tmpData[1];
				$StuffId = $tmpData[2];
				$oldQty = $tmpData[3];
				$Qty = $tmpData[4];
				$Remark = $tmpData[5];
				$Price = $tmpData[6];
				$ReturnCkSign = $tmpData[7];
				$LocationId = $LocationId ==""?0:$LocationId;
				$ReturnCkSign = $ReturnCkSign ==""?0:$ReturnCkSign;
				$addRecodes="INSERT INTO $DataIn.sc_tlsheet (Id,sPOrderId,StockId,StuffId,LocationId,oldQty,Qty,Price,Remark,
				ReturnCkSign,Type,Estate,Locks, Date,Operator) VALUES  (NULL,'$sPOrderId','$StockId','$StuffId',
				'$LocationId','$oldQty','$Qty','$Price','$Remark','$ReturnCkSign','1','1','0','$Date','$Operator')";
				$addAction=@mysql_query($addRecodes);	
				if($addAction){
					$Log.="&nbsp;&nbsp;流水号$StockId 退料设置成功(退料数量:$Qty).<br>";
				    }
				else{
				    $Log.="&nbsp;&nbsp;流水号$StockId 退料设置失败(退料数量:$Qty).<br>";
					$OperationResult="N";
				 } 	
			 }
		  }else { $Log.="新增退料记录失败.$TempValue <br>";$OperationResult="N";}
	 
	      echo $OperationResult;
	  break;
	  
	  case 3:
	      $delSql="DELETE FROM $DataIn.sc_tlsheet WHERE Id='$Id' AND Estate>0";
		  $delResult=mysql_query($delSql);	
			if($delResult){
			    echo "Y";
		        $Log.="<div class=greenB>" . $Id . "退料记录删除成功!</div><br>";
		     } 
	         else{
			     echo "N"; 
	  	         $Log.="<div class=redB>" . $Id . "退料记录删除失败!</div><br>";
		         $OperationResult="N";
		    }
			
		break;
	}
	
	//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) 
	VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
}

?>