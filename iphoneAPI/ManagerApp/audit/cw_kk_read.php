<?php 
//扣款审核
$cztest = ' AND M.Estate=1 ';
$czLimt = '';
/*
if ($test_cz) {
	$cztest = '';
	$czLimt = 'limit 0,3';
}
*/
$mySql="SELECT M.Operator,M.OPdateTime,M.Remark,
		M.Id,M.BillNumber,M.Date,M.TotalAmount,M.BillFile,P.Forshort,M.Picture,
		sum(S.Qty) as QtyTotal,S.Price,C.PreChar,S.StuffName
        FROM d7.cw15_gyskksheet S
		Left join $DataIn.cw15_gyskkmain M on S.Mid=M.id   
		LEFT JOIN $DataIn.trade_object  P ON P.CompanyId=M.CompanyId   	
		LEFT JOIN $DataPublic.currencydata C on C.Id=P.currency 	
		WHERE 1 $cztest  group by M.id $czLimt 
		";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']."/download/cgkkbill/";
 while($myRow = mysql_fetch_array($Result)) 
 {
	 
	    $m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["OPdateTime"];
		$Number=$myRow["BillNumber"];
		$BillFile = $myRow["BillFile"];
		$Amount=$myRow["TotalAmount"];
       $AveAmount=$myRow["Price"];
		$Currency=$myRow["PreChar"];		
		$Content=$myRow["StuffName"];
		$Name=$myRow["Forshort"];
		
		$Qty=$myRow["QtyTotal"];
       $isPic = $myRow["Picture"];
		$LeaveReason=$myRow["Remark"];
	 

         $Amount=number_format($Amount,2);
		 

	    $Operator=$myRow["Operator"];
	     include "../../model/subprogram/staffname.php";
	
	  
	    $Date=GetDateTimeOutString($Date,'');
    $Attached = $Number;
     $ImageList=array();  
     if ($BillFile==1){
		 
	     $ImageList[]=array("Title"=>"$Number","Type"=>($isPic == 1)?"JPG":"PDF","ImageFile"=>"$Dir$Attached.".(($isPic == 1)?"jpg":"pdf") );
     }
		
	$tapValue=count($ImageList)>0?1:0;
	
	 $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$Content"),
	                     "Col1"=>array("Text"=>"$Name"),
	                     "Col2"=>array("Text"=>"$Qty"),
	                     "Col3"=>array("Text"=>"$Currency$AveAmount"),
	                     "Col4"=>array("Text"=>"$Currency$Amount"),
	                     "Remark"=>array("Text"=>"$LeaveReason"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("ImageList"=>$ImageList)
                     );
   
      
 }

?>