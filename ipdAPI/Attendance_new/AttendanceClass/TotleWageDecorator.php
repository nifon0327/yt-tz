<?php

    $path = $_SERVER["DOCUMENT_ROOT"];
    include_once("$path/ipdAPI/Attendance_new/AttendanceClass/StaffAvatar.php");
    include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WageDecorator.php");

    class TotleWageStaffAvatar extends StaffAvatar{
        private $Dx;
        private $Jbf;
        private $Gljt;
        private $Gwjt;
        private $Shbz;
        private $Jtbz;
        private $Studybz;
        private $Jj;
        private $Ywjj;
        private $Kqkk;
        private $dkfl;
        private $Sb;
        private $Gjj;
        private $RandP;
        private $Amount;

        public function calculateTotleWage(WageStaffAvatar $wage){
            $this->Dx += $wage->getDX();
            $this->Jbf += $wage->getJbf();
            $this->Gljt += $wage->getGljt();
            $this->Gwjt += $wage->getGwjt();
            $this->Shbz += $wage->getShbz();
            $this->Jtbz += $wage->getJtbz();
            $this->Studybz += $wage->getStudybz();
            $this->Jj += $wage->getJj();
            $this->Ywjj += $wage->getYwjj();
            $this->Kqkk += $wage->getKqkk();
            $this->dkfl += $wage->getdkfl();
            $this->Sb += $wage->getSb();
            $this->Gjj += $wage->getGjj();
            $this->RandP += $wage->getRandP();
            $this->Amount += $wage->getAmount();
        }

        public function outputWageInfomation()
        {
            $wageInfomation = array("总计",
                                    $this->Amount."",
                                    $this->Dx."", 
                                    $this->Jbf."",
                                    $this->Gljt."",
                                    $this->Gwjt."",
                                    $this->Shbz."",
                                    $this->Jtbz."",
                                    $this->Studybz."",
                                    $this->Jj."",
                                    $this->Ywjj."",
                                    $this->Kqkk."",
                                    $this->dkfl."",
                                    $this->Sb."",
                                    $this->Gjj."",
                                    $this->RandP."",
                                   );
            return $wageInfomation;
        }

    }


?>