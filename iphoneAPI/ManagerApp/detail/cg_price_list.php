<?php 
//入库记录
 $CheckGSql=mysql_query("SELECT MAX(Price) AS hPirce,MIN(Price) AS lPrice FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId' AND Mid>0 ",$link_id);
      if($CheckGRow=mysql_fetch_array($CheckGSql)){
        $hPirce=$CheckGRow["hPirce"];
        $lPrice=$CheckGRow["lPrice"];
}
$Result=mysql_query("SELECT S.Price,M.Date,C.PreChar,P.Forshort  FROM $DataIn.cg1_stocksheet S 
		 LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
		 LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
		 LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
		 WHERE S.StuffId='$StuffId' AND S.Mid>0 ORDER BY M.Date DESC",$link_id);
 if($myRow = mysql_fetch_array($Result)) {
       $subArray=array();$sumQty=0;
     do {
            $Price=$myRow["Price"];
            $PirceColor="#000000";
            if ($hPirce==$Price) $PirceColor="#FF0000";
            if ($lPrice==$Price) $PirceColor="#00AA00";
            $Date=$myRow["Date"];
            $PreChar=$myRow["PreChar"];
            $Forshort=$myRow["Forshort"];
            $Price= $PreChar . $Price;
            $subArray[]= array( 
               array("$Date",""),
               array("$Forshort",""),
               array("$Price","$PirceColor"),"0"); 
        } while($myRow = mysql_fetch_array($Result));
           $titleArray= array( 
               array("采购日期","100","L"),
               array("供应商","80","L"),
               array("价   格","80","R")
              ); 
               
          $jsonArray[]=array($titleArray,$subArray);
 }
?>