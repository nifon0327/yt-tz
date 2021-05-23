<?php 
 include_once("ck_function.php");
 
 $Log_Item="退货记录";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;

 switch($ActionId){
	 
	 case "BackOper":
	 {
		 // 201502789|2|

		 $Log_Funtion = "退料单签名";
		 $BillNumber = $info[0];
		 $estate = $info[1];
		 $countInfo = count($info);
		 $sign = "";
		 if ($countInfo>=3) {
			 array_splice($info,0,2);
		 $sign = implode("|", $info);
		 
		 }
		 
		// 
		 		 
		 $inSql="INSERT INTO $DataIn.thsheet_sign (Id,BillNumber,Sign,Estate,Date,Operator,creator,created) VALUE(NULL,'$BillNumber','$sign','$estate','$curDate','$Operator','$Operator','$DateTime')";
                $inResult = mysql_query($inSql,$link_id);
				if ($inResult){
                        $Log=$Log_Item .$Log_Funtion . "保存成功!<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功"; 
                  } 
                else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 保存失败! $upSql </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }

		 
	 }
	 break;
	 
          case "PrintLabel":
               $Mid=$info[0];$Cartons=$info[1];
               $Log_Funtion="退货单打印";
                $inSql="INSERT INTO $DataIn.ck12_thprintlabel (Id,Mid,Cartons,Estate,Locks,Date,Operator) VALUE(NULL,'$Mid','$Cartons','1','0','$DateTime','$Operator')";
                $inResult = mysql_query($inSql,$link_id);
				if ($inResult){
                        $Log=$Log_Item .$Log_Funtion . "保存成功!<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功"; 
                  } 
                else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 保存失败! $upSql </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
            break;  
        case "SAVE"://生成退料单
              $Log_Funtion="生成退料单";
              $Sids=$info[0];	 
			$checkResult=mysql_query("SELECT  S.Id,S.StuffId,S.Qty,M.CompanyId   FROM $DataIn.qc_badrecord S  LEFT JOIN $DataIn.gys_shmain M ON S.shMid=M.Id WHERE  S.Id IN ($Sids) ",$link_id);
			 if($checkRow = mysql_fetch_array($checkResult)){
			     $CompanyId=$checkRow["CompanyId"];
			     $BillNumber=newThBillNumber($DataIn,$link_id); //取得退货主单号
			     //生成主表
			     $inRecode="INSERT INTO $DataIn.ck12_thmain (Id,BillNumber,CompanyId,Attached,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','0','$DateTime','$Operator')";
				$inAction=@mysql_query($inRecode);
	            $Mid=mysql_insert_id();
			    if ($Mid>0){
				 do{
				       $Bid=$checkRow["Id"];
				       $StuffId=$checkRow["StuffId"];
				       $Qty=$checkRow["Qty"];
				       $Remark=getQcBadRecordCause($Bid,$DataIn,$link_id);//取得品检不良原因
				       
				       $addRecodes="INSERT INTO $DataIn.ck12_thsheet (Id,Mid,Bid,StuffId,Qty,Remark,Estate,Locks) VALUES(NULL,'$Mid','$Bid','$StuffId','$Qty','$Remark','0','0')"; 
				      
                       $addAction=@mysql_query($addRecodes);
                       if ($addAction){
                             //更改品检记录状态
                             $updateSQL = "UPDATE $DataIn.qc_badrecord SET Estate=0 WHERE Id='$Bid'";
	                         $updateResult = mysql_query($updateSQL,$link_id);
	                        $OperationResult="Y";
                        }
				      }while($checkRow = mysql_fetch_array($checkResult));
			      }		
		    }
		    
		    if ($OperationResult=="Y"){
                        $Log=$Log_Item .$Log_Funtion . "保存成功!<br>";
                        $infoSTR=$Log_Funtion ."成功"; 
             } else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 保存失败!($Sids)</div><br>";
                        $infoSTR=$Log_Funtion ."失败";
           }
           break;
 }

  

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>