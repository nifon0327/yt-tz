<?php 
//拆分订单审核
 $mySql="SELECT O.Id,O.POrderId,O.Qty,O.Remark,O.Price,O.Operator,O.Date,O.Attached,
 P.ProductId,P.cName,P.TestStandard,C.Forshort,S.OrderPO,D.PreChar    
		FROM $DataIn.yw1_orderdeleted O 
		INNER JOIN  $DataIn.yw1_ordersheet S ON O.POrderId=S.POrderId AND S.OrderNumber=O.OrderNumber  AND O.ProductId=S.ProductId 
		LEFT JOIN  $DataIn.yw1_ordermain M  ON M.OrderNumber=S.OrderNumber
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
		LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency   
		WHERE  O.Estate=0  AND S.Estate>0 Order BY O.POrderId DESC " ;

 $Result=mysql_query($mySql,$link_id);
 $Dir=  "http://".$_SERVER ['HTTP_HOST']. "/download/orderdelcause/";
 while($myRow = mysql_fetch_array($Result)) 
 {
    $Id=$myRow["Id"];
    $POrderId=$myRow["POrderId"];
    $ProductId=$myRow["ProductId"];
    $Forshort=$myRow["Forshort"];
    $cName=$myRow["cName"];//产品名称
    $Price=number_format($myRow["Price"],3);
    
	$TestStandard=$myRow["TestStandard"];	
	include "order/order_TestStandard.php";	
		  
    $Qty=number_format($myRow["Qty"]);
    
    $OPdatetime=$myRow["Date"];
    //$Date=date("m-d H:i",strtotime($OPdatetime));
   $Date=GetDateTimeOutString($OPdatetime,'');
   
    $PreChar=$myRow["PreChar"];
    $Remark=$myRow["Remark"];

    $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
     
     $ImageSign=0;$ImageFile="";$ImageType="PDF";
     $Attached=$myRow["Attached"];
     if ($Attached!=""){
	     $ImageSign=1; $ImageFile=$Dir . $Attached;
	     $ImageType=strtoupper(substr($Attached,-3));
     }
     
      $listArray=array();
      //检查已下采购单的配件
      $checkcgResult=mysql_query("SELECT G.StuffId,YEARWEEK(G.DeliveryDate,1) AS Weeks,(G.AddQty+G.FactualQty) AS Qty,M.PurchaseID,D.StuffCname,D.Picture,B.Forshort,SUM(IFNULL(K.Qty,0)) AS SendQty   
			FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			LEFT JOIN $DataIn.trade_object B ON B.CompanyId=M.CompanyId
			LEFT JOIN $DataIn.ck1_rksheet K ON K.StockId=G.StockId 
			WHERE G.POrderId='$POrderId' AND G.Mid>0 GROUP BY G.StockId ",$link_id);
		while($cgRow = mysql_fetch_array($checkcgResult)){
			$StuffId=$cgRow["StuffId"];
			$StuffCname=$cgRow["StuffCname"];
			$Picture=$cgRow["Picture"];
			 include "submodel/stuffname_color.php";
			 
			$PurchaseID=$cgRow["PurchaseID"];
			$CG_Forshort=$cgRow["Forshort"];
			$CG_Qty=$cgRow["Qty"];
			$CG_SendQty=$cgRow["SendQty"];
			$CG_Weeks=$cgRow["Weeks"]>0?substr($cgRow["Weeks"],4,2):"00";
			
			$listArray[]=array(
			             "Week"=>array("Text"=>"$CG_Weeks"),
			             "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
	                     "Col1"=>array("Text"=>"$PurchaseID"),
	                     "Col2"=>array("Text"=>"$CG_Forshort"),
	                     "Col3"=>array("Text"=>"$CG_Qty","IconType"=>"6"),
	                     "Col4"=>array("Text"=>"$CG_SendQty","IconType"=>"25"),
			);
		}
     
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
	                     "Col1"=>array("Text"=>"$POrderId","Margin"=>"0,0,20,0"),
	                     "Col2"=>array("Text"=>"$Forshort","Margin"=>"20,0,0,0"),
	                     "Col3"=>array("Text"=>"$Qty","Margin"=>"20,0,0,0"),
	                     "Col4"=>array("Text"=>"$PreChar$Price"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"$ImageSign","Type"=>"PDF","ImageFile"=>"$ImageFile","data1"=>$listArray)
                     );
 }

?>