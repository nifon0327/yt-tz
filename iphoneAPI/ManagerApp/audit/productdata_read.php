<?php 
//产品资料审核

$condition = " P.Estate=2 ";
$limited = "";
/*

if ($LoginNumber == "11965") {
	$condition = "1 ";
	$limited = "limit 0,5";
}
*/

$mySql= "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.Moq,P.CompanyId,P.Description,P.Remark,P.pRemark,P.bjRemark,
	P.TestStandard,P.Date,P.PackingUnit,P.Estate,P.Locks,P.Code,P.Operator,P.OPdatetime,T.TypeName,C.Forshort,D.Rate,D.PreChar 
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE  $condition  order by OPdatetime desc $limited ";

 $Result=mysql_query($mySql,$link_id);
  $Dir=  "http://".$_SERVER ['HTTP_HOST']. "/download/teststandard/";
 while($myRow = mysql_fetch_array($Result)) 
 {
    $Id=$myRow["Id"];
    $ProductId=$myRow["ProductId"];
    $Forshort=$myRow["Forshort"];
    $cName=$myRow["cName"];//产品名称
    $TestStandard=$myRow["TestStandard"];	
	include "order/order_TestStandard.php";	
	$ImageFile=$Dir . "T$ProductId.jpg";

    $Price=$myRow["Price"];
    $Rate=$myRow["Rate"];
    $TypeName=$myRow["TypeName"];
       
    $OPdatetime=$myRow["OPdatetime"];
   $aDate = $myRow["Date"];
   $maxDate = $OPdatetime;
   if (strtotime($aDate) > strtotime($maxDate)) {
	   $maxDate = $aDate;
	}
    $PreChar=$myRow["PreChar"];
    $Remark=$myRow["Remark"];

    $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
     
     $listArray=array();
    $Code=$myRow["Code"];
    $eCode=$myRow["eCode"];
    $bjRemark=$myRow["bjRemark"];
    $Description=$myRow["Description"];
    $listArray[]=array("Cols"=>"1","Name"=>"Code:","Text"=>"$eCode");
    $listArray[]=array("Cols"=>"1","Name"=>"外箱条码:","Text"=>"$Code");
    $listArray[]=array("Cols"=>"1","Name"=>"报价规则:","Text"=>"$bjRemark");
    $listArray[]=array("Cols"=>"1","Name"=>"描       述:","Text"=>"$Description");  
    
        $listArray1=array();
       $saleRMB=sprintf("%.2f",$Price*$Rate);//产品销售RMB价格
       $Price=number_format($myRow["Price"],3);
		$profitRMB_STR="未设定";$profitColor="";
		$StuffResult = mysql_query("SELECT A.Relation,B.Price,E.Rate,E.PreChar,D.Currency,A.StuffId,B.StuffCname,B.Picture,D.Forshort,ST.mainType,MT.TypeColor 
		, B.OPdatetime 
		FROM $DataIn.pands A
		LEFT JOIN $DataIn.stuffdata B ON B.StuffId=A.StuffId
		LEFT JOIN $DataIn.bps C ON C.StuffId=B.StuffId
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=C.CompanyId		
		LEFT JOIN $DataPublic.currencydata E ON E.Id=D.Currency	
		LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=B.TypeId 	
		LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType 
		WHERE A.ProductId='$ProductId' order by A.Id",$link_id);
		if($StuffmyRow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			$buyRMB=0;$buyHZsum=0;
			$m=1;
			do{	
			
			$aaDate = $StuffmyRow["OPdatetime"];
			 if (strtotime($aaDate) > strtotime($maxDate)) {
	  			 $maxDate = $aaDate;
			}
			
			
				$stuffPrice=$StuffmyRow["Price"];
				$stuffRelation=$StuffmyRow["Relation"];
				$stuffRate=$StuffmyRow["Rate"];//加密
				$CurrencyTemp=$StuffmyRow["Currency"];
				    
				//成本
				$OppositeRelation=explode("/",$stuffRelation);
				if ($OppositeRelation[1]!=""){//非整数对应关系
					$thisRMB=sprintf("%.4f",$stuffRate*$stuffPrice*$OppositeRelation[0]/$OppositeRelation[1]);
					}
				else{//整数对应关系
					$thisRMB=sprintf("%.4f",$stuffRate*$stuffPrice*$OppositeRelation[0]);
					}
				$buyRMB=$buyRMB+$thisRMB;	//总成本
				if($CurrencyTemp!=2){		//非外购
					$buyHZsum+=$thisRMB;
				}
				
				$StuffId=$StuffmyRow["StuffId"];
				$StuffCname=$StuffmyRow["StuffCname"];
				$Picture=$StuffmyRow["Picture"];
			    include "submodel/stuffname_color.php";
				 
				$stuffForshort=$StuffmyRow["Forshort"];
				$stuffPreChar=$StuffmyRow["PreChar"];
				$TypeColor=$StuffmyRow["mainType"]==1?"":$StuffmyRow["TypeColor"];
				 
				 $stuffPrice=number_format($stuffPrice,3);
				 $thisRMB=number_format($thisRMB,3);
				 $listArray1[]=array(
				         "RowSet"=>array("bgColor"=>"$TypeColor"),
			             "Index"=>array("Text"=>"$m."),
			             "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
	                     "Col1"=>array("Text"=>"$stuffForshort"),
	                     "Col2"=>array("Text"=>"$stuffPreChar$stuffPrice"),
	                     "Col3"=>array("Text"=>"$stuffRelation"),
	                     "Col4"=>array("Text"=>"¥$thisRMB"),
			    );
				$m++;
				}while($StuffmyRow=mysql_fetch_array($StuffResult));
			$profitRMB=sprintf("%.2f",$saleRMB-$buyRMB-$buyHZsum*$HzRate);
			$profitRMBPC=$saleRMB>0?sprintf("%.0f",($profitRMB*100/$saleRMB)):0;
			$profitRMB_STR="$profitRMB($profitRMBPC%)";
			
			$buyRMB=number_format($buyRMB,3);
			$listArray[]=array("Cols"=>"1","Type"=>"Total","Name"=>"BOM","Text"=>"¥$buyRMB");  
			if ($profitRMBPC>15){
                     $profitColor="#009900";
		    }
		    else{
		      $profitColor=$profitRMBPC>=7?"#FF6633":"#FF0000";
		    }
   }
        $Date=GetDateTimeOutString($maxDate,'');
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$ProductId-$cName","Color"=>"$TestStandardColor"),
	                     "Col1"=>array("Text"=>"$TypeName","Margin"=>"0,0,10,0"),
	                     "Col2"=>array("Text"=>"$Forshort","Margin"=>"10,0,0,0"),
	                     "Col3"=>array("Text"=>"$PreChar$Price","Margin"=>"20,0,0,0"),
	                     "Col4"=>array("Text"=>"$profitRMB_STR","Color"=>"$profitColor"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"$TestStandard","Type"=>"JPG","ImageFile"=>"$ImageFile","data"=>$listArray,"data1"=>$listArray1)
                     );
 }

?>