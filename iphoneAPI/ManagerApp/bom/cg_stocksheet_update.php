<?php 
//BOM下单明细
include "../../basic/downloadFileIP.php";

$curDate=date("Y-m-d");
if ($BuyerId!=""){
	 $SearchRows.=" AND  S.BuyerId='$BuyerId' "; 
}

if ($CompanyId!="") {
       $SearchRows.=" AND  S.CompanyId='$CompanyId' "; 
       $SearchRows1=" AND  M.CompanyId='$CompanyId' "; 
}

//供应商审核
if ($ColSign=="Audit"){
	$mySql="SELECT S.Id,S.StockId,S.StuffId,M.Date,M.CompanyId,(S.FactualQty+S.AddQty) AS Qty,S.Price,
             A.StuffCname,A.Gfile,A.Picture,P.Forshort,E.Rate,E.PreChar    
            FROM $DataIn.cg1_stockmain M  
			LEFT JOIN  $DataIn.cg1_stocksheet S ON S.Mid=M.Id 
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
			LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	        LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
			LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId 
			  LEFT JOIN $DataIn.stuffproperty PE ON PE.StuffId=S.StuffId 
			WHERE  M.BuyerId='$BuyerId'  AND PE.Property<>2 AND PE.Property<>4  AND NOT EXISTS (SELECT R.Mid FROM $DataIn.cg1_stockreview R WHERE  R.Mid=M.Id )  AND M.CompanyId IN (SELECT DISTINCT B.CompanyId FROM $DataIn.UserTable A 
																				             LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number 
																				             WHERE A.Estate=1 and A.uType=3 and B.CompanyId<>'2270')
			 AND NOT EXISTS (SELECT P.StuffId FROM $DataIn.stuffdevelop P WHERE P.StuffId=A.StuffId AND P.Estate>0)  																	             
			  $SearchRows1 ORDER BY  S.ywOrderDTime"; 
}
else{
	if ($ColSign=="Over"){
		   $SearchRows.=" AND  TIMESTAMPDIFF(HOUR,S.ywOrderDTime,NOW())>='4' "; 
	}

  $mySql="SELECT S.Id,S.StockId,S.StuffId,S.CompanyId,(S.FactualQty+S.AddQty) AS Qty,S.Price,S.ywOrderDTime AS Date,
           A.StuffCname,A.Gfile,A.Picture,P.Forshort,E.Rate,E.PreChar,TIMESTAMPDIFF(HOUR,S.ywOrderDTime,NOW()) as Hours   
			FROM $DataIn.cg1_stocksheet S 
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	        LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
			LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN $DataIn.cg1_lockstock I ON I.StockId =S.StockId
			WHERE S.Mid=0 and  S.Estate=0 and T.mainType<2 and (S.FactualQty>0 OR S.AddQty>0) and M.BranchId=4 $SearchRows 
			AND NOT EXISTS (SELECT P.StuffId FROM $DataIn.stuffdevelop P WHERE P.StuffId=A.StuffId AND P.Estate>0)  
			AND NOT ( (H.Type='2' AND H.Type is NOT NULL ) or (I.Locks=0 AND I.Locks is NOT NULL)) 
			AND NOT ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  
			AND NOT ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  
			AND NOT ((A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1))
			ORDER BY  S.ywOrderDTime "; 
}
 // echo $mySql; 
 $Result = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($Result)) {
     do {
           $CompanyId=$myRow["CompanyId"];
            $StockId=$myRow["StockId"];
            $Date=$myRow["Date"];
            $Hours=$myRow["Hours"];
            $Rate=$myRow["Rate"];
            $PreChar=$myRow["PreChar"];
            $Forshort=$myRow["Forshort"];

            $StuffId=$myRow["StuffId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $Qty=$myRow["Qty"];
             $Price=$myRow["Price"];
             $Amount=$Qty*$Price;
            $Qty=number_format($Qty);    //送货数量
            $Amount=sprintf("%.2f",$Amount);
            $Picture=$myRow["Picture"];
            include "submodel/stuffname_color.php";
           //配件属性$StuffProperty
             include "submodel/stuff_property.php";
             
			 $Date=$ColSign=="Audit"?date("Y/m/d",strtotime("$Date")):date("m/d H:m:s",strtotime("$Date"));
			 //$IconType=$ColSign=="Audit"?"6":"";
			 
             $Hours=abs($Hours);
             $DateColor=($Hours>=4 && $ColSign!="Audit")?"#FF0000":"";
              $jsonArray[]= array(
					             "View"=>"List",
					             "Id"=>"1184",
					             "RowSet"=>array("Cols"=>"3","ReSet"=>"1"),
					             "onTap"=>array("Title"=>"$StuffCname","Value"=>"1","Tag"=>"StuffDetail","Args"=>"$StockId"),
					             "Index"=>array("Title"=>"$Hours","bgColor"=>""), 
					             "Caption"=>array("Title"=>"$StuffCname","Color"=>"$StuffColor","Align"=>"L","GysIcon"=>"$StuffProperty"),
					             "Col_A"=>array("Title"=>"$Date","Color"=>"$DateColor"),
					             "Col_B"=>array("Title"=>"$Qty"),
					             "Col_C"=>array("Title"=>"$PreChar$Amount","Align"=>"R")
					          ); 
	   } while($myRow = mysql_fetch_array($Result));
 }

?>