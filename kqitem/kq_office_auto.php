<?php
$toDay=date("Y-m-d");
$FristDay=date("Y-m-d",strtotime("$toDay - 2 days"));
//$FristDay="2013-06-10";//要加入记录的月份
$CheckMonth=substr($FristDay,0,7);//要处理的月份
$nowMonth=date("Y-m");//当前系统月份
$NumberResult=mysql_query("SELECT Number FROM $DataPublic.staffmain WHERE KqSign='3' AND cSign='$Login_cSign' AND Estate>0 AND Number!='10001'",$link_id);
if($NumberRow=mysql_fetch_assoc($NumberResult)){
do{
      $thisNumber[]=$NumberRow;
      }while($NumberRow=mysql_fetch_assoc($NumberResult));
}
$Nums=count($thisNumber);
if (count($Nums)>0 ){
           $days=round((strtotime($toDay)-strtotime($FristDay))/3600/24)+1 ;
           for($i=0;$i<$days;$i++){
                     $theDay=date("Y-m-d",strtotime("$FristDay + $i days"));
                 //******************************************1、给人员排个序
                    for($j=0;$j<$Nums;$j++){
                             $TempNumber=$thisNumber[$j]["Number"];
                             $CheckResult1=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.kq_officenum WHERE Number='$TempNumber' AND Date='$theDay'",$link_id));
                             $CheckId=$CheckResult1["Id"];
                             if($CheckId==""){
                                      $SortId=rand(1,10000);
                                      $In_Sql="INSERT INTO $DataIn.kq_officenum(Id, Number, SortId, Date)VALUES(Null,'$TempNumber','$SortId','$theDay')";
                                      $In_Result=@mysql_query($In_Sql);
                                 }
                         }
                     //******************************************1、插入 考勤信息     
                     $weekDay=date("w",strtotime($theDay));
                     if($weekDay>0 && $weekDay<6){//工作日
                              //分钟的随机数
                              $InMinute=mt_rand(45,50);
                              $OutMinute=mt_rand(10,50);
                              $InTime=$theDay." 07:".$InMinute.":00";//随机起始签到时间
                              $OutTime=$theDay." 17:".$OutMinute.":00";//随机起始签退时间
                              $InTime=strtotime($InTime);
                              $OutTime=strtotime($OutTime);
                             $CheckResult2=mysql_query("SELECT K.Number,M.BranchId,M.JobId FROM $DataIn.kq_officenum  K
                             LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number
                             WHERE  K.Date='$theDay' ORDER BY K.SortId",$link_id);
                             while($CheckRow2=mysql_fetch_array($CheckResult2)){
                                        $Number=$CheckRow2["Number"];
                                        $BranchId=$CheckRow2["BranchId"];
                                        $JobId=$CheckRow2["JobId"];
                                        $CheckResult3=mysql_fetch_array(mysql_query("SELECT Id FROM $DataPublic.kqqjsheet WHERE Number='$Number' AND left(StartDate,12)='$theDay'",$link_id));
                                        $CheckId3=$CheckResult3["Id"];
                                        if($CheckId3==""){
                                              $InTime+=mt_rand(1,4);//考勤签到时间
                                              $OutTime+=mt_rand(1,4);//考勤签退时间
                                              $CheckInTime=date('Y-m-d H:i:s',$InTime);
                                              $CheckOutTime=date('Y-m-d H:i:s',$OutTime);
		                                      $CheckInNext= mysql_query("SELECT CheckTime,CheckType FROM $DataIn.kq_office WHERE 1 and Number=$Number and CheckType='I' AND 
LEFT(CheckTime,10)='$theDay' LIMIT 1",$link_id);
	                                   	    if(!$CheckInNextRow = mysql_fetch_array($CheckInNext)){
                                                  $In_Sql3="INSERT INTO $DataIn.kq_office(Id, BranchId, JobId, Number, CheckTime, CheckType, dFrom, Estate, Locks, ZlSign, KrSign, Operator) VALUES(NULL,'$BranchId','$JobId','$Number','$CheckInTime','I','0','0','1','0','0','0')";
                                                   $In_Result3=@mysql_query($In_Sql3);
                                                 }
		                                       $CheckOutNext= mysql_query("SELECT CheckTime,CheckType FROM $DataIn.kq_office WHERE 1 and Number=$Number and CheckType='O' AND LEFT(CheckTime,10)='$theDay' LIMIT 1",$link_id);
	                                   	       if(!$CheckOutNextRow = mysql_fetch_array($CheckOutNext ) ){
                                                     $In_Sql4="INSERT INTO $DataIn.kq_office(Id, BranchId, JobId, Number, CheckTime, CheckType, dFrom, Estate, Locks, ZlSign, KrSign, Operator) VALUES(NULL,'$BranchId','$JobId','$Number','$CheckOutTime','O','0','0','1','0','0','0')";
                                                    $In_Result4=@mysql_query($In_Sql4);
                                                  }                      
                                           }
                                  }
                       }
            }
}
?>