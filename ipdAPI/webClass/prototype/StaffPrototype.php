<?php
	
	abstract class StaffPrototype
	{
		
		protected $name;
		protected $number;
		protected $jobName;
		protected $jobId;
		protected $branchName;
		protected $branchId;
		protected $groupName;
		protected $groupId;
		protected $cSign;
		protected $workAddress;
		protected $company;
		protected $kqSign;
		protected $comeIn;
		protected $sex;
		protected $attendanceFloor;
		
		public function setStaffInfomaiton($number, $DataIn, $DataPublic, $link_id, $isAllState=-1)
		{
			if($number ==="")
			{
				return ;
			}
			
			$this->number = $number;
			//获取员工的相关信息
			$getStaffInfomationSql = "Select A.Number,A.Name, A.JobId, A.BranchId, A.GroupId, A.kqSign, A.ComeIn, A.cSign, B.Name as JobName, C.Name as BranchName, E.Name as WorkAddress, F.CShortName, S.Sex, A.AttendanceFloor
									  From $DataPublic.staffmain A
									  INNER Join $DataPublic.branchdata C On C.Id = A.BranchId
									  INNER Join $DataPublic.jobdata B On B.Id = A.JobId
									  LEFT JOIN $DataPublic.staffsheet S On A.Number = S.Number
									  Left Join $DataPublic.staffworkadd E On E.Id = A.WorkAdd
									  INNER Join $DataPublic.companys_group F On F.cSign = A.cSign
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
			//echo $groupSql;
			$groupResult = mysql_query($groupSql);
			$groupRow = mysql_fetch_assoc($groupResult);
			$this->groupName = $groupRow['GroupName'];
			
		}
		
		public function getStaffNumber()
		{
			return $this->number;
		}
		
		public function getStaffName()
		{
			return $this->name;
		}
		
		public function getStaffJobName()
		{
			return $this->jobName;
		}
		
		public function getStaffBranchName()
		{
			return $this->branchName;
		}
		
		public function getStaffGroupName()
		{
			return $this->groupName;
		}
		
		public function getStaffWorkAddress()
		{
			return $this->workAddress;
		}
		
		public function getStaffCompany()
		{
			return $this->company;
		}

		public function getKqSign()
		{
			return $this->kqSign;
		}

		public function getComeId()
		{
			return $this->comeIn;
		}

		public function getCSign()
		{
			return $this->cSign;
		}

		public function getSex(){
			return $this->sex;
		}
		
		abstract public function __clone();
				
	}
	
	
?>