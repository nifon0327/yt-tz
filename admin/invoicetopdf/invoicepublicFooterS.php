<?php   
        $pdf->SetFont('uGB','',TableFontSize); //设回字体大小  
		//$eurTableList="<table  border=1 >
		$pdf->SetXY($CurMargin,$MaxContainY);   //页脚位置
		$eurTableFooter="<table border=1 >
		<tr >
		<td width=27  height=13 bgcolor=#CCCCCC align=center valign=middle style=bold>授权</td>	
		<td width=49 align=center valign=middle style=bold>YingZi Liu</td>
		<td width=20  bgcolor=#CCCCCC align=center valign=middle style=bold>签名</td>			
		<td width=59 align=center valign=middle style=bold><img src='../images/BossSignature.jpg' /> </td>	
		<td width=10  bgcolor=#CCCCCC align=center valign=middle style=bold>日期</td>	
		<td width=30 align=center valign=middle style=bold>$DateMDY</td>
		</tr>
		</table>" ;//$eurTableList;

		
		$pdf->htmltable($eurTableFooter);
		//老板签名
		$Boss_Logo="../images/BossSignature.jpg";
		$pdf->Image($Boss_Logo,110,$MaxContainY+1,38,0,"JPG");
		
	 

?>