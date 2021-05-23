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
$Layout=array(			'Title'=>array('Frame'=>'20, 2, 230, 25'),
'Col1'=>array('Frame'=>'260, 2, 50, 25','Align'=>'R'),
						"Col2"=>array("Frame"=>"32,32,55, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"167,32,55, 15","Align"=>"L"),
                        "Col4"=>array("Frame"=>"260,32,55, 15","Align"=>"L"));
                         
 //图标设置                        
$IconSet=array("Col2"=>array("Name"=>"scdj_11","Frame"=>"120,35,10,10"),
                          "Col3"=>array("Name"=>"scdj_12","Frame"=>"185,35,10,10"),
                          "Col4"=>array("Name"=>"scdj_13","Frame"=>"250,35,10,10"),
                          );

$IconSet1=array("Col2"=>array("Name"=>"iquality","Frame"=>"20,35,10,10"),
                           "Col3"=>array("Name"=>"iwrong","Frame"=>"155,35,10,10")
                          );
                          
$SegmentIndex=$SegmentIndex==""?0:$SegmentIndex;

{
                          
$myResult=mysql_query("SELECT  DATE_FORMAT(B.Date,'%Y-%m') AS Month,SUM(B.shQty) AS shQty,SUM(B.Qty) AS Qty,COUNT(*) AS Counts   
			FROM  $DataIn.qc_badrecord  B 
			LEFT JOIN $DataIn.gys_shmain M  ON M.Id=B.shMid 
			
			WHERE  M.Floor='$Floor'   GROUP BY  DATE_FORMAT(B.Date,'%Y-%m') ORDER BY Month DESC ",$link_id);
 $jsondata=array();
 $curTime=date("Y-m-d H:i:s");
$dataArray=array();
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
							                      "onTap"=>array("Value"=>"1","Args"=>"List3|$Month"),
							                      "Title"=>array("Text"=>"$Month"),
							                      "Col1"=>array("Text"=>"$Qty","Color"=>"#FF0000"),
							                      "Col3"=>array("Text"=>"$shQty","Margin"=>"0,0,10,0")
							                   );  
			
			$dataArray=array();
			if ($hidden==0){
				  $FromPage="Read";
				  $CheckMonth=$Month;
				  include "ck_qc_record_list3.php";
			}
						                   
             $jsondata[]=array("head"=>$headArray,"hidden"=>"$hidden","IconSet"=>$IconSet1,"Layout"=>$Layout,"data"=>$dataArray); 
             $hidden=1;
      } while($myRow = mysql_fetch_array($myResult));
   }
} 


	
$jsonArray=array("data"=>$jsondata,'switch'=>'2');



?>
