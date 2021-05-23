<?php   
//电信-zxq 2012-08-01
//二合一已更新 业务专用
	//标准$LockRemark
if($isTotal==1)  //专为统计2011-03-26
{	


	echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
	
	echo"<tr bgcolor=''>";

	//$ColbgColor=$ColbgColor==""?"bgcolor='#FFFFFF'":$ColbgColor;
	//上下左右（1表有边，0表示无），上色下色左色右色  （1表黑色，0表示白色）
	$w=$Field[1]+$Field[3];
	echo"<td class='B01111110' width='$w' align='center'  colspan=\"2\" height='25'>$ShowtotalRemark</td>";//$OrderSignColor为订单状态标记色
	
	$m=$m+2;

	for($k=0;$k<count($ValueArray);$k++){
		$currentcount=count($ValueArray);  //add by zx 2011-03-26
		if($ValueArray[$k][4]==""){
			$m=$m+2;
			$Value0=$ValueArray[$k][0];
			//add by zx  201100326
			if ($m==($Count-1))
			{
				$Field[$m]="";
			}		
			if($ValueArray[$k][3]=="..."){
				$Value0="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>$Value0</NOBR></DIV>";
				}
			
			
				if( $Value0=="&nbsp;") {  //无数值时
					$ValueNext="&nbsp;";
					if($k<$currentcount-1){$ValueNext=$ValueArray[$k+1][0];} //下一个值
					if(($ValueNext!="&nbsp;")   || ($Field[$m]=="")  ) {  //如果当前是空的，当前下一个不空值。或是最后一列，则要封下右
						//echo"<td  class='A0101' width='$Field[$m]' ".$ValueArray[$k][1]." ".$ValueArray[$k][2].">".$Value0."</td>";	
						echo"<td  class='A0101' width='$Field[$m]' ".$ValueArray[$k][1]." ".$ValueArray[$k][2].">".$Value0."</td>";	
					}
					else {
						echo"<td  class='B01011110' width='$Field[$m]' ".$ValueArray[$k][1]." ".$ValueArray[$k][2].">".$Value0."</td>";
					}
				}
				else{  //有数值在时
					 $ValueNext="";
					if($k<$currentcount-1){$ValueNext=$ValueArray[$k+1][0];} //下一个值
					if($ValueNext=="&nbsp;") { //如果当前是不空的，当前下一为空值。则要封下 即可
					   
						echo"<td  class='B01011110' width='$Field[$m]' ".$ValueArray[$k][1]." ".$ValueArray[$k][2].">".$Value0."</td>";
					}
					else {
						
						echo"<td  class='A0101' width='$Field[$m]' ".$ValueArray[$k][1]." ".$ValueArray[$k][2].">".$Value0."</td>";
					}
				}
	

		}
	}
	echo"</tr></table>";

}



	
	
?>