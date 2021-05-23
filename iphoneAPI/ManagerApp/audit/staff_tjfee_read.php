<?php 
//体检费审核
$mySql="SELECT 
	S.Id,S.Number,S.Month,S.Amount,S.Locks,S.Date,S.Operator,S.Estate,S.Mid,S.OPdatetime,P.Name,B.Name AS BranchName,J.Name AS JobName,S.Remark,S.Attached ,S.tjType,P.ComeIn,S.CheckT,S.HG
	 FROM $DataIn.cw17_tjsheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
    LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	WHERE S.Estate=2";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']."/download/tjfile/";
 $GDnumber= array("①","①","②","③","④","⑤","⑥","⑦","⑧","⑨","⑩");
 while($myRow = mysql_fetch_array($Result)) 
 {
        $Id=$myRow["Id"];
		$Name=$myRow["Name"];		
		$Amount=$myRow["Amount"];
		$Remark=$myRow["Remark"];
	    $JobName=$myRow["JobName"];
		$BranchName=$myRow["BranchName"];
		$ComeIn=$myRow["ComeIn"];

		 $Attached=$myRow["Attached"];     
		 
         $Amount=number_format($Amount,2);
        
        $tjType=$myRow["tjType"];
        $CheckT=$myRow["CheckT"];
        $CheckTime=$GDnumber[$CheckT];
        switch($tjType){
            case "1":  $tjType="岗前体检".$CheckTime;  break;
            case "2":  $tjType="岗中体检".$CheckTime;  break;
            case "3":  $tjType="离职体检".$CheckTime;  break;
			case "4":  $tjType="健康体检".$CheckTime;  break;
            }
        $HG=$myRow["HG"];
         if ($HG==1){
	           $HG="合格";$HG_Color="#00FF00";
         }      
         else{
	          $HG="不合格";$HG_Color="#FF0000";
         }   
         
	    $Operator=$myRow["Operator"];
	     include "../../model/subprogram/staffname.php";
	
	    $OPdatetime=$myRow["OPdatetime"];
	    $Date=GetDateTimeOutString($OPdatetime,'');
    
     $ImageList=array();  
     if ($Attached!=''){
	     $ImageList[]=array("Title"=>"","Type"=>"JPG","ImageFile"=>"$Dir$Attached" );
     }
		
	$tapValue=count($ImageList)>0?1:0;
   
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"$tapValue","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$Name","Text2"=>"$BranchName-$JobName"),
	                     "Month"=>array("Text"=>"$ComeIn" ),
	                     "Col1"=>array("Text"=>"$tjType"),
	                     "Col2"=>array("Text"=>"¥$Amount","Margin"=>"20,0,0,0" ),
	                     "Col4"=>array("Text"=>"$HG","Color"=>"$HG_Color"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("ImageList"=>$ImageList)
                     );          
 }

?>