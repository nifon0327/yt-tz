<?php 
//抽检、全检
 include "../../basic/downloadFileIP.php";
 $SearchRows="";
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   
// $noSegment 
$SegmentArray=array();
$SegmentIdArray=array();
$k=0;

if ($Floor=="") $Floor=$dModuleId==2152?3:6;
	    
//布局设置
$Layout=array("Col2"=>array("Frame"=>"122,32,55, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"182,32,45, 15","Align"=>"L"),
                        "Col4"=>array("Frame"=>"230,32,55, 15","Align"=>"L"));
                         
 //图标设置                        
$IconSet=array("Col2"=>array("Name"=>"scdj_11","Frame"=>"110,35,10,10"),
                          "Col3"=>array("Name"=>"scdj_12","Frame"=>"170,35,10,10"),
                          "Col4"=>array("Name"=>"scdj_13","Frame"=>"220,35,10,10"),
                          );

$IconSet1=array("Col2"=>array("Name"=>"qc_check","Frame"=>"110,35,10,10"),
                           "Col3"=>array("Name"=>"iwrong","Frame"=>"200,35,10,10")
                          );
                          
$SegmentIndex=$SegmentIndex==""?0:$SegmentIndex;

if ($SegmentIndex==0){
	  include "ck_qc_record_sub1.php";
	  $COUNT_0=$m;
}
else{
                          
$myResult=mysql_query("SELECT  DATE_FORMAT(B.Date,'%Y-%m') AS Month,SUM(B.shQty) AS shQty,SUM(B.Qty) AS Qty,COUNT(*) AS Counts   
			FROM  $DataIn.qc_badrecord  B 
			INNER JOIN $DataIn.gys_shmain M  ON M.Id=B.shMid 
			WHERE  M.Floor='$Floor'   GROUP BY  DATE_FORMAT(B.Date,'%Y-%m') ORDER BY Month DESC ",$link_id);
 $jsondata=array();
 $curTime=date("Y-m-d H:i:s");
$dataArray=array();
$hidden=0;
 if($myRow = mysql_fetch_array($myResult)) 
  {
     do {
            $Month=$myRow["Month"];
            $shQty=number_format($myRow["shQty"]);
            $Qty=number_format($myRow["Qty"]);
            $Counts=$myRow["Counts"];
            
             $headArray=array(
							                      "Id"=>"$Month",
							                      "onTap"=>array("Value"=>"1","Args"=>"List0|$Month"),
							                      "Title"=>array("Text"=>"$Month"),
							                      "Col1"=>array("Text"=>"$Qty","Color"=>"#FF0000"),
							                      "Col3"=>array("Text"=>"$shQty","Margin"=>"0,0,10,0")
							                   );  
			
			$dataArray=array();
			if ($hidden==0){
				  $FromPage="Read";
				  $CheckMonth=$Month;
				  include "ck_qc_record_list0.php";
			}
						                   
             $jsondata[]=array("head"=>$headArray,"hidden"=>"$hidden","IconSet"=>$IconSet1,"Layout"=>$Layout,"data"=>$dataArray); 
             $hidden=1;
      } while($myRow = mysql_fetch_array($myResult));
   }
} 
if ($noSegment != 1) {
	
	$CountResult=mysql_fetch_array(mysql_query("SELECT  COUNT(*) AS Counts   
			FROM  $DataIn.qc_badrecord  B 
			INNER JOIN $DataIn.gys_shmain M  ON M.Id=B.shMid 
			WHERE  DATE_FORMAT(B.Date,'%Y-%m-%d')=CURDATE() AND M.Floor='$Floor' ",$link_id));
$COUNT_1=$CountResult["Counts"]==""?0:$CountResult["Counts"];
		
 $SegmentArray=array("待处理($COUNT_0)","已处理($COUNT_1)");
 $SegmentIdArray=array("0","1");
 

$jsonArray=array("Segment"=>array("Segmented"=>$SegmentArray,"SegmentedId"=>$SegmentIdArray,"SegmentIndex"=>"$SegmentIndex"),"data"=>$jsondata,"printIp"=>"192.168.30.102"); 
} else {
	                         
$myResult=mysql_query("SELECT  DATE_FORMAT(B.Date,'%Y-%m') AS Month,SUM(B.shQty) AS shQty,SUM(B.Qty) AS Qty,COUNT(*) AS Counts   
			FROM  $DataIn.qc_badrecord  B 
			INNER JOIN $DataIn.gys_shmain M  ON M.Id=B.shMid 
			WHERE  M.Floor='$Floor'   GROUP BY  DATE_FORMAT(B.Date,'%Y-%m') ORDER BY Month DESC ",$link_id);
 $jsondataNew=array();
 $curTime=date("Y-m-d H:i:s");
$hidden=1;
 if($myRow = mysql_fetch_array($myResult)) 
  {
     do {
            $Month=$myRow["Month"];
            $shQty=number_format($myRow["shQty"]);
            $Qty=number_format($myRow["Qty"]);
            $Counts=$myRow["Counts"];
            
             $headArray=array(
							                      "Id"=>"$Month",
							                      "onTap"=>array("Value"=>"1","Args"=>"List0|$Month"),
							                      "Title"=>array("Text"=>"$Month"),
							                      "Col1"=>array("Text"=>"$Qty","Color"=>"#FF0000"),
							                      "Col3"=>array("Text"=>"$shQty","Margin"=>"0,0,10,0")
							                   );  
			
			$dataArray=array();
			if ($hidden==0){
				  $FromPage="Read";
				  $CheckMonth=$Month;
				  include "ck_qc_record_list0.php";
			}
						                   
             $jsondata[]=array("head"=>$headArray,"hidden"=>"$hidden","IconSet"=>$IconSet1,"Layout"=>$Layout,"data"=>$dataArray); 
             $hidden=1;
      } while($myRow = mysql_fetch_array($myResult));
   }

	
$jsonArray=array("data"=>$jsondata,'multi'=>'1','switch'=>'1');
}


?>
