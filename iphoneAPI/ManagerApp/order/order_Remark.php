<?php 
 /*
 功能模块:订单备注显示
 传入参数:$POrderId
 输出参数:$Remark,$RemarkDate,$RemarkOperator;
 */

 if ($POrderId!=""){
		//客人页面，显示英文备注
		 if ($mModuleId=="Client"){
			      $RemarkResult=mysql_query("SELECT S.Remark,S.Date,M.Name FROM $DataIn.yw2_orderremark S 
			       LEFT JOIN $DataPublic.staffmain  M ON M.Number=S.Operator 
			      WHERE S.POrderId='$POrderId' AND S.Type=1 ORDER BY S.Id DESC LIMIT 1",$link_id);
			      if($RemarkRow=mysql_fetch_array($RemarkResult)){
			             $Remark=$RemarkRow["Remark"];
			             $RemarkDate=$RemarkRow["Date"];
		                $RemarkDate=GetDateTimeOutString($RemarkDate,'');
	                    $RemarkOperator=$RemarkRow["Name"];
			     }
		}
		else{
		    if ($Remark==""){
				    $RemarkResult=mysql_query("SELECT S.Remark,S.Date,M.Name FROM $DataIn.yw2_orderremark S 
				    LEFT JOIN $DataPublic.staffmain  M ON M.Number=S.Operator 
				    WHERE S.POrderId='$POrderId' AND S.Type=2  ORDER BY S.Id DESC LIMIT 1",$link_id);//AND TRIM(S.Remark)!=''
				    if($RemarkRow=mysql_fetch_array($RemarkResult)){
				             $Remark=$RemarkRow["Remark"];
				             $RemarkDate=$RemarkRow["Date"];
			                 $RemarkDate=GetDateTimeOutString($RemarkDate,'');
		                     $RemarkOperator=$RemarkRow["Name"];
				      }
			 }
		}
}
 ?>