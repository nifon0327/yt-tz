<?php   
       //门禁
        $checkSql="SELECT COUNT(*) AS Counts FROM $DataPublic.come_data I WHERE I.Estate>0  and TIMESTAMPDIFF(HOUR,I.InTime,NOW())>4 ";
        $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
        $badgenums8=$checkRow["Counts"]==""?0:$checkRow["Counts"]; 
        $jsonArray[] = array("Id"=>"115", "Name"=>"门禁","Count"=>"$badgenums8","ColorSign"=>"1");  
        
       //上班人数
       /*
       $checkSql="SELECT SUM(IFNULL(A.dCounts,0)) AS dCounts,SUM(IFNULL(A.kqCounts,0)) AS kqCounts
		FROM (
		SELECT COUNT(*) AS dCounts,0 as kqCounts FROM $DataPublic.staffmain WHERE Estate=1 AND cSign IN (3,7) AND KqSign>2   
		AND Number NOT IN( 
				SELECT Number FROM $DataPublic.kqqjsheet 
				WHERE NOW() BETWEEN StartDate and EndDate OR (DATE_FORMAT(EndDate,'%Y-%m-%d %H:%i')>=CONCAT(CURDATE(),' 17:00') 
				and DATE_FORMAT(StartDate,'%Y-%m-%d')=CURDATE() and StartDate<now())
		) GROUP BY Number 
UNION ALL
	   SELECT 0 AS dCounts,COUNT(*) AS kqCounts FROM $DataIn.checkinout WHERE DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE() AND CheckType='I' 
     AND Number NOT IN(SELECT A.Number FROM (SELECT Number,Max(IF(CheckType='I',CheckTime,'')) AS inTime,Max(IF(CheckType='O',CheckTime,'')) AS outTime FROM $DataIn.checkinout WHERE DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE() GROUP BY Number) A WHERE A.outTime>A.inTime)  GROUP BY Number 
UNION ALL 
        SELECT 0 AS dCounts,COUNT(*) AS kqCounts FROM ptsub.checkinout WHERE DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE() AND CheckType='I' 
     AND Number NOT IN(SELECT Number FROM ptsub.checkinout WHERE DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE() AND CheckType='O')
     GROUP BY Number  
		)A";
		
       $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
       $kqCounts=$checkRow["kqCounts"]==""?0:$checkRow["kqCounts"]; 
       $dCounts=$checkRow["dCounts"]==""?0:$checkRow["dCounts"];
        $defaultState=1;
        include "submodel/staff_worktime.php";
        $badgenums9=$defaultState==1?$kqCounts+$dCounts:$kqCounts;
        */
        
        $checkSql="SELECT  COUNT(*) AS kqCounts
		 FROM (
		   SELECT A.Number FROM (SELECT Number,Max(IF(CheckType='I',CheckTime,'')) AS inTime,Max(IF(CheckType='O',CheckTime,'')) AS outTime FROM $DataIn.checkinout WHERE DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE() GROUP BY Number) A 
	       WHERE A.inTime>A.outTime 
UNION ALL 
	        SELECT Number FROM ptsub.checkinout WHERE DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE() AND CheckType='I' 
	        AND Number NOT IN(SELECT Number FROM ptsub.checkinout WHERE DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE() AND CheckType='O')
	        GROUP BY Number  
		)A";
		
       $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
       $badgenums9=$checkRow["kqCounts"]==""?0:$checkRow["kqCounts"]; 
               
        //跨日夜班
      $today=date("Y-m-d");
      $sDate=date("Y-m-d",strtotime("$today -1 day"));
      $checkSql2="SELECT COUNT(*) AS ybCounts FROM(
      SELECT Number,COUNT(*) AS Counts,IFNULL(Max(case when CheckType='I' then CheckTime end),'') AS  iTime,IFNULL(Max(case when CheckType='O' then CheckTime end),'') as oTime  
               FROM $DataIn.checkinout
               WHERE  DATE_FORMAT(CheckTime,'%Y-%m-%d')='$sDate' group by Number
            )A  
           WHERE  A.Counts=2 AND A.oTime<A.iTime  AND NOT EXISTS(SELECT C.Number FROM $DataIn.checkinout C WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')=CURDATE()  and C.Number=A.Number AND C.CheckType='O') AND EXISTS(SELECT M.Number FROM $DataPublic.staffmain M WHERE M.JobId=38 AND M.Number=A.Number)";
        $checkRow2=mysql_fetch_array(mysql_query($checkSql2,$link_id));
        $ybCounts=$checkRow2["ybCounts"]==""?0:$checkRow2["ybCounts"];
        $badgenums9+=$ybCounts;
         
        $jsonArray[] = array("Id"=>"103", "Name"=>"人员","Count"=>"$badgenums9","ColorSign"=>"3");  
        
        //读取无线用户数
       $totalClients = file_get_contents("http://192.168.16.2/Web_Query/Cisco_WLC_TotalClients.php");
       
        //行事曆
       	//add by cabbage 20150206 加上行事曆
       	
       	if (intval($Login_uType)!=4 ) {
	        	$checkSql = "SELECT COUNT(Id) AS EventCounts
					FROM $DataPublic.event_sheet E
					WHERE DATE_FORMAT(DateTime,'%Y-%m-%d') = DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d')
					AND Estate = 1;";
		$checkRow = mysql_fetch_array(mysql_query($checkSql, $link_id));
		$badgenums10 = $checkRow["EventCounts"] == "" ? 0 : $checkRow["EventCounts"]; 
        $jsonArray[] = array("Id" => "100", "Name"=>"", "Count" => "$badgenums10", "ColorSign" => "3","LeftBadge"=>"$totalClients"); 	
       	}
      
       
       
       	//新品
       	$checkSql = "select sum(1) as ALLCount ,sum(if(Date_format(created,'%Y-%m-%d')=current_date,1,0)) as Counts 
					FROM $DataPublic.new_arrivaldata 
					WHERE Estate >0;";
		$checkRow = mysql_fetch_array(mysql_query($checkSql, $link_id));
		$badgenums10 = $checkRow["Counts"] == 0 ? '' : $checkRow["Counts"]; 
		$allNew = $checkRow["ALLCount"] == 0 ? '' : $checkRow["ALLCount"]; 
        $jsonArray[] = array("Id" => "129", "Name"=>"", "Count" => "$badgenums10", "ColorSign" => "3","LeftBadge"=>"$allNew"); 
       
       //已分开读取
	     // include "badge_pt_read.php";
      
       //$badgenums2+=$badgenums202;
      
      
?>