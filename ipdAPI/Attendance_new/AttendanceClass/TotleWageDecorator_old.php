<?php

	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/StaffAvatar.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WageDecorator.php");

	class TotleWageStaffAvatar extends StaffAvatar
	{
		private $Dx;
		private $Gljt;
		private $Gwjt;
		private $Jj;
		private $Shbz;
		private $Zsbz;
		private $Jbf;
		private $Yxbz;
		private $Jtbz;
		private $taxbz;
		private $ywjj;

		private $Jz;
		private $Sb;
		private $Gjj;
		private $Kqkk;
		private $RandP;
		private $Otherkk;
		private $Amount;

		public function calculateTotleWage(WageStaffAvatar $wage)
		{
			$this->Dx += $wage->getDX();
			$this->Gljt += $wage->Gljt();
			$this->Gwjt += $wage->Gwjt();
			$this->Jj += $wage->Jj();
			$this->Shbz += $wage->Shbz();
			$this->Zsbz += $wage->Zsbz();
			$this->Jbf += $wage->Jbf();
			$this->Yxbz += $wage->Yxbz();
			$this->Jtbz += $wage->Jtbz();
			$this->taxbz += $wage->taxbz();
			$this->Jz += $wage->Jz();
			$this->Sb += $wage->Sb();
			$this->Gjj += $wage->Gjj();
			$this->Kqkk += $wage->Kqkk();
			$this->RandP += $wage->RandP();
			$this->Otherkk += $wage->Otherkk();
			$this->Amount += $wage->Amount();
			$this->ywjj += $wage->ywjj();
		}

		public function outputWageInfomation()
		{
			$wageInfomation = array("总计",
									$this->Amount."",
									$this->Dx."", 
									$this->Gljt."",
									$this->Gwjt."",
									$this->Jj."",
									$this->ywjj."",
									$this->Shbz."",
									$this->Zsbz."",
									$this->Jbf."",
									$this->Yxbz."",
									$this->Jtbz."",
									$this->taxbz."",
									$this->Jz."",
									$this->Sb."",
									$this->Gjj."",
									$this->Kqkk."",
									$this->RandP."",
									$this->Otherkk.""
								   );
			return $wageInfomation;
		}

	}


?>