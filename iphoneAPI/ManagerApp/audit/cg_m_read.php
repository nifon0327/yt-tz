<?php 
//异常采单审核
$test = " S.Estate=1 ";
$limit = "";

if ("11965"==$LoginNumber) {
	$test = "1";
	$limit = "limit 0,4";
}
 $mySql="SELECT S.Id,S.StockId,S.AddQty,S.StuffId,S.BuyerId,S.OrderQty,S.FactualQty,S.Price,S.BuyerId,IFNULL(S.modified,S.ywOrderDTime) AS modified,S.AddRemark,
         A.StuffCname,A.TypeId,A.Picture,C.PreChar,U.Name AS UnitName,P.Forshort,K.tStockQty,K.oStockQty   
         FROM $DataIn.cg1_stocksheet S 
         LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
         LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId  
         LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
         LEFT JOIN $DataIn.stufftype T ON A.TypeId=T.TypeId 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
         LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
         WHERE  $test  AND (S.FactualQty>0 OR S.AddQty>0) AND  T.mainType<2   ORDER BY modified,Id $limit  ";

 $Result=mysql_query($mySql,$link_id);
 while($myRow = mysql_fetch_array($Result)) 
 {
        $Id=$myRow["Id"];
        $TypeId=$myRow["TypeId"];
        $Forshort=$myRow["Forshort"];
        $StuffCname=$myRow["StuffCname"];//配件名称
        $OrderQty=$myRow["OrderQty"];    //订单数量
        $FactualQty=$myRow["FactualQty"];//需求数量
        $AddQty=$myRow["AddQty"];	//增购数量
        $Price=$myRow["Price"];
        $PreChar=$myRow["PreChar"];
        $AddRemark=$myRow["AddRemark"];
        $StockId=$myRow["StockId"];
        $tStockQty=$myRow["tStockQty"];
        $oStockQty=$myRow["oStockQty"];
        
        $StuffId=$myRow["StuffId"];
         $Picture=$myRow["Picture"];
         include "submodel/stuffname_color.php";
        
	     
	    if($TypeId=='9104'){//如果是客户退款，请款总额为订单数*价格
	        $Qty=$OrderQty;	//采购总数		
	    }
	    else{
	        $Qty=$FactualQty+$AddQty;
	    }
	    $Amount=sprintf("%.2f",$Qty*$Price);
	    $Amount=number_format($Amount,2);

      
      $OperateArray=getCgOperateDate($StockId,'3',$DataIn,$link_id);
      if (count($OperateArray)>0){
	        $ywOrderDTime=$OperateArray["Date"];
	        $Operator=$OperateArray["Operator"];
      }
     else{
	       $ywOrderDTime=$myRow["modified"];
	       $Operator=$myRow["BuyerId"];
            include "../../model/subprogram/staffname.php";
     }
     
   // $Date=date("m-d H:i",strtotime($ywOrderDTime));
    $Date=GetDateTimeOutString($ywOrderDTime,'');
    $opHours= geDifferDateTimeNum($ywOrderDTime,"",1);
    if ($opHours>=$timeOut[0]) {$OverNums++;$DateColor="#FF0000";} else $DateColor="";
     
     $Price=number_format($Price,3);
     //$AddRemark="改单价,原价格2.00，再改成3.00";
     $oStockQty=number_format($oStockQty);
     
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"0","hidden"=>"1","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
	                     "Col1"=>array("Text"=>"$Forshort"),
	                     "Col2"=>array("Text"=>"$Qty","IconType"=>"17",'fit'=>'1'),
	                     "Col3"=>array("Text"=>"$oStockQty","IconType"=>"26",'fit'=>'1'),
	                     "Col4"=>array("Text"=>"$PreChar$Price"),
	                     "Remark"=>array("Text"=>"$AddRemark"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator")
                     );
 }

?>