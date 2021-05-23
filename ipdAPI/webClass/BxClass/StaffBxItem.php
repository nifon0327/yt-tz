<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/webClass/prototype/StaffPrototype.php");
	
	class StaffBxItem extends StaffPrototype
	{
		private $startDate;
		private $endDate;
		private $hours;
		private $note;
		private $backReason;
		private $checker;
		private $operator;
		private $itemId;
		private $logDate;
		private $state;
		private $stateName;
		
		public function setupBxItem($bxItem, $DataIn, $DataPublic, $link_id)
		{
			$this->number = $bxItem["Number"];
			$this->startDate = $bxItem["StartDate"];
			$this->endDate = $bxItem["EndDate"];
			$this->hours = $bxItem["hours"];
			$this->note = $bxItem["Note"] == ""?"&nbsp;":$bxItem["Note"];
			$this->backReason = $bxItem["backReason"]==""?"&nbsp;":$bxItem["backReason"];
			$this->itemId = $bxItem["Id"];
			$this->logDate = $bxItem["Date"];
			
			$operatorNumber = $bxItem["Operator"];
			$getOperatorNameSql = "Select Name From $DataPublic.staffmain Where Number = '$operatorNumber'";
			$getOperatorNameResult = mysql_query($getOperatorNameSql);
			$getOperatorNameRow = mysql_fetch_assoc($getOperatorNameResult);
			$this->operator = $getOperatorNameRow["Name"];
			
			if($bxItem["Checker"] != "")
			{
				$checkerNumber = $bxItem["Checker"];
				$getCheckerNameSql = "Select Name From $DataPublic.staffmain Where Number = $checkerNumber";
				$getCheckerNameResult = mysql_query($getCheckerNameSql);
				$getChereerNameRow = mysql_fetch_assoc($getCheckerNameResult);
				
				$this->checker = $getChereerNameRow["Name"];
			}
			else
			{
				$this->checker = "&nbsp;";
			}
			
			$this->state = $bxItem["Estate"];
			
			switch($this->state)
			{
				case "0":
				{
					//echo "here";
					$this->stateName = "<div class='greenB'>通过</div>";
				}
				break;
				case "1":
				{
					$this->stateName = "<div class='yellowB'>申请中</div>";
				}
				break;
				case "2":
				{
					$this->stateName = "<div class='redB'>退回</div>";
				}
				break;
			}
			
			
		}
		
		public function getStartDate()
		{
			return $this->startDate;
		}
		
		public function getEndDate()
		{
			return $this->endDate;
		}
		
		public function getHours()
		{
			return $this->hours;
		}
		
		public function getNote()
		{
			return $this->note;
		}
		
		public function getOperator()
		{
			return $this->operator;
		}
		
		public function getLogDate()
		{
			return $this->logDate;
		}
		
		public function getItemId()
		{
			return $this->itemId;
		}
		
		public function getStateName()
		{
			return $this->stateName;
		}
		
		public function getChecherName()
		{
			return $this->checker;
		}
		
		public function getBackReason()
		{
			return $this->backReason;
		}
		
		function __clone(){}
		
	}
	
?>