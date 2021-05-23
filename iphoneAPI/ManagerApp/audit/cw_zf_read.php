<? /*
if($TypeId==1){
    $TempTable="ch1_shipmain";
	$SearchRows.=" AND F.TypeId='$TypeId'";
    $mySql="SELECT M.Number,M.InvoiceNO,M.Date AS ShipDate,M.InvoiceFile,
    F.Id,F.chId,F.HoldNO,F.ForwardNO,F.BoxQty,F.mcWG,F.forwardWG,F.Volume,F.Amount,F.InvoiceDate,F.PayType,
    F.ETD,F.Remark,F.Estate,F.Locks,F.Date,F.Operator,D.Forshort,I.Mid
    FROM $DataIn.ch3_forward F
    LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
    LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
    LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
    WHERE 1 $SearchRows AND F.Estate='2' AND M.Number>0 ORDER BY F.Id DESC";
    }
else{
    $TempTable="ch1_deliverymain";
	$SearchRows.=" AND F.TypeId='$TypeId'";
    $mySql="SELECT M.DeliveryNumber AS InvoiceNO,M.DeliveryDate AS ShipDate,
    F.Id,F.chId,F.HoldNO,F.ForwardNO,F.BoxQty,F.mcWG,F.forwardWG,F.Volume,F.Amount,F.InvoiceDate,F.PayType,
    F.ETD,F.Remark,F.Estate,F.Locks,F.Date,F.Operator,D.Forshort,I.Mid
    FROM $DataIn.ch3_forward F
    LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
    LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
    LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
    WHERE 1 $SearchRows AND F.Estate='2' ORDER BY F.Id DESC";
    }
    //echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
*/ ?>

<?php 
//审核
$cztest = ' AND F.Estate=2 ';
$czLimt = '';

if ($LoginNumber == "11965") {
	$cztest = '';
	$czLimt = 'limit 0,3';
}
/*

*/
 $TempTable="ch1_shipmain";
	$SearchRows=" AND F.TypeId='1'";
    $mySql="SELECT M.Number,M.InvoiceNO,M.Date AS ShipDate,M.InvoiceFile,
    F.Id,F.chId,F.HoldNO,F.ForwardNO,F.BoxQty,F.mcWG,F.forwardWG,F.Volume,F.Amount,F.InvoiceDate,F.PayType,
    F.ETD,F.Remark,F.Estate,F.Locks,F.modified AS Date,F.Operator,D.Forshort,I.Mid,C.PreChar
    FROM $DataIn.ch3_forward F
    LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
    LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
    LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency
    WHERE 1 $cztest  AND M.Number>0 ORDER BY F.Id DESC $czLimt";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']."/download/invoice/";
 $Dir1= "http://".$_SERVER ['HTTP_HOST']."/download/expressbill/";
 while($myRow = mysql_fetch_array($Result)) 
 {
	 
	    $m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Name=$myRow["Forshort"];

		$ShipDate=$myRow["ShipDate"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$ForwardNO=$myRow["ForwardNO"];	
		$InvoiceFile=$myRow["InvoiceFile"];		
		
		if ($InvoiceFile!=0) {
			
		}
		$ShipDate=$myRow["ShipDate"];
		$BoxQty=$myRow["BoxQty"];
		$mcWG=$myRow["mcWG"];
		$forwardWG=$myRow["forwardWG"];
		$Amount=$myRow["Amount"];
       

     $preChar = $myRow["PreChar"];
		$ComeIn=$myRow["ComeIn"];
       
		$LeaveReason=$myRow["Remark"];
	 
   
	

         $Amount=sprintf("%.2f",$Amount);
		 

        

	    $Operator=$myRow["Operator"];
	     include "../../model/subprogram/staffname.php";
	
	  $Date = $myRow["Date"];
	    $Date=GetDateTimeOutString($Date,'');

     $ImageList=array();  
    
		$Lading=$Dir1.$ForwardNO.".jpg";
		if($ForwardNO && $ForwardNO!=""){
			
			 $ImageList[]=array("Title"=>"$ForwardNO","Type"=>"JPG","ImageFile"=>$Lading );
		}
		 if ($InvoiceFile!=0){
	     $ImageList[]=array("Title"=>"$InvoiceNO","Type"=>"PDF","ImageFile"=>"$Dir$InvoiceNO.pdf" );
     }
	$tapValue=count($ImageList)>0?1:0;
    $len =  strlen($Name);
	if ($len>=12) {
		$Name = substr($Name,0,12)."...";
	}
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"$tapValue","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$Name","Text2"=>"$ForwardNO"),
	                     "Month"=>array("Text"=>"$ShipDate","Margin"=>"0,2.5,0,0","LIcon"=>"iship" ),
	                     "Col1"=>array("Text"=>"$mcWG","Color"=>"#000000"),
	                     "Col2"=>array("Text"=>"$forwardWG","Color"=>"#000000"),
	                     
						 "Col4"=>array("Text"=>"$BoxQty"."件    $preChar$Amount","Margin"=>"-68,0,68,0","Color"=>"#000000"),
	                     "Remark"=>array("Text"=>"$LeaveReason"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("ImageList"=>$ImageList)
                     );          
 }

?>