<?php 
//电信-zxq 2012-08-01
/*
MC、DP共享代码
*/
//步骤1：初始化参数、页面基本信息及CSS、javascrip函数
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤2：
$Log_Item="免抵退税明细";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;
$y=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
	
	
	      $sheetResult = mysql_query("select M.TaxNo,M.Id From  $DataIn.cw14_mdtaxmain M
								   WHERE M.Id='$Id'  ",$link_id);
			if($sheetRow = mysql_fetch_array($sheetResult)){
			        
			       $TaxNo=$sheetRow["TaxNo"];
				   }
			
	
		   //===================================删除免抵退税主信息
				$delmdtaxSql="DELETE FROM $DataIn.cw14_mdtaxmain WHERE Id='$Id' LIMIT 1";
				//echo $delmdtaxSql;
				$delmdtaxRresult = mysql_query($delmdtaxSql);
				if($delmdtaxRresult && mysql_affected_rows()>0){
					    $Log.="&nbsp;&nbsp; $x - ID为 $ Id 的记录删除成功.<br>";
					    //============删除报关费用
					    $deldecSql="DELETE FROM $DataIn.cw14_mdtaxsheet WHERE TaxNo='$TaxNo'";
					    //echo $deldecSql;
					    if($deldecRresult = mysql_query($deldecSql))
						$Log.="报关费用记录已做清除处理.<br>";
					    //==============删除行政费用
					    $delotherSql="delete from $DataIn.cw14_mdtaxfee where TaxNo='$TaxNo'";
					    if($delotherResult = mysql_query($delotherSql))
					    $Log.="行政费用记录已做清除处理.<br>";
					}
				else{
					$Log.="&nbsp;&nbsp; $x - 报关单号为 $DeclarationNo 的单删除失败.$delOrderSql<br>";
					}		
       }
	}

//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw14_customsmain,$DataIn.cw14_customssheet");
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>