<?php
	

	include "../../basic/downloadFileIP.php";
	$POrderId;
	//$POrderId = "201211020402";
	$onEdit = ($onEdit==-1)?0:1;
	$products = array();
	$zuzhuang = "";
	if ($showZZ) {
		$zuzhuang=" or (ST.TypeId=7100) ";
	}
	$checkProduct=mysql_query("SELECT Y.ProductId,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,C.Forshort AS Client,P.cName,P.TestStandard    
        					   FROM $DataIn.yw1_ordersheet Y
        					   LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
        					   LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        					   LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
        					   WHERE Y.POrderId='$POrderId'",$link_id);
    
    								
    while($checkProductRows = mysql_fetch_assoc($checkProduct))
    {    	
	    $ProductId = $checkProductRows["ProductId"];
	    $OrderPO = $checkProductRows["OrderPO"];
	    $PQty = $checkProductRows["PQty"];
	    $Client = $checkProductRows["Client"];
	    $cName = $checkProductRows["cName"];
	    $TestStandard = $checkProductRows["TestStandard"];
	    $PackRemark = $checkProductRows["PackRemark"] == ""?"--":$checkProductRows["PackRemark"];
	    $sgRemark = $checkProductRows["sgRemark"] == ""?"--":$checkProductRows["sgRemark"];
	    $ShipType = $checkProductRows["ShipType"] == ""?"--":$checkProductRows["ShipType"];
	    $Leadtime = $checkProductRows["Leadtime"] == "0000-00-00"?"--":$checkProductRows["Leadtime"];
	    
	   
	    $sListResult = mysql_query("SELECT 
	S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,
	M.Date,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.TypeId,B.Name,C.Forshort,C.Currency,MP.Remark AS Position,ST.mainType,MT.TypeColor ,K.tStockQty ,C.CompanyId
									FROM $DataIn.cg1_stocksheet S
									LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
									LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
									LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType
									LEFT JOIN $DataIn.base_mposition MP ON MP.Id=ST.Position 
									LEFT JOIN $DataPublic.staffmain B ON B.Number=S.BuyerId
									LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId 
									LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
									WHERE S.POrderId='$POrderId'
									and ((ST.mainType in (0,1)) or (ST.mainType = 5 and A.TypeId = 9124) $zuzhuang)
									 ORDER BY ST.mainType",$link_id);
	    
	    while($StockRows = mysql_fetch_assoc($sListResult))
	    {
	    	$scQty="-";
	    $comId = $StockRows["CompanyId"];
	    	$Mid=$StockRows["Mid"];
	    	$thisId=$StockRows["Id"];
	    	$StockId=$StockRows["StockId"];
	    	$StuffCname=$StockRows["StuffCname"];
	    	$Position=$StockRows["Position"]==""?"未设置":$StockRows["Position"];
	    	$Price=$StockRows["Price"];
	    	$Forshort=$StockRows["Forshort"];
	    	$Buyer=$StockRows["Name"];
	    	$BuyerId=$StockRows["BuyerId"];
	    	$OrderQty=$StockRows["OrderQty"];
	    	$StockQty=$StockRows["StockQty"];
	    	$FactualQty=$StockRows["FactualQty"];
	    	$AddQty=$StockRows["AddQty"];
	    	$Date=$StockRows["Date"];
	    	$UnitName=$StockRows["UnitName"]==""?"&nbsp;":$StockRows["UnitName"];
	    	$DeliveryDate=$StockRows["DeliveryDate"];		
	    	$StuffId=$StockRows["StuffId"];
	    	$Picture=$StockRows["Picture"];
	    	$TypeId=$StockRows["TypeId"];
	    	$mainType=$StockRows["mainType"];
	    	$TypeColor=$StockRows["TypeColor"];
	    	$Currency=$StockRows["Currency"];
	    	$tStockQty=$StockRows["tStockQty"];
			$info001 =$StockId;
			
	    	if($FactualQty==0 && $AddQty==0)
	    	{
				$TempColor=3;			//绿色
				$Date="使用库存";
				//$FactualQty="-";$AddQty="-";$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";//$Buyer="-";$Forshort="-";
			}
			else
			{
				if($Date=="")
				{//未下采购单
					if($mainType==1)
					{
						$TempColor=1;		//白色
						$Date="未下采购单";
					}
					else
					{		//统计项目:8000以下黄色，	8000-9000绿色
						if($mainType==3)
						{
							//生产数量
							$scSql=mysql_query("SELECT ifnull(SUM(S.Qty),0) AS scQty
											FROM $DataIn.sc1_cjtj S
											LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
											LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId 
											WHERE 1 AND G.StockId='$StockId' AND D.TypeId=S.TypeId",$link_id); 
							$scQty=mysql_result($scSql,0,"scQty");				
							$TempColor=$OrderQty==$scQty?3:2;
							$Date="生产项目";
						}
						else
						{
							$Date="统计项目";
							$TempColor=3;		//绿色
						}
						//$Date="统计项目";
						$Position="-";
					}	
					$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";
				}
				else
				{//已下采购单
					$TempColor=3;		//绿色
					$ReceiveDate=$StockRows["ReceiveDate"];
					//收货情况				
					$rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet where StockId='$StockId' order by StockId",$link_id);
					$rkQty=mysql_result($rkTemp,0,"Qty");
					$Mantissa=$FactualQty+$AddQty-$rkQty;
                                        
					if ($Mantissa>0)
					{
                		$rkQty=$rkQty; 
                	}
                	else
                	{
                		$rkQty=$rkQty; 
                	}
						
						//可更新交期,如果当前浏览者的ID与采购的ID一致，则可以更新交期
					//if($BuyerId==){}
					if($DeliveryDate=="0000-00-00"){$DeliveryDate="-";}
				}
			}
		
			//备领料情况
			$llQty=0;$llBgColor="";$llEstate="";
			if($mainType==1) 
			{	
				$checkllQty=mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id);
				$llQty=mysql_result($checkllQty,0,"llQty");
				$llQty=$llQty==""?0:$llQty;
				if($llQty>$OrderQty)
				{//领料总数大于订单数,提示出错
					$llBgColor="";
				}
				else
				{
					if($llQty==$OrderQty)
					{//刚好全领，绿色
						$llBgColor="";
					}
					else
					{				//未领完，黄色
						$llBgColor="";
					}
				}
				$llEstate=mysql_result($checkllQty,0,"llEstate");
			}
	    
			
       		
       $StuffColor="#000000";
 switch ($Picture){
           case  1: $StuffColor="#FFA500";break;
           case  2: $StuffColor="#FF00FF";break;
           case  4: $StuffColor="#FFD800";break;
           case  7: $StuffColor="#0033FF";break;
 }
 $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";//_iPad
 $swapDic = array("Right"=>"358FC1-占用");
 $cellIder = "data2-";
 if ($distriStar==true) {
	 if ($dModuleId=="213" ) {
		 $swapDic= array();
	} else 
	 $swapDic = array("Right"=>"FF0000-取消占用");
	 $StarHid = ($llQty>=$OrderQty) ? 0:1;
} else {
		 if($mainType==5)
		{
			 $swapDic = array("Right"=>"358FC1-占用");
			 $info001 = $POrderId;
			 $cellIder = "data22";
			
		} 
       		$StarHid = ($tStockQty>=$llQty && $llQty>=$OrderQty) ? 0:1;
}

	$StuffProp="";
   if ($StuffId==114133 || $StuffId==127622 || $StuffId==129301 || $StuffId==126088 ){
        $StuffProp="gysc1";
   }
   else{
       $PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId='$StuffId' ORDER BY Property",$link_id);
       if($PropertyRow=mysql_fetch_array($PropertyResult)){
            $Property=$PropertyRow["Property"];
			
			
            $StuffProp="gys$Property";     
       }
   }
   $SetColor = "";
    if ($mainType==3){
									         //已完成的工序数量
											$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId' AND C.TypeId='$TypeId'",$link_id));
											$scQty=$CheckscQty["scQty"]==""?0:$CheckscQty["scQty"];
											//$llBgColor=$scQty==$OrderQty?"#009900":"#FF6633";
											$SetColor = "A4A4FF";
											$llQty=number_format($scQty);
									     }
   
   if ($noStar==1) {$StarHid=1;} 
   if ($isred==1) {$cellIder="sxsi";$swapDic=array("no"=>"");}
       $tempdata = array("RowSet"=>array("bgColor"=>"$SetColor"),"Title"=>array("Text"=>"$StuffCname","Color"=>"$StuffColor"),"Col1"=>array("Text"=>"$Position"),"Col2"=>array("Text"=>"$OrderQty"),"Col3"=>array("Text"=>"$tStockQty"),"Col4"=>array("Text"=>"$llQty","Color"=>"#307BB5"),"Col5"=>array("Text"=>"$Forshort"),"Picture"=>"$ImagePath","star"=>"$StarHid","Prop"=>"$StuffProp","Id"=>"$StuffId");
       		//$products[] = array("Tag"=>"aessery","$Date", "$StockId", "$StuffCname", "$OrderQty", "$rkQty", "$tStockQty", "$llQty", "$scQty", "$Buyer", "$Position", "$StuffId", "$Picture");
		if ($onEdit <= 0) {$swapDic=array("Right"=>""); }		$products[] = array("Tag"=>"acessery","data"=>$tempdata,"Args"=>"$info001|$OrderQty|$llQty|$StuffId" ,"CellID"=>"$cellIder",
		
			"onTap"=>array("Value"=>"$Picture","File"=>"$ImagePath"),"onEdit"=>"$onEdit","Swap"=>$swapDic);
	   }
	    
		
		if ($StuffProp == "gys9") {
			
			$findChildren = mysql_query("select AL.StockId,AL.StuffId,AL.OrderQty,
			K.tStockQty,AL.StockQty,pr.Property,A.Picture,A.StuffCname
			from $DataIn.cg1_stuffcombox AL 
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=AL.StuffId
			LEFT JOIN $DataIn.stuffproperty  pr on pr.StuffId=A.StuffId
			LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
			LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType
			LEFT JOIN $DataIn.base_mposition MP ON MP.Id=ST.Position 
			LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=AL.StuffId 
			where AL.POrderId='$POrderId' and AL.mStuffId='$StuffId'  
			;");
			while ($findChildrenRow = mysql_fetch_assoc($findChildren)) {
	
			$StockId = $findChildrenRow["StockId"];
			if (strlen($StockId)==12) {
				$StockId = substr($POrderId,0,2).$StockId;
			}
			$StuffId = $findChildrenRow["StuffId"];
			$tStockQty = $findChildrenRow["tStockQty"];
			$OrderQty = $findChildrenRow["OrderQty"];
			$Picture = $findChildrenRow["Picture"];
			$Property = $findChildrenRow["Property"];
			$StuffCname = $findChildrenRow["StuffCname"];
	
	   		$checkllQty=mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id);
				$llQty=mysql_result($checkllQty,0,"llQty");
				$llQty=$llQty==""?0:$llQty;
      			 $StuffColor="#000000";
 				switch ($Picture){
       			    case  1: $StuffColor="#FFA500";break;
       			    case  2: $StuffColor="#FF00FF";break;
       			    case  4: $StuffColor="#FFD800";break;
       			    case  7: $StuffColor="#0033FF";break;
 				}
 				$ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
				$StarHid=0;
				$swapDic = array("Right"=>"358FC1-占用");
 $cellIder = "data2-";
 if ($distriStar==true) {
	 if ($dModuleId=="213" ) {
		 $swapDic= array("Right"=>"");
	} else 
	 $swapDic = array("Right"=>"FF0000-取消占用");
	 $StarHid = ($llQty>=$OrderQty) ? 0:1;
} else {
		 
       		$StarHid = ($tStockQty>=$llQty && $llQty>=$OrderQty) ? 0:1;
}
				if ($noStar==1) {$StarHid=1;} 
				if ($showRed==1) {$cellIder="sxs";$swapDic=array("no"=>"");}
				 $tempdataa = array("RowSet"=>array("bgColor"=>"$SetColor"),"Title"=>array("Text"=>"$StuffCname","Color"=>"$StuffColor"),"Col1"=>array("Text"=>""),"Col2"=>array("Text"=>"$OrderQty"),"Col3"=>array("Text"=>"$tStockQty"),"Col4"=>array("Text"=>"$llQty","Color"=>"#307BB5"),"Col5"=>array("Text"=>"$Forshort"),"Picture"=>"$ImagePath","star"=>"$StarHid","Prop"=>"gys$Property","Id"=>"$StuffId");
       		//$products[] = array("Tag"=>"aessery","$Date", "$StockId", "$StuffCname", "$OrderQty", "$rkQty", "$tStockQty", "$llQty", "$scQty", "$Buyer", "$Position", "$StuffId", "$Picture");
			 
		$products[] = array("Tag"=>"acessery","data"=>$tempdataa,"Args"=>"$StockId|$OrderQty|$llQty|$StuffId" ,"CellID"=>"$cellIder",
		
			"onTap"=>array("Value"=>"$Picture","File"=>"$ImagePath"),"onEdit"=>"$onEdit","Swap"=>$swapDic);
	   
			}
			
		}
		
    }
    
  
	
?>