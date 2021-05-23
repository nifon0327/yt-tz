<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$BillNumber = $_POST["BillNumber"];
	$CompanyId2 = $_POST["CompanyId"];
	$BuyerId = $_POST["BuyerId"];
	$Remark = $_POST["Remark"];
	$rkDate = $_POST["RkDate"];
	$Operator = $_POST["Operator"];
	$AddIds = $_POST["StuffInfomation"];
	
	header("pragma:no-cache");
	$Log_Item="物料入库数据";			//需处理
	$Log_Funtion="数据更新";
	$Date=date("Y-m-d");
	$DateTime=date("Y-m-d H:i:s");
	$OperationResult="Y";


    //保存主单资料
	// add by zx 20110823 上传文件
	$filename=$_FILES["fileinput"]["name"]; 
	if(($BillNumber!="") &&  ($filename!=""))
	{   //单号不为空，且上传文件有效
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
		$FilePath="../../download/deliverybill/";
		@$copymes=copy($_FILES["fileinput"]["tmp_name"],"$FilePath"."$newname"); 
		if($copymes)
		{
			$Log.="上传文件成功 $newname\n";
		}
		else
		{
			$Log.="上传文失败 $newname\n";
		}		  
	}
	  
	$succeed = "Y";
	$inRecode="INSERT INTO $DataIn.ck1_rkmain (Id,BillNumber,CompanyId,BuyerId,Remark,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId2','$BuyerId','$Remark','0','$rkDate','$Operator')";
    $inAction=mysql_query($inRecode);
    $Mid=mysql_insert_id();
     //分割字符串
     $valueArray=explode("|",$AddIds);
	 $Count=count($valueArray);
	 for($i=0;$i<$Count;$i++)
	 {
		 $valueTemp=explode("!",$valueArray[$i]);
		 $StockId=$valueTemp[0];
		 $StuffId=$valueTemp[1];	
		 $Qty=$valueTemp[2];	
		 // 1 加入入库明细
		$addRecodes="INSERT INTO $DataIn.ck1_rksheet (Id,Mid,StockId,StuffId,Qty,Locks) VALUES (NULL,'$Mid','$StockId','$StuffId','$Qty','0')";
		$addAction=@mysql_query($addRecodes);
		if($addAction)
		{
			$Log.="$StockId 入库成功(入库数量 $Qty).\n";
			// 2 更新在库
			$upCk="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty+$Qty WHERE StuffId='$StuffId' LIMIT 1";
			$upCkAction=mysql_query($upCk);		
			if($upCkAction)
			{
				$Log.="配件 $StuffId 在库入库成功(入库数量 $Qty).\n";
			}
			else
			{
				$Log.="配件 $StuffId 在库入库失败(入库数量 $Qty). $upCk \n";
			
			}
				// 3 入库状态:有入库则2，最后才统一更新状态?
			$uprkSign="UPDATE $DataIn.cg1_stocksheet SET rkSign=(CASE 
						   WHEN 
						   FactualQty+AddQty>(SELECT SUM( Qty ) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId = '$StockId'
						   ) THEN 2
						   ELSE 0 END) WHERE StockId='$StockId'";
			$upRkAction=mysql_query($uprkSign);	
			if($upRkAction)
			{
				$Log.="需求单 $StockId 的入库标记更新成功.\n";
			}
			else
			{
				$Log.="需求单 $StockId 的入库标记更新失败. $uprkSign \n";
			}
		}
		else
		{
			$Log.="$StockId 入库失败. $addRecodes \n";
			$OperationResult="N";
		}
	}
     
    echo json_encode(array("$OperationResult", "$Log"));
    
    //步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);

	
	
?>