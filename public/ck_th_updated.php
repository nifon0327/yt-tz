<?php 


if ($ActionId==17 ){
    $MyPDOEnabled=1;
	include "../model/modelhead.php";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	$Log_Item="退换记录";		//需处理
	$upDataSheet="$DataIn.ck2_thsheet";	//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Date=$Date==""?date("Y-m-d"):$Date;
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	switch ($ActionId){
		case 17:
			$Log_Funtion="审核";		 //审核后修改库存数，审核后不能再操作了	
			$Lens=count($checkid);
			for($i=0;$i<$Lens;$i++){
				$Id=$checkid[$i];	        
			    $myResult=$myPDO->query("CALL proc_ck2_thsheet_updatedestate('$Id',$Operator);");
			    $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
			    $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;
			          
			    $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>";
			    $Log.="</br>"; 
			    $myResult=null;$myRow=null;  
		   }
		   $fromWebPage=$funFrom."_m";
		 break;	
	}
	
}else{
	include "../model/modelhead.php";
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="退换记录";		//需处理
	$upDataSheet="$DataIn.ck2_thsheet";	//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	//步骤3：需处理，更新操作
	$x=1;
	switch($ActionId){
		case 7:
			$Log_Funtion="锁定";	$SetStr="Locks=0";				
			include "../model/subprogram/updated_model_3d.php";		break;
		case 8:
			$Log_Funtion="解锁";	$SetStr="Locks=1";				
			include "../model/subprogram/updated_model_3d.php";		break;
	    case 15:
			$Log_Funtion="退回";	$SetStr="Estate=4,Locks=0";		   
			include "../model/subprogram/updated_model_3d.php";		
			$fromWebPage=$funFrom."_m";	break;	
				
		case 20:
			$Log_Funtion="主退换单更新";
		       $PreFileName1="T".$Mid.".jpg";
			$FilePath="../download/thimg/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			if($Attached!=""){
				$OldFile1=$Attached;
				$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);
				$BillSTR=$uploadInfo1==""?",Attached='0'":",Attached='1'";
				}
			if($BillSTR=="" && $oldAttached==1){//没有上传文件并且已选取删除原文件
				$FilePath1=$FilePath."/$PreFileName1";
				if(file_exists($FilePath1)){
					unlink($FilePath1);
					}
				$BillSTR=",Attached='0'";
				}
				
			
			//图片
			$upSql = "UPDATE $DataIn.ck2_thmain SET Date='$Date' $BillSTR WHERE Id='$Mid'";
			$upResult = mysql_query($upSql);		
			if($upResult && mysql_affected_rows()>0){
				$Log="退换主单资料更新成功.<br>";
				}
			else{
				$Log="<div class='redB'>退换主单资料更新失败! $upSql </div><br>";
				$OperationResult="N";
				}
			break;
			
		default:
			$Log_Funtion="退换数据更新";
			$thSTR="";
			if($Operators>0){	//增加退换数量的条件 在库>=增加的数量
				$thSTR=" and K.tStockQty>=$changeQty";
				}
			$upSql = "UPDATE $upDataSheet T 
			LEFT JOIN $DataIn.ck9_stocksheet K ON T.StuffId=K.StuffId 
			SET T.Qty=T.Qty+$changeQty*$Operators,T.Estate=2 WHERE T.Id=$Id $thSTR";
			$upResult = mysql_query($upSql);		
			if($upResult && mysql_affected_rows()>0){
				$Log="退换数据更新成功.<br>";
				if ($changeQty>0) { 
				    $delSql = "DELETE  FROM $DataIn.ck2_threview  WHERE Mid='$Id' AND Estate=2"; 
				    $delRresult = mysql_query($delSql);
				}
			 }
			else{
				$Log="<div class='redB'>退换数据更新失败!</div><br>";
				$OperationResult="N";
				}
				
			break;
		}
	$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseDate=$chooseDate&CompanyId=$CompanyId";
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	//$IN_res=@mysql_query($IN_recode);
}
include "../model/logpage.php";
?>
  