<?php
        //新的Credit,debit PDF

		$ConTableBegin_Y=$CurMargin+4;  //$CurMargin=7;

		$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
		$pdf->SetFont('uGB','B',20);
		if($Com_Pack_PI!=""){  //如PI
			$pdf->Text($CurMargin,$ConTableBegin_Y+3,"$Com_Pack_PI");  //输出I
		}
		else {
			$pdf->Text($CurMargin,$ConTableBegin_Y+3,"上海市研砼包装有限公司  送货单");  //输出I
		}

		$pdf->SetFont('uGB','',$FontSize_TiTle);


		//$pdf->Text(); //是从下往上写的
		//$pdf->htmltable($eurSInvoice) //是从上往下画的,表在后写时不会盖掉到原来的，
		{
			$pdf->SetXY($CurMargin+130,$ConTableBegin_Y-3);  //94,57
			$tmptable="<table border=0 >
				<tr>
					<td width=21 align=left  >单号:</td>
					<td width=3 align=right style=bold ></td>
					<td width=15 align=right  >日期:</td>
					<td width=16 align=right style=bold ></td>
				<tr>	
				<tr>
					<td width=21 align=right style=bold ></td>
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
			$pdf->SetXY($CurMargin+131,$ConTableBegin_Y);
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
			$pdf->SetXY($CurMargin+130,$ConTableBegin_Y);
			$tmptable="<table border=0 >
				<tr >
					<td  align=left style=bold>$Invoice_PI</td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->transhtmltable($tmptable,0,0);


			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetFont('uGB','',$FontSize_TiTle);
			$pdf->SetXY($CurMargin+119,$ConTableBegin_Y-3);
			$tmpDate=date("Y-m-d");
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
					<td width=80 align=left style=bold >收货方:</td>
					<td width=106 align=right style=bold >联络人:</td>
				<tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable);

			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('uGB','',$FontSize_TiTle);
			$ConTableBegin_Y=$pdf->GetY(); //获取的是eurSInvoiceNO中位置
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);
			$Mobile=trim($TransMobile);
			$tmpMobile=$Mobile;
			if(strlen($Mobile)>10 ){
				if(substr($Mobile,0,3)!='886'){
					$tmpMobile='+86 '.substr($Mobile,0,3).'-'.substr($Mobile,3,4).'-'.substr($Mobile,7);
				}
			}
			if($tmpMobile!=''){
				$tmpMobile='('.$tmpMobile.')';
			}

			if($ClientSoldTo!=""){
				$tmptable="<table border=0 >
					<tr>
						<td width=80 align=left  >$ClientSoldTo.</td>
						<td width=106 align=right  >$ZTransactor $tmpMobile</td>
					<tr>	
				</table>" ;//$eurTableList;
			}
			else{

				$tmptable="<table border=0 >
					<tr>
						<td width=80 align=left  >$Company, <br> $Address. </td>
						<td width=106 align=right  >$ZTransactor $tmpMobile</td>
					<tr>	
				</table>" ;//$eurTableList;
			}

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
					<td width=80 align=left style=bold >收货地址:</td>
					<td width=106 align=right style=bold >备注:</td>
				<tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable);

			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('uGB','',$FontSize_TiTle);
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
					<td width=106 align=right >$PaymentTerm <br> $Priceterm <br> $Terms <br> $Notes  币种:$Symbol</td>
				<tr>	
			</table>" ;//$eurTableList;

			$pdf->htmltable($tmptable);
		}


		$ConTableBegin_Y=$pdf->GetY()+5;

?>