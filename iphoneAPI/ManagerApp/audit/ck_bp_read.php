<?php 
//备品转入审核  LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
include "../../basic/downloadFileIP.php";
$cztest = ' AND B.Estate=1 ';
$czLimt = '';
/*
if ($test_cz) {
	$cztest = '';
	$czLimt = 'limit 0,3';
}
*/

 $mySql="SELECT B.Id,B.Date,B.StuffId,B.Qty,D.Price*B.Qty AS Amount,B.Remark,B.Operator,D.StuffCname,D.Price,K.tStockQty,K.oStockQty,D.Picture
FROM $DataIn.ck7_bprk B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId
WHERE 1 $cztest ORDER BY  B.Id DESC $czLimt" ;

 $Result=mysql_query($mySql,$link_id);
 $Dir= "$donwloadFileIP/download/stufffile/";
 while($myRow = mysql_fetch_array($Result)) 
 {
    $Id=$myRow["Id"];
    $StuffId=$myRow["StuffId"];
	
	$hPrice=0;
		$lPrice=0;
	$PriceResult = mysql_query("SELECT S.Price,M.Date,C.PreChar,P.Forshort 
	 FROM $DataIn.cg1_stocksheet S
	 LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
	 LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id
	 LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	 LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	 WHERE S.StuffId=$StuffId and S.Mid!=0 group by S.Price order by M.Date",$link_id);
	 if($PriceRows = mysql_fetch_array($PriceResult)){
		$i=1;
		
                		
		do{
			$Date=$PriceRows["Date"];
			$Price=$PriceRows["Price"];
		
			if($i==1){
				$hPrice=$Price;
				$lPrice=$Price;
				}
			else{
				$hPrice=$Price>$hPrice?$Price:$hPrice;
				$lPrice=$Price<$lPrice?$Price:$lPrice;
				}
	
                       
			
			
			$i++;
			}while($PriceRows = mysql_fetch_array($PriceResult));
			;
		}
	else{

	}
	
    $ProductId=$myRow["ProductId"];
    $Forshort=$myRow["Forshort"];
	
	$Amount = round($myRow["Amount"],1);
	
	$tStockQty = $myRow["tStockQty"];
	$oStockQty = $myRow["oStockQty"];
	

    $cName=$myRow["StuffCname"];//名称
    
	  $Picture=$myRow["Picture"];
     include "submodel/stuffname_color.php";
    $ImageFile=$Picture>0?"$Dir".$StuffId. "_s.jpg":"";
	
    $Price=sprintf("%.4f",$myRow["Price"]);
    


		  
    $Qty=($myRow["Qty"]);

    $Date=$myRow["Date"];
    
    
    //$Date=date("m-d H:i",strtotime($OPdatetime));
   $Date=GetDateTimeOutString($Date,'');
   

    $Remark=$myRow["Remark"];
 
    $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
     
     $listArray=array();
     $listArray[]=array("Cols"=>"1","Name"=>"历史单价:","Text"=>"H:$hPrice    L:$lPrice","Tag"=>"Order");
     $listArray[]=array("Cols"=>"1","Name"=>"可用库存:","Text"=>"$oStockQty");
	 $listArray[]=array("Cols"=>"1","Name"=>" 在       库:","Text"=>"$tStockQty");
      
     $dataArray[]=array(
	                     "Id"=>"$Id",//,"LIcon"=>"ibl_r"
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$cName","Color"=>"#FF8C00"),
	                     "Col1"=>array("Text"=>"$Qty","Margin"=>"12,0,0,0","Color"=>"#000000"),
	                   
	                     "Col3"=>array("Text"=>"¥$Price","Color"=>"#000000"),
	                     "Col4"=>array("Text"=>"¥$Amount","Color"=>"#000000"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"$Picture","Type"=>"JPG","ImageFile"=>"$ImageFile","data"=>$listArray)
                     );
 }

?>