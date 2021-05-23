<?php   
        $DtateTemp=date("Y");
  
	    $maxSql = mysql_query("SELECT MAX(BillNumber) AS BillNumber 
	    FROM $DataIn.ck2_thmain WHERE BillNumber LIKE '$DtateTemp%'",$link_id);			
	     $BillNumberTemp=mysql_result($maxSql,0,"BillNumber");
	     if($BillNumberTemp){
		     $BillNumber=$BillNumberTemp+1;
		   }
	     else{
		    $BillNumber=$DtateTemp."00001";//默认
		 }
		  
	   //保存主单资料
        $inRecode="INSERT INTO $DataIn.ck2_thmain (Id,BillNumber,CompanyId,Attached,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','0','$DateTime','$Operator')";
        //echo $inRecode;
        $inAction=@mysql_query($inRecode);
        $Mid=mysql_insert_id();
	    if($Mid>0){
		   $Lens=count($thQTY);
		   $EstateSTR=$EstateSTR==""?1:$EstateSTR;
		   for($i=0;$i<$Lens;$i++){
			 $Id=$thQTY[$i];
			 if($Id!=""){
			    $StuffId=$thStuffId[$i];
			    $Qty=$thQTY[$i];
			    $Remark=$thRemark[$i];
	     	    $thisPicture=$Picture[$i];	
			    $addRecodes="INSERT INTO $DataIn.ck2_thsheet(Id,Mid,StuffId,Qty,Remark,ReturnReason,
			    Picture,Estate,Locks,Operator,Date,creator,created)
			    SELECT NULL,'$Mid',StuffId,'$Qty','$Remark','','0','$EstateSTR','0',
			    '$Login_P_Number',CURDATE(),'$Login_P_Number',NOW() 
			    FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' AND tStockQty>=$Qty";
			    $addAction=@mysql_query($addRecodes);
	            $thisId=mysql_insert_id();
				if($addAction){
					$Log.=$StuffId. "退换成功(退换数量 $Qty).<br>";
		            if($thisPicture!=""){
		                $FileType=".jpg";
		              	$OldFile=$thisPicture;
		              	$FilePath="../download/thimg/";
		                if(!file_exists($FilePath)){
		                    makedir($FilePath);
		                }
		                $PreFileName="T".$thisId.$FileType;
		                $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		                if($Attached){
		                       $sql = "UPDATE $DataIn.ck2_thsheet  SET Picture='1' WHERE Id=$thisId";
		                       $result = mysql_query($sql);
		                    }
		                }
		
					}
				else{
					$Log.="<div class='redB'>$StuffId 退换失败(退换数量 $Qty).</div><br>";
					$OperationResult="N";
					}		
				}
			}
		}
	else{
		$Log.="<div class='redB'>退换操作失败.</div><br>";
		$OperationResult="N";
		}
?>
