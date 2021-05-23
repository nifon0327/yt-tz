<?php
	
	class KqPbSet
	{
		public $mCheckStartTime;
		public $mCheckEndTime;
		public $aCheckStartTime;
		public $aCheckEndTime;
		public $otStartTime;
		public $noonRestTime;
		public $aRestTime;
		
		function __construct()
		{
			$this->mCheckStartTime = "08:00:00";
			$this->mCheckEndTime = "12:00:00";
			$this->aCheckStartTime = "13:00:00";
			$this->aCheckEndTime = "17:00:00";
			$this->otStartTime = "18:00:00";
			
			$this->noonRestTime = (strtotime($this->aCheckStartTime) - strtotime($this->mCheckEndTime))/60;
			$this->aRestTime = (strtotime($this->otStartTime) - strtotime($this->aCheckEndTime))/60;
			
		}
		
		
		public function returnSheet()
		{
			return array($this->mCheckStartTime, $this->mCheckEndTime, $this->aCheckStartTime, $this->aCheckEndTime, $this->otStartTime);
		}
		
	}
	
?>