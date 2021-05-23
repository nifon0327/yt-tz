<?php
        //新的Credit,debit PDF

		$ConTableBegin_Y=$CurMargin+4;  //$CurMargin=7;

		$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
		$pdf->SetFont('uGB','B',20,'','true');

		//$pdf->Text($CurMargin,$ConTableBegin_Y+3,"上海市研砼包装有限公司  采购合同");  //输出I
		$pdf->Text($CurMargin,$ConTableBegin_Y+3,"$SealCompanyStr  采购合同");  //输出I
		$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');


		//$pdf->Text(); //是从下往上写的
		//$pdf->htmltable($eurSInvoice) //是从上往下画的,表在后写时不会盖掉到原来的，
		{
			$pdf->SetXY($CurMargin+130,$ConTableBegin_Y-3);  //94,57
			$tmptable="<table border=0 >
				<tr>
					<td width=21 align=left  >单号 $TaxStr:</td>
					<td width=4 align=right style=bold ></td>
					<td width=15 align=right  >页&nbsp;&nbsp;数:</td>
					<td width=15 align=right style=bold ></td>
				<tr>	
				<tr>
					<td width=21 align=right style=bold ></td>
					<td width=4 align=right style=bold ></td>
					<td width=15 align=right  >送货至:</td>
					<td width=15 align=right style=bold ></td>
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
			$pdf->SetFont('uGB','',$FontSize_TiTle+1,'','true');
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY($CurMargin+131,$ConTableBegin_Y);
			$tmpInvoice_PI=substr($Invoice_PI,0,strlen($Invoice_PI)-2);
			$tmptable="<table border=0 >
				<tr bgcolor=#000000 repeat>
					<td  align=left style=bold>$tmpInvoice_PI</td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->transhtmltable($tmptable,1,1);
			//加重色
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFont('uGB','',$FontSize_TiTle+1,'','true');
			$pdf->SetXY($CurMargin+130,$ConTableBegin_Y);
			$tmptable="<table border=0 >
				<tr >
					<td  align=left style=bold>$Invoice_PI</td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->transhtmltable($tmptable,0,0);


			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');
			$pdf->SetXY($CurMargin+119,$ConTableBegin_Y-3);
			$tmpDate=date("Y-m-d");
			$tmptable="<table border=0 >
				<tr>
					<td width=31 align=right style=bold ></td>
					<td width=4 align=right style=bold ></td>
					<td width=15 align=right style=bold ></td>
					<td width=15 align=left style= >$pageNo</td>
				<tr>	
				<tr>
					<td width=31 align=right style=bold ></td>
					<td width=4 align=right style=bold ></td>
					<td width=15 align=right style=bold ></td>
					<td width=15 align=left  >$SendFloor</td>
				<tr>				
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable);
		}

		{
			$ConTableBegin_Y=$pdf->GetY()+6; //获取的是eurSInvoiceNO中位置
			$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin-1,$ConTableBegin_Y);
			$tmptable="<table border=0 >
				<tr height=5>
					<td width=12  height=5 align=left style=bold >供应商:</td>
					<td width=120 align=left style=bold ></td>
					<td width=16 align=left style=bold >采购日期:</td>
					<td width=36 align=left style=bold ></td>
				<tr>
				<tr>
					<td width=12 height=5 align=left style=bold >接洽人:</td>
					<td width=120 align=left style=bold ></td>
					<td width=16 align=left style=bold >采&nbsp;购&nbsp;人:</td>
					<td width=36 align=left style=bold ></td>
				<tr>	
				<tr>
					<td width=12 height=5 align=left style=bold >电&nbsp;&nbsp;话:</td>
					<td width=120 align=left style=bold ></td>
					<td width=16 align=left style=bold >联系邮箱:</td>
					<td width=36 align=left style=bold ></td>
				<tr>	
				<tr>
					<td width=12 height=5 align=left style=bold >传&nbsp;&nbsp;真:</td>
					<td width=120 align=left style=bold ></td>
					<td width=16 align=left style=bold >结付方式:</td>
					<td width=36 align=left style=bold ></td>
				<tr>
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable);

			//填写内容
			$pdf->SetXY($CurMargin-2,$ConTableBegin_Y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');
			$tmptable="<table border=0 >
				<tr height=5>
					<td width=12  height=5 align=left style=bold ></td>
					<td width=120 align=left >$Provider</td>
					<td width=16 align=left style=bold ></td>
					<td width=40 align=left  >$Date</td>
				<tr>
				<tr>
					<td width=12 height=5 align=left style=bold ></td>
					<td width=120 align=left  >$Linkman</td>
					<td width=16 align=left style=bold ></td>
					<td width=40 align=left  >$Buyer</td>
				<tr>	
				<tr>
					<td width=12 height=5 align=left style=bold ></td>
					<td width=120 align=left  >$Tel</td>
					<td width=16 align=left style=bold ></td>
					<td width=40 align=left  >$Mail</td>
				<tr>	
				<tr>
					<td width=12 height=5 align=left style=bold ></td>
					<td width=120 align=left  >$Fax</td>
					<td width=16 align=left style=bold ></td>
					<td width=40 align=left  >$PaySTR</td>
				<tr>
			</table>" ;//$eurTableList;
			$pdf->transhtmltable($tmptable,0,0);

		}




		$ConTableBegin_Y=$pdf->GetY()+5;

?>