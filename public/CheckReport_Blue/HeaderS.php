<?php   
		
		$ConTableBegin_Y=$CurMargin+4; 
		
		$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
		$pdf->SetFont('uGB','B',20,'','true');
		$pdf->Text($CurMargin,$ConTableBegin_Y+3,"$S_Company  品检报告");  //输出I
		$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');

	
		{
			$pdf->SetXY($CurMargin+130,$ConTableBegin_Y-3);  //94,57
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
			</table>" ;//$eurTableList;
			$pdf->htmltable($tmptable); 
			
       
			//加重色////////////////////////////////////////////
			$pdf->SetFont('uGB','',$FontSize_TiTle+1,'','true');
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY($CurMargin+131,$ConTableBegin_Y); 
			$tmptable="<table border=0 ><tr bgcolor=#000000 repeat>
			           <td align=right style=bold>$GysNumber</td><tr></table>" ;
			$pdf->transhtmltable($tmptable,1,1); 
			//加重色
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFont('uGB','',$FontSize_TiTle+1,'','true');
			$pdf->SetXY($CurMargin+130,$ConTableBegin_Y);  
			$tmptable="<table border=0 ><tr><td  align=right style=bold>$GysNumber</td><tr></table>" ;
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
					<td width=65  align=right style=bold >开单人:</td>
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
					<td width=65 align=right >$Operator</td>
				<tr>
			</table>" ;
			$pdf->transhtmltable($tmptable,0,0);				
			
		}
		
		{  
		
		   $ConTableBegin_Y=$pdf->GetY()+5;
		    
			$pdf->SetXY($CurMargin-0.2,$ConTableBegin_Y); 
			$pdf->SetFont('uGB','',$FontSize_TiTle,'','true');			
			$stufftable="<table border=0 >
				<tr bgcolor=#f9f9fb repeat>
					<td width=185 align=left height=25 ></td>
				<tr>
			</table>" ;
			$pdf->htmltable($stufftable,0,0);		
			
			if($AppFilePath!=""){
				  $pdf->Image($AppFilePath,$CurMargin+2,$ConTableBegin_Y+1,25,23,"jpg");
			  
		    }else{
		    
			     $pdf->Image("../public/checkReport_blue/nopic.jpg",$CurMargin+2,$ConTableBegin_Y+1,25,23,"jpg");
		    }	
		    
		    //配件名称，送货数量，合格率
		    
		    $pdf->SetTextColor($Color_blackA['r'],$Color_blackA['g'],$Color_blackA['b']);
		    $pdf->SetFont('uGB','B',15,'','true');
		    $pdf->Text($CurMargin+30,$ConTableBegin_Y+10,$StuffCname);  //配件名称
		    
		    
		   
		    $pdf->SetTextColor($Color_StuffTitleA['r'],$Color_StuffTitleA['g'],$Color_StuffTitleA['b']);
		    $pdf->SetFont('uGB','B',13,'','true');
		    $pdf->Text($CurMargin+30,$ConTableBegin_Y+20,"送货数量");
		    
		    $pdf->SetTextColor($Color_blackA['r'],$Color_blackA['g'],$Color_blackA['b']);
		    $pdf->SetFont('uGB','B',13,'','true');
		    $pdf->Text($CurMargin+55,$ConTableBegin_Y+20,$shQty);
		    
		    
		    $pdf->SetTextColor($Color_StuffTitleA['r'],$Color_StuffTitleA['g'],$Color_StuffTitleA['b']);
		    $pdf->SetFont('uGB','B',13,'','true');
		    $pdf->Text($CurMargin+72,$ConTableBegin_Y+20,"合格率");
		    
		    $pdf->SetTextColor($Color_StuffRateA['r'],$Color_StuffRateA['g'],$Color_StuffRateA['b']);
		    $pdf->SetFont('uGB','B',13,'','true');
		    $pdf->Text($CurMargin+92,$ConTableBegin_Y+20,$GoodRate);
		    
		    
		}
		
		
		
		
		$ConTableBegin_Y=$pdf->GetY();
		
?>