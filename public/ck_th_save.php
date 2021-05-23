<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="退换资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$chooseDate=substr($rkDate,0,7);
$ALType="CompanyId=$CompanyId";
//编号计算:-00001
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
$inRecode="INSERT INTO $DataIn.ck2_thmain (Id,BillNumber,CompanyId,Attached,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','0','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();
if($Mid>0){
	$Lens=count($thQTY);
	for($i=0;$i<$Lens;$i++){
		$Id=$thQTY[$i];
		if($Id!=""){
			$StuffId=$thStuffId[$i];
			$Qty=$thQTY[$i];
			$Remark=$thRemark[$i];
     	    $thisPicture=$Picture[$i];	
			$addRecodes=  "INSERT INTO $DataIn.ck2_thsheet 
			SELECT NULL,'$Mid',StuffId,'$Qty','$Remark','','0','1','0',0,'$Operator','$DateTime','$Operator','$DateTime','$DateTime','$Operator' 
			FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' AND tStockQty>=$Qty";
			$addAction=@mysql_query($addRecodes);
           $thisId=mysql_insert_id();
			if($addAction){
				$Log.="$StuffId 退换成功(退换数量 $Qty).<br>";
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
	                        	$Log.="$Attached 图片上传成功.<br>";
	                                 $sql = "UPDATE $DataIn.ck2_thsheet  SET Picture='1' WHERE Id=$thisId";
	                             $result = mysql_query($sql);
	                        }
	                  }				
					
				}
			else{
				$Log.="<div class='redB'>$StuffId 退换失败(退换数量 $Qty).$addRecodes</div><br>";
				$OperationResult="N";
				}		
			}
		}
	}
else{
	$Log.="<div class='redB'>退换操作失败.</div><br>";
	$OperationResult="N";
	}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
