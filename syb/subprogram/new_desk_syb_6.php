<?php 
//已取消 2013-10-24 ewen
$Sum_Y=$Sum_W=$Sum_A=0;
//$Value_Y[$Subscript][]				$Value_W[$Subscript][]				$Value_A[$Subscript][]
//$SumType_Y[$Subscript][] 	 		$SumType_W[$Subscript][]		 	$SumType_A[$Subscript][]
$Sum_Y+=$Value_Y[$Subscript][]=$HZ653_Y+$ZW5_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ653_W+$ZW5_W;							$Sum_A+=$Value_A[$Subscript][]=$HZ653_A+$ZW5_Y+$ZW5_W;								//F77-653丝移印辅料+总务5
$Sum_Y+=$Value_Y[$Subscript][]=$HZ657_Y+$ZW3_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ657_W+$ZW3_W;							$Sum_A+=$Value_A[$Subscript][]=$HZ657_A+$ZW3_Y+$ZW3_W;								//F78-657雷雕辅料+总务3
$Sum_Y+=$Value_Y[$Subscript][]=$HZ659_Y+$ZW2_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ659_W+$ZW2_W;							$Sum_A+=$Value_A[$Subscript][]=$HZ659_A+$ZW2_Y+$ZW2_W;									//F79-659车缝辅料+总务2
$Sum_Y+=$Value_Y[$Subscript][]=$HZ661_Y+$ZW1_Y+$ZW4_Y;		$Sum_W+=$Value_W[$Subscript][]=$HZ661_W+$ZW1_W+$ZW4_W;			$Sum_A+=$Value_A[$Subscript][]=$HZ661_A+$ZW1_Y+$ZW1_W+$ZW4_Y+$ZW4_W;		//F80-661一般辅料+总务1,4
$Sum_Y+=$Value_Y[$Subscript][]=$HZ673_Y+$ZW11_Y;					$Sum_W+=$Value_W[$Subscript][]=$HZ673_W+$ZW11_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ673_A+$ZW11_Y+$ZW11_W;								//F81-673皮套辅料+总务皮套11
$Sum_Y+=$Value_Y[$Subscript][]=$HZ616_Y;									$Sum_W+=$Value_W[$Subscript][]=$HZ616_W;										$Sum_A+=$Value_A[$Subscript][]=$HZ616_A;																//F82-616车间设备
$Sum_Y+=$Value_Y[$Subscript][]=$ZW12_Y;										$Sum_W+=$Value_W[$Subscript][]=$ZW12_W;											$Sum_A+=$Value_A[$Subscript][]=$ZW12_Y+$ZW12_W;												//F83-总务12
$Sum_Y+=$Value_Y[$Subscript][]=$ZW13_Y;										$Sum_W+=$Value_W[$Subscript][]=$ZW13_W;											$Sum_A+=$Value_A[$Subscript][]=$ZW13_Y+$ZW13_W;												//F83-总务13

$SumType_Y[$Subscript][]=$Sum_Y;
$SumType_W[$Subscript][]=$Sum_W;
$SumType_A[$Subscript][]=$Sum_A;
$SumCol_Y[$Subscript]-=$Sum_Y; 
$SumCol_W[$Subscript]-=$Sum_W; 
$SumCol_A[$Subscript]-=$Sum_A; 
$SumOut_Y[$Subscript]+=$Sum_Y; 
$SumOut_W[$Subscript]+=$Sum_W; 
$SumOut_A[$Subscript]+=$Sum_A; 
if($Subscript>0){//数据写入当月行政费用
			//$DataCheck3A[$Subscript]=$Sum_A;
			}
?>