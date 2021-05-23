<?php 
//BOM未收明细
include "../../basic/downloadFileIP.php";

$curDate=date("Y-m-d");
if ($BuyerId!=""){
	 $SearchRows.=" AND  M.BuyerId='$BuyerId' "; 
}
$SearchRows.=" AND  M.CompanyId='$CompanyId' "; 
/*
switch($ColSign){
	case "Over5":
      $SearchRows.=" AND  DATEDIFF('$curDate',S.DeliveryDate)>5"; 
	   break;
	case "Over":
      $SearchRows.=" AND  DATEDIFF('$curDate',S.DeliveryDate)>0 AND  DATEDIFF('$curDate',S.DeliveryDate)<=5 "; 
	   break;
}
*/
 $curSeconds=strtotime("$curDate"); 
$mySql="SELECT M.Id,M.Date,DATEDIFF('$curDate',M.Date) AS Days,S.StockId,S.POrderId,S.StuffId,S.Price,U.Name AS UnitName,(S.AddQty+S.FactualQty) AS Qty,D.StuffCname,D.Gfile,D.Picture,M.CompanyId,P.Forshort,M.BuyerId,A.Name AS Operator,R.Remark,
        S.DeliveryDate,E.Rate,E.PreChar,D.TypeId,PI.Leadtime   
        FROM $DataIn.cg1_stocksheet S
        LEFT JOIN  $DataIn.cg1_stockmain M ON S.Mid=M.Id 
        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
        LEFT JOIN $DataPublic.staffmain A ON A.Number=M.BuyerId 
    	LEFT JOIN  $DataPublic.stuffunit U ON U.Id=D.Unit
	    LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	    LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
	    LEFT JOIN $DataIn.cg_remark R ON R.StockId=S.StockId 
	    LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
	    LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=Y.Id 
        WHERE 1  AND S.rkSign>0 AND S.Mid>0  AND D.Estate=1  $SearchRows 
                         AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C WHERE 1 AND C.StockId=S.StockId) 
         ORDER BY M.CompanyId DESC,Days DESC"; 
  //echo $mySql; 
 $Result = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($Result)) {
     do {
            $continueSign=0;
             $Leadtime=$myRow["Leadtime"]; 
             $Leadtime=str_replace("*", "", $Leadtime);
             $DeliveryDate=$myRow["DeliveryDate"];
		    switch($ColSign){
				case "Over5":
		          if ($Leadtime=="" || strtotime($Leadtime)-$curSeconds>=0){
			           $continueSign=1;
		          }
				   break;
				case "Over":
				          if ($DeliveryDate=="0000-00-00"|| strtotime($DeliveryDate)-$curSeconds>=0){
					           $continueSign=1;
				          }
				   break;
		   }
          if ($continueSign==1) continue;
          
           $CompanyId=$myRow["CompanyId"];
            $StockId=$myRow["StockId"];
            $Date=$myRow["Date"];
            $Days=$myRow["Days"];
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
             
			 $Days=abs($Days);
			 $Date=date("Y/m/d",strtotime("$Date"));
			 $DeliveryColor=$DeliveryDate<$curDate?"#FF0000":"";
			 $DeliveryDate=$DeliveryDate=="0000-00-00"?"未设置":date("m/d",strtotime("$DeliveryDate"));
			 
			 $POrderId=$myRow["POrderId"];
			 $LeadTimeColor="";
			
			 if ($Leadtime!=""){
					    $LeadtimeColor=strtotime("$Leadtime")-$curSeconds<0?"#FF0000":"";
					    $Leadtime=strtotime("$Leadtime")>0?date("m/d",strtotime("$Leadtime")):"00/00";
				 }
				 else{
					   $LeadTime=$POrderId==""?"特采单":"未设置";
				 }                                                
			
              $POrderId=$myRow["POrderId"];
              //检查是否订单中最后一个需备料的配件 传入参数:$StuffId/$POrderId
		      include "../../model/subprogram/stuff_blcheck.php";
              $jsonArray[]= array(
					             "View"=>"List",
					             "Id"=>"165",
					             "RowSet"=>array("Cols"=>"4","ReSet"=>"1","bgColor"=>"$LastBgColor"),
					             "onTap"=>array("Title"=>"$StuffCname","Value"=>"1","Tag"=>"StuffDetail","Args"=>"$StockId"),
					             "Index"=>array("Title"=>"$Days","bgColor"=>""), 
					             "Caption"=>array("Title"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","Align"=>"L","GysIcon"=>"$StuffProperty"),
					             "Col_A"=>array("Title"=>"$Qty","Align"=>"L"),
					             "Col_B"=>array("Title"=>"$Date","IconType"=>"6","Margin"=>"5,0,15,0"),
					             "Col_C"=>array("Title"=>"$DeliveryDate","Color"=>"$DeliveryColor","IconType"=>"8","Align"=>"R","Margin"=>"15,0,0,0"),
					             "Col_D"=>array("Title"=>"$Leadtime","Color"=>"$LeadtimeColor","IconType"=>"7")
					          ); 
	   } while($myRow = mysql_fetch_array($Result));
 }

?>