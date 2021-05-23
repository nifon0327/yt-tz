<?php   
		
		$ConTableBegin_Y=$CurMargin+4; 
		
		$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
		$pdf->SetFont('uGB','B',20,'','true');
		$pdf->Text($CurMargin,$ConTableBegin_Y+3,"$S_Company  扣款单");  //输出I
		$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');

	
		{
			$pdf->SetXY($CurMargin+130,$ConTableBegin_Y-3);  
			$tmptable="<table border=0 >
				<tr>
					<td width=21 align=right  >单号 $TaxStr:</td>
					<td width=2 align=right style=bold ></td>
					<td width=15 align=right  >页&nbsp;&nbsp;数:</td>
					<td width=17 align=right style=bold ></td>
				<tr>	
				<tr>
					<td width=21 align=right style=bold ></td>
					<td width=4 align=right style=bold ></td>
					<td width=15 align=right  >日&nbsp;&nbsp;期:</td>
					<td width=17 align=right style=bold ></td>
				<tr>				
			</table>" ;
			$pdf->htmltable($tmptable); 
			
       
			//加重色////////////////////////////////////////////
			$pdf->SetFont('uGB','',$FontSize_TiTle+1,'','true');
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY($CurMargin+131,$ConTableBegin_Y); 
			$tmptable="<table border=0 ><tr bgcolor=#000000 repeat>
			           <td align=right style=bold>$BillNumber</td><tr></table>" ;
			$pdf->transhtmltable($tmptable,1,1); 
			//加重色
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFont('uGB','',$FontSize_TiTle+1,'','true');
			$pdf->SetXY($CurMargin+130,$ConTableBegin_Y);  
			$tmptable="<table border=0 ><tr><td  align=right style=bold>$BillNumber</td><tr></table>" ;
			$pdf->transhtmltable($tmptable,0,0); 
			
			
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');
			$pdf->SetXY($CurMargin+119,$ConTableBegin_Y-3);  
			$tmpDate=date("Y-m-d");
			$tmptable="<table border=0 >
				<tr>
					<td width=31 align=right style=bold ></td>
					<td width=2  align=right style=bold ></td>
					<td width=15 align=right style=bold ></td>
					<td width=17 align=left >$pageNo</td>
				<tr>	
				<tr>
					<td width=31 align=right style=bold ></td>
					<td width=2  align=right style=bold ></td>
					<td width=15 align=right style=bold ></td>
					<td width=17 align=left  >$Date</td>
				<tr>				
			</table>" ;
			$pdf->htmltable($tmptable); 		
		}
		
		{
			$ConTableBegin_Y=$pdf->GetY()+6; //获取的是eurSInvoiceNO中位置
			$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);  
			$tmptable="<table border=0 >
				<tr >
					<td width=119  height=5 align=left style=bold >供应商:</td>
					<td width=65  align=right style=bold >采购员:</td>
				<tr>
			</table>" ;
			$pdf->htmltable($tmptable);	
			
			//填写内容
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y+4);  
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');			
			$tmptable="<table border=0 >
				<tr height=5>
					<td width=119 align=left >$Company</td>
					<td width=65 align=right >$CgName</td>
				<tr>
			</table>" ;
			$pdf->transhtmltable($tmptable,0,0);				
			
		}
		

        {
			$ConTableBegin_Y=$pdf->GetY(); 
			$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);  
			$tmptable="<table border=0 >
				<tr >
					<td width=184  height=5 align=right style=bold >币别:</td>
				<tr>
			</table>" ;
			$pdf->htmltable($tmptable);	
			
			//填写内容
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y+4);  
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');			
			$tmptable="<table border=0 >
				<tr height=5>
					<td width=184 align=right >$CurrencyName</td>
				<tr>
			</table>" ;
			$pdf->transhtmltable($tmptable,0,0);				
			
		}


         {
			$ConTableBegin_Y=$pdf->GetY(); //获取的是eurSInvoiceNO中位置
			$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);  
			$tmptable="<table border=0 >
				<tr >
					<td width=80  height=5 align=left style=bold >收款银行账号:</td>
					<td width=40   ></td>
					<td width=64  align=right style=bold >备注:</td>
				<tr>
			</table>" ;
			$pdf->htmltable($tmptable);	
			
			//填写内容
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y+4);  
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');			
			$tmptable="<table border=0 >
				<tr height=20>
					<td width=80 align=left ></td>
					<td width=40   ></td>
					<td width=64 align=right >$Remark</td>
				<tr>
			</table>" ;
			$pdf->transhtmltable($tmptable,0,0);				
			
		}
		
		
		$ConTableBegin_Y=$pdf->GetY();
		
?>