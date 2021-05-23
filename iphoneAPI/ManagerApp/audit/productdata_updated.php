<?
 $Log_Item="产品资料审核"; 
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 
 $AuditStr="";
 switch($ActionId){
    case "PASS":
            $updateSql="UPDATE $DataIn.productdata SET Estate=1 WHERE Id=$Id";
            $UpdateResult = mysql_query($updateSql,$link_id);
            if($UpdateResult)
            {
	            	$insql="INSERT INTO  $DataIn.productdata_audit   (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,1,$Operator,$Operator,'$DateTime','$curDate')";
				    mysql_query($insql,$link_id);
		    
	                $Log="<div class=greenB>ID:$Id $Log_Item 成功!</div><br>";
	                $OperationResult="Y";
	                
	                $checkResult=mysql_query("SELECT CompanyId FROM  $DataIn.productdata  WHERE CompanyId IN(1064,1066) AND Id='$Id' GROUP BY CompanyId",$link_id);
					while($checkRows = mysql_fetch_array($checkResult)){
				           $CompanyId=$checkRows["CompanyId"];
				          	//更新xml文件
							include "../../public/productdata_toxml.php"; 
			        }
        
			        //更新pandsCharge表的状态
			        $pandsChargeUpdateSql = "update $DataIn.pandscharge A 
			        						 Left Join $DataIn.productdata B On A.ProductId = B.ProductId
			        						 Set A.Estate = '0' 
			        						 Where B.Id='$Id'";
			       
			        mysql_query($pandsChargeUpdateSql);
			        
                } 
            else{
                $Log="<div class=redB>ID:$Id $Log_Item 失败! </div><br>$updateSql</br>";   
                }
              break;
       case "BACK":
	        $updateSql="UPDATE $DataIn.productdata SET ReturnReasons='$ReturnReasons',Estate=3 WHERE Id=$Id";
            $UpdateResult = mysql_query($updateSql,$link_id);
            if($UpdateResult)
            {
	            	$insql="INSERT INTO  $DataIn.productdata_audit   (Sid,Estate,Checker,creator,created,Date,Reason) VALUES  ($Id,2,$Operator,$Operator,'$DateTime','$curDate','$ReturnReasons')";
				    mysql_query($insql,$link_id);
	                $Log="<div class=greenB>ID:$Id $Log_Item 退回成功!</div><br>";
	                $OperationResult="Y";
                } 
            else{
                    $Log="<div class=redB>ID:$Id $Log_Item 退回失败! </div><br>$updateSql</br>";   
                }

        break;

   }
?>