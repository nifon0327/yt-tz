<?php
//小组人数
$CheckNums =mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums   
             FROM $DataPublic.staffmain M  
            LEFT JOIN $DataIn.staffgroup G  ON G.GroupId=M.GroupId 
            WHERE M.cSign='7' AND M.Estate=1 AND  G.TypeId='$SC_TYPE' ",$link_id));
    $GroupNums=$CheckNums["Nums"]==""?0:$CheckNums["Nums"];

//上班人数
 $CheckNums=mysql_fetch_array(mysql_query(" SELECT COUNT(*) AS Nums FROM $DataIn.checkinout  C 
  LEFT JOIN  $DataPublic.staffmain M  ON M.Number=C.Number  
  LEFT JOIN $DataIn.staffgroup G  ON G.GroupId=M.GroupId 
 WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$curDate' AND C.CheckType='I'  AND  G.TypeId='$SC_TYPE'",$link_id));
  $workNums=$CheckNums["Nums"]==""?0:$CheckNums["Nums"];
   
 //考勤人数
 $CheckNums=mysql_fetch_array(mysql_query(" SELECT COUNT(*) AS Nums FROM $DataIn.checkinout  C 
  LEFT JOIN  $DataPublic.staffmain M  ON M.Number=C.Number  
  LEFT JOIN $DataIn.staffgroup G  ON G.GroupId=M.GroupId 
 WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$curDate' AND C.CheckType='I'  AND  G.TypeId='$SC_TYPE'
 AND NOT EXISTS(SELECT K.Number FROM $DataIn.checkinout K WHERE DATE_FORMAT(K.CheckTime,'%Y-%m-%d')='$curDate' AND K.CheckType='O'  AND  K.Number=C.Number )",$link_id));
  $kqNums=$CheckNums["Nums"]==""?0:$CheckNums["Nums"];
                    
 //请假人数
 $OverTime=date("Y-m-d") . " 17:00:00";
 $LeaveResult =mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts   FROM (SELECT K.Number 
        FROM $DataPublic.kqqjsheet K
        LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
        LEFT JOIN $DataIn.staffgroup G  ON G.GroupId=M.GroupId 
        WHERE (K.StartDate<NOW() AND (K.EndDate>=NOW() OR K.EndDate>='$OverTime'))  AND M.cSign='7' AND M.Estate=1 AND G.TypeId='$SC_TYPE'  GROUP BY K.Number)A  ",$link_id));
$LeaveNums=$LeaveResult["Counts"];
?>