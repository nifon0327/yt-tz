<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="产品资料";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		//检查产品资料是否锁定,是否可以删除
		$sResult = mysql_query("SELECT ProductId,cName,TestStandard,dzSign FROM $DataIn.productdata WHERE Id='$Id' 
		AND ProductId NOT IN (SELECT ProductId FROM $DataIn.yw1_ordersheet GROUP BY ProductId )
		AND ProductId NOT IN (SELECT ProductId FROM $DataIn.sale_ordersheet GROUP BY ProductId)",$link_id);
 		if($sRow = mysql_fetch_array($sResult)){//可删除
			$cName=$sRow["cName"];
			$ProductId=$sRow["ProductId"];
			$TestStandard=$sRow["TestStandard"];
			$dzSign=$sRow["dzSign"];
			//执行删除
			$DelSql = "DELETE $DataIn.productdata,$DataIn.pands 
			FROM $DataIn.productdata 
			LEFT JOIN $DataIn.pands ON $DataIn.productdata.ProductId=$DataIn.pands.ProductId
			WHERE $DataIn.productdata.Id='$Id'";
			
			$DelResult = mysql_query($DelSql);
			if($DelResult){
				$y++;
				$Log.="$x - 产品 $cName /$ProductId 删除成功。<br>";
				if ($TestStandard==1){
					$delFile="T".$ProductId.".jpg";
					$FilePath="../download/teststandard/".$delFile;
					if(file_exists($FilePath)){unlink($FilePath);}
					}
					
				if($dzSign==1){
					$CheckFile=mysql_fetch_array(mysql_query("SELECT COUNT(Picture) AS Count 
					FROM $DataIn.product_certification WHERE ProductId='$ProductId'"));
					$Cnt=$CheckFile["Count"];
					for($index=1; $index<=$Cnt; $index++){
						
						$CerFile=$ProductId."_".$index.".pdf";
						echo "CerFile:".$CerFile;
						$CerFilePath="../download/productcer/".$CerFile;
						if(file_exists($CerFilePath)){unlink($CerFilePath);}
					}
					$CerDelSql="DELETE FROM $DataIn.product_certification WHERE ProductId=$ProductId";
					$CerResult=mysql_query($CerDelSql);
					}
					 $delSql="delete from yw7_clientproduct where ProductId=$ProductId";
			        $delResult=mysql_query($delSql);
				}
			else{
				$Log.="<div class='redB'>$x - 产品 $cName /$ProductId 删除失败。</div><br>";
				$OperationResult="N";
				}//if($Del_Result)
			}//end if ($Check_myrow = mysql_fetch_array($Check))
		else{			
			$Log=$Log."<div class='redB'>$x - Id号为 $Id 的产品已有使用记录，不能删除。</div><br>";
			$OperationResult="N";
			}//end if ($Check_myrow = mysql_fetch_array($Check))
		$x++;
		}//end if($Id!="")
	}//end for 
//操作日志
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&CompanyId=$CompanyId&TypeId=$TypeId&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>