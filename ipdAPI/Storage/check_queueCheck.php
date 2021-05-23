<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	include_once "check_function.php";
	
	$GysId = $_POST["companyId"];
	$StuffId = $_POST["stuffId"];
	$BillNumber = $_POST["billNumber"];
	$Login_P_Number = $_POST["operator"];
	$ipadTag = "yes";
	
	$result = "N";
	
	$checkSql=mysql_query("SELECT D.Picture,D.CheckSign,T.AQL FROM $DataIn.stuffdata D 
                           LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
                           WHERE D.StuffId='$StuffId' LIMIT 1",$link_id);
                           
    if($checkRow=mysql_fetch_array($checkSql))
    {//追加
		$Picture=$checkRow["Picture"];
		$CheckSign=$checkRow["CheckSign"];
		$AQL=$checkRow["AQL"];
        
        if($Picture!='1')
        {
        	echo json_encode(array($result, "图档不存在或未审核"));
            break;
        }
        if ($CheckSign==0 && $AQL=="")
        { 
            echo json_encode(array($result, "AQL未设置"));
            break;
        }
              
        $SearchRow="AND S.Estate=2 AND M.CompanyId='$GysId' AND S.StuffId='$StuffId' ";
        if (trim($BillNumber!="")) 
        {
        	$SearchRow.=" AND  M.BillNumber='$BillNumber' ";
        }
             
        $sumResult=mysql_query("SELECT SUM(S.Qty) AS Qty FROM $DataIn.gys_shsheet S
                                LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
                                WHERE 1 
                                $SearchRow 
                                ORDER BY S.Id",$link_id);
             
        if($sumResult && $sumRow = mysql_fetch_array($sumResult))
        {
	    	$Qty=$sumRow["Qty"]; //批量检查总数
                
	    	//取得抽检标准
	    	$checkResult = mysql_query("SELECT L.Ac,L.Re,L.Lotsize,S.SampleSize 
                 					   	FROM $DataIn.qc_levels L
                 					   	LEFT JOIN  $DataIn.qc_lotsize S ON S.Code=L.Code     
                 					   	WHERE L.AQL='$AQL' 
                 					   	AND S.Start<='$Qty' 
                 					   	AND S.End>='$Qty'",$link_id);
                  
            if ($checkRow = mysql_fetch_array($checkResult))
            {
	          	$SampleSize=$checkRow["SampleSize"]; 
	          	$Lotsize=$checkRow["Lotsize"]; 
	          	if ($Lotsize>0)
	          	{
	              	$CheckQty=$Lotsize;
	            }
	            else
	            {
		            $CheckQty=$SampleSize;
		        }
		    } 
		    else
		    {
	          	$CheckQty=$Qty;
	        }
	        //计算批量检查比例
	        $checkScale=$CheckQty/$Qty;
                
	        $Date=date("Y-m-d H:i:s");
     if($DataIn=="ac"){
	        $inSql="INSERT INTO $DataIn.qc_badrecord SELECT NULL,S.Mid,S.StockId,S.StuffId,S.Qty,IF( $checkScale*S.Qty<1,1, $checkScale*S.Qty),'0','$AQL','来自批量品检','0','0','$Date','$Login_P_Number','0',null,null,null,null
                FROM  $DataIn.gys_shsheet S
                LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
                WHERE 1 $SearchRow  ";
}else{
	        $inSql="INSERT INTO $DataIn.qc_badrecord SELECT NULL,S.Mid,S.StockId,S.StuffId,S.Qty,IF( $checkScale*S.Qty<1,1, $checkScale*S.Qty),'0','$AQL','来自批量品检','0','0','$Date','$Login_P_Number' 
                FROM  $DataIn.gys_shsheet S
                LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
                WHERE 1 $SearchRow  ";
}
            
            $inAction=@mysql_query($inSql,$link_id); 
            if($inAction)
            {
	          	$qcResult="来料品检不良记录主表保存成功！"; 
	          	$result = "Y";
	        }
	        else
	        {
	          	$qcResult="来料品检不良记录主表保存失败！"; 
	          	echo json_encode(array($result, $qcResult));
	          	break;
	        }
	        //入库操作
              
	        $rkResult=mysql_query("SELECT S.Id FROM $DataIn.gys_shsheet S  
          						   LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
          						   WHERE 1 $SearchRow 
          						   ORDER BY S.Id",$link_id);
          	while($rkRow = mysql_fetch_array($rkResult))
          	{
	          	$Id=$rkRow["Id"];
	          	include "check_shrk.php";
	          	$qcResult.= "送货单Id:" . $Id. $OperResult; 
	        }
                 
	        $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','来料审核','批量品检',' $qcResult','Y','$Login_P_Number')";
	        $IN_res=@mysql_query($IN_recode);
          
          
	        echo  json_encode(array($result, $qcResult));  
          
          }
             
	}
    
    
  ?>