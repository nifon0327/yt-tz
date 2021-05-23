<?php 
date_default_timezone_set("Asia/shanghai");
class CalendarForm {
        private $year;       
        private $month;
        private $day;      
        private $weekend;
        private $currentdate;     
        private $dayofmonth;      
        private $day_count;
        private $num;
        private $week = array();
        private $retunrhtml="";
        private $CurrentColor="bgcolor='#CCC'";
        private $weekColor="#ff0000";//周末的颜色
        private $purpleColor="#FF00CC";
        private $greenColor="#009900";
        private $A0101="BORDER:none;BORDER-BOTTOM:2px solid;BORDER-RIGHT: 2px solid;";
        private $A0100="BORDER: none;BORDER-BOTTOM: 2px solid;";
        private $A0001="BORDER: none;BORDER-RIGHT:2px solid;";
        private $A1111="BORDER: 2px solid;";
        private $A1101="BORDER-BOTTOM: 2px solid;BORDER-top: 2px solid;BORDER-LEFT:none;BORDER-RIGHT: 2px solid;";
        private $A0010="BORDER: none;BORDER-LEFT:2px solid;";
        private $A0111="BORDER: none;BORDER-BOTTOM: 2px solid;BORDER-LEFT:2px solid;BORDER-RIGHT: 2px solid;";
        private $getArrayDate=array();
        function __construct($year, $month,$getArrayDate) {
                $this->getArrayDate=$getArrayDate;
                $this->makeWeeks($year, $month);
        }

        public function setYearMonth($year, $month) {
                $this->year = $year;
                $this->month = $month;
        }

        private function resetDayCount() {
                $this->day_count = 1;
        }

        private function setFirstWeek() {
                $this->num = 0;
        }

        public function getDayOfMonth($year, $month) {
                $this->resetDayCount();
                return date('t', mktime(0, 0, 0, $month, $this->day_count, $year ));
        }

        private function setDayOfMonth($year, $month) {
                $this->dayofmonth = $this->getDayOfMonth($year, $month);
        }      

        private function getDayOfWeek() {
                return date('w', mktime(0, 0, 0, $this->month, $this->day_count, $this->year ));
        }

       public function getNextMonth() {
                return date('m', mktime(0, 0, 0, $this->month, 28, $this->year )+432000);
        }

        public function getNextYear() {
                return date('Y', mktime(0, 0, 0, $this->month, 28, $this->year )+432000);
        }

        public function getPrevMonth() {
                return date('m', mktime(0, 0, 0, $this->month, 1, $this->year )-432000);
        }

        public function getPrevYear() {
                return date('Y', mktime(0, 0, 0, $this->month, 1, $this->year )-432000);
        }

        private function makeWeeks($year, $month) {
                $this->setYearMonth($year, $month);
                $this->setDayOfMonth($this->year, $this->month);
                $this->setFirstWeek();
                $this->num = 0;
                for($i = 0; $i < 7; $i++) {                  
                        $dayofweek = $this->getDayOfWeek();
                        $dayofweek = $dayofweek - 1;
                        if($dayofweek == -1) $dayofweek = 6;
                        if($dayofweek == $i) {
                                $this->week[$this->num][$i] = $this->day_count;
                                $this->day_count++;
                        } else {
                                $this->week[$this->num][$i] = "";
                        }
                }
               while(true) {
                        $this->num++;
                        for($i = 0; $i < 7; $i++) {
                                $this->week[$this->num][$i] = $this->day_count;
                                $this->day_count++;
                                if($this->day_count > $this->dayofmonth) break;
                        }
                        if($this->day_count > $this->dayofmonth) break;
                }
        }

        public function getCalendarHeader() {
                $style1="font-family:'Arial Black',Gadget,sans-serif;font-weight:bold;font-size:24px;";
                $style2="font-family:'Arial Black',Gadget,sans-serif;color:".$this->weekColor.";font-weight:bold;font-size:24px;";
                $this->retunrhtml ="<table cellpadding=0 cellspacing=0 border=5 style=\"margin-left: 0px;\" width='1050'>
                                                <tr><td height='80' colspan=\"7\" style=\"text-align: center;font-weight:bold;font-size:32px;".$this->A0100."\">".$this->year."年".$this->month."月"."</td></tr>
                                                <tr>
                                                <td width='150' style=\"text-align:center;".$this->A0101."\"><span style=\"$style1\" height='60' >星期一</span></td>
                                                <td width='150' style=\"text-align:center;".$this->A0101."\"><span style=\"$style1\">星期二</span></td>
                                                <td width='150' style=\"text-align:center;".$this->A0101."\"><span style=\"$style1\">星期三</span></td>
                                                <td width='150' style=\"text-align:center;".$this->A0101."\"><span style=\"$style1\">星期四</span></td>
                                                <td width='150' style=\"text-align:center;".$this->A0101."\"><span style=\"$style1\">星期五</span></td>
                                                <td width='150' style=\"text-align:center;".$this->A0101."\"><span style=\"$style2\">星期六</span></td>
                                                <td width='150' style=\"text-align:center;".$this->A0100."\"><span style=\"$style2\">星期日</span></td>
                                                </tr>";
        }

        public function getCalendarFooter() {
              $this->retunrhtml.="</table>";
        }

        public function getBeginTR() {
              $this->retunrhtml.="<tr>";
        }

        public function getEndTR() {
              $this->retunrhtml.="</tr>";
        }

        protected function getDay() {
              return $this->day;
        }

        protected function getMonth() {
              return $this->month;
        }

        protected function getYear() {
              return $this->year;
        }

        protected function isWeekend() {
              return $this->weekend;
        }

        protected function isCurrent() {
              return $this->currentdate;
        }    
    
        public function getTDHref() {
              return $this->getDay();
        }

       /* public function getFirstTD(){
              $this->retunrhtml.="<td height='90' width='20'>&nbsp;</td>";
        }*/

      public function utf8Substr($str, $from, $len)
         {
               return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
                       '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
                       '$1',$str);
          }

        public function getTD($weeksum,$daysum) {
             $Number=$this->getTDHref();
             if($this->isCurrent())$CurrentColor=$this->CurrentColor;
             if($Number<10)$tempNumber="0".$Number;
             else $tempNumber=$Number;
             $Count=count($this->getArrayDate[$Number]);
             $ShowStr="";     $TempName="";$TypeId="";$getQty="";
             $Style0="color:".$this->purpleColor.";font-family:'Arial Black',Gadget,sans-serif;font-weight:bold;font-size:12px;CURSOR: pointer;";
             $Style1="color:".$this->greenColor.";font-family:'Arial Black',Gadget,sans-serif;font-weight:bold;font-size:12px;CURSOR: pointer;";
             $Style2="font-family:'Arial Black',Gadget,sans-serif;font-weight:bold;font-size:12px;CURSOR: pointer;";
             if($weeksum>0){//最后一周
                      $Line=$this->A0001;
                     }
              else{
                     /*if($weeksum==-1){$Line=$this->A1101;}
                     else{
                             //if($weeksum==0)$Line=$this->A0010;
                             //else $Line=$this->A0101;
                              $Line=$this->A0101;
                            }*/
                     $Line=$this->A0101;
                    }
             if($Number==1)$Line=$this->A0111;
             for($i=0;$i<$Count;$i++){
                   $TempName= $this->getArrayDate[$Number][$i][2];
                   $TypeName=$this->utf8Substr($TempName,0,2);
                   $TypeId= $this->getArrayDate[$Number][$i][1];
                   $getQty= $this->getArrayDate[$Number][$i][0];
                   $chooseDay=$this->year."-".$this->month."-".$tempNumber;
                   if($TypeId==7100){//是组装并且产量少于10000显示红色
                        if($getQty<100000){
                                        $ShowStr.="<span style=\"$Style0\" onClick='ShowQty(\"$TypeId\",\"$chooseDay\")'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$TypeName.": ".$getQty."<br></span>";
                                      }
                             else {
                                        $ShowStr.="<span style=\"$Style1\" onClick='ShowQty(\"$TypeId\",\"$chooseDay\")'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$TypeName.": ".$getQty."<br></span>";
                                    }
                         }
                   else{
                          $ShowStr.="<span style=\"$Style2\" onClick='ShowQty(\"$TypeId\",\"$chooseDay\")'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$TypeName.": ".$getQty."</span><br>";
                         }
                 }
             $this->retunrhtml.="<td  height='100' valign='top' style='$Line' $CurrentColor><span style=\"text-align:left;font-family:'Arial Black',Gadget,sans-serif;font-weight:bold;font-size:20px;\">$Number</span><br>$ShowStr</td>";
        }     


        public function getTDWeekend($weeksum,$daysum) {
             $Number=$this->getTDHref();
             if($this->isCurrent())$CurrentColor=$this->CurrentColor;
             else $CurrentColor="";
             if($Number<10)$tempNumber="0".$Number;
             else $tempNumber=$Number;
             $Count=count($this->getArrayDate[$Number]);
             $ShowStr="";     $TempName="";$TypeId="";$getQty="";
             $Style0="color:".$this->purpleColor.";font-family:'Arial Black',Gadget,sans-serif;font-weight:bold;font-size:12px;CURSOR: pointer;";
             $Style1="color:".$this->greenColor.";font-family:'Arial Black',Gadget,sans-serif;font-weight:bold;font-size:12px;CURSOR: pointer;";
             $Style2="font-family:'Arial Black',Gadget,sans-serif;font-weight:bold;font-size:12px;CURSOR: pointer;";
             if($daysum==6){//周日                  
                      if($weeksum>0){//最后一周
                             $Line=$this->A0001;
                            }
                        else $Line=$this->A0100;
                   }
             else { 
                         if($weeksum>0){//最后一周
                             $Line=$this->A0001;
                            }
                        else $Line=$this->A0101;
                     }
             if($Number==1)$Line=$this->A0111;
             for($i=0;$i<$Count;$i++){
                   $TempName= $this->getArrayDate[$Number][$i][2];
                   $TypeName=$this->utf8Substr($TempName,0,2);
                   $TypeId= $this->getArrayDate[$Number][$i][1];
                   $getQty= $this->getArrayDate[$Number][$i][0];
                   $chooseDay=$this->year."-".$this->month."-".$tempNumber;
                   if($TypeId==7100){//是组装并且产量少于10000显示红色
                        if($getQty<100000){
                                        $ShowStr.="<span style=\"$Style0\" onClick='ShowQty(\"$TypeId\",\"$chooseDay\")'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$TypeName.": ".$getQty."<br></span>";
                                      }
                             else {
                                        $ShowStr.="<span style=\"$Style1\" onClick='ShowQty(\"$TypeId\",\"$chooseDay\")'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$TypeName.": ".$getQty."<br></span>";
                                    }
                         }
                   else{
                          $ShowStr.="<span style=\"$Style2\" onClick='ShowQty(\"$TypeId\",\"$chooseDay\")'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$TypeName.": ".$getQty."</span><br>";
                         }
                 }
             $this->retunrhtml.="<td  height='100' valign='top' style='$Line' $CurrentColor><span style=\"text-align: left;font-family:'Arial Black',Gadget,sans-serif;font-weight:bold;font-size:20px;color:".$this->weekColor.";\">".$Number."</span><br>$ShowStr</td>";
        }



        protected function makeCodeMonth($year, $month) {
                $this->makeWeeks($year, $month);
                $this->getCalendarHeader();
                for($i = 0; $i < count($this->week); $i++) {
                        $this->getBeginTR();
                      //  $this->getFirstTD();
                        for($j = 0; $j < 7; $j++) {
                                if(!empty($this->week[$i][$j])) {
                                        $this->day = $this->week[$i][$j];
                                        $this->currentdate = 0;
                                        if ( $this->year==date('Y') && $this->month==date('m') && $this->day==date('j')) $this->currentdate = 1;
                                        if($i==count($this->week)-1)$tempi=$i;//最后一周
                                        else $tempi=-$i;
                                        if($j == 5 || $j == 6) {
                                                $this->weekend = 1;
                                                $this->getTDWeekend($tempi,$j);
                                        } else {
                                                $this->weekend = 0;
                                                $this->getTD($tempi,$j);
                                        }

                                } else $this->retunrhtml.="<td style='".$this->A0100."'>&nbsp;</td>";
                        }
                        $this->getEndTR();
                }
                $this->getCalendarFooter();
        }
        public function getCodeMonth() {
                $this->makeCodeMonth($this->year, $this->month);
                return $this->retunrhtml;
        }
        public function showCodeMonth() {
                echo $this->getCodeMonth();
        }
}

class TechCalendarForm extends CalendarForm {
        public function getTDHref() {
                if ($this->isWeekend()) $font = "<font color=\"#FF3F4F\">"; else $font = "<font color=\"#4A5B6C\">";
                return "<a href=\"".$_SERVER["PHP_SELF"]."?action=showdate&date=".parent::getYear()."-".parent::getMonth()."-".parent::getDay()."\">".$font.parent::getDay()."</font></a>";
        }
}
?>