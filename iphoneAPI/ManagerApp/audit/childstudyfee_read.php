<?php 
//助学补助审核
$mySql="SELECT   S.Id,S.Amount,S.Remark,S.Month,S.Attached,S.Date,S.Estate,S.Locks,S.Operator,S.OPdatetime,M.Name,B.Name AS Branch,J.Name AS Job,A.Number,A.ChildName,A.Sex,C.Name AS ClassName
FROM  $DataIn.cw19_studyfeesheet   S 
LEFT JOIN  $DataPublic.childinfo A  ON A.Id=S.cId
LEFT JOIN $DataPublic.childclass C ON C.Id=S.NowSchool
LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Number
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
WHERE  S.Estate=2 ";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']."/download/childinfo/";
 $TotalAmount=0;$Ids="";
 while($myRow = mysql_fetch_array($Result)) 
 {
        $Id=$myRow["Id"];
		$Name=$myRow["Name"];		
		$ChildName=$myRow["ChildName"];
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Amount=$myRow["Amount"];
		$NowSchool=$myRow["NowSchool"];
		$Remark=$myRow["Remark"];
		$Month=$myRow["Month"];
	    $JobName=$myRow["Job"];
		$BranchName=$myRow["Branch"];
		
		$ClassName=$myRow["ClassName"];

		 $Attached=$myRow["Attached"];     
		 
		 $TotalAmount+=$Amount;
         $Amount=number_format($Amount);
    
	    $Date=$myRow["Date"];
	    $Operator=$myRow["Operator"];
	     include "../../model/subprogram/staffname.php";
	
	    $OPdatetime=$myRow["OPdatetime"];
	    $Date=GetDateTimeOutString($OPdatetime,'');
    
     $ImageList=array();  
     if ($Attached!=''){
	     $ImageList[]=array("Title"=>"","Type"=>"JPG","ImageFile"=>"$Dir$Attached" );
     }
   
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$Name","Text2"=>"$BranchName-$JobName"),
	                     "Col1"=>array("Text"=>"$ChildName($Sex)"),
	                     "Col2"=>array("Text"=>"¥$Amount","Margin"=>"20,0,0,0" ),
	                     "Col4"=>array("Text"=>"$ClassName","Margin"=>"-50,0,50,0"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("ImageList"=>$ImageList)
                     );
      $Ids=$Ids==""?$Id:"$Ids,$Id";              
 }
 
 if ($TotalAmount>0){
      $TotalAmount=number_format($TotalAmount);
	  $dataArray2[]=array(
	                     "Tag"=>"Total",
	                      "Id"=>"$Ids",
	                     "onTap"=>array("Value"=>"0","hidden"=>"1","Args"=>"","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"合计"),
	                     "Col2"=>array("Text"=>"¥$TotalAmount" )
                     );  
       array_splice($dataArray,0,0,$dataArray2);
 }

?>