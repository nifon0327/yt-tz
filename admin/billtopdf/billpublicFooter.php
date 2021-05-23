<?php   
//电信-joseph
        $pdf->SetFont('Arial','B',12);
		$pdf->SetXY($CurMargin,$MaxContainY+2);   //页脚位置
		$eurTableFooter="<table border=1 >
		<tr >
		<td width=137  height=13 align=center valign=middle style=bold bgcolor=#CCCCCC >WE HEREBY CERTIFY THIS INVOICE TO BE TRUE AND CORRECT</td>	
		<td width=58 ></td>
		</tr>
		</table>" ;//$eurTableList;

		$SignBy="Kung-Yi Chen(Fred)";
		$pdf->htmltable($eurTableFooter);
		//老板签名
		$Boss_Logo="../images/BossSignature.jpg";
		$pdf->Image($Boss_Logo,147,$MaxContainY+3,38,0,"JPG");
		
		$pdf->SetFont('Arial','B',$TableFontSize); //设回字体大小
		$pdf->Text(176,$MaxContainY+14,"$SignBy");
?>