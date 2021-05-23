<?php

$path = $_SERVER["DOCUMENT_ROOT"];
include_once "$path/basic/parameter.inc";
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/StaffAvatar.php");
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceCalculateModle.php");
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceDecorator.php");
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WorkSchedule.php");
include_once("$path/ipdAPI/webClass/prototype/StaffPrototype.php");

class AttendanceAvatar_lw extends AttendanceAvatar{

    function setStaffInfomaiton($number, $DataIn, $DataPublic, $link_id, $isAllState=-1){
        if($number ===""){
            return ;
        }
        
        $this->number = $number;
        //获取员工的相关信息
        $getStaffInfomationSql = "Select A.Number,A.Name, A.JobId, A.BranchId, A.GroupId, A.kqSign, A.ComeIn, B.Name as JobName, C.Name as BranchName, E.Name as WorkAddress, S.Sex, A.AttendanceFloor
                                  From $DataPublic.lw_staffmain A
                                  INNER Join $DataPublic.branchdata C On C.Id = A.BranchId
                                  INNER Join $DataPublic.jobdata B On B.Id = A.JobId
                                  LEFT JOIN $DataPublic.lw_staffsheet S On A.Number = S.Number
                                  Left Join $DataPublic.staffworkadd E On E.Id = A.WorkAdd
                                  Where (A.Number = $number or A.IdNum = $number) ";
        
        switch ($isAllState) {
            case 0:
                $getStaffInfomationSql .= 'AND A.Estate = 0';
                break;
            case 1:
                $getStaffInfomationSql .= 'AND A.Estate = 1';
                break;
        }                         
        $getStaffInfomationReslut = mysql_query($getStaffInfomationSql);                          
        $getStaffInfomationRow = mysql_fetch_assoc($getStaffInfomationReslut);
        
        $this->number = $getStaffInfomationRow["Number"];
        $this->name = $getStaffInfomationRow["Name"];
        $this->jobName = $getStaffInfomationRow["JobName"];
        $this->jobId = $getStaffInfomationRow["JobId"];
        $this->branchName = $getStaffInfomationRow["BranchName"];
        $this->branchId = $getStaffInfomationRow["BranchId"];
        $this->groupId = $getStaffInfomationRow["GroupId"];
        $this->cSign = $getStaffInfomationRow["cSign"];
        $this->workAddress = $getStaffInfomationRow["WorkAddress"];
        $this->company = $getStaffInfomationRow["CShortName"];
        $this->kqSign = $getStaffInfomationRow["kqSign"];
        $this->comeIn = $getStaffInfomationRow["ComeIn"];
        $this->sex = $getStaffInfomationRow["Sex"];
        $this->attendanceFloor = $getStaffInfomationRow['AttendanceFloor'];
        //人工判断上班位置
        $groupSql = "SELECT GroupName FROM $DataPublic.staffgroup WHERE GroupId =".$this->groupId;
        $groupResult = mysql_query($groupSql);
        $groupRow = mysql_fetch_assoc($groupResult);
        $this->groupName = $groupRow['GroupName'];
        
    }


    private function getLeaveRate($DataIn, $DataPublic, $link_id){
        $today = date("Y-m-d");
        $checkQjSql=mysql_fetch_array(mysql_query("SELECT COUNT(*)  AS countLeave FROM $DataPublic.kqqjsheet S 
                                               LEFT JOIN $DataPublic.lw_staffmain M  ON S.Number=M.Number 
                                               WHERE M.BranchId='".$this->branchId."' AND M.cSign='".$this->cSign."' AND S.StartDate<='$today 08:00' AND S.EndDate>='$today 17:00'",$link_id));             
        $qjNums=$checkQjSql["Nums"]==""?0:$checkQjSql["Nums"];
        $qjRate = $this->branchCount==0?0:($qjNums/$this->branchCount)*100;

        $this->leaveRate = $qjNums."人($qjRate%)";

    }

    private function getBranchCount($DataIn, $DataPublic, $link_id)
    {
        $getBranchCountResult = mysql_query("Select Count(*) as count From $DataPublic.lw_staffmain Where BranchId='".$this->branchId."' and cSign='".$this->cSign."' and Estate = '1'");
    
        $getBranchCountRow = mysql_fetch_assoc($getBranchCountResult);
        $this->branchCount = $getBranchCountRow["count"];
    }

    private function isNeedToSignAttendance($DataIn, $DataPublic, $link_id){
        $attendanceSql = "Select * From $DataIn.lw_kqdata K Where Number = '".$this->getStaffNumber()."' And ConfirmSign = '1'";
        $attendanceResult = mysql_query($attendanceSql, $link_id);
        if(mysql_num_rows($attendanceResult) > 0){
            $attendanceRows = mysql_fetch_assoc($attendanceResult);
            $this->isNeedSignAttendance = $attendanceRows["Month"];
        }
    }

    private function isNeedToSignWage($DataIn, $DataPublic, $link_id){
        $currentMonth = date("Y-m");
        $getNoSignSql = "SELECT A.Number, B.Name, C.sign, C.Date AS SignDate, W.PayDate AS listDate, A.Month                             
                              FROM $DataIn.cwxzsheet A
                              Left Join $DataIn.cwxzmain W On W.Id = A.Mid
                              LEFT JOIN $DataIn.lw_staffmain B ON A.Number = B.Number
                              Left Join $DataIn.branchdata E On E.Id = B.BranchId
                              Left Join $DataIn.jobdata F On F.Id = B.JobId
                              LEFT JOIN $DataIn.wage_list_sign C ON C.Number = A.Number AND A.Month = C.SignMonth                                 
                              WHERE A.Estate = 0
                              AND (C.sign IS NULL OR C.sign =  '')
                              AND B.Estate =  '1'
                              AND B.Number = '".$this->getStaffNumber()."'
                              Order By A.Month Desc";
        $noSignResult = mysql_query($getNoSignSql);
        if($noSignRow = mysql_fetch_assoc($noSignResult)){
            $this->isNeedSignWage = $noSignRow['Month'];
        }
        
    }

    public function setupAttendanceData($number, $checkDay, $DataIn, $DataPublic, $link_id){
            $this->defaultWorkHours = 8;
            $this->targetDate = $checkDay;

            $getCheckDataSql = "SELECT CheckTime,CheckType,KrSign,Id
                                FROM $DataIn.lw_checkinout 
                                WHERE Number=$number 
                                and ((CheckTime LIKE '$checkDay%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$checkDay%' and KrSign='1'))
                                and Id not in (Select checkId From $DataIn.disableCheckId)
                                order by CheckTime";
            $getCheckDataResult = mysql_query($getCheckDataSql);
            while($getCheckDataRow = mysql_fetch_assoc($getCheckDataResult)){
                $checkType = $getCheckDataRow["CheckType"];
                switch($checkType){
                    case "I":{
                        $this->startTime = substr($getCheckDataRow["CheckTime"], 0, 16);
                    }
                    break;
                    case "O":{
                        $this->endTime = substr($getCheckDataRow["CheckTime"], 0, 16);
                        $this->KrSign = $getCheckDataRow["KrSign"];
                    }
                    break;
                }
            }
            $dateArray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
            $this->weekDay="星期".$dateArray[date("w",strtotime($checkDay))];
            $this->nightShit = ($this->KrSign == "1")?"1":"";
        }
}



?>