<?php
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-20
defined('IN_COMMON') || include '../basic/common.php';

if ($DataIn==""){
    include "../basic/chksession.php";
    include "../basic/parameter.inc";
}
?>
<table width="98%" height="100%" align="right">
	<tr>
		<td width="70" valign="top">出差信息:</td><td valign="top">
		  <?php
		  $checkSql="SELECT C.StartTime,C.CarId,C.Remark,C.Businesser,C.Drivers,A.carListNo,B.Name,B.Color,D.CShortName
				FROM $DataPublic.cardata A
				LEFT JOIN $DataPublic.cartype B ON B.Id=A.TypeId
				LEFT JOIN (SELECT * FROM $DataPublic.info1_business WHERE EndTime='0000-00-00 00:00:00' OR EndTime>NOW()) C ON C.CarId=A.Id
				LEFT JOIN $DataPublic.companys_group D ON D.cSign=A.cSign
				WHERE A.Estate=1 and C.Id>0 and A.TypeId !=3  ORDER BY A.TypeId,C.StartTime DESC,A.Id ASC";// and A.UserSign=1
		  $checkMsgOvertime=mysql_query($checkSql,$link_id);
		  if($OvertimeRow=mysql_fetch_array($checkMsgOvertime)){
		  	$i=1;
			do{
				    $StartTime=$OvertimeRow["StartTime"];
				    $CarId=$OvertimeRow["CarId"];
				    $Remark=$OvertimeRow["Remark"];
				    $Color=$OvertimeRow["Color"];
				    $TypeName=$OvertimeRow["Name"];
				    $Businesser=$OvertimeRow["Businesser"];
				    $CarName=$OvertimeRow["carListNo"];
				    $Drivers=$OvertimeRow["Drivers"];
				    $CShortName=$OvertimeRow["CShortName"];
				    if ($CarId==""){
					       $Listcolor="";
			         	}
				else{
				      switch($Drivers){
				     	case 0:
						     $Drivers="其他人开车";
						    break;
					    case 1:
						$Drivers="自驾";
						break;
					default:
						$CheckSql=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number='$Drivers' LIMIT 1",$link_id));
						$Drivers=$CheckSql["Name"]."开车";
					break;
					}
				    $Drivers="(" . $Drivers . ")&nbsp;";
					$Listcolor=" class='redN'";
				 }
			    echo "<span style='color:$Color'>$CShortName($TypeName)$CarName:&nbsp;" . $Businesser."&nbsp;".$StartTime."&nbsp;".$Remark."$Drivers &nbsp;</span><br>";
				}while($OvertimeRow=mysql_fetch_array($checkMsgOvertime));
			}
		 ?>
		</td>
	</tr>
<tr><td colspan="2" height="2">&nbsp;</td></tr>
<tr>
<td width="70" valign="top">请假名单:</td><td valign="top">
<?php
	$NowTime=date("Y-m-d H:i:s");
    $thisYear=date("Y");
	$checkQjSql=mysql_query("SELECT M.Name,M.Number FROM $DataPublic.kqqjsheet J
	LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number
	WHERE 1 AND M.KqSign>1 AND J.EndDate>'$NowTime' and J.Estate='0' GROUP BY M.Number  ORDER BY M.BranchId ",$link_id);
	if($checkQjRow=mysql_fetch_array($checkQjSql)){
                 echo "<table width='95%' ><tr>";
                  $k=1;
					do{
                         $Number=$checkQjRow["Number"];
                         $StartResult=mysql_fetch_array(mysql_query("SELECT DATE_FORMAT(J.StartDate,'%c.%d') AS StartDate,J.StartDate AS StartTime 
                          FROM $DataPublic.kqqjsheet J
                          LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number
                          WHERE M.Number=$Number  AND  J.EndDate>'$NowTime'  ORDER BY J.StartDate ASC LIMIT 1",$link_id));
                         $StartDate=$StartResult["StartDate"];
                         $StartTime=$StartResult["StartTime"];

                         $EndResult=mysql_fetch_array(mysql_query("SELECT DATE_FORMAT(J.EndDate,'%c.%d') AS EndDate,J.EndDate AS EndTime 
                          FROM $DataPublic.kqqjsheet J
                          LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number
                          WHERE M.Number=$Number  AND  J.EndDate>'$NowTime'  ORDER BY J.EndDate DESC LIMIT 1",$link_id));
                         $EndDate=$EndResult["EndDate"];
                         $EndTime=$EndResult["EndTime"];
                         //如果这人有打卡，但还在请假(用红色标记名字)
                         $PassCardResult=mysql_fetch_array(mysql_query("SELECT Count(*) AS Num FROM $DataIn.checkinout WHERE CheckTime Between '$StartTime' and '$EndTime' AND DATE_FORMAT(CheckTime,'%Y')='$thisYear' AND Number='$Number' AND CheckType='I'",$link_id));
                         $PassCardNum=$PassCardResult["Num"];
                         $str=$checkQjRow["Name"];
                         $pa = '/[\x{4e00}-\x{9fa5}]/siu';
                         preg_match_all($pa, $str, $r);
                         $count = count($r[0]);
                         if($count==2)$str=$r[0][0]."&nbsp;&nbsp;".$r[0][1];
                         if($PassCardNum>0){
                                   $show="<span class='redB' title='该员工已回来上班,有打卡记录,请调整他的请假时间!'>$str</span>&nbsp;".$StartDate."--".$EndDate;
                                   }
                         else{
                                   $show=$str."&nbsp;".$StartDate."--".$EndDate;
                                 }
                         echo "<td width='25%'>$show</td>";
                         if($k%3==0)echo "</tr><tr>";
						$k++;
				}while ($checkQjRow=mysql_fetch_array($checkQjSql));
			 echo"<tr></table>";
	    }
?>
</td>
</tr>
<tr><td colspan="2" height="10">&nbsp;</td></tr>
	<tr>
		<td width="70" valign="top">人数统计:</td><td valign="top">
<table  width="95%"><tr><td width="12%" align="center">地点</td><td width="18%" align="center">总人数</td><td width="14%" align="center">上班(固)</td><td width="14%" align="center">上班(时)</td><td width="14%" align="center">请假(固)</td><td width="14%" align="center">请假(时)</td><td width="14%" align="center">无记录</td></tr>
<?php
$ToDay=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$WorkAddResult=mysql_query("SELECT Id FROM $DataPublic.staffworkadd WHERE Estate=1 and Id not in (5)",$link_id);
while($WorkAddRow=mysql_fetch_array($WorkAddResult)){
$TotalNumber=0;$WorkNumber1=0;$WorkNumber3=0;$QjNumber1=0;$QjNumber3=0;$NoNumber=0;
    $workaddId=$WorkAddRow["Id"];
    switch($workaddId){
         case 1: $WorkAddName="48";break;
         case 2: $WorkAddName="47";break;
         case 3: $WorkAddName="WXD";break;
         case 4: $WorkAddName="BHS";break;
         //case 5: $WorkAddName="博士达";break;
         case 6: $WorkAddName="台湾";break;
         }
   /* if($workaddId==1){
            $DataBase=$DataIn;
            }
    else $DataBase=$DataOut;*/
    //***********总共人数
    $TotalSql=mysql_query("SELECT COUNT(*) AS TotalNumber 
    FROM $DataPublic.staffmain M 
    WHERE  M.Estate=1 AND M.WorkAdd='$workaddId' ",$link_id);
    $TotalNumber=mysql_result($TotalSql,0,"TotalNumber");

    //***********固定薪上班人数
    $OfficeSql1=mysql_query("SELECT COUNT(*) AS OfficeNumber 
    FROM $DataPublic.staffmain M 
    WHERE M.KqSign>2 AND M.Estate=1 AND M.WorkAdd='$workaddId' AND M.Number 
    NOT IN (SELECT Number FROM $DataPublic.kqqjsheet J  WHERE DATE_FORMAT(J.EndDate,'%Y-%m-%d')>='$ToDay' and J.Estate='0' AND DATE_FORMAT(J.StartDate,'%Y-%m-%d')<='$ToDay' )",$link_id);
    $WorkNumber1=mysql_result($OfficeSql1,0,"OfficeNumber");
    //***********非固定薪上班人数(两边打卡)
    $KqSql3=mysql_query("SELECT COUNT(*) AS KqNumber FROM (
    SELECT Number FROM (
	     SELECT C.Number FROM $DataIn.checkinout  C
         LEFT JOIN $DataPublic.staffmain M ON M.Number=C.Number
        WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$ToDay' AND M.WorkAdd='$workaddId' 
      )A WHERE 1 GROUP BY A.Number
   )B",$link_id);
	$WorkNumber3=mysql_result($KqSql3,0,"KqNumber");
       //***********固定薪请假人数
    $QjSql1=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS OfficeNumber  
    FROM $DataPublic.kqqjsheet J
    LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number
    WHERE DATE_FORMAT(J.EndDate,'%Y-%m-%d')>='$ToDay' and J.Estate='0' AND DATE_FORMAT(J.StartDate,'%Y-%m-%d')<='$ToDay'  AND M.WorkAdd='$workaddId' AND M.KqSign>1 ",$link_id));
    $QjNumber1=$QjSql1["OfficeNumber"];
    //***********非固定薪请假人数(请半天假的，算是上班)
    $QjSql3=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS OfficeNumber 
    FROM $DataPublic.kqqjsheet J
    LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number
    WHERE DATE_FORMAT(J.EndDate,'%Y-%m-%d')>='$ToDay' and J.Estate='0' AND DATE_FORMAT(J.StartDate,'%Y-%m-%d')<='$ToDay'  AND M.WorkAdd='$workaddId' AND M.KqSign=1
     AND J.Number NOT IN (
                SELECT Number FROM (
	            SELECT C.Number FROM $DataIn.checkinout  C
                LEFT JOIN $DataPublic.staffmain M ON M.Number=C.Number
                WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$ToDay' AND M.WorkAdd='$workaddId' 
              )A GROUP BY A.Number
      )",$link_id));
    $QjNumber3=$QjSql3["OfficeNumber"];
    $WorkNumbertotal=$WorkNumber1+$WorkNumber3;
    $NoNumber=$TotalNumber-$WorkNumber1-$WorkNumber3-$QjNumber1-$QjNumber3;
    echo"<tr><td align='center' >$WorkAddName</td>
        <td align='center' ><a href=\"desk_worknumber_ajax.php?workaddId=$workaddId&ActionId=0\"  target=\"_blank\" style='CURSOR: pointer;'>$TotalNumber($WorkNumbertotal)</a></td>
        <td  align='center' ><a href=\"desk_worknumber_ajax.php?workaddId=$workaddId&ActionId=1\"  target=\"_blank\" style='CURSOR: pointer;'>$WorkNumber1</a></td>
        <td  align='center'><a href=\"desk_worknumber_ajax.php?workaddId=$workaddId&ActionId=2\"  target=\"_blank\" style='CURSOR: pointer;'>$WorkNumber3</a></td>
        <td  align='center'><a href=\"desk_worknumber_ajax.php?workaddId=$workaddId&ActionId=3\"  target=\"_blank\" style='CURSOR: pointer;'>$QjNumber1</a></td>
        <td  align='center'><a href=\"desk_worknumber_ajax.php?workaddId=$workaddId&ActionId=4\"  target=\"_blank\" style='CURSOR: pointer;'>$QjNumber3</a></td>
        <td  align='center' ><a href=\"desk_worknumber_ajax.php?workaddId=$workaddId&ActionId=5\"  target=\"_blank\" style='CURSOR: pointer;'>$NoNumber</a></td>
</tr>";
}
		  ?>
</table>
		</td>
	</tr>
</table>
