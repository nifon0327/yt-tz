<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	include_once "check_function.php";
	
	$Id = $_POST["Id"];
	//$Id = "297703";

	$CheckQty = $_POST["CheckQty"];
	//$CheckQty = "1020";

	$CheckAQL = $_POST["CheckAQL"];
	//$CheckAQL = "";

	$ReQty = $_POST["ReQty"];

	$sumQty = $_POST["SumQty"];
	//$sumQty = 0;

	$otherCause = $_POST["OtherCause"];
	$causes = $_POST["cause"];
	$badQtys = $_POST["badQty"];
	
	$CauseId = explode("^", $causes);
	$badQty = explode("^", $badQty);
	
	$otherbadQty = $_POST["otherbadQty"];
	$otherCause = $_POST["OtherCause"];
	
	$Login_P_Number = $_POST["Login_P_Number"];
	//$Login_P_Number = "11008";
	
	$FromqcCause=1;
	$Date=date("Y-m-d H:i:s");
	$Estate=$sumQty==0?0:1; 
	
	$succeedFlag = "Y";
    //生成主表
    if ($CheckAQL!="") 
    { //抽检
	    $Estate=0;  // 全部入库，不良品不做退换操作
	}
if($DataIn =="ac"){
	$inSql="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty','$sumQty','$CheckAQL','','$Estate','0','$Date','$Login_P_Number','0',null,null,null,null
    FROM  $DataIn.gys_shsheet WHERE Id='$Id' LIMIT 1";
}else{
    	$inSql="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty','$sumQty','$CheckAQL','','$Estate','0','$Date','$Login_P_Number' FROM  $DataIn.gys_shsheet WHERE Id='$Id' LIMIT 1";
}
	$inAction=@mysql_query($inSql,$link_id);
	$Mid=mysql_insert_id();
	if ($Mid>0)
	{
		$qcResult="来料品检不良记录主表保存成功！\n"; 
		if ($sumQty>0)
		{  //有不良品
			$FileType=".jpg";
			$FilePath="../../download/qcbadpicture/";
			if(!file_exists($FilePath))
			{
				makedir($FilePath);
			}               
			$counts=count($badQty);
			for ($i=0;$i<$counts;$i++)
			{
				if ($badQty[$i]>0)
				{
					//生成明细表
					$insheetSql="INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$Mid', '$CauseId[$i]', '$badQty[$i]', '','0')";
					$insheetAction=mysql_query($insheetSql,$link_id);
					if (!$insheetAction)
					{
						$qcError=1;
						break;
					}
					else
					{
	                	$Sid=mysql_insert_id();
	                	//上传不良图片
	                	$fileName = "fileinput".$i;
	                	
	                	if ($_FILES[$fileName]["tmp_name"])
	                	{
		                	$PreFileName="Q".$Sid.$FileType;	
		                	$copymes=copy($_FILES[$fileName]["tmp_name"],"$FilePath" . "$PreFileName"); 
		                	if($copymes)
		                	{
			                //更新刚才的记录
			            		$sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='1' WHERE Id=$Sid";
			            		$result = mysql_query($sql);
			            	}
			            	else
			            	{
				            	$qcResult.="不良图片上传失败！\n";			
				            }
				        }
				    }	
                } 
            }//end for
            //有其它不良原因
            if ($otherbadQty>0)
            {
	        	//生成明细表
	        	$insheetSql="INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$Mid', '-1', '$otherbadQty', '$otherCause','0')";
	        	$insheetAction=@mysql_query($insheetSql,$link_id);
	        	if (!$insheetAction)
	        	{
		        	$qcError=1;
		        	break;
		        }
		        else
		        {
	            	$Sid=mysql_insert_id();
	            	//上传不良图片
	            	if ($otherfileinput)
	            	{
		            	$PreFileName="Q".$Sid.$FileType;	
		            	$copymes=copy($_FILES["otherfileinput"]["tmp_name"],"$FilePath" . "$PreFileName"); 
		            	if($copymes)
		            	{
			        		//更新刚才的记录
			        		$sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='1' WHERE Id=$Sid";
			        		$result = mysql_query($sql);
			        	}
			        	else
			        	{
	                    	$qcResult.="不良图片上传失败！\n";			
	                    }
	                }
	            }
		    }
                        
            if  ($qcError==1) 
            {
            	$succeedFlag = "N";
            	$qcResult.="来料品检不良明细记录保存失败！\n";
            } 
            else
            { 
            	$qcResult.="来料品检不良明细记录保存成功！\n";
            }
         }
     }
     else
     {
	     $qcError=1;$qcResult="来料品检不良记录保存失败！\n"; 
	     $succeedFlag = "N"; 
     }
        
    if ($CheckAQL=="")
    {       
    	//全检审核，记录入库
        $openResult = shRk($Id, $DataIn, $link_id, $Login_P_Number); 
        if($openResult == "配件入库失败")
        {
	        $succeedFlag = "N";
        }
        else
        {
        	if(!isCustomerSupplier($Id, $DataIn, $link_id))
        	{
	        	autoPayment($Id, $DataIn, $link_id, $Log);
	        }
        }

        
    }
    else
    {    
    	//抽检，要分允收？拒收？
        if ($sumQty==0 || $sumQty<$ReQty)
        {
            $openResult = shRk($Id, $DataIn, $link_id, $Login_P_Number);  //允收
            if($openResult == "配件入库失败")
            {
	        	$succeedFlag = "N";
	        }
	        else
	        {
	        	if(!isCustomerSupplier($Id, $DataIn, $link_id))
	        	{
		        	autoPayment($Id, $DataIn, $link_id, $Log);
		        }
	        }
        }
        else
        {
            $openResult = shBack($Id, $DataIn, $link_id); ////抽检拒收，全部退回不入库。
            if($openResult == "配件入库失败")
            {
	            $succeedFlag = "N";
            }
            $openResult = $openResult;
        }
    }
    
    echo json_encode(array($succeedFlag ,$openResult." ".$qcResult));
		
	
?>