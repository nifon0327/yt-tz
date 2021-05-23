<?php 
//业务处理-未出
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
 $curWeek=$dateResult["curWeek"];
  $dataArray=array();

 $checkBranchId=mysql_query("SELECT BranchId FROM  $DataPublic.staffmain WHERE Number='$LoginNumber' AND BranchId='3'",$link_id);
 if (mysql_num_rows($checkBranchId)>0){
	  $editSign=1;
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


 $onEdit4=$editSign==1?4:0;
 
   $myResult=mysql_query("
            SELECT TIMESTAMPDIFF(DAY,M.OrderDate,CURDATE()) AS OdDays,M.OrderPO,M.CompanyId, 
                     S.POrderId,S.OrderNumber,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,P.cName,P.TestStandard,C.Forshort,A.PreChar, 
                     PI.Leadtime,YEARWEEK(substring(PI.Leadtime,1,10),1)  AS Weeks,SM.Name AS Operator ,
                     E.Type,E.Remark,E.Date AS LockDate 
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
		    LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2  
	        LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	        LEFT JOIN $DataPublic.currencydata A ON A.Id=C.Currency 
		    LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
		    LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		    LEFT JOIN $DataPublic.staffmain SM ON SM.Number=E.Operator 
		    WHERE  S.Estate>0 AND M.Operator='$checkNumber'    GROUP BY S.POrderId ORDER BY M.CompanyId,Weeks ",$link_id);
if($myRow = mysql_fetch_array($myResult)){
      $oldId=$myRow["CompanyId"];
      $Forshort=$myRow["Forshort"];
      $PreChar=$myRow["PreChar"];
      $pos=0;$sumQty=0;$sumAmount=0;
   do{
           $newId=$myRow["CompanyId"];
           if ($newId!=$oldId){
              $dataArray=array();
              $sumQty=number_format($sumQty);
              $sumAmount=number_format($sumAmount);
              $tempArray=array(
				                      "Id"=>"$oldId",
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"#000000"),
				                      "Col3"=>array("Text"=>"$sumQty"),
				                      "Col5"=>array("Text"=>"$PreChar$sumAmount")
				                   );
			  $dataArray[]=array("Tag"=>"Total","data"=>$tempArray);
			   array_splice($jsonArray,$pos,0,$dataArray);
			   $pos=count($jsonArray);
			   $sumQty=0;$sumAmount=0;
	           $oldId=$myRow["CompanyId"];
               $Forshort=$myRow["Forshort"];
               $PreChar=$myRow["PreChar"];
           }
	       $POrderId=$myRow["POrderId"];
	       $ProductId=$myRow["ProductId"];
	        $OrderPO=$myRow["OrderPO"];
	        $cName=$myRow["cName"];
	        $Qty = $myRow["Qty"];
	        $Price=$myRow["Price"];
	        $Amount=sprintf("%.2f",$Qty*$Price);
	        $sumQty+=$Qty;
	        $sumAmount+=$Amount;
	        $Price=sprintf("%.2f",$myRow["Price"]);
	        
	        $OrderDate=$myRow["OrderDate"];
	        $TestStandard=$myRow["TestStandard"];
	        include "order/order_TestStandard.php";
	        
	        $ShipType=$myRow["ShipType"];
	        $OdDays=$myRow["OdDays"];
	        $Weeks=$myRow["Weeks"];
	        $bgColor=($Weeks<$curWeek && $Weeks!="") ?"#FF0000":"";
	        $Weeks=$Weeks==""?" ":substr($Weeks,4,2);
	        $QtySTR=number_format($Qty);
	        
	        $Remark="";$iIcon="";
	        $LockDate="";$Operator="";
	        if ($myRow["Type"]==2){
		         $LockDate=$myRow["LockDate"];
	             $Remark=$myRow["Remark"]; 
	             $Operator=$myRow["Operator"]; 
	             $iIcon=1;
	        }
	        else{
		         //检查BOM表配件是否锁定
		         $checkcgLockSql=mysql_query("SELECT GL.Id  FROM $DataIn.cg1_stocksheet G 
												LEFT JOIN $DataIn.cg1_lockstock GL  ON G.StockId=GL.StockId 
												WHERE  G.POrderId='$POrderId' AND GL.Locks=0 ",$link_id);
				    if($checkcgLockRow = mysql_fetch_array($checkcgLockSql)){
				        $iIcon=2;
				   }
	        }
	        
	         include "submodel/stuff_factualqty_bgcolor.php";  
	        $tempArray=array(
	              "Id"=>"$POrderId",
	              "RowSet"=>array("bgColor"=>"$rowColor"),
	              "Index"=>array("Text"=>"$Weeks","bgColor"=>"$bgColor","iIcon"=>"$iIcon"),
	              "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor","Picture"=>"$TestStandardFile"),
	              "Col1"=> array("Text"=>"$OrderPO"),
	              "Col2"=>array("Text"=>"$QtySTR","bgColor"=>"$FactualQty_Color"),
	              "Col3"=>array("Text"=>"$PreChar$Price"),
	              "Col5"=>array("Text"=>"$PreChar$Amount"),
	              "Remark"=>array("Text"=>"$Remark","Date"=>"$LockDate","DateColor"=>"$DateColor","Operator"=>"$Operator"),
	              "rTopTitle"=>array("Text"=>"$OdDays"."d","Margin"=>"-22,0,0,0","Color"=>"#0000FF"),
	              "rIcon"=>"ship$ShipType"
	           );
	          $jsonArray[]=array("Tag"=>"data2","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"onEdit"=>"$onEdit4","data"=>$tempArray);   
    }while($myRow = mysql_fetch_array($myResult)); 
              $dataArray=array();
              $sumQty=number_format($sumQty);
              $sumAmount=number_format($sumAmount);
              $tempArray=array(
				                      "Id"=>"$oldId",
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"#000000"),
				                      "Col3"=>array("Text"=>"$sumQty"),
				                      "Col5"=>array("Text"=>"$PreChar$sumAmount")
				                   );
			  $dataArray[]=array("Tag"=>"Total","data"=>$tempArray);
			   array_splice($jsonArray,$pos,0,$dataArray);
}	
//$jsonArray=array("data"=>$dataArray); 
?>