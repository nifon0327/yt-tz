<?php 
//采购请款审核
include "../../basic/downloadFileIP.php";

 $mySql="SELECT S.Id,S.StuffId,S.CompanyId,S.StockId,S.AddQty,S.BuyerId,S.Month,S.OrderQty,S.FactualQty,S.Price,S.BuyerId,S.OPdatetime,
 A.Price AS defaultPrice,A.StuffCname,A.TypeId,A.Picture,C.PreChar,C.Rate,U.Name AS UnitName,P.Forshort,M.Date,P.GysPayMode,P.Prepayment,M.PurchaseID,G.Mid   
         FROM $DataIn.cw1_fkoutsheet S 
         LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
         LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
         LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
         LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
         LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=G.Mid 
         WHERE S.Estate=2 ORDER BY GysPayMode DESC,S.OPdatetime" ;

 $Result=mysql_query($mySql,$link_id);
 $Dir= "$donwloadFileIP/download/stufffile/";
 while($myRow = mysql_fetch_array($Result)) 
 {
     $Id=$myRow["Id"];
    $StuffId=$myRow["StuffId"];
    $TypeId=$myRow["TypeId"];
    $Forshort=$myRow["Forshort"];
    $StuffCname=$myRow["StuffCname"];//配件名称
    $OrderQty=$myRow["OrderQty"];    //订单数量
    $FactualQty=$myRow["FactualQty"];//需求数量
    $AddQty=$myRow["AddQty"];	//增购数量
    $Price=$myRow["Price"];
    $defaultPrice=$myRow["defaultPrice"];
    $PreChar=$myRow["PreChar"];
    $Rate=$myRow["Rate"];
    $Month=$myRow["Month"];
    $Month=substr($Month, 5) . "月";
    $Picture=$myRow["Picture"];
     include "submodel/stuffname_color.php";
    $ImageFile=$Picture>0?"$Dir".$StuffId. "_s.jpg":"";
     
    if($TypeId=='9104'){//如果是客户退款，请款总额为订单数*价格
        $Qty=$OrderQty;	//采购总数		
    }
    else{
        $Qty=$FactualQty+$AddQty;
    }
    $sumQty+=$Qty;
    $Amount=sprintf("%.2f",$Qty*$Price);
    $sumAmount+=$Amount*$Rate;
    $Amount=number_format($Amount,2);
    
    $Operator=$myRow["BuyerId"];
     include "../../model/subprogram/staffname.php";

    $cgDate=$myRow["Date"];
    $OPdatetime=$myRow["OPdatetime"];
    //$Date=date("m-d H:i",strtotime($OPdatetime));
    $Date=GetDateTimeOutString($OPdatetime,'');
    //$opHours= geDifferDateTimeNum($OPdatetime,"",1);
    
     
     $StockId=$myRow["StockId"];
     $PurchaseID=$myRow["PurchaseID"];
     $Mid=$myRow["Mid"];
     $PurchasePath=$Mid>0?"model/subprogram/purchaseorder_view.php?Id=$Mid&FromPage=iPhone":"";
      //取得历史最高价与最底价
     $historyPrice="";
     $CheckGSql=mysql_query("SELECT MAX(Price) AS hPirce,MIN(Price) AS lPrice FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId' AND Mid>0 ",$link_id);
      if($CheckGRow=mysql_fetch_array($CheckGSql)){
	        $hPirce=$CheckGRow["hPirce"];
	        $lPrice=$CheckGRow["lPrice"];
	        if ($hPirce!="")
	        {
	           $historyPrice=" H:$hPirce  L:$lPrice";
	        }
       }

      $GysPayMode=$myRow["GysPayMode"];
      if ($GysPayMode==1) $OverNums++;
      $PayColor=$GysPayMode==1?"#0000FF":"";
      
      $Prepayment=$myRow["Prepayment"];
      $Remark="";
      
      if ($GysPayMode==1) $Remark=$Prepayment==1?"现金先付款结付 ":"现金结付 ";
      if ($Price<>$defaultPrice)  {
             $defaultPrice=number_format($defaultPrice,3);
             $Remark.="与默认单价" . $PreChar . $defaultPrice . "不一致";
      }
      
      //退补数量计算
		$CompanyId=$myRow["CompanyId"];
		$sSearch1=" AND S.StuffId='$StuffId'  AND M.CompanyId = '$CompanyId'";
		$checkSql=mysql_query("
		SELECT (B.thQty-A.bcQty) AS wbQty
			FROM (
				SELECT IFNULL(SUM(S.Qty),0) AS thQty,'$StuffId' AS StuffId FROM $DataIn.ck2_thsheet S 
				LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				WHERE 1 $sSearch1
				)B
			LEFT JOIN (
				SELECT IFNULL(SUM(Qty),0) AS bcQty,'$StuffId' AS StuffId FROM $DataIn.ck3_bcsheet  S
				LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid 
				WHERE 1 $sSearch1
				) A ON A.StuffId=B.StuffId",$link_id);

		$wbQty=mysql_result($checkSql,0,"wbQty");
		if($wbQty!=0){
			$Remark.="有退货" . $wbQty .  "pcs未补";
			}


      $Price=number_format($Price,3);
      
     $listArray=array();
     $listArray[]=array("Cols"=>"1","Name"=>"采  购  ID:","Text"=>"$StockId","onTap"=>"1","ServerId"=>"$ServerId","Tag"=>"StuffDetail","Args"=>"$StockId");
     $listArray[]=array("Cols"=>"1","Name"=>"采购单号:","Text"=>"$PurchaseID","onTap"=>"1","ServerId"=>"$ServerId","Tag"=>"Web","Args"=>"$PurchasePath");
     $listArray[]=array("Cols"=>"1","Name"=>"历史单价:","Text"=>"$historyPrice","onTap"=>"1","ServerId"=>"$ServerId","Tag"=>"HistoryPrice","Args"=>"$StuffId");
      
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor",'Margin'=>'-12,0,-30,0'),
	                     "Col1"=>array("Text"=>"$Forshort","Color"=>"$PayColor"),
	                     "Col2"=>array("Text"=>"$Qty"),
	                     "Col3"=>array("Text"=>"$PreChar$Price"),
	                     "Col4"=>array("Text"=>"$PreChar$Amount"),
	                     "Month"=>array("Text"=>"$Month","Color"=>"#0000FF",'Margin'=>'38,0,0,0'),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"$Picture","Type"=>"JPG","ImageFile"=>"$ImageFile","data"=>$listArray)
                     );
 }

?>