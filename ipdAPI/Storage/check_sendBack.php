<?php

	include_once "../../basic/parameter.inc";

	$upId = $_POST["Id"];
	$CompanyId = $_POST["CompanyId"];
	$Operator = $_POST["Operator"];

	$Log_Item="来料品检记录";			//需处理
	$Log_Funtion="生成退换数据";
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$OperationResult="Y";

    $qcResult= mysql_query("SELECT StuffId,Qty FROM $DataIn.qc_badrecord WHERE  Id='$upId' AND Estate=1 ",$link_id);
    if ($qcRow = mysql_fetch_array($qcResult))
    {
		$thQTY[0]=$qcRow["Qty"];
        $thStuffId[0]=$qcRow["StuffId"];
        $cause_Result=mysql_query("SELECT T.Cause,B.CauseId,B.Reason FROM $DataIn.qc_badrecordsheet B LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  AND T.Type=1 WHERE B.Mid='$upId'",$link_id);
        while($cause_row = mysql_fetch_array($cause_Result))
        {
        	$CauseId=$cause_row["CauseId"];
            if ($CauseId=="-1")
            {
            	if ($Reason!="") $Reason.=" / ";
                $Reason.=$cause_row["Reason"];
            }
            else
            {
            	if ($Reason!="") $Reason.=" / ";
                $Reason.=$cause_row["Cause"];
            }

        }

	    $thRemark[0]=$Reason;
	    $FromPage="item2_4";
        $checkMid=mysql_query("SELECT Id FROM $DataIn.ck2_thmain WHERE CompanyId='$CompanyId' AND Date='$Date'",$link_id);
        if ($checkMidRow = mysql_fetch_array($checkMid))
        {
            $oldMid=$checkMidRow["Id"];
        }

        /******************************/

        $DtateTemp=date("Y");
        $maxSql = mysql_query("SELECT MAX(BillNumber) AS Mid FROM $DataIn.ck2_thmain WHERE BillNumber LIKE '$DtateTemp%'",$link_id);
        $BillNumberTemp=mysql_result($maxSql,0,"Mid");
        if($BillNumberTemp)
        {
	    	$BillNumber=$BillNumberTemp+1;
	    }
	    else
	    {
	    	$BillNumber=$DtateTemp."00001";//默认
	    }
	  /*
  //锁定表
	    $LockSql=" LOCK TABLES $DataIn.ck2_thmain WRITE,$DataIn.ck2_thsheet WRITE,$DataIn.ck9_stocksheet WRITE";
	    $LockRes=@mysql_query($LockSql);
*/

	    //保存主单资料
	    if ($oldMid>0 && $FromPage=="item2_4")
	    {  //来自品检退货。
		    $Mid=$oldMid;
		}
		else
		{
			$inRecode="INSERT INTO $DataIn.ck2_thmain (Id,BillNumber,CompanyId,Attached,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','0','$DateTime','$Operator')";
			$inAction=@mysql_query($inRecode);
			$Mid=mysql_insert_id();
		}

		if($Mid>0)
		{
			$Lens=count($thQTY);
			for($i=0;$i<$Lens;$i++)
			{
				$Id=$thQTY[$i];
				if($Id!="")
				{
					$StuffId=$thStuffId[$i];
					$Qty=$thQTY[$i];
					$Remark=$thRemark[$i];
					// 1 库存足够的情况下加入入库明细
		    if($DataIn=="ac"){
					$addRecodes="INSERT INTO $DataIn.ck2_thsheet SELECT NULL,'$Mid',StuffId,'$Qty','$Remark','0' ,'1','0','$Operator','$DateTime','$Operator','$DateTime','$Date','$Operator'
                     FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' AND tStockQty>=$Qty";
             }else{
					$addRecodes="INSERT INTO $DataIn.ck2_thsheet SELECT NULL,'$Mid',StuffId,'$Qty','$Remark','0' FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' AND tStockQty>=$Qty";
              }
					$addAction=@mysql_query($addRecodes);
					if($addAction)
					{
						$Log.=$StuffId. "退换成功(退换数量 $Qty)";
						// 2 更新在库
						$upCk="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty-$Qty WHERE StuffId='$StuffId' LIMIT 1";
						$upCkAction=mysql_query($upCk);
						if($upCkAction)
						{
							$Log.="  配件 $StuffId 在库扣除成功(数量 $Qty)./n";
						}
						else
						{
							$Log.="  配件 $StuffId 在库扣除失败(数量 $Qty)./n";
							$OperationResult="N";
						}
					}
					else
					{
						$Log.="$StuffId 退换失败(退换数量 $Qty)./n";
						$OperationResult="N";
				    }
				}
			}
		}
		else
		{
			$Log.="退换操作失败./n";
			$OperationResult="N";
		}
		//解锁
/*
		$unLockSql="UNLOCK TABLES";
		$unLockRes=@mysql_query($unLockSql);
*/

        /*************************************/

        if ($OperationResult=="Y")
        { //更新状态
        	$upSql="UPDATE $DataIn.qc_badrecord SET Estate=0 WHERE Id='$upId' AND Estate=1 LIMIT 1";
        	$upAction=@mysql_query($upSql);
        }
    }

    echo json_encode(array($OperationResult, $Log));

?>