<?php 
//补货审核
$limit = "";

 $mySql="SELECT S.Id,S.StockId,S.StuffId,S.Qty,S.OPdatetime,S.Remark,S.Operator,S.POrderId,
         A.StuffCname,A.TypeId,A.Picture,G.Price,G.BuyerId,C.PreChar,U.Name AS UnitName,P.Forshort,K.tStockQty,K.oStockQty   
         FROM $DataIn.ck13_replenish S 
         LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
         LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
         LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId  
         LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
         LEFT JOIN $DataIn.stufftype T ON A.TypeId=T.TypeId 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
         LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
         WHERE  S.Estate=2  AND S.Qty>0   ORDER BY S.OPdatetime,S.Id ";

 $Result=mysql_query($mySql,$link_id);
 while($myRow = mysql_fetch_array($Result)) 
 {
        $Id=$myRow["Id"];
        $TypeId=$myRow["TypeId"];
        $Forshort=$myRow["Forshort"];
        $StuffCname=$myRow["StuffCname"];//配件名称
        $Qty=$myRow["Qty"];    //补货数量
        $Price=$myRow["Price"];
        $PreChar=$myRow["PreChar"];
        $Remark=$myRow["Remark"];
        $StockId=$myRow["StockId"];
        $tStockQty=$myRow["tStockQty"];
        $oStockQty=$myRow["oStockQty"];
        
        $StuffId=$myRow["StuffId"];
         $Picture=$myRow["Picture"];
         include "submodel/stuffname_color.php";
        
	    $Amount=sprintf("%.2f",$Qty*$Price);
	    $Amount=number_format($Amount,2);

        $OPdatetime=$myRow["OPdatetime"];
	    $Operator=$myRow["Operator"];
       include "../../model/subprogram/staffname.php";
     
     $Date=GetDateTimeOutString($OPdatetime,'');
     $opHours= geDifferDateTimeNum($OPdatetime,"",1);
     if ($opHours>=$timeOut[0]) {$OverNums++;$DateColor="#FF0000";} else $DateColor="";
     
     $Price=number_format($Price,3);
     //$AddRemark="改单价,原价格2.00，再改成3.00";
     $oStockQty=number_format($oStockQty);
     
     $POrderId=$myRow["POrderId"];
     $cNameResult= mysql_fetch_array(mysql_query("SELECT G.GroupName,P.cName 
			      FROM $DataIn.yw1_ordersheet S 
			      LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
			      LEFT JOIN  $DataIn.sc1_mission M ON M.POrderId=S.POrderId 
			      LEFT JOIN $DataIn.staffgroup G ON G.Id=M.Operator 
			      WHERE S.POrderId='$POrderId'",$link_id));
     $cName=$cNameResult["cName"];
     $ScLine=substr($cNameResult["GroupName"],-1);
     
     $listArray=array();
     $listArray[]=array("Cols"=>"1","Name"=>"订 单 ID:","Text"=>"$POrderId","onTap"=>"1","Tag"=>"Order","ServerId"=>"0","Args"=>"$POrderId");
     $listArray[]=array("Cols"=>"1","Name"=>"产品名称:","Text"=>"$cName","Color"=>"#FFA500");
     
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"1","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","LineNo"=>"$ScLine"),
	                     "Col1"=>array("Text"=>"$Forshort"),
	                     "Col3"=>array("Text"=>"$Qty"),//,"IconType"=>"17"
	                    // "Col3"=>array("Text"=>"$oStockQty","IconType"=>"26"),
	                     "Col4"=>array("Text"=>"$PreChar$Price"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                      "List"=>array("data"=>$listArray)
                     );
 }

?>