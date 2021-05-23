<?php 
//BOM采购单更新
$dataArray=array();$CompnayArray=array();$BuyerArray=array();
 $Result=mysql_query("SELECT S.POrderId,S.AddQty,S.FactualQty,S.Price,C.PreChar,P.Forshort,M.Name AS Buyer,S.AddRemark 
         FROM $DataIn.cg1_stocksheet S 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
         LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
         LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId 
         WHERE  S.Id='$Id' LIMIT 1",$link_id);
 if($myRow = mysql_fetch_array($Result)) 
 {
     $Forshort=$myRow["Forshort"];
     $AddQty=$myRow["POrderId"]>0?"增购数量:" . $myRow["AddQty"]: "特采数量:" . $myRow["FactualQty"];
     
     $Price=number_format($myRow["Price"],3);
     $PreChar=$myRow["PreChar"];
     $Buyer=$myRow["Buyer"];
     $AddRemark=$myRow["AddRemark"]==""?"备注":$myRow["AddRemark"];
     
     $dataArray=array("Forshort"=>"$Forshort","Buyer"=>"$Buyer","AddQty"=>"$AddQty","Price"=>"原单价:$PreChar$Price","Remark"=>"$AddRemark");
 }

if ($PickSign==1){
    $checkCompnay=mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE (ObjectSign=1 OR ObjectSign=3 ) AND Estate=1 ORDER BY Letter",$link_id);
     while($checkRow = mysql_fetch_array($checkCompnay)){
          $CompanyId=$checkRow["CompanyId"];
          $Forshort=$checkRow["Forshort"];
          $CompnayArray[]=array("Id"=>"$CompanyId","Name"=>"$Forshort");
    }
    
	 $checkBuyer=mysql_query("SELECT M.Number,M.Name FROM $DataPublic.staffmain M WHERE  M.BranchId=4 AND M.Estate='1'",$link_id);
     while($checkRow = mysql_fetch_array($checkBuyer)){
          $Number=$checkRow["Number"];
          $Name=$checkRow["Name"];
          $BuyerArray[]=array("Id"=>"$Number","Name"=>"$Name");
    }
}
$jsonArray=array("CompanyList"=>$CompnayArray,"BuyerList"=>$BuyerArray,"data"=>$dataArray);

?>