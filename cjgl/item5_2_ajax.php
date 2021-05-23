<?php   
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="物料入库数据";			//需处理
$Log_Funtion="数据更新";
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch($ActionId){
	/*case 1://新增入库数据
	   $filename=$_FILES["fileinput"]["name"]; 
	   if(($BillNumber!="") &&  ($filename!="")){   //单号不为空，且上传文件有效
			//取得原名 
			
			//取得扩展名 
			$file_ext=explode(".",$filename); 
			$file_ext=$file_ext[count($file_ext)-1]; 
			$file_ext=strtolower($file_ext); 
			//创建一个新的文件名 
			//$newname=date("YmdHis").".".$file_ext; 
			$newname=$BillNumber.".".$file_ext;
			//从缓存中把文件复制到目标地址 
			//copy($_FILES["fileinput"]["tmp_name"],"目标地址"."新文件名"); 
			$FilePath="../download/deliverybill/";
			$copymes=copy($_FILES["fileinput"]["tmp_name"],"$FilePath"."$newname"); 
			if($copymes){
					$Log.="上传文件成功 $newname <br>";
				}
			else{
					$Log.="上传文失败 $newname <br>";
				}		  
	   }
	   
	  
$inRecode="INSERT INTO $DataIn.ck1_rkmain (Id,BillNumber,CompanyId,Remark,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId2','$Remark','0','$rkDate','$Operator')";
      $inAction=@mysql_query($inRecode);
      $Mid=mysql_insert_id();
     //分割字符串
      $valueArray=explode("|",$AddIds);
      $Count=count($valueArray);
      for($i=0;$i<$Count;$i++){
	    $valueTemp=explode("!",$valueArray[$i]);
	    $StockId=$valueTemp[0];
	    $StuffId=$valueTemp[1];	
	    $Qty=$valueTemp[2];	
	   // 1 加入入库明细
	   $addRecodes="INSERT INTO $DataIn.ck1_rksheet (`Mid`, `sPOrderId`, `StockId`, `StuffId`, `Price`, `Qty`, `llQty`, `llSign`, `gys_Id`, `Type`, `Locks`, `Estate`) VALUES (NULL,'$Mid','0','$StockId','$StuffId','$Price','$Qty','0','1','0','1','0','1')";
	    $addAction=@mysql_query($addRecodes);
	if($addAction){
		$Log.="$StockId 入库成功(入库数量 $Qty).<br>";

		// 3 入库状态:有入库则2，最后才统一更新状态?
		$uprkSign="UPDATE $DataIn.cg1_stocksheet SET rkSign=(CASE 
    		WHEN 
			FactualQty+AddQty>(
							SELECT SUM( Qty ) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId = '$StockId'
						 ) THEN 2
    			ELSE 0 END) WHERE StockId='$StockId'";
		$upRkAction=mysql_query($uprkSign);	
		if($upRkAction){
			$Log.="&nbsp;&nbsp;&nbsp;&nbsp;需求单 $StockId 的入库标记更新成功.<br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;需求单 $StockId 的入库标记更新失败. $uprkSign </div><br>";
			}
		}
	else{
		$Log.="<div class='redB'>$StockId 入库失败. $addRecodes </div><br>";
		$OperationResult="N";
		}
	}

     $alertLog=$Log_Item . "数据保存成功";$alertErrLog=$Log_Item . "数据保存失败";
    break;*/
	case 21://更新入库数量
		   $upDataSheet="$DataIn.ck1_rksheet";	
		   $rkSTR="";
		   if($Operators<0){	//减少入库数量的条件 在库>=减少的数量
				$rkSTR=" and K.tStockQty>=$changeQty";
		   }
	       $upSql = "UPDATE $upDataSheet R 
			         LEFT JOIN $DataIn.ck9_stocksheet K ON R.StuffId=K.StuffId 
			         SET R.Qty=R.Qty+$changeQty*$Operators   
			         WHERE R.Id=$Id $rkSTR";
			$upResult = mysql_query($upSql);		
			if($upResult && mysql_affected_rows()>0){
				 echo "Y";
				 $Log="<div class=greenB>入库单:" . $Id . "更新入库数量成功!</div><br>";
				$uprkSign="UPDATE $DataIn.cg1_stocksheet SET rkSign=(CASE 
			    WHEN (SELECT SUM(Qty) AS Qty FROM $upDataSheet WHERE StockId = '$StockId')>0 THEN 2
			    ELSE 1 END) WHERE StockId='$StockId'";
		        $upRkAction=mysql_query($uprkSign);
				}
			else{
				echo "N";
			    $Log="<div class=redB>入库单:" . $Id . "更新入库数量失败!</div><br>";
	            $OperationResult="N";
				}
		break;
	 case 20://主入库单更新
		$upSql = "UPDATE $DataIn.ck1_rkmain 
		SET Date='$Date',BillNumber='$BillNumber',Remark='$Remark' WHERE Id='$Mid'";
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			echo "Y";
			}
		else{
			echo "N";
			}
		break;
	}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

if ($ActionId==1){
	if ($OperationResult=="N"){
       echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertErrLog');</script>";
      }
   else{
	  echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertLog');parent.closeWinDialog();parent.ResetPage(1,5);</script>";
   }
 }
?>