<?php

    $path = $_SERVER["DOCUMENT_ROOT"];
    include_once("$path/ipdAPI/Attendance_new/AttendanceClass/StaffAvatar.php");

    class WageStaffAvatar extends StaffAvatar
    {
        private $Month;
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

        public function getWageInfomation($wageInfo){
            $this->Month = $wageInfo["Month"];
            $this->Dx = sprintf("%.2f",$wageInfo["Dx"]);
            $this->Jbf = sprintf("%.2f",$wageInfo["Jbf"]);
            $this->Gljt = sprintf("%.2f",$wageInfo["Gljt"]);
            $this->Gwjt = sprintf("%.2f",$wageInfo["Gwjt"]);
            $this->Shbz = sprintf("%.2f",$wageInfo["Shbz"] + $wageInfo["Zsbz"]);
            $this->Jtbz = sprintf("%.2f",$wageInfo["Jtbz"]);
            $this->Studybz = sprintf("%.2f",$wageInfo["Studybz"]);
            $this->Jj = sprintf("%.2f",$wageInfo["Jj"]);
            $this->Ywjj = sprintf("%.2f",$wageInfo["Ywjj"]);
            $this->Kqkk = sprintf("%.2f",$wageInfo["Kqkk"]);
            $this->dkfl = sprintf("%.2f",$wageInfo["dkfl"]);
            $this->Sb = sprintf("%.2f",$wageInfo["Sb"]);
            $this->Gjj = sprintf("%.2f",$wageInfo["Gjj"]);
            $this->RandP = sprintf("%.2f",$wageInfo["RandP"]);
            $this->Amount = sprintf("%.2f",$wageInfo["Amount"]);
        }

        public function getDX(){
            return $this->Dx;
        }

        public function getJbf(){
            return $this->Jbf;
        }

        public function getGljt(){
            return $this->Gljt;
        }

        public function getGwjt(){
            return $this->Gwjt;
        }

        public function getShbz(){
            return $this->Shbz;
        }

        public function getJtbz(){
            return $this->Jtbz;
        }

        public function getStudybz(){
            return $this->Studybz;
        }

        public function getJj(){
            return $this->Jj;
        }

        public function getYwjj(){
            return $this->Ywjj;
        }

        public function getKqkk(){
            return $this->Kqkk;
        }

        public function getdkfl(){
            return $this->dkfl;
        }

        public function getSb(){
            return $this->Sb;
        }

        public function getGjj(){
            return $this->Gjj;
        }

        public function getRandP(){
            return $this->RandP;
        }

        public function getAmount(){
            return $this->Amount;
        }


        public function outputWageInfomation()
        {
            $wageInfomation = array($this->Month,
                                    $this->Amount,
                                    $this->Dx, 
                                    $this->Jbf,
                                    $this->Gljt,
                                    $this->Gwjt,
                                    $this->Shbz,
                                    $this->Jtbz,
                                    $this->Studybz,
                                    $this->Jj,
                                    $this->Ywjj,
                                    $this->Kqkk,
                                    $this->dkfl,
                                    $this->Sb,
                                    $this->Gjj,
                                    $this->RandP
                                   );
            return $wageInfomation;

        }

    }

?>