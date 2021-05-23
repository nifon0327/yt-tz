<?php 
//配件报废审核
include "../../basic/downloadFileIP.php";

 $mySql="SELECT S.Id,S.Qty,S.Date,S.Operator,S.StuffId,S.Remark,S.OPdatetime,K.oStockQty,K.tStockQty,K.mStockQty,
           A.StuffCname,A.Price,A.Picture,C.PreChar,U.Name AS UnitName,P.Forshort,T.TypeName   
         FROM $DataIn.ck8_bfsheet S  
         LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
         LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
         LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
         LEFT JOIN $DataPublic.ck8_bftype  T ON T.Id=S.Type 
         LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
         LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
         WHERE  S.Estate=1 OR S.Estate=2   ORDER BY S.OPdatetime " ;
 $Result=mysql_query($mySql,$link_id);
 $Dir= "$donwloadFileIP/download/stufffile/";
 while($myRow = mysql_fetch_array($Result)) 
 {
     $Id=$myRow["Id"];
    $Forshort=$myRow["Forshort"];
    $TypeName=$myRow["TypeName"];
     $StuffId=$myRow["StuffId"];       
    $StuffCname=$myRow["StuffCname"];//配件名称
    $Qty=$myRow["Qty"];    //报废数量
    $Price=sprintf("%.2f",$myRow["Price"]);
    $PreChar=$myRow["PreChar"];
    $Date=$myRow["Date"];
     $Remark=$myRow["Remark"];
    $oStockQty=number_format($myRow["oStockQty"]);
    $tStockQty=number_format($myRow["tStockQty"]);
    $mStockQty=number_format($myRow["mStockQty"]);
      
    $Picture=$myRow["Picture"];
     include "submodel/stuffname_color.php";
    $ImageFile=$Picture>0?"$Dir".$StuffId. "_s.jpg":"";
     
     /*
    if($TypeId=='9104'){//如果是客户退款，请款总额为订单数*价格
        $Qty=$OrderQty;	//采购总数		
    }
    else{
        $Qty=$FactualQty+$AddQty;
    }
    */
    $sumQty+=$Qty;
    $Amount=sprintf("%.2f",$Qty*$Price);
    $sumAmount+=$Amount*$Rate;
    $Amount=number_format($Amount,2);
    
    $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";

    $cgDate=$myRow["Date"];
    $OPdatetime=$myRow["OPdatetime"];
    //$Date=date("m-d H:i",strtotime($OPdatetime));
    $Date=GetDateTimeOutString($OPdatetime,'');
    
    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
    if ($opHours>=$timeOut[0]) {$OverNums++;$DateColor="#FF0000";} else $DateColor="";
     
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

      $Price=number_format($Price,3);
      
     $listArray=array();
     $listArray[]=array("Cols"=>"1","Name"=>"历史单价:","Text"=>"$historyPrice");
     $listArray[]=array("Cols"=>"2","Name"=>"可用库存:","Text"=>"$oStockQty","Text2"=>"在     库: $tStockQty");
     $listArray[]=array("Cols"=>"1","Name"=>"最低库存:","Text"=>"$mStockQty");
     
      
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
	                     "Col1"=>array("Text"=>"$TypeName"),
	                     "Col2"=>array("Text"=>"$Qty"),
	                     "Col3"=>array("Text"=>"$PreChar$Price"),
	                     "Col4"=>array("Text"=>"$PreChar$Amount"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"$Picture","Type"=>"JPG","ImageFile"=>"$ImageFile","data"=>$listArray)
                     );
 }

?>