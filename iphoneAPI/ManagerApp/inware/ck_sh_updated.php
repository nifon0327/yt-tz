<?php 
 $Log_Item="送货单";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
  include "ck_function.php";
   
 switch($ActionId){
	 
	 case "current_tv": {
		 $stuffid=$info[0];
		 $gys_id=$info[1];
		  $Log_Funtion="连接电视";
		 $lineQuanChou=$info[2];
		 $lineQuanChou = $lineQuanChou == "" ? "3B-4" : "3B-5";
		 // -1 全检   0 抽检
		  $upSql = "UPDATE $DataIn.qc_currentcheck SET gys_id='$gys_id',stuffId='$stuffid' WHERE line_app='$lineQuanChou'";
			   $upResult = mysql_query($upSql,$link_id);
			   $infoSTR = $upSql;
				if ($upResult){
					$OperationResult = 'Y';
					}
		 
	 }
	 break;
	 
          case "Estate":
               $upId=$info[0];
               $Log_Funtion="状态更新";
               $upSql = "UPDATE $DataIn.gys_shsheet SET Estate=2,shDate=NOW()  WHERE Estate=1 AND Id IN ($upId) ";
			   $upResult = mysql_query($upSql,$link_id);
				if ($upResult){
                        $Log=":ID:$upId" . $Log_Item .$Log_Funtion . "(已到达)成功!<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
                        
                        //更新相同送货单、相同配件Id的备品状态
                        $checkResult= mysql_query("SELECT StuffId,Mid FROM $DataIn.gys_shsheet WHERE  Id IN ($upId) GROUP BY StuffId,Mid",$link_id);
                        while($checkRow = mysql_fetch_array($checkResult)) {
                             $upMid=$checkRow["Mid"];
                             $upStuffId=$checkRow["StuffId"];
                             $upSql2 = "UPDATE $DataIn.gys_shsheet SET Estate=2  WHERE  Mid='$upMid' AND StuffId='$upStuffId' AND Estate=1 AND SendSign=2";
			                 $upResult2 = mysql_query($upSql2,$link_id);
                        }
                         
                       /* $inSql="INSERT INTO $DataIn.gys_shdate SELECT NULL,Id,'$DateTime','1','0' ,'0','$Operator','$DateTime','$Operator','$DateTime','$curDate','$Operator'
 FROM $DataIn.gys_shsheet WHERE  Id IN ($upId) ";
                        $inResult = mysql_query($inSql,$link_id);
                        */
                        if ($Floor==6 || $Floor==12) {
	                        $allIds = explode(',', $upId);
	                        foreach ($allIds as $singleSid) {
		                        $Sid=$singleSid; $lineId=$Floor==6 ? 4 : 5;
            $Log_Funtion="分配拉线";
            $inSql="INSERT INTO $DataIn.qc_mission  (Id, Sid, LineId, Remark,Estate,rkSign,DateTime, Operator) VALUES(NULL,'$Sid','$lineId','','1','1','$DateTime','$Operator') ON DUPLICATE KEY UPDATE LineId='$lineId',DateTime='$DateTime',Operator='$Operator' ";
            $inResult = mysql_query($inSql,$link_id);
            if ($inResult){
                        $Log.=$Log_Item .$Log_Funtion . "成功!<br>";
                    
                        $infoSTR.=$Log_Funtion ."成功";
              } 
            else{
                        $Log.="<div class=redB>Sid:$Sid $Log_Item $Log_Funtion 失败! </div><br>";
                        $infoSTR.=$Log_Funtion ."失败";
                 } 
	                        }
                        }
                        
                        
                        
                        //信息推送
                        $shIds=$upId;
                         include "../subpush/gyssh_push.php";
                } 
                else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion (已到达)失败! $upSql </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
            break;  
         case "Allot":
            $Sid=$info[0]; $lineId=$info[1];
            $Log_Funtion="分配拉线";
            $inSql="INSERT INTO $DataIn.qc_mission  (Id, Sid, LineId, Remark,Estate,rkSign,DateTime, Operator) VALUES(NULL,'$Sid','$lineId','','1','1','$DateTime','$Operator') ON DUPLICATE KEY UPDATE LineId='$lineId',DateTime='$DateTime',Operator='$Operator' ";
            $inResult = mysql_query($inSql,$link_id);
            if ($inResult){
                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
              } 
            else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
           break;
         case "Remark":
               $Sid=$info[0]; $Remark=$info[1];
               $Log_Funtion="备注保存";
               $inSql = "INSERT INTO $DataIn.gys_shremark (Id, Sid, Remark, Date, Operator) VALUES (NULL, '$Sid', '$Remark', '$DateTime', '$Operator')";
			   $inResult = mysql_query($inSql,$link_id);
              if ($inResult){
                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
                } 
                else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inSql </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
            break;  
           case "Return"://退回
                $upId=$info[0];
               $Remark=$info[1];
               $Log_Funtion="退回";
               $upResult=shBack($upId,$Remark,$DataIn, $link_id,$Operator);
                if ($upResult){
                        $Log=$upId . "-" . $Log_Item .$Log_Funtion . "成功!<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
                 }
                 else{
	                 $Log="<div class=redB>$Log_Item $Log_Funtion失败! $upSql </div><br>";
                     $infoSTR=$Log_Funtion ."失败";
                 }
              /*
               $upSql = "UPDATE $DataIn.gys_shsheet SET Estate=1  WHERE Estate=2 AND Id IN ($upId) ";
			   $upResult = mysql_query($upSql,$link_id);
				if ($upResult){
                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
                        
                        //更新相同送货单、相同配件Id的备品状态
                        $checkResult= mysql_query("SELECT StuffId,Mid,COUNT(*) AS Nums FROM $DataIn.gys_shsheet WHERE  Id IN ($upId) GROUP BY StuffId,Mid",$link_id);
                        while($checkRow = mysql_fetch_array($checkResult)) {
                                   $upNums=$checkRow["Nums"];
		                          if ($upNums==1){
				                             $upMid=$checkRow["Mid"];
				                             $upStuffId=$checkRow["StuffId"];
				                             $upSql2 = "UPDATE $DataIn.gys_shsheet SET Estate=1  WHERE  Mid='$upMid' AND StuffId='$upStuffId' AND Estate=2 AND SendSign=2";
							                 $upResult2 = mysql_query($upSql2,$link_id);
					               }
                        }
                } 
                else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion失败! $upSql </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
              */
           break;
  }
  

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>