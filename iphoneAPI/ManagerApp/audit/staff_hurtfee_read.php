<?php 
//工伤费审核
$mySql="SELECT 
	S.Id,S.Number,S.Month,S.Amount,S.SSecurityAmout,S.AllAmout,S.Locks,S.Date,S.Operator,S.OPdatetime,S.Estate,S.Mid,P.Name,B.Name AS BranchName,J.Name AS JobName,S.Remark,S.Attached ,P.ComeIn,S.CheckT,S.HurtDate,S.HostpitalInvoice,S.SSecurityInvoice
	 FROM $DataIn.cw18_workhurtsheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
    LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	WHERE  S.Estate=2 ";	

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']."/download/Hurtfile/";
 while($myRow = mysql_fetch_array($Result)) 
 {
        $Id=$myRow["Id"];
		$Name=$myRow["Name"];		
		$Amount=$myRow["Amount"];
		$Remark=$myRow["Remark"];
	    $JobName=$myRow["JobName"];
		$BranchName=$myRow["BranchName"];

		 $Attached=$myRow["Attached"];     
		 
         $Amount=number_format($Amount,2);
    
	    $Operator=$myRow["Operator"];
	     include "../../model/subprogram/staffname.php";
	
	    $OPdatetime=$myRow["OPdatetime"];
	    $Date=GetDateTimeOutString($OPdatetime,'');
    
     $ImageList=array();  
     if ($Attached!=''){
	     $ImageList[]=array("Title"=>"工伤凭证","Type"=>"JPG","ImageFile"=>"$Dir$Attached" );
     }
     
     $HostpitalInvoice=$myRow["HostpitalInvoice"];
      if($HostpitalInvoice!="" ){
           $ImageList[]=array("Title"=>"社保凭证","Type"=>"JPG","ImageFile"=>"$Dir$HostpitalInvoice" );
      }
      
      $SSecurityInvoice=$myRow["SSecurityInvoice"];
		if($SSecurityInvoice!="" ){
		    $ImageList[]=array("Title"=>"费用凭证","Type"=>"JPG","ImageFile"=>"$Dir$SSecurityInvoice" );
		}
		
		$tapValue=count($ImageList)>0?1:0;
   
    $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"$tapValue","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Col1"=>array("Text"=>"$Name"),
	                     "Col2"=>array("Text"=>"$BranchName-$JobName"),
	                     "Col4"=>array("Text"=>"¥$Amount"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("ImageList"=>$ImageList) 
                     );          
 }

?>