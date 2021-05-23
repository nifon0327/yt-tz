<?php 
 /*
 功能模块:毛利计算 
 传入参数:$ProductId;
 输出参数:$Profit 
 */
 $profitRMB=0;
 if ($ProductId!=""){
		//配件成本计算:只计算需要采购的部分
		include_once(	"../../model/subprogram/sys_parameters.php");//$HzRate
		$BuyRmbSum=$BuyHzSum=0;//初始化
		$CostResult=mysql_query("SELECT A.Relation,S.Price,C.Rate,D.Currency,F.Rate AS ProductRate   
		        FROM $DataIn.Pands A
		        LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
		        LEFT JOIN $DataIn.stuffdata S ON S.StuffId=A.StuffId 
		        LEFT JOIN $DataIn.trade_object D ON D.CompanyId=B.CompanyId
		        LEFT JOIN $DataPublic.currencydata C ON D.Currency=C.Id	
		        LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId  
		        LEFT JOIN $DataIn.trade_object DA ON DA.CompanyId=P.CompanyId
                LEFT JOIN $DataPublic.currencydata F ON F.Id=DA.Currency
		        WHERE 1  AND A.ProductId='$ProductId' GROUP BY A.Id ",$link_id);
		if($cbRow= mysql_fetch_array($CostResult)){
		             $ProductRate=$cbRow["ProductRate"];
		             $saleRMB=$Price*$ProductRate;
		        do{
		                $Relation=$cbRow["Relation"];
		                $Price=$cbRow["Price"];
		                $Rate=$cbRow["Rate"];
		                $Currency=$cbRow["Currency"];
		                $OppositeQTY=explode("/",$Relation);
		                $thisRMB=$OppositeQTY[1]!=""?sprintf("%.4f",$Rate*$Price*$OppositeQTY[0]/$OppositeQTY[1]):sprintf("%.4f",$Rate*$Price*$OppositeQTY[0]);	//此配件的成本
				       $BuyRmbSum+=$thisRMB;//成本累加
				       $BuyHzSum=$Currency==1?($BuyHzSum+$thisRMB):$BuyHzSum;				//自购成本累加
		                }while ($cbRow= mysql_fetch_array($CostResult));
		   }
		$profitRMB=($saleRMB-$BuyRmbSum-$BuyHzSum*$HzRate);        
}

 ?>