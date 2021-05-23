<?php  
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="采购单扣款资料";			//需处理
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
$x=1;
$TotalAmount=0;
//保存主单资料
$inRecode="INSERT INTO $DataIn.cw15_gyskkmain (Id,BillNumber,CompanyId,Date,TotalAmount,BillFile,Picture,Remark, Estate,Locks,Operator) VALUES (NULL,'$BillNumber','$CompanyId','$KKDate','0','1','0','$Remark','1','0','$Operator')";
//echo $inRecode;
$inAction=mysql_query($inRecode);
$Mid=mysql_insert_id();
if($inAction && mysql_affected_rows()>0){
       $Log.="采购单扣款主单添加成功!<br>";
       if($Attached!=""){//有上传文件
		            $FileType=".jpg";
		            $OldFile=$Attached;
		            $FilePath="../download/cgkkbill/";
		            if(!file_exists($FilePath)){
			            makedir($FilePath);
			           }
		             $PreFileName=$BillNumber.$FileType;
		             $AttachedFile=UploadFiles($OldFile,$PreFileName,$FilePath);
		            if($AttachedFile){
			             $Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
		                 $upSql = "UPDATE $DataIn.cw15_gyskkmain SET Picture='1' WHERE Id='$Mid'";
		                 $upResult = mysql_query($upSql);		
			            }
		       else{
			          $Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
			           $OperationResult="N";			
			          }
            }
       $valueArray=explode("|",$AddIds);
       $Count=count($valueArray);
       for($i=0;$i<$Count;$i++){
	        $valueTemp=explode("!",$valueArray[$i]);
	        $PurchaseID=$valueTemp[0];//采购单号
	        $StockId=$valueTemp[1];//采购单流水号
	        $StuffId=$valueTemp[2];	//配件ID
	        $Price=$valueTemp[3];//单价
			$Qty=$valueTemp[4];	//数量
			$SheetRemark=$valueTemp[5];//扣款原因
	        $Amount=sprintf("%.2f",$Qty*$Price);
			$StuffResult=mysql_query("SELECT StuffCname FROM $DataIn.stuffdata WHERE StuffId='$StuffId'",$link_id);
			$StuffCname=mysql_result($StuffResult,0,"StuffCname");
			
	        $addRecodes="INSERT INTO $DataIn.cw15_gyskksheet (Id, Mid, PurchaseID, StockId, StuffId, StuffName,Qty, Price, Amount,Remark,GoodsId,Kid) VALUES (NULL,'$Mid','$PurchaseID','$StockId','$StuffId','$StuffCname','$Qty','$Price','$Amount','$SheetRemark','0','0')";
	        $addAction=mysql_query($addRecodes);
	        if($addAction){
		          $Log.="$x ---采购单扣款从表添加成功!<br>";
	              }
			  else{
			      $Log.="<div class=redB>$x ---采购单扣款从表添加失败! $addRecodes<br></div>";
				  $OperationResult="N";
			      }
			$TotalAmount+=$Amount;	
			$x++;
		 }
		 if($TotalAmount!=0){
		   $UpdateAmount="Update $DataIn.cw15_gyskkmain SET TotalAmount='$TotalAmount' WHERE Id='$Mid'";
		   $UpdateResult=mysql_query($UpdateAmount);
		     }
		$Id=$Mid;
		include "cw_cgkk_topdf.php";
    }
else{
    $Log.="<div class=redB>采购单扣款主单添加失败! $inRecode <br></div>";
    $OperationResult="N";
    }
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>