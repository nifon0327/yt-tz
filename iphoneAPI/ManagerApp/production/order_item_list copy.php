<?php
	//$POrderId;
	$noBLsign = $info[1] && "YBL"== $info[1];
		include "../../basic/downloadFileIP.php";
	$onEdit = ($onEdit==-1)?0:1;

	$operName = $DzzSign == true ? "补料" : "车间领料";
	$operColor = "358FC1";
	if ($dfpPage==true) {
		$operColor = "ff0000";
		$operName = "取消占用";
	}
	   //订单产品对应的配件信息
			$products = array();
            	$checkStockSql=mysql_query("SELECT G.OrderQty,G.StockId,K.tStockQty,D.StuffId,D.StuffCname,D.Picture,D.TypeId,F.Remark,M.Name,P.Forshort,U.Name AS UnitName ,T.mainType,S.ProductId
										FROM $DataIn.cg1_stocksheet G 
										LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
										LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
										LEFT JOIN $DataPublic.staffmain M ON M.Number=G.BuyerId 
										LEFT JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
										LEFT JOIN $DataIn.base_mposition F ON F.Id=D.SendFloor
										LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
										LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
										LEFT JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId  
										WHERE G.POrderId='$POrderId' 
										AND ((T.mainType in (0,1)) or (T.mainType = 5 and D.TypeId = 9124))
										ORDER BY D.SendFloor",$link_id);


				while($checkStockRow=mysql_fetch_array($checkStockSql))
				{
					$llCount=0;
					$Name=$checkStockRow["Name"];
					$Forshort=$checkStockRow["Forshort"];
					$StockId=$checkStockRow["StockId"];
					$StuffId=$checkStockRow["StuffId"];
					$StuffCname=$checkStockRow["StuffCname"];
					$UnitName=$checkStockRow["UnitName"];
					$Picture=$checkStockRow["Picture"];
					  $ProductIdde = $checkStockRow["ProductId"];
       $StuffColor="#000000";
 switch ($Picture){
           case  1: $StuffColor="#FFA500";break;
           case  2: $StuffColor="#FF00FF";break;
           case  4: $StuffColor="#FFD800";break;
           case  7: $StuffColor="#0033FF";break;
 }
					$tStockQty=$checkStockRow["tStockQty"];
					$OrderQty=$checkStockRow["OrderQty"];
					$Remark=$checkStockRow["Remark"];
					$TypeId = $checkStockRow["TypeId"];
					$mainType = $checkStockRow["mainType"];


					$swapDic = array("Right"=>$noBLsign?"":"$operColor-$operName");

					$cellId = $noBLsign?"ccea11":"acce11";
					$hasPrint = '0';
					 if($mainType==5 && $dfpPage!=true)
		{
			$checkTM = mysql_query("SELECT SUM(A.suma) AS suma FROM (
			 SELECT sum(1)  as suma  
		        FROM $DataIn.cg1_stuffunite U 
                LEFT JOIN  $DataIn.stuffdata S  ON S.StuffId=U.uStuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
                LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId  
                WHERE U.ProductId='$ProductIdde' AND U.StuffId='$StuffId' 
               UNION ALL 
                SELECT sum(1)  as suma  
		        FROM $DataIn.pands_unite U 
                LEFT JOIN  $DataIn.stuffdata S  ON S.StuffId=U.uStuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
                LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId  
                WHERE U.ProductId='$ProductIdde' AND U.StuffId='$StuffId')A");
				if ($checkTMRow = mysql_fetch_assoc($checkTM)) {
					if ($checkTMRow["suma"]>0) {
					$hasPrint = 1;
					if ($noBLsign!=true) {
					$swapDic = array("Left"=>"#009933-打印","Right"=>"$operColor-$operName");
			 		$info001 = $POrderId;
					 $cellId = "no";
					} else {
						$swapDic = array("Left"=>"#009933-打印");
					 $info001 = $POrderId;
					 $cellId = "nono";
					}
					}
				}


		}

$StarHid = "0";
		        	$checkllQty=mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id);
					$checkllQtyRow = mysql_fetch_assoc($checkllQty);
					$llQty = $checkllQtyRow["llQty"];
					$llEstate = $checkllQtyRow["llEstate"];
					$llQty=$llQty==""?0:$llQty;

					$OrderQty -= $llQty;


					$StarHid = $llEstate>0?"0":"1";
					/*
					if($llOperator && $llOperator!="" && $OrderQty <= 0)
					{

			        	$llDateTime = $llCheckRow["Date"]." ".$llCheckRow["Time"];
						$llDateTime = substr($llDateTime, 0, 16);
						$hadLl = "yes";
						$StarHid = 1;
					}
					$llCheckSql = "SELECT B.Date, B.Time, C.Name
		        			   	   FROM $DataIn.ck5_llsheet A
							   	   LEFT JOIN $DataIn.ck5_llmain B ON B.id = A.Mid
							   	   LEFT JOIN $DataPublic.staffmain C ON C.Number = B.Operator
							   	   WHERE A.StockId =  '$StockId'  Limit 1";
					$llCheckResult = mysql_query($llCheckSql);
					$llCheckRow = mysql_fetch_assoc($llCheckResult);
					$llOperator = $llCheckRow["Name"];
					$llDateTime = "";
						$blInfomationSql = "SELECT A.Date, C.Name
		        						FROM $DataIn.yw9_blmain A
										LEFT JOIN $DataIn.ck5_llsheet B ON A.Id = B.Pid
										LEFT JOIN $DataPublic.staffmain C ON C.Number = A.Operator
										Where B.StockId = '$StockId'
										Order By A.Date Desc
										Limit 1";

					$blInfomationResult = mysql_query($blInfomationSql);
					$blInfomationRow = mysql_fetch_assoc($blInfomationResult);
					$blDate = substr($blInfomationRow["Date"], 0, 16);
					$blOperator = $blInfomationRow["Name"];

					if($TasksQty==1 && $Forshort=="研砼条码")
					{
			        	$Remark = "";
					}
					else if($Forshort=="研砼条码" && $TasksQty>0)
					{
			        	$Remark = "printed";
					}
						//是否已经领料
					$StarHid = "0";
					$hadLl = "no";
					$llCheckSql = "SELECT B.Date, B.Time, C.Name
		        			   	   FROM $DataIn.ck5_llsheet A
							   	   LEFT JOIN $DataIn.ck5_llmain B ON B.id = A.Mid
							   	   LEFT JOIN $DataPublic.staffmain C ON C.Number = B.Operator
							   	   WHERE A.StockId =  '$StockId' Limit 1";
					$llCheckResult = mysql_query($llCheckSql);
					$llCheckRow = mysql_fetch_assoc($llCheckResult);
					$llOperator = $llCheckRow["Name"];
					$llDateTime = "";
					if($llOperator)
					{
			        	$llDateTime = $llCheckRow["Date"]." ".$llCheckRow["Time"];
						$llDateTime = substr($llDateTime, 0, 16);
						$hadLl = "yes";
						$StarHid = 1;
					}

					if($canLlState == "yes" && $llQty > 0)
					{
			        	$hadLl = "no";
						$StarHid = 0;
					}

					//引入isOccupy,isGet区分配件当前状态
					$isOccupy = "no";
					$isGet = "no";
					$totleEstate = "";
					$stuffStateSql = mysql_query("Select Estate From $DataIn.ck5_llsheet Where StockId = '$StockId'");

					if(mysql_num_rows($stuffStateSql) > 0)
					{
						$isOccupy = "yes";
						while($stuffStateResult = mysql_fetch_assoc($stuffStateSql))
						{
							$totleEstate += $stuffStateResult["Estate"];
						}
						if($totleEstate != "" && $totleEstate == 0)
						{
							//$StarHid = 1;
							$isGet = "yes";
						}
					}
					*/


					$StuffProp="";
   if ($StuffId==114133 || $StuffId==127622 || $StuffId==129301 || $StuffId==126088 ){
        $StuffProp="gysc1";
   }
   else{
       $PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId='$StuffId' ORDER BY if(Property=9,-1,Property)",$link_id);
       if($PropertyRow=mysql_fetch_array($PropertyResult)){
            $Property=$PropertyRow["Property"];
            $StuffProp="gys$Property";
       }
   }
    $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";

					 $tempdata = array("Title"=>array("Text"=>"$StuffCname","Color"=>"$StuffColor"),"Id"=>"$StuffId",
					 //"Col1"=>array("Text"=>""),
					 "Col2"=>array("Text"=>"".$checkStockRow["OrderQty"].""),
					 "Col3"=>array("Text"=>"$tStockQty"),
					 "Col4"=>array("Text"=>"$llQty","Color"=>"#307BB5"),
					 "Col5"=>array("Text"=>"$Forshort"),
					 "Picture"=>"$ImagePath","star"=>"$StarHid","Prop"=>"$StuffProp","hasPrint"=>"$hasPrint");
       		//$products[] = array("Tag"=>"aessery","$Date", "$StockId", "$StuffCname", "$OrderQty", "$rkQty", "$tStockQty", "$llQty", "$scQty", "$Buyer", "$Position", "$StuffId", "$Picture");
			if ($dfpPage==true) $cellId="qxzy";
			if ($noOper == "yes") {$cellId="nono"; $swapDic= array("-1"=>"");}




			if ($StuffProp=="gys9") {

			$findChildrenSql = "select AL.StockId,AL.StuffId,AL.OrderQty,
			K.tStockQty,AL.StockQty,A.Picture,A.StuffCname
			from $DataIn.cg1_stuffcombox AL 
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=AL.StuffId
			LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
			LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType
			LEFT JOIN $DataIn.base_mposition MP ON MP.Id=ST.Position 
			LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=AL.StuffId 
			where AL.POrderId='$POrderId' and AL.mStuffId='$StuffId' ";

          $findChildren = mysql_query($findChildrenSql,$link_id);
			while ($findChildrenRow = mysql_fetch_assoc($findChildren)) {

			$StockId = $findChildrenRow["StockId"];
			if (strlen($StockId)==12) {
				$StockId = substr($POrderId,0,2).$StockId;
			}
			$StuffId = $findChildrenRow["StuffId"];
			$tStockQty = $findChildrenRow["tStockQty"];
			$OrderQty = $findChildrenRow["OrderQty"];
			$Picture = $findChildrenRow["Picture"];

			$Property = '';
			  $PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId='$StuffId' ORDER BY if(Property=10,-1,Property)",$link_id);
       if($PropertyRow=mysql_fetch_array($PropertyResult)){
            $Property=$PropertyRow["Property"];
       }

			$StuffCname = $findChildrenRow["StuffCname"];
	        $StarHid=0;
	   		$checkllQty=mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id);
				$llQty=mysql_result($checkllQty,0,"llQty");
				$llQty=$llQty==""?0:$llQty;

				$llEstate=mysql_result($checkllQty,0,"llEstate");
				$StarHid = $llEstate>0?"0":"1";

      			 $StuffColor="#000000";
 				switch ($Picture){
       			    case  1: $StuffColor="#FFA500";break;
       			    case  2: $StuffColor="#FF00FF";break;
       			    case  4: $StuffColor="#FFD800";break;
       			    case  7: $StuffColor="#0033FF";break;
 				}
 				$ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";

				$swapDic = array("Right"=>$noBLsign?"":"$operColor-$operName");

					$cellId = $noBLsign?"ccea11":"acce11";
					$hasPrint = '0';
					 if($mainType==5 && $dfpPage!=true)
		{
			$checkTM = mysql_query("SELECT sum(1)  as suma  
		        FROM $DataIn.pands_unite U 
                LEFT JOIN  $DataIn.stuffdata S  ON S.StuffId=U.uStuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
                LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId  
                WHERE U.ProductId='$ProductIdde' AND U.StuffId='$StuffId'  ORDER BY T.mainType;");
				if ($checkTMRow = mysql_fetch_assoc($checkTM)) {
					if ($checkTMRow["suma"]>0) {
					$hasPrint = 1;
					if ($noBLsign!=true) {
					$swapDic = array("Left"=>"#009933-打印","Right"=>"$operColor-$operName");
			 		$info001 = $POrderId;
					 $cellId = "no";
					} else {
						$swapDic = array("Left"=>"#009933-打印");
					 $info001 = $POrderId;
					 $cellId = "nono";
					}
					}
				}


		}
		// -- dict dick operation qxzy nono swapdict stuffcname color stuffcolor
	if ($dfpPage==true) $cellId="qxzy";
			if ($noOper == "yes") {$cellId="nono"; $swapDic= array("-1"=>"");}


				 $tempdataa = array("RowSet"=>array("bgColor"=>"$SetColor"),"Title"=>array("Text"=>"$StuffCname","Color"=>"$StuffColor"),"Col1"=>array("Text"=>""),"Col2"=>array("Text"=>"$OrderQty"),"Col3"=>array("Text"=>"$tStockQty"),"Col4"=>array("Text"=>"$llQty","Color"=>"#307BB5"),"Col5"=>array("Text"=>"$Forshort"),"Picture"=>"$ImagePath","star"=>"$StarHid","Prop"=>"gys$Property","Id"=>"$StuffId");
       		//$products[] = array("Tag"=>"aessery","$Date", "$StockId", "$StuffCname", "$OrderQty", "$rkQty", "$tStockQty", "$llQty", "$scQty", "$Buyer", "$Position", "$StuffId", "$Picture");

		$products[] = array("Tag"=>"acessery","data"=>$tempdataa,"Args"=>"$StockId|$OrderQty|$llQty|$StuffId" ,"CellID"=>"$cellId",

			"onTap"=>array("Value"=>"$Picture","File"=>"$ImagePath"),"onEdit"=>"$onEdit","Swap"=>$swapDic);

			}

		}
			else {
				$products[] = array("Tag"=>"acessery","data"=>$tempdata,"Args"=>"$StockId|".$checkStockRow["OrderQty"]."|$llQty|$StuffId",
			"CellID"=>"$cellId","onTap"=>array("Value"=>"$Picture","File"=>"$ImagePath"),"onEdit"=>"1","Swap"=>$swapDic);

			}
		}
	$jsonArray = array("List"=>$products);

?>