<?php   

		//$eurTableList="<table  border=1 >
		if ($InvoiceNO=="CL-A debit note 020") $Date="15-Nov-13";
		if ($InvoiceNO=="CL-A credit note 075") $Date="19-Nov-13";
		if ($InvoiceNO=="CL-A credit note 083") $Date="31-Dec-13";
		$pdf->SetXY($CurMargin,$MaxContainY);   //页脚位置
		if ($CompanyId==1104){
			$BossName="LEO LU";
			$Boss_Logo="../images/BossSignature_lu.jpg";
			
		}
		else{
			//$BossName="Kung-Yi Chen(Fred)";
			$BossName="YingZi Liu";
			$Boss_Logo="../images/BossSignature.jpg";
			
		}
		$BossName="";
		$eurTableFooter="<table border=1 >
		<tr >
		<td width=27  height=13 bgcolor=#CCCCCC align=center valign=middle style=bold>Authorised By</td>	
		<td width=49 align=center valign=middle style=bold>$BossName</td>
		<td width=20  bgcolor=#CCCCCC align=center valign=middle style=bold>Signature</td>			
		<td width=59 align=center valign=middle style=bold><img src='' /> </td>	
		<td width=10  bgcolor=#CCCCCC align=center valign=middle style=bold>Date</td>	
		<td width=30 align=center valign=middle style=bold>$Date</td>
		</tr>
		</table>" ;//$eurTableList;

		
		$pdf->htmltable($eurTableFooter);
		//老板签名 gtboss.jpg
		if($ModelId==478 ){  //GT
		  $Boss_Logo="../images/gtboss.jpg";
		  $pdf->Image($Boss_Logo,110,$MaxContainY+1,20,0,"JPG"); 
		}
		
		if($ModelId==504 ){  //GLTWin
		  $Boss_Logo="../images/GIANTWIN.jpg";
		  $pdf->Image($Boss_Logo,110,$MaxContainY+1,20,0,"JPG"); 
		}		
?>