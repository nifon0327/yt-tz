<?php

	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/StaffAvatar.php");

	class WageStaffAvatar extends StaffAvatar
	{
		private $Month;
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

		private $Jz;
		private $Sb;
		private $Gjj;
		private $Kqkk;
		private $RandP;
		private $Otherkk;
		private $Amount;

		public function getWageInfomation($wageInfo)
		{
			$this->Month = $wageInfo["Month"];
			$this->Dx = sprintf("%.2f",$wageInfo["Dx"]);
			$this->Gljt = sprintf("%.2f",$wageInfo["Gljt"]);
			$this->Gwjt = sprintf("%.2f",$wageInfo["Gwjt"]);
			$this->Jj = sprintf("%.2f",$wageInfo["Jj"]);
			$this->Shbz = sprintf("%.2f",$wageInfo["Shbz"]);
			$this->Zsbz = sprintf("%.2f",$wageInfo["Zsbz"]);
			$this->Jbf = sprintf("%.2f",$wageInfo["Jbf"]);
			$this->Yxbz = sprintf("%.2f",$wageInfo["Yxbz"]);
			$this->Jtbz = sprintf("%.2f",$wageInfo["Jtbz"]);
			$this->taxbz = sprintf("%.2f",$wageInfo["taxbz"]);
			$this->Jz = sprintf("%.2f",$wageInfo["Jz"]);
			$this->Sb = sprintf("%.2f",$wageInfo["Sb"]);
			$this->Gjj = sprintf("%.2f",$wageInfo["Gjj"]);
			$this->Kqkk = sprintf("%.2f",$wageInfo["Kqkk"]);
			$this->RandP = sprintf("%.2f",$wageInfo["RandP"]);
			$this->Otherkk = sprintf("%.2f",$wageInfo["Otherkk"]);
			$this->Amount = sprintf("%.2f",$wageInfo["Amount"]);

		}

		public function getDX()
		{
			return $this->Dx;
		}

		public function Gljt()
		{
			return $this->Gljt;
		}

		public function Gwjt()
		{
			return $this->Gwjt;
		}

		public function Jj()
		{
			return $this->Jj;
		}

		public function Shbz()
		{
			return $this->Shbz;
		}

		public function Zsbz()
		{
			return $this->Zsbz;
		}

		public function Jbf()
		{
			return $this->Jbf;
		}

		public function Yxbz()
		{
			return $this->Yxbz;
		}

		public function Jtbz()
		{
			return $this->Jtbz;
		}

		public function taxbz()
		{
			return $this->taxbz;
		}

		public function Jz()
		{
			return $this->Jz;
		}

		public function Sb()
		{
			return $this->Sb;
		}

		public function Gjj()
		{
			return $this->Gjj;
		}

		public function Kqkk()
		{
			return $this->Kqkk;
		}

		public function RandP()
		{
			return $this->RandP;
		}

		public function Otherkk()
		{
			return $this->Otherkk;
		}

		public function Amount()
		{
			return $this->Amount;
		}

		public function outputWageInfomation()
		{
			$wageInfomation = array($this->Month,
									$this->Amount,
									$this->Dx, 
									$this->Gljt,
									$this->Gwjt,
									$this->Jj,
									$this->Shbz,
									$this->Zsbz,
									$this->Jbf,
									$this->Yxbz,
									$this->Jtbz,
									$this->taxbz,
									$this->Jz,
									$this->Sb,
									$this->Gjj,
									$this->Kqkk,
									$this->RandP,
									$this->Otherkk
								   );
			return $wageInfomation;

		}

	}

?>