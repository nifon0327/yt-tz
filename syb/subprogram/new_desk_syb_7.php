<?php 
//已取消 2013-10-24 ewen
//7支出(投资)
$Sum_Y=$Sum_W=$Sum_A=0;
//$Value_Y[$Subscript][]				$Value_W[$Subscript][]				$Value_A[$Subscript][]
//$SumType_Y[$Subscript][] 	 		$SumType_W[$Subscript][]		 	$SumType_A[$Subscript][]

//$Sum_Y+=$Value_Y[$Subscript][]=$HZ671_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ671_W;							$Sum_A+=$Value_A[$Subscript][]=$HZ671_A;							//G84-671涉外设备

$SumType_Y[$Subscript][]=$Sum_Y;
$SumType_W[$Subscript][]=$Sum_W;
$SumType_A[$Subscript][]=$Sum_A;
$SumCol_Y[$Subscript]-=$Sum_Y; 
$SumCol_W[$Subscript]-=$Sum_W; 
$SumCol_A[$Subscript]-=$Sum_A; 
$SumOut_Y[$Subscript]+=$Sum_Y; 
$SumOut_W[$Subscript]+=$Sum_W; 
$SumOut_A[$Subscript]+=$Sum_A; 
?>