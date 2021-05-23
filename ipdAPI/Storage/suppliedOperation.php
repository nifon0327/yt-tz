<?php
	
	
	include_once "../../basic/parameter.inc";
	$Id = $_POST["stockId"];
	//$Id = "130063";
	$action = $_POST["action"];
	//$action = "reject";
	$Remark = $_POST["remark"];
	//$Login_P_Number = "11008";
	$Operator = $_POST["operator"];
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$operationResult = "N";
	$errorInfo = "";
	
	switch($action)
	{
		case "pass":
		{
			$updateSQL = "UPDATE $DataIn.gys_shsheet Q SET Q.Estate=2,Q.Locks=0 WHERE Q.Id='$Id'";
			$updateResult = mysql_query($updateSQL);
			if ($updateResult && mysql_affected_rows()>0)
			{
              	$In_Sql="INSERT INTO $DataIn.gys_shdate (Id,Sid,shDate)values(NULL,'$Id','$DateTime')";
              	if(mysql_query($In_Sql))
              	{
	              	$operationResult = "Y";
              	}
			}
			else
			{
		     	$errorInfo = "送货单审核失败";
	        } 

		}
		break;
		case "reject":
		{
			$CheckSql= mysql_query("SELECT S.Mid,S.StockId,S.StuffId,S.Qty,M.BillNumber,M.CompanyId,M.Date AS SendDate 
                        FROM $DataIn.gys_shsheet S 
                        LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
                        WHERE S.Id='$Id' AND S.Locks=1",$link_id);
            
            if($CheckRow = mysql_fetch_array($CheckSql))
            {
            	$Mid=$CheckRow["Mid"];
                $StockId=$CheckRow["StockId"];
                $StuffId=$CheckRow["StuffId"];
                $Qty=$CheckRow["Qty"];
                $CompanyId=$CheckRow["CompanyId"];
                $BillNumber=$CheckRow["BillNumber"];
                $SendDate=$CheckRow["SendDate"];
                $InsertSql="INSERT INTO $DataIn.gys_shback (Id, CompanyId, BillNumber, StockId, SendDate, StuffId, Qty, remark, Estate, Locks, Date, Operator) VALUES (NULL, '$CompanyId', '$BillNumber', '$StockId', '$SendDate', '$StuffId', '$Qty', '$Remark', '2', '0', '$Date', '$Login_P_Number')";
                
                $InsertRresult = mysql_query($InsertSql);     
                $delSql = "DELETE FROM $DataIn.gys_shsheet WHERE Id='$Id'";
                $delRresult = mysql_query($delSql);
                if($delRresult && mysql_affected_rows()>0)
                {
                	//$Log.="配件 $StuffId 的需求单 $StockId 待送货记录删除成功!<br>";
                	$operationResult = "Y";
                	$errorInfo = "待送货记录删除成功";
                    //主入库单
                    $delMainSql = "DELETE FROM $DataIn.gys_shmain WHERE Id=$Mid AND Id NOT IN (SELECT Mid FROM $DataIn.gys_shsheet WHERE Mid=$Mid)"; 
                    $delMianRresult = mysql_query($delMainSql);
                    if($delMianRresult && mysql_affected_rows()>0)
                    {
                        //$Log.="主入库单已经没有内容，清除成功!<br>";
                        $errorInfo .= "主入库单已经没有内容，清除成功!";
                     }
                }
                else
                {
                	$operationResult = "N";
                    //$Log="<div class=redB>送货单:" . $Id . "退回失败!</div><br>";
                    $errorInfo = "送货单退回失败!";
                }
            }

		}
		break;
	}
	
	echo json_encode(array($operationResult, $delMainSql));
	
	//步骤4：
	
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES 		('$DateTime','送货单审核','状态更新','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);

	
?>