<?php 
//抽检、全检
 include "../../basic/downloadFileIP.php";
 $SearchRows="";
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   

$SegmentArray=array();
$SegmentIdArray=array();
$k=0;
//布局设置
$Layout=array(
                        "Title"=>array("Frame"=>"18, 2, 290, 25"),
						"Col2"=>array("Frame"=>"132,32,55, 15","Align"=>"L"),
                        "Col3"=>array("Frame"=>"197,32,55, 15","Align"=>"L"),
                        "Col4"=>array("Frame"=>"260,32,55, 15","Align"=>"R"));
                         
 //图标设置                        
$IconSet=array("Col1"=>array("Name"=>"iquality","Frame"=>"23,35,10,10"),
                           "Col2"=>array("Name"=>"iwrong","Frame"=>"120,35,10,10")
                          );
 
  //图标设置                        
$IconSet1=array("Col1"=>array("Name"=>"iwrong","Frame"=>"18,35,10,10")
                          );                         
                          
$SegmentIndex=$SegmentIndex==""?0:$SegmentIndex;

if ($SegmentIndex==0){
	  include "ck_th_read_sub1.php";
	  $COUNT_0=$m;
}
else{
                          
$myResult=mysql_query("SELECT  DATE_FORMAT(M.Date,'%Y-%m') AS Month,SUM(S.Qty) AS Qty,COUNT(*) AS Counts   
			FROM  $DataIn.ck12_thmain M  
			LEFT JOIN $DataIn.ck12_thsheet S ON S.Mid=M.Id  
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			WHERE   D.SendFloor='$Floor'   GROUP BY  DATE_FORMAT(M.Date,'%Y-%m') ORDER BY Month DESC ",$link_id);
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
							                      "Col3"=>array("Text"=>"$Qty($Counts)","Margin"=>"0,0,10,0")
							                   );  
			
			$dataArray=array();
			if ($hidden==0){
				  $FromPage="Read";
				  $CheckMonth=$Month;
				  include "ck_th_record_list0.php";
			}
						                   
             $jsondata[]=array("head"=>$headArray,"hidden"=>"$hidden","IconSet"=>$IconSet1,"Layout"=>$Layout,"data"=>$dataArray); 
             $hidden=1;
      } while($myRow = mysql_fetch_array($myResult));
   }
} 

$CountResult=mysql_fetch_array(mysql_query("SELECT  COUNT(*) AS Counts   
			FROM  $DataIn.ck12_thmain M  
			LEFT JOIN $DataIn.ck12_thsheet S ON S.Mid=M.Id  
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId  
			WHERE  DATE_FORMAT(M.Date,'%Y-%m-%d')=CURDATE() AND D.SendFloor='$Floor' ",$link_id));
$COUNT_1=$CountResult["Counts"]==""?0:$CountResult["Counts"];
		
 $SegmentArray=array("待处理($COUNT_0)","已处理($COUNT_1)");
 $SegmentIdArray=array("0","1");

$jsonArray=array("Segment"=>array("Segmented"=>$SegmentArray,"SegmentedId"=>$SegmentIdArray,"SegmentIndex"=>"$SegmentIndex"),"data"=>$jsondata,"printIp"=>"192.168.30.109"); 

?>
