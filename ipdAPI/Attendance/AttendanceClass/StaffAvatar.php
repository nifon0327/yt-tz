<?php

	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/webClass/prototype/StaffPrototype.php");
	$ipadTag = "yes";
	include_once("$path/model/kq_YearHolday.php");

	class StaffAvatar extends StaffPrototype
	{

		private $totleAnnual;
		private $usedAnnual;
		private $totleBxHours;
		private $usedBxHours;
		private $branchCount;
		private $leaveRate;
		private $isNeedSignAttendance = "";
		private $isNeedSignWage = "";

		public function __construct($number,$DataIn, $DataPublic, $link_id)
		{
			$this->setStaffInfomaiton($number, $DataIn, $DataPublic, $link_id);
			$this->getBranchCount($DataIn, $DataPublic, $link_id);
			$this->getLeaveRate($DataIn, $DataPublic, $link_id);
			$this->getTotleAnnual($DataIn, $DataPublic, $link_id);
			$this->getUsedAnnual($DataIn, $DataPublic, $link_id);
			$this->isNeedToSignAttendance($DataIn, $DataPublic, $link_id);
			$this->isNeedToSignWage($DataIn, $DataPublic, $link_id);
		}

		private function getTotleAnnual($DataIn, $DataPublic, $link_id)
		{
			$startTime = date("Y-m-d");
			$this->totleAnnual = GetYearHolDayDays($this->getStaffNumber(),$startTime,$endTime,$DataIn,$DataPublic,$link_id);
		}

		private function getUsedAnnual($DataIn, $DataPublic, $link_id)
		{
			$startTime = date("Y-m-d");
			$this->usedAnnual = number_format(HaveYearHolDayDays($this->getStaffNumber(),$startTime,$endTime,$DataIn,$DataPublic,$link_id)/8,1);
		}

		private function getTotleBxHours($DataIn, $DataPublic, $link_id)
		{

		}

		private function getUsedBxHours($DataIn, $DataPublic, $link_id)
		{}

		private function getLeaveRate($DataIn, $DataPublic, $link_id)
		{
			$today = date("Y-m-d");
			$checkQjSql=mysql_fetch_array(mysql_query("SELECT COUNT(*)  AS countLeave FROM $DataPublic.kqqjsheet S 
	         									   LEFT JOIN $DataPublic.staffmain M  ON S.Number=M.Number 
			 									   WHERE M.BranchId='".$this->branchId."' AND M.cSign='".$this->cSign."' AND S.StartDate<='$today 08:00' AND S.EndDate>='$today 17:00'",$link_id));				
	    	$qjNums=$checkQjSql["Nums"]==""?0:$checkQjSql["Nums"];
	   	 	$qjRate = ($qjNums/$this->branchCount)*100;

	   	 	$this->leaveRate = $qjNums."人($qjRate%)";

		}

		private function getBranchCount($DataIn, $DataPublic, $link_id)
		{
			$getBranchCountResult = mysql_query("Select Count(*) as count From $DataPublic.staffmain Where BranchId='".$this->branchId."' and cSign='".$this->cSign."' and Estate = '1'");
		
			$getBranchCountRow = mysql_fetch_assoc($getBranchCountResult);
			$this->branchCount = $getBranchCountRow["count"];
		}

		private function isNeedToSignAttendance($DataIn, $DataPublic, $link_id)
		{
			$attendanceSql = "Select * From $DataIn.kqdata K Where Number = '".$this->getStaffNumber()."' And ConfirmSign = '1'";
			$attendanceResult = mysql_query($attendanceSql, $link_id);
			if(mysql_num_rows($attendanceResult) > 0){
				$attendanceRows = mysql_fetch_assoc($attendanceResult);
				$this->isNeedSignAttendance = $attendanceRows["Month"];
			}
		}

		private function isNeedToSignWage($DataIn, $DataPublic, $link_id){
			$currentMonth = date("Y-m");
			$monthGetSql = "Select distinct Month 
							From $DataPublic.wage_list
							Where cSign = '".$this->getcSign()."' 
							and Estate = '0' 
							order by month desc Limit 1";
			$monthResult = mysql_query($monthGetSql);
			$monthRow = mysql_fetch_assoc($monthResult);
			$sMonth = $monthRow["Month"];

			if($sMonth != "" and $currentMonth - $sMonth < 2) {
				$wageMonth = $sMonth;
				$checkSign=mysql_query("SELECT Id,sign FROM $DataPublic.wage_list_sign WHERE Number='".$this->getStaffNumber()."' AND SignMonth='$wageMonth' LIMIT 1",$link_id);
				$checkSignResult = mysql_fetch_assoc($checkSign);
				$sign = $checkSignResult["sign"];

				if(mysql_num_rows($checkSign) == 0 || $sign==""){
					$this->isNeedSignWage = $wageMonth;
				}
			}
		}

		public function outputIntomation(){
			$infomatinos = array(

							"Number" => $this->number."",
							"Name" => $this->name."",
							"JobName" => $this->jobName,
							"JobId" => $this->jobId,
							"BranchName" => $this->branchName,
							"BranchId" => $this->branchId,
							"GroupName" => $this->groupName,
							"GroupId" => $this->groupId,
							"cSign" => $this->cSign,
							"WordAddress" => $this->workAddress,
							"kqSign" => $this->kqSign,
							"Company" => $this->company,
							"ComeInDate" =>$this->comeIn,
							"LeaveRate" => $this->leaveRate,
							"branchCount" => $this->branchCount,
							"totleAnnual" => $this->totleAnnual."",
							"usedAnnual" => $this->usedAnnual."",
							"isNeedSignAttendance" => $this->isNeedSignAttendance,
							"isNeedSignWage" => $this->isNeedSignWage
							);

			return $infomatinos;

		}

		public function __clone()
		{}

	}


?>