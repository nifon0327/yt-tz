<?php 
//生产登记信息
$today=date("Y-m-d");$m=0;
$endDate=date("Y-m-d",strtotime("$today -1 month"));   
$SearchRows=$dModuleId=="1111"?" AND D.TypeId<>'7100' ":" AND D.TypeId='7100' ";

$Layout=array( "Title"=>array("Frame"=>"40, 2, 230, 25"),
                          "Col2"=>array("Frame"=>"135,32,48, 15","Align"=>"L","Color"=>"#AAAAAA"),
                          "Col3"=>array("Frame"=>"215,32,48, 15","Align"=>"L","Color"=>"#AAAAAA")
                         );
 //图标设置                        
$IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"125,35,8.5,10"),
                          "Col3"=>array("Name"=>"scdj_5","Frame"=>"200,35,13,10"),
                          "Col4"=>array("Name"=>"scdj_3","Frame"=>"265,35,6.5,13")
                          );
                          

$mySql="SELECT DATE_FORMAT(D.Date,'%Y-%m-%d') AS Date,SUM(D.Qty) AS Qty FROM $DataIn.sc1_cjtj D WHERE 1  AND DATE_FORMAT(D.Date,'%Y-%m-%d')>='$endDate'  $SearchRows  GROUP BY DATE_FORMAT(D.Date,'%Y-%m-%d')  ORDER BY Date DESC";
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=0;

while($myRow = mysql_fetch_array($Result)) {
	  $Date=$myRow["Date"];
	  $Qty=number_format($myRow["Qty"]);
	  	
	 //检查当天的员工数
	 $GroupNums=0;
	 $GroupResult=mysql_query("SELECT D.GroupId  
            FROM $DataIn.sc1_cjtj D
            WHERE  D.TypeId>0  AND  DATE_FORMAT(D.Date,'%Y-%m-%d')='$Date'  $SearchRows  GROUP BY  D.GroupId ",$link_id);
     while($GroupRow = mysql_fetch_array($GroupResult)) {
          $GroupId=$GroupRow["GroupId"];
          $checkNums= mysql_query("SELECT * FROM $DataIn.sc1_memberset  S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
WHERE S.Date='$Date' AND S.GroupId='$GroupId' AND M.cSign='7'
AND NOT EXISTS(SELECT Number FROM $DataPublic.kqqjsheet K WHERE K.Number=M.Number  AND K.StartDate<=CURRENT_DATE AND K.EndDate>=CURRENT_DATE)  ",$link_id);
      $GroupNums+=@mysql_num_rows($checkNums);
     }        
          
	  $DateTitle=date("m-d",strtotime($Date));		
	  $wName=date("D",strtotime($Date));    
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$Date"),
				                      "Title"=>array("Text"=>"$DateTitle","FontSize"=>"14","rIcon"=>"$wName"),
				                      "Col1"=>array("Text"=>"$GroupNums" . "人","Frame"=>"110, 2, 80, 30"),
				                      "Col3"=>array("Text"=>"$Qty","Frame"=>"210, 2, 103, 30","FontSize"=>"14"),
				                      //"Rank"=>array("Icon"=>"1"),
				                     // "AddRows"=>$AddRows
				                   ); 
		if ($hiddenSign==0){
		      $FromPage="Read";
		      $checkDate=$Date;
			  include "order_scdj_list.php";
		}
		else{
			$dataArray=array();
		}	                   	                   
	   $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign","Layout"=>$Layout,"IconSet"=>$IconSet,"data"=>$dataArray); 
	   $hiddenSign=1;
}
$jsonArray=array("data"=>$jsondata); 
?>