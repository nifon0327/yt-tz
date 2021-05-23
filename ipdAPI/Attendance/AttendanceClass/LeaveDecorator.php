<?php

	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/Attendance/AttendanceClass/StaffAvatar.php");
	$ipadTag = "yes";
	include_once "$path/model/kq_YearHolday.php";

	class LeaveStaffAvatar extends StaffAvatar
	{
		private $startDate;
		private $endDate;
		private $reason;
		private $estate;
		private $leaveTypeName;
		private $leaveType;
		private $leaveHours;
		private $Operator;
		private $OperatorName;

		public function getLeaveInfomation($infomation,$DataIn,$DataPublic,$link_id)
		{
			$this->startDate = substr($infomation["StartDate"], 0, 16);
			$this->endDate = substr($infomation["EndDate"], 0, 16);
			$this->reason = $infomation["Reason"];
			$this->leaveTypeName = $infomation["TypeName"];
			$this->estate = ($infomation["Estate"] == 1)?"未批准":($infomation["Estate"]==2)?"退回":"已批准";

			$this->leaveHours = GetBetweenDateDays($this->number,$this->startDate,$this->endDate,$infomation["bcType"],$DataIn,$DataPublic,$link_id);

			$this->Operator = $infomation["Operator"]."";
			$this->OperatorName = $infomation["Name"]==""?"系统":$infomation["Name"];
			$this->leaveType = $infomation["Type"]."";
		}

		public function outputLeaveInf()
		{
			$leaveInfo = array($this->startDate."", $this->endDate."", $this->leaveHours."", $this->leaveTypeName, $this->reason."", $this->estate."", $this->Operator, $this->OperatorName, $this->leaveType);

			return $leaveInfo;
		}

	}

?>