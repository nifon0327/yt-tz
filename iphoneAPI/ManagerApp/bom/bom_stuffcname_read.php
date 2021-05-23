<?php 
//BOM采购单更新
$dataArray=array();$CompnayArray=array();$BuyerArray=array();
$Result=mysql_query("SELECT S.StuffId,S.StuffCname,S.Price,P.Forshort,C.PreChar,M.Name AS Buyer    
         FROM $DataIn.stuffdata S 
         LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
         LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency  
         LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId 
         WHERE  (S.StuffId='$SearchText' OR S.StuffCname LIKE '%$SearchText%') AND S.Estate=1 ",$link_id);

 while($myRow = mysql_fetch_array($Result)) 
 {
     $StuffId=$myRow["StuffId"];
     $StuffCname=$myRow["StuffCname"];
     $Forshort=$myRow["Forshort"];
     $Buyer=$myRow["Buyer"];
     $Price=number_format($myRow["Price"],3);
     $PreChar=$myRow["PreChar"];

     $dataArray[]=array("StuffId"=>"$StuffId","Name"=>"$StuffCname","Buyer"=>"$Buyer","Price"=>"$PreChar$Price","Company"=>"$Forshort");
 }

$PickSign=1;
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