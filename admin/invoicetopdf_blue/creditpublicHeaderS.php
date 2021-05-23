<?php   
        //新的Credit,debit PDF
		
		$ConTableBegin_Y=$CurMargin+4;  //$CurMargin=7;
		
		$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
		$pdf->SetFont('uGB','B',18);
		$pdf->Text($CurMargin,$ConTableBegin_Y+3,"$C_Company $tmpType");  //输出I
		
		$pdf->SetFont('uGB','',$FontSize_TiTle);

 		
		//$pdf->Text(); //是从下往上写的
		//$pdf->htmltable($eurSInvoice) //是从上往下画的,表在后写时不会盖掉到原来的，
		{
			$pdf->SetXY($CurMargin+110,$ConTableBegin_Y-3);  //130-120;94,57
			$tmptable="<table border=0 >
				<tr>
					<td width=41 align=left  >单号:</td>
					<td width=3 align=right style=bold ></td>
					<td width=15 align=right  >日期:</td>
					<td width=16 align=right style=bold ></td>
				<tr>	
				<tr>
					<td width=41 align=right style=bold ></td>
					<td width=3 align=right style=bold ></td>
					<td width=15 align=right  >页数:</td>
					<td width=16 align=right style=bold ></td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable); 
			
            /*		
			$pdf->SetFont('uGB','',11);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY($CurMargin+130,$ConTableBegin_Y-5);  
			$tmptable="<table border=0 >
				<tr>
					<td width=21 align=right style=bold ></td>
					<td width=4 align=right style=bold ></td>
					<td width=15 align=right style=bold ></td>
					<td width=15 align=right style=bold ></td>
				<tr>	
				<tr>
					<td width=21 align=left >$Invoice_PI</td>
					<td width=4 align=right style=bold ></td>
					<td width=15 align=right style=bold ></td>
					<td width=15 align=right style=bold ></td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->transhtmltable($tmptable,0,0); 	
			*/
			//加重色////////////////////////////////////////////
			$pdf->SetFont('uGB','',$FontSize_TiTle+1);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY($CurMargin+111,$ConTableBegin_Y); 
			$tmpInvoice_PI=substr($Invoice_PI,0,strlen($Invoice_PI));
			$tmptable="<table border=0 >
				<tr bgcolor=#000000 repeat>
					<td  align=left style=bold>$tmpInvoice_PI</td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->transhtmltable($tmptable,1,1); 
			//加重色
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFont('uGB','',$FontSize_TiTle+1);
			$pdf->SetXY($CurMargin+110,$ConTableBegin_Y);  
			$tmptable="<table border=0 >
				<tr >
					<td  align=left style=bold>$Invoice_PI</td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->transhtmltable($tmptable,0,0); 
			
			
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetFont('uGB','',$FontSize_TiTle);
			$pdf->SetXY($CurMargin+119,$ConTableBegin_Y-3);  
			//$tmpDate=date("Y-m-d");
			$tmpDate=date("Y-m-d",strtotime($ShipDate));
			$tmptable="<table border=0 >
				<tr>
					<td width=31 align=right style=bold ></td>
					<td width=3 align=right style=bold ></td>
					<td width=15 align=right style=bold ></td>
					<td width=16 align=left style= >$tmpDate</td>
				<tr>	
				<tr>
					<td width=31 align=right style=bold ></td>
					<td width=3 align=right style=bold ></td>
					<td width=15 align=right style=bold ></td>
					<td width=16 align=left  >$pageNo</td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable); 		
		}		
		
		//SOLD TO, FORWARDER
		{
			
			$ConTableBegin_Y=$pdf->GetY()+6; //获取的是eurSInvoiceNO中位置
			$pdf->SetFont('uGB','',$FontSize_TiTle);
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);  
			$tmptable="<table border=0 >
				<tr>
					<td width=80 align=left style=bold >$tmpShipType</td>
					<td width=106 align=right style=bold >&nbsp;</td>
				<tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable);			
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('uGB','',$FontSize_TiTle);
			$ConTableBegin_Y=$pdf->GetY(); //获取的是eurSInvoiceNO中位置
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y); 
		
			$tmptable="<table border=0 >
				<tr>
					<td width=80 align=left  >$Company, <br> $Address. </td>
					<td width=106 align=right  >&nbsp;</td>
				<tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable); 			
		}
		

		//SHIP TO, TRANSCATOR
		{
			
			$ConTableBegin_Y=$pdf->GetY()+2; //获取的是eurSInvoiceNO中位置
			$pdf->SetFont('uGB','',$FontSize_TiTle);
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);  
			$tmptable="<table border=0 >
				<tr>
					<td width=80 align=left style=bold >备注:</td>
					<td width=106 align=right style=bold >开单人:</td>
				<tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable);			
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('uGB','',$FontSize_TiTle);
			$ConTableBegin_Y=$pdf->GetY(); //获取的是eurSInvoiceNO中位置
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);
			
			$Mobile=trim($Mobile);
			$tmpMobile=$Mobile;
			if(strlen($Mobile)>10 ){
				if(substr($Mobile,0,3)!='886'){
					$tmpMobile='+86 '.substr($Mobile,0,3).'-'.substr($Mobile,3,4).'-'.substr($Mobile,7);
				}
			}
			if($tmpMobile!=''){
				$tmpMobile='('.$tmpMobile.')';
			}
			
			$tmptable="<table border=0 >
				<tr>
					<td width=80 align=left  >$Commoditycode$StableNote$Notes <br> $PaymentTerm$Priceterm$Terms <br> 币种:$Symbol</td>
					<td width=106 align=right >$Nickname $tmpMobile</td>
				<tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable); 			
		}

		
		$ConTableBegin_Y=$pdf->GetY()+5;

?>