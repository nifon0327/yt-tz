<?php   

//$LineRealW=2;  //实际线的长度
//$LineVirW=2;   //简隔长度
//$LineStarX=$CurMargin;  //线的起点
//$LineLen=190;  //线的的长度
for($li=$LineStarY; $li<$LineStarY+$LineLen;$li=$li+$LineRealW+$LineVirW){
	$pdf->Line($LineStarX,$li,$LineStarX,$li+$LineRealW);
}

?>