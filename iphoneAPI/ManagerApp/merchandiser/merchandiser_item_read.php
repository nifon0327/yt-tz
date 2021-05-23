<?php 
//业务处理
$hidden=1;$jsondata=array();
 $checkBranchId=mysql_query("SELECT BranchId FROM  $DataPublic.staffmain WHERE Number='$LoginNumber' AND BranchId='3' AND Number NOT IN (10051,10691,10007)",$link_id);
 if (mysql_num_rows($checkBranchId)>0){
	 //只显示业务个人信息
	 $SearchRows=" AND M.Operator='$LoginNumber' ";
	 $hidden=0;$editSign=1;
 }
 else{
	 $actionResult = mysql_query("SELECT A.Action   
	                FROM  $DataIn.usertable B
                    LEFT JOIN  $DataIn.upopedom A ON B.Id=A.UserId 
                    WHERE  B.Number='$LoginNumber' AND B.Estate=1  AND A.Action>=16 AND A.ModuleId=1003" ,$link_id);
     if (mysql_num_rows($actionResult)>0){
           $editSign=1;
     }          
 }
 

  //布局设置
$Layout=array( "Title"=>array("Frame"=>"40, 2, 240, 25"));

 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
 $curWeek=$dateResult["curWeek"];
 
 $checkSql="SELECT M.Operator AS Number,A.Name,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount, 
 SUM(IF(YEARWEEK(substring(PI.Leadtime,1,10),1)<'$curWeek' AND Year(substring(PI.Leadtime,1,10))>0,S.Qty,0)) AS OverQty 
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
LEFT JOIN $DataPublic.staffmain A ON A.Number=M.Operator 
WHERE S.Estate>0  $SearchRows GROUP BY M.Operator  ORDER BY Number";
$checkResult = mysql_query($checkSql,$link_id);
while($checkRow = mysql_fetch_array($checkResult)){
	      $Number=$checkRow["Number"];
	      $Name=$checkRow["Name"];
	      $noChQty=$checkRow["Qty"]==0?"":number_format($checkRow["Qty"]);
	      $noChOverQty=$checkRow["OverQty"]==0?"":number_format($checkRow["OverQty"]);
	      $noChAmount=$checkRow["Amount"]==0?"":number_format($checkRow["Amount"]);

	      $dataArray=array();
	      $NameArray=array(
                      "Id"=>"Total",
                      "RowSet"=>array("bgColor"=>"#EFEFEF"),
                      "Title"=>array("Text"=>"$Name","FontSize"=>"14","Color"=>"#0066FF")
                   );
		$dataArray[]=array("Tag"=>"Total","data"=>$NameArray);
		$jsondata[]=array("data"=>$dataArray); 
		
		 //成品锁
		  $onEdit1=$editSign==1?1:0;
		  $dataArray=array();$Locks=0;$OverLocks=0;
		   $LockResult=mysql_query("
	                SELECT TIMESTAMPDIFF(DAY,M.OrderDate,CURDATE()) AS OdDays,M.OrderPO,
	                         S.POrderId,S.OrderNumber,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,P.cName,P.TestStandard,C.Forshort,A.PreChar, 
                             PI.Leadtime,YEARWEEK(substring(PI.Leadtime,1,10),1)  AS Weeks ,SM.Name AS Operator,
                             E.Remark,E.Date AS LockDate,TIMESTAMPDIFF(DAY,E.Date,CURDATE()) AS LockDays  
					FROM $DataIn.yw1_ordersheet S 
					LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
				    LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2  
			        LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
			        LEFT JOIN $DataPublic.currencydata A ON A.Id=C.Currency 
				    LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
				    LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
				    LEFT JOIN $DataPublic.staffmain SM ON SM.Number=E.Operator 
				    WHERE  S.Estate=1 AND M.Operator='$Number' AND  E.Type=2   GROUP BY S.POrderId 
			",$link_id);
		while($LockRow = mysql_fetch_array($LockResult)){
		       $Locks++;
		       $OverLocks+=$LockRow["LockDays"]>3?1:0;
		       $POrderId=$LockRow["POrderId"];
	            $OrderPO=$LockRow["OrderPO"];
	            $cName=$LockRow["cName"];
	            $Qty = $LockRow["Qty"];
	            $Price=$LockRow["Price"];
	            $Amount=sprintf("%.2f",$Qty*$Price);		
	            $Price=sprintf("%.2f",$LockRow["Price"]);
	            
	            $OrderDate=$LockRow["OrderDate"];
	            $TestStandard=$LockRow["TestStandard"];
	            $ProductId=$LockRow["ProductId"];
	            include "order/order_TestStandard.php";
	            
	            $PreChar=$LockRow["PreChar"];
	            $ShipType=$LockRow["ShipType"];
	            $OdDays=$LockRow["OdDays"];
	            $Weeks=$LockRow["Weeks"];
	            $bgColor=($Weeks<$curWeek && $Weeks!="") ?"#FF0000":"";
	            $Weeks=$Weeks==""?"":substr($Weeks,4,2);
                $Qty=number_format($Qty);
                $Amount=number_format($Amount,2);
                
                $LockDate=$LockRow["LockDate"];
                $Operator=$LockRow["Operator"];
                $Remark=$LockRow["Remark"];
                $DateColor=$LockRow["LockDays"]>3?"#FF0000":"";
	            $tempArray=array(
                      "Id"=>"$POrderId",
                       "RowSet"=>array("bgColor"=>"$rowColor"),
                       "Index"=>array("Text"=>"$Weeks","bgColor"=>"$bgColor","iIcon"=>"1"),
                      "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
                      "Col1"=> array("Text"=>"$OrderPO"),
                      "Col2"=>array("Text"=>"$Qty"),
                      "Col3"=>array("Text"=>"$PreChar$Price"),
                      "Col5"=>array("Text"=>"$PreChar$Amount"),
                      "Remark"=>array("Text"=>"$Remark","Date"=>"$LockDate","DateColor"=>"$DateColor","Operator"=>"$Operator"),
                      "rTopTitle"=>array("Text"=>"$OdDays"."d","Color"=>"#0000FF")
                   );
                  $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"onEdit"=>"$onEdit1","data"=>$tempArray);    
		}
		if ($hidden==0 || $Locks>0){
				$unQverLocks=$Locks-$OverLocks;
				 $headArray=array(
		                  "Id"=>"$Number",
		                 "onTap"=>array("Value"=>"1","Target"=>"","Args"=>""),
		                  "Title"=>array("Text"=>"成品锁","FontSize"=>"14","Bold"=>"1"),
		                  "Col3"=>array("Text"=>"$Locks","FontSize"=>"14"),
		                  "Legend"=>"$OverLocks,$unQverLocks"
		              );
		         $jsondata[]=array("head"=>$headArray,"hidden"=>"$hidden","Layout"=>$Layout,"data"=>$dataArray); 
         }
         
         
          //配件锁
        $dataArray=array();$cgLocks=0;$cgOverLocks=0;
        $onEdit2=$editSign==1?2:0;
	   $LockResult=mysql_query("
	                SELECT TIMESTAMPDIFF(DAY,M.OrderDate,CURDATE()) AS OdDays,M.OrderPO,
	                         S.POrderId,S.OrderNumber,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,P.cName,P.TestStandard,C.Forshort,A.PreChar, 
                             PI.Leadtime,YEARWEEK(substring(PI.Leadtime,1,10),1)  AS Weeks,
                             IF(TIMESTAMPDIFF(DAY,MIN(GL.Date),CURDATE())>'3',1,0) as OverLocks 
					FROM $DataIn.yw1_ordersheet S 
					LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			        LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
			        LEFT JOIN $DataPublic.currencydata A ON A.Id=C.Currency 
					LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId  
				    LEFT JOIN  $DataIn.cg1_lockstock GL ON GL.StockId=G.StockId
				    LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
				    LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
				    WHERE  S.Estate=1 AND M.Operator='$Number' AND  GL.Locks=0  GROUP BY S.POrderId 
			",$link_id);
		while($LockRow = mysql_fetch_array($LockResult)){
		       $cgLocks++;
		       $cgOverLocks+=$LockRow["OverLocks"];
		       $POrderId=$LockRow["POrderId"];
	            $OrderPO=$LockRow["OrderPO"];
	            $cName=$LockRow["cName"];
	            $Qty = $LockRow["Qty"];
	            $Price=$LockRow["Price"];
	            $Amount=sprintf("%.2f",$Qty*$Price);		
	            $Price=sprintf("%.2f",$LockRow["Price"]);
	            
	            $OrderDate=$LockRow["OrderDate"];
	            $TestStandard=$LockRow["TestStandard"];
	            include "order/order_TestStandard.php";
	            
	            $PreChar=$LockRow["PreChar"];
	            $ShipType=$LockRow["ShipType"];
	            $OdDays=$LockRow["OdDays"];
	            $Weeks=$LockRow["Weeks"];
	            $bgColor=($Weeks<$curWeek && $Weeks!="") ?"#FF0000":"";
	            $Weeks=$Weeks==""?" ":substr($Weeks,4,2);
                 $Qty=number_format($Qty);
                $Amount=number_format($Amount,2);
               
	            $tempArray=array(
                      "Id"=>"$POrderId",
                       "RowSet"=>array("bgColor"=>"#EEEEEE"),
                       "Index"=>array("Text"=>"$Weeks","bgColor"=>"$bgColor","iIcon"=>"2"),
                      "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
                      "Col1"=> array("Text"=>"$OrderPO"),
                      "Col2"=>array("Text"=>"$Qty"),
                      "Col3"=>array("Text"=>"$PreChar$Price"),
                      "Col5"=>array("Text"=>"$PreChar$Amount"),
                        "rTopTitle"=>array("Text"=>"$OdDays"."d","Color"=>"#0000FF")
                   );
                  $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);  
                  
                  $StockResult = mysql_query("SELECT 
					S.Mid,S.StockId,S.StuffId,S.OrderQty,S.Price,M.Date,D.StuffCname,D.Picture,B.Name,C.Forshort,C.Currency,A.PreChar,
					MAX(GL.Date) AS LockDate,GL.Remark,TIMESTAMPDIFF(DAY,GL.Date,CURDATE()) AS LockDays,B2.Name AS Operator   
					FROM $DataIn.cg1_stocksheet S
					LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
					LEFT JOIN  $DataIn.cg1_lockstock GL ON GL.StockId=S.StockId
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
					LEFT JOIN $DataPublic.staffmain B ON B.Number=S.BuyerId
					LEFT JOIN $DataPublic.staffmain B2 ON B2.Number=GL.Operator 
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId 
					LEFT JOIN $DataPublic.currencydata A ON A.Id=C.Currency  
					WHERE S.POrderId='$POrderId' AND  GL.Locks=0  GROUP BY S.StockId  ORDER BY S.StockId ",$link_id);

					if ($StockRows = mysql_fetch_array($StockResult)) {
							  do{ 
								    $Mid=$StockRows["Mid"];
									$StockId=$StockRows["StockId"];
						            $StuffId=$StockRows["StuffId"];
									$BuyDate=$StockRows["Date"];
									$StuffCname=$StockRows["StuffCname"];
									$SForshort=$StockRows["Forshort"];
									$Buyer=$StockRows["Name"];
									$OrderQty=$StockRows["OrderQty"];
									$Price=sprintf("%.2f",$StockRows["Price"]);
									$PreChar=$StockRows["PreChar"];
									$Picture=$StockRows["Picture"];
									include "submodel/stuffname_color.php";
									
								    $OrderQty=number_format($OrderQty);
								    $tStockQty=number_format($tStockQty);
								    $PicutreImage=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
								    $ForshortColor=$StockRows["Currency"]==2?"#FF0000":"";
                                    
                                    $LockDate=$StockRows["LockDate"];
					                $Operator=$StockRows["Operator"];
					                $Remark=str_replace(" ","",$StockRows["Remark"])==""?"未填写原因":$StockRows["Remark"];
					                $Operator=$StockRows["Operator"];
					                $DateColor=$StockRows["LockDays"]>3?"#FF0000":"";
                                     $bgColor=$Mid>0?"#00FF00":"#FFCC00";
                                     
									$tempArray=array(
					                       "Id"=>"$StockId",
					                       "Index"=>array("Text"=>" ","bgColor"=>"$bgColor","iIcon"=>"2"),
					                       "Title"=>array("Text"=>"$StuffCname","Color"=>"$StuffColor","Margin"=>"0,0,25,0"),
					                       "Col1"=> array("Text"=>"$SForshort"),
					                       "Col2"=>array("Text"=>"$OrderQty"),
					                       "Col3"=>array("Text"=>"$PreChar$Price"),
					                       "Col5"=>array("Text"=>"$Buyer","Margin"=>"-5,0,0,0"),
					                       "Remark"=>array("Text"=>"$Remark","Date"=>"$LockDate","DateColor"=>"$DateColor","Operator"=>"$Operator")
					                   );
					                  $dataArray[]=array("Tag"=>"sfdata","onTap"=>array("Target"=>"StuffDetail","Args"=>"$StockId"),"onEdit"=>"$onEdit2","data"=>$tempArray);  							
							}while($StockRows = mysql_fetch_array($StockResult));
						}
						  
		}
	   if ($hidden==0 || $cgLocks>0){
			$uncgQverLocks=$cgLocks-$cgOverLocks;
			$headArray=array(
	                  "Id"=>"$Number",
	                  "onTap"=>array("Value"=>"1","Target"=>"","Args"=>""),
	                  "Title"=>array("Text"=>"配件锁","FontSize"=>"14","Bold"=>"1"),
	                  "Col3"=>array("Text"=>"$cgLocks","FontSize"=>"14"),
	                  "Legend"=>"$cgOverLocks,$uncgQverLocks"
	              );
	         $jsondata[]=array("head"=>$headArray,"hidden"=>"$hidden","Layout"=>$Layout,"data"=>$dataArray); 
         }
        
         $dataArray=array(); $PicCount=0;$PicNoAudit=0;
          $onEdit3=$editSign==1?3:0;
         //未完成标准图（订单已分配拉线）
         $PicResult=mysql_query("
	                SELECT TIMESTAMPDIFF(DAY,M.OrderDate,CURDATE()) AS OdDays,M.OrderPO,
	                         S.POrderId,S.OrderNumber,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,P.cName,P.TestStandard,C.Forshort,A.PreChar, 
                             PI.Leadtime,YEARWEEK(substring(PI.Leadtime,1,10),1)  AS Weeks
					FROM $DataIn.yw1_ordersheet S 
					LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			        LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
			        LEFT JOIN $DataPublic.currencydata A ON A.Id=C.Currency 
					LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId  
				    LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
				    LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
				    WHERE  S.Estate=1 AND M.Operator='$Number'   AND (P.TestStandard=2 OR  (P.TestStandard<>1
				    AND EXISTS ( SELECT POrderId FROM $DataIn.sc1_mission SC WHERE SC.POrderId=S.POrderId))) GROUP BY S.POrderId 
			",$link_id);
	   while($PicRow = mysql_fetch_array($PicResult)){
		       $PicCount++;
		       $cgOverLocks+=$PicRow["OverLocks"];
		       $POrderId=$PicRow["POrderId"];
	            $OrderPO=$PicRow["OrderPO"];
	            $cName=$PicRow["cName"];
	            $Qty= $PicRow["Qty"];
	            $Price=$PicRow["Price"];
	            $Amount=sprintf("%.2f",$Qty*$Price);		
	            $Price=sprintf("%.2f",$PicRow["Price"]);
	            
	            $OrderDate=$PicRow["OrderDate"];
	            $ProductId=$PicRow["ProductId"];
	            $TestStandard=$PicRow["TestStandard"];
	            include "order/order_TestStandard.php";
	           if ($TestStandard==2) {
	                $PicNoAudit++;$editSign3=$onEdit3;
	            }
	            
	            if (file_exists("../../$TestStandardFile")){
		            $upTime=filemtime ("../../$TestStandardFile");
	                $upDate=date("m/d H:i",$upTime);
	            }
	            else{
		            //$upDate=" ";
		            $checkRkTime=mysql_fetch_array(mysql_query("SELECT Max(M.rkDate) AS rkDate 
		                  FROM $DataIn.cg1_stocksheet S 
						  LEFT JOIN  $DataIn.ck1_rksheet R ON R.StockId=S.StockId
						  LEFT JOIN  $DataIn.ck1_rkmain M ON M.Id=s.Mid 
		                  WHERE S.POrderId='$POrderId'  ",$link_id));
		            $upTime= $checkRkTime["rkDate"];
		            $upDate=$upTime==""?" ": date("m/d H:i",$upTime);
	            }
	            
	            if ($TestStandard==0){
		            $editSign3=0;
	            }
	            $PreChar=$PicRow["PreChar"];
	            $ShipType=$PicRow["ShipType"];
	            $OdDays=$PicRow["OdDays"];
	            $Weeks=$PicRow["Weeks"];
	            $bgColor=($Weeks<$curWeek && $Weeks!="") ?"#FF0000":"";
	            $Weeks=$Weeks==""?"":substr($Weeks,4,2);
                $Qty=number_format($Qty);
                $Amount=number_format($Amount,2);
                
                $LockDate=$PicRow["LockDate"];
	            $tempArray=array(
                      "Id"=>"$POrderId",
                       "RowSet"=>array("bgColor"=>"$rowColor"),
                        "Edit"=>array("Id"=>"$ProductId","Sign"=>"2","ModuleId"=>"118|main","ActionId"=>"Ts"),
                       "Index"=>array("Text"=>"$Weeks","bgColor"=>"$bgColor"),
                       "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor","Picture"=>"$TestStandardFile"),
                       "Col1"=> array("Text"=>"$OrderPO"),
                       "Col2"=>array("Text"=>"$Qty"),
                       "Col3"=>array("Text"=>"$PreChar$Price"),
                       "Col5"=>array("Text"=>"$upDate"),
                       "Remark"=>array("Text"=>""),
                       "rTopTitle"=>array("Text"=>"$OdDays"."d","Margin"=>"-22,0,0,0","Color"=>"#0000FF"),
                       "rIcon"=>"ship$ShipType"
                   );
                  $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"onEdit"=>"$editSign3","data"=>$tempArray);    
		}
		
		 if ($hidden==0 || $PicCount>0){
			$PicNoAudit=$PicNoAudit==0?"":$PicNoAudit;
			$headArray=array(
	                  "Id"=>"$Number",
	                  "onTap"=>array("Value"=>"1","Target"=>"","Args"=>""),
	                  "Title"=>array("Text"=>"标准图","FontSize"=>"14","Bold"=>"1"),
	                  "Col1"=>array("Text"=>"","Color"=>"#FF0000","Margin"=>"-50,0,0,0"),
	                  "Col2"=>array("Text"=>"$PicNoAudit","Color"=>"#0066FF","Margin"=>"-50,0,30,0"),
	                  "Col3"=>array("Text"=>"$PicCount","FontSize"=>"14"),
	              );
	         $jsondata[]=array("head"=>$headArray,"hidden"=>"$hidden","Layout"=>$Layout,"data"=>$dataArray);   
          }
          
        $dataArray=array();
	    $headArray=array(
                  "Id"=>"$Number",
                   "onTap"=>array("Value"=>"1","Target"=>"101","Args"=>"$Number"),
                  "Title"=>array("Text"=>"未出","FontSize"=>"14","Bold"=>"1"),
                  "Col1"=>array("Text"=>"$noChOverQty","Color"=>"#FF0000","Margin"=>"-50,0,0,0"),
                  "Col2"=>array("Text"=>"$noChQty","Margin"=>"-50,0,30,0"),
                  "Col3"=>array("Text"=>"¥$noChAmount","FontSize"=>"14")
              );
         $jsondata[]=array("head"=>$headArray,"hidden"=>"1","Layout"=>$Layout,"data"=>$dataArray); 
         
         //待出
         $dataArray=array();$noShip=0; $waitQty=0;$waitAmount=0;
         $WaitResult=mysql_query("SELECT  SUM(S.Qty) AS Qty,SUM( S.Price*S.Qty*D.Rate) AS Amount,SUM(IF(S.ShipType='',1,0)) AS noShip  
                     FROM $DataIn.yw1_ordermain M
                     LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
                     LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
                     LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
                     WHERE  S.Estate>=2 AND M.Operator='$Number' ",$link_id);
        if($WaitRow = mysql_fetch_array($WaitResult)){
              $noShip=$WaitRow["noShip"];
              $waitQty=$WaitRow["Qty"];
              $waitAmount=$WaitRow["Amount"];
        }
       
		//逾期待出    
	    $noshipResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount
	       FROM(
	              SELECT Max(T.Date) AS scDate,Y.Qty,Y.Price,Y.OrderNumber,M.CompanyId     
	              FROM $DataIn.yw1_ordersheet Y 
	              LEFT JOIN $DataIn.yw1_ordermain M ON Y.OrderNumber=M.OrderNumber  
	    		  LEFT JOIN  $DataIn.sc1_cjtj  T ON  T.POrderId=Y.POrderId  
	    		  WHERE  Y.Estate>=2   AND M.Operator='$Number'  GROUP BY Y.POrderId 
	    		)S 
				LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
				LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
				WHERE  TIMESTAMPDIFF( DAY, S.scDate , CURDATE())>=5",$link_id));
	     $waitOverQty=$noshipResult["Qty"]; 	
	     
	     $AddRows=array();
		 if ($waitOverQty>0){
			   $waitOverQty=number_format($noshipResult["Qty"]); 	
		       $waitOverAmount=number_format($noshipResult["Amount"]);
		       $AddRows=array(
		              array("ColName"=>"Col2","Text"=>"$waitOverQty","Color"=>"#FF0000","Margin"=>"0,0,0,38","DateIcon"=>array("Type"=>"4","Title"=>"5d")),
                      array("ColName"=>"Col3","Text"=>"$waitOverAmount","Color"=>"#FF0000","Margin"=>"0,0,0,38")
		        );
		 }
	  if ($hidden==0 || $waitQty>0){
		    $noShip=$noShip==0?"":$noShip;
	        $waitQty=$waitQty==0?"":number_format($waitQty);
			$waitAmount=number_format($waitAmount);
			$headArray=array(
	                  "Id"=>"$Number",
	                   "onTap"=>array("Value"=>"1","Target"=>"102","Args"=>"$Number"),
	                  "Title"=>array("Text"=>"待出","FontSize"=>"14","Bold"=>"1"),
	                  "Col1"=>array("Text"=>"$noShip","Color"=>"#0066FF","Margin"=>"-50,0,0,0"),
	                  "Col2"=>array("Text"=>"$waitQty","Margin"=>"-50,0,30,0"),
	                  "Col3"=>array("Text"=>"¥$waitAmount","FontSize"=>"14"),
	                  "AddRows"=>$AddRows
	              );
	         $jsondata[]=array("head"=>$headArray,"hidden"=>"1","Layout"=>$Layout,"data"=>$dataArray); 
         }
 }
 $jsonArray=array("data"=>$jsondata); 

?>