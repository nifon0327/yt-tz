<?php

    $path = $_SERVER["DOCUMENT_ROOT"];
    include_once("$path/ipdAPI/webClass/prototype/StaffPrototype.php");

    class CheckAvatar_lw extends StaffPrototype
    {
        private $checkDate;
        private $KrSign=0;
        private $AttendanceFloor;
        public $checkInTime; 
        public $checkOutTime;
        public $dFrom;
        public $targetFloor;
        public $dFromId;

        public function setStaffInfomaiton($number, $DataIn, $DataPublic, $link_id, $isAllState=-1)
        {
            if($number ==="")
            {
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
            //echo $getStaffInfomationSql;  
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

        public function checkLegalIpad($identifier, $DataIn, $DataPublic, $link_id){

            //check ipad楼层
            $isAttendanceIpadSql = "Select * From $DataPublic.attendanceipadsheet Where Identifier = '$identifier' and Estate = '1'";
            $isAttendanceIpadResult = mysql_query($isAttendanceIpadSql);
                if($isAttendanceIpadRows = mysql_fetch_assoc($isAttendanceIpadResult)){
                $this->dForm = $isAttendanceIpadRows["Name"];
                $this->dFromId = $isAttendanceIpadRows["Id"];
                $this->targetFloor = $isAttendanceIpadRows["Floor"];
            }
        }

        public function checkAttendanceFloor($DataIn, $DataPublic, $link_id){
            $staffInfoStr = sprintf("SELECT af.Floor 
                                    FROM $DataPublic.lw_staffmain st
                                    Left Join $DataPublic.attendance_floor as af On af.Id = st.AttendanceFloor
                                     WHERE Number='%s'  
                                    AND st.Estate='1' LIMIT 1",$this->getStaffNumber());
            $staffResult = mysql_query($staffInfoStr);
            if($staffInfo = mysql_fetch_assoc($staffResult)){
                $this->AttendanceFloor = ($staffInfo["Floor"] == "")?" ":$staffInfo["Floor"];
            }
        }

        public function setupCheckTime($type, $time)
        {
            switch($type)
            {
                case "I":
                    $this->checkInTime = $time;
                break;
                case "O":
                    $this->checkOutTime = $time;
                break;
            }

            $this->checkDate = substr($time, 0, 10);
        }

        public function checkIn($DataIn, $DataPublic, $link_id){

            // $needAttendance = $this->isNeedAttendance();
            // if(!$needAttendance["state"]){
            //  return $needAttendance;
            // }

            $legal = false;
            $error = "";
            $checkInsql = "SELECT * FROM $DataIn.lw_checkinout WHERE Number='".$this->getStaffNumber()."' AND KrSign=0 AND DATE_FORMAT(CheckTime,'%Y-%m-%d')='".$this->checkDate."' ORDER BY CheckTime";
            //echo $checkInsql;
            $checkInResult = mysql_query($checkInsql);
            if(mysql_num_rows($checkInResult) == 0){
                $legal = true;
            }
            else{
                $error = $this->otherError(mysql_num_rows($checkInResult));
            }

            return array("state"=>$legal, "infomation"=>"$error");

        }

        public function checkOut($DataIn, $DataPublic, $link_id){

            // $needAttendance = $this->isNeedAttendance();
            // if(!$needAttendance["state"]){
            //  return $needAttendance;
            // }

            $legal = false;
            $error = "";
            $checkOutsql = "SELECT * FROM $DataIn.lw_checkinout WHERE Number='".$this->getStaffNumber()."' AND KrSign=0 AND DATE_FORMAT(CheckTime,'%Y-%m-%d')='".$this->checkDate."' ORDER BY CheckTime";

            $checkOutResult = mysql_query($checkOutsql);
            if(mysql_num_rows($checkOutResult) == 1){

                $checkOutRow = mysql_fetch_assoc($checkOutResult);
                $checkIn = $checkOutRow["CheckTime"];
                $checkType = $checkOutRow["CheckType"];
                if(strtotime($this->checkOutTime) - strtotime($checkIn) < 60 * 10 || $checkType == "O"){

                    $error = $checkType == "O"?"签卡异常":"连续签卡";
                }
                else{
                    $legal = true;
                }
            }
            else{
                $error = $this->otherError(mysql_num_rows($checkOutResult));
            }

            return array("state"=>$legal, "infomation"=>"$error");
        }

        private function isNeedAttendance(){
            $state = false;
            if($this->getKqSign() < 3){
                $state = true;
            }
            return array("state"=>$state, "infomation"=>"无需考勤");
        }

        public function insertCheckTime($type, $DataIn, $DataPublic, $link_id){
            //$result = array();
            $currentDate = date('Y-m-d');
            $operatorionResult = false;
            $insetTime = $type == "I"?$this->checkInTime:$this->checkOutTime;

            $inRecode="INSERT INTO $DataIn.lw_checkinout (Id,Number ,CheckTime,CheckType,dFrom, dFromId,Estate,Locks,ZlSign,KrSign,Operator,BranchId,JobId,Date) 
VALUES (NULL,'".$this->getStaffNumber()."','$insetTime','$type','".$this->dForm."','".$this->dFromId."','1','1','0','".$this->KrSign."','0','".$this->branchId."','".$this->jobId."', '$currentDate')";
            if(mysql_query($inRecode)){
                $operatorionResult = true;
                $showTime = substr($insetTime, 11, 5);
                $info = $this->getStaffName().'+'.$showTime.'+'.$type.'+'.$this->dForm.'+'.$this->AttendanceFloor.'+'.$this->getStaffNumber();

                $result = array("state"=>$operatorionResult, "infomation"=>$info);
            }
            else{
                $result = array("state"=>$operatorionResult, "infomation"=>"签卡失败");
            }

            return $result;
        }

        private function otherError($count){
            $error = "";
            switch($count)
            {
                case 1:
                    $error = "重复签卡";
                case 2:
                    $error = "重复签卡";
                break;
                default:
                    $error = "签卡异常,需人事检查";
                break;
            }

            return $error;
        }

        public function __clone(){}
    }



