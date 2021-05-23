<?php 
//离职补助审核
$cztest = ' AND S.Estate=2 ';
$czLimt = '';

function file_existsa($url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	$result = curl_exec($curl);
	$found = false;
	if ($result !== false) {
		$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($statusCode == 200) {
			$found = true;
		}
	}
	return $found;
}
if ($LoginNumber == "11965") {
	$cztest ="";
	$czLimt = " limit 0,5";
}
$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.ReturnReasons,S.Date,S.Estate,S.OPdateTime,S.Locks,S.Operator,C.PreChar,C.Symbol AS Currency,S.TotalRate,S.Time,M.Name,B.Name AS Branch,M.ComeIn,D.outDate,S.AveAmount,S.Number,T.Name AS TypeName,S.PaySign,S.Content AS LeaveReason,S.TypeId ,J.Name AS JobName 
 	FROM $DataIn.staff_outsubsidysheet S 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
    LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	
LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
    LEFT JOIN $DataPublic.dimissiondata D ON D.Number=M.Number 
    LEFT JOIN $DataPublic.dimissiontype T ON T.Id =D.LeaveType
	WHERE 1  $cztest order by S.Date DESC $czLimt ";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']."/download/staff_subsidy/";
 $fileE ="www.middlecloud.com/download/staff_subsidy/";
 while($myRow = mysql_fetch_array($Result)) 
 {
	 
	    $m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Date=$myRow["OPdateTime"];
		$Number=$myRow["Number"];
		$Amount=$myRow["Amount"];
       $AveAmount=$myRow["AveAmount"];
		$Currency=$myRow["Currency"];		
		$Content=$myRow["Content"];
		$Name=$myRow["Name"];

     
		$ComeIn=$myRow["ComeIn"];
       
		$LeaveReason=$myRow["LeaveReason"];
	    $PreChar=$myRow["PreChar"];
   
	    $JobName=$myRow["JobName"];
		$BranchName=$myRow["Branch"];

         $Amount=number_format($Amount,2);
		 
		     $TotalRate =$myRow["TotalRate"];
       $Time ="第".$myRow["Time"]."次";
	   $TimeColor = '#000000';
       $PaySign =$myRow["PaySign"];
       if($PaySign==1) {
		   $Time="一次性支付";
		   $TimeColor = '#FF0000';
	   }
       $Rate =$TotalRate."个月";
        

	    $Operator=$myRow["Operator"];
	     include "../../model/subprogram/staffname.php";
	
	  
	    $Date=GetDateTimeOutString($Date,'');
    $Attached = $Number;
     $ImageList=array(); 
	 $filea = "$Dir$Attached.jpg";
	 $tapValueA = 0;
	
     if ($Attached!='' && @fopen($filea,'r')){
		 $tapValueA  =1;
	     $ImageList[]=array("Title"=>"","Type"=>"JPG","ImageFile"=>"$Dir$Attached.jpg","info"=>$headers );
     }
		
	$tapValue=count($ImageList)>0?1:0;
   
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"$tapValueA","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$Name","Text2"=>"$BranchName-$JobName"),
	                     "Month"=>array("Text"=>"$Time","Color"=>"$TimeColor" ),
	                     "Col1"=>array("Text"=>"$PreChar$AveAmount","Color"=>"#000000"),
	                     "Col2"=>array("Text"=>"$Rate","Margin"=>"40,0,0,0" ,"Color"=>"#000000"),
	                     "Col4"=>array("Text"=>"$PreChar$Amount","Color"=>"#000000"),
	                     "Remark"=>array("Text"=>"$LeaveReason"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("ImageList"=>$ImageList)
                     );          
 }

?>