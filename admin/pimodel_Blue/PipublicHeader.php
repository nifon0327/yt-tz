<?php   
        //新的Credit,debit PDF
		
		$ConTableBegin_Y=$CurMargin+4;  //$CurMargin=7;
		$pdf->SetFont('Arial','B',10);
		$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
		$pdf->Text($CurMargin,$ConTableBegin_Y,"ASH CLOUD");  //输出I
		$pdf->SetFont('Arial','',10);
		$pdf->Text($CurMargin+22,$ConTableBegin_Y,"Co., Ltd. Shenzhen");  //

		$pdf->SetFont('Arial','B',19);
		if($Com_Pack_PI!=""){  //如PI
			$pdf->Text($CurMargin,$ConTableBegin_Y+6,"$Com_Pack_PI");  //输出I
		}else{
			$pdf->Text($CurMargin,$ConTableBegin_Y+6,"COMMERCIAL INVOICE");  //输出I
		}
		
		$pdf->SetFont('Arial','B',$FontSize_TiTle);
		/*
		$pdf->Text($CurMargin+120,$ConTableBegin_Y,"INVOICE NO:");  //
		$pdf->Text($CurMargin+170,$ConTableBegin_Y,"DATAE:");  //
		$pdf->Text($CurMargin+182,$ConTableBegin_Y,"PAGE:");  //
		*/
 	
		
		

		
		//$pdf->Text(); //是从下往上写的
		//$pdf->htmltable($eurSInvoice) //是从上往下画的,表在后写时不会盖掉到原来的，
		{
			$pdf->SetXY($CurMargin+115,$ConTableBegin_Y-2);  //88,57
			$tmptable="<table border=0 >
				<tr>
					<td width=31 align=left  style=bold>INVOICE NO:</td>
					<td width=10 align=right style=bold ></td>
					<td width=14 align=right style=bold >DATE:</td>
					<td width=16 align=right style=bold ></td>
				<tr>	
				<tr>
					<td width=31 align=right style=bold ></td>
					<td width=10 align=right style=bold ></td>
					<td width=14 align=right style=bold >PAGE:</td>
					<td width=16 align=right style=bold ></td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable); 
			
			/*
			$pdf->SetXY($CurMargin+120,$ConTableBegin_Y);   //SHIP TO   modify by zx 2011-0129  $pdf->SetXY(130,18);
			$pdf->SetFont('Arial','',11);
			$pdf->SetTextColor(0,0,0);
			$tmptable="<table border=0 >
				<tr>
					<td width=57 align=right style=bold >$Invoice_PI</td>
					<td width=14 align=right style=bold >$pageNo</td>
				<tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable);
			*/
			/*
			$pdf->SetFont('Arial','',11);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY($CurMargin+102,$ConTableBegin_Y-4);  
			$tmptable="<table border=0 >
				<tr>
					<td width=43 align=right style=bold ></td>
					<td width=12 align=right style=bold ></td>
					<td width=14 align=right style=bold ></td>
					<td width=14 align=right style=bold ></td>
				<tr>	
				<tr>
					<td width=43 align=right ></td>
					<td width=12 align=right style=bold ></td>
					<td width=14 align=right style=bold ></td>
					<td width=14 align=right style=bold ></td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->transhtmltable($tmptable,0,0); 	
			*/
			//加重色////////////////////////////////////////////
			$pdf->SetFont('Arial','',$FontSize_TiTle);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY($CurMargin+116,$ConTableBegin_Y+1); 
			$tmpInvoice_PI=substr($Invoice_PI,0,strlen($Invoice_PI));
			$tmptable="<table border=0 >
				<tr bgcolor=#000000 repeat>
					<td  align=left style=bold>$tmpInvoice_PI</td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->transhtmltable($tmptable,1,1); 
			//加重色
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFont('Arial','B',$FontSize_TiTle+1);
			$pdf->SetXY($CurMargin+115,$ConTableBegin_Y+1);  
			$tmptable="<table border=0 >
				<tr >
					<td  align=left style=bold>$Invoice_PI</td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->transhtmltable($tmptable,0,0); 
			////////////////////////////////////////////////////////////////
			
			
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetFont('Arial','',$FontSize_TiTle);
			$pdf->SetXY($CurMargin+114,$ConTableBegin_Y-2);  //87,57
			$tmpDate=date("d-M-y");
			$tmptable="<table border=0 >
				<tr>
					<td width=31 align=right style=bold ></td>
					<td width=10 align=right style=bold ></td>
					<td width=14 align=right style=bold ></td>
					<td width=16 align=left style= >$tmpDate</td>
				<tr>	
				<tr>
					<td width=31 align=right style=bold ></td>
					<td width=10 align=right style=bold ></td>
					<td width=14 align=right style=bold ></td>
					<td width=16 align=left  >$pageNo</td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable); 		
		}		
		
		
		$ConTableBegin_Y=$pdf->GetY()+6; //获取的是eurSInvoiceNO中位置
		$pdf->SetFont('Arial','B',9);
		if ($CompanyId==1004 ){
			$pdf->SetTextColor(0,0,0);
			$pdf->Text($CurMargin+160,$ConTableBegin_Y-3,"MADE IN CHINA");  //输出I
		}		
		
		//SOLD TO:
		{
			
			//$ConTableBegin_Y=$pdf->GetY()+6; //获取的是eurSInvoiceNO中位置
			$pdf->SetFont('Arial','B',$FontSize_TiTle);
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);  
			$tmptable="<table border=0 >
				<tr>
					<td width=80 align=left style=bold >SOLD TO:</td>
					<td width=106 align=right style=bold >FORWARDER:</td>
				<tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable);			
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('Arial','',$FontSize_TiTle);
			$ConTableBegin_Y=$pdf->GetY(); //获取的是eurSInvoiceNO中位置
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y); 
			
			if($ClientSoldTo!=""){
				$tmptable="<table border=0 >
					<tr>
						<td width=80 align=left  >$ClientSoldTo. </td>
						<td width=106 align=right style=bold >$Wise</td>
					<tr>	
				</table>" ;//$eurTableList;
				
			}
			else{
				$tmptable="<table border=0 >
					<tr>
						<td width=80 align=left  >$Company, <br> $Address. </td>
						<td width=106 align=right style=bold >$Wise</td>
					<tr>	
				</table>" ;//$eurTableList;
			}
			
			$pdf->htmltable($tmptable); 			
		}
		

		//SHIP TO, TERM,Currency:
		{
			
			$ConTableBegin_Y=$pdf->GetY()+2; //获取的是eurSInvoiceNO中位置
			$pdf->SetFont('Arial','B',$FontSize_TiTle);
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);  
			$tmptable="<table border=0 >
				<tr>
					<td width=80 align=left style=bold >SHIP TO:</td>
					<td width=106 align=right style=bold >TERM:</td>
				<tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable);			
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('Arial','',$FontSize_TiTle);
			$ConTableBegin_Y=$pdf->GetY(); //获取的是eurSInvoiceNO中位置
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);  
			//if ($ShipTo!="" && ($CompanyId==1088 || $CompanyId==1036 || $CompanyId==1046)){
			if ($ShipTo!=""){
				$tmpShipTo="$ShipTo.";
			}
			else{
				$tmpS1=trim($SoldTo)==''?'':',';
				$tmpS2=trim($ToAddress)==''?'':'.';
				$tmpShipTo="$SoldTo$tmpS1 <br> $ToAddress$tmpS2";
			}
			$tmptable="<table border=0 >
				<tr>
					<td width=80 align=left  >$tmpShipTo</td>
					<td width=106 align=right >$PaymentTerm <br> $Priceterm <br> $Terms <br> Currency:$Symbol</td>
				<tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable); 			
		}



		//Note : REQUISITION BY:
		{
			
			$ConTableBegin_Y=$pdf->GetY(); //获取的是eurSInvoiceNO中位置
			$pdf->SetFont('Arial','B',$FontSize_TiTle);
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);  
			$tmptable="<table border=0 >
				<tr>
					<td width=80 align=left style=bold >NOTE:</td>
					<td width=106 align=right style=bold >REQUISITION BY:</td>
				<tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable);			
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('Arial','',$FontSize_TiTle);
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
					<td width=80 align=left  >$Commoditycode$StableNote$Notes</td>
					<td width=106 align=right >$Nickname $tmpMobile</td>
				<tr>	
			</table>" ;
			//$eurTableList;
		
			$pdf->htmltable($tmptable); 			
		}
		
		$ConTableBegin_Y=$pdf->GetY()+4;


?>