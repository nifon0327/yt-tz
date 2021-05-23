<?php 
function ToLable($PreWord,$LableID,$LableSUM,$i,$Udate,$BoxTotal,$InvoiceNO,$OrderPO,$Description,$WG,$BoxPcs,$eCode,$BoxSpec,$BoxCode,$PackingUnit,$PackingRemark,$StartPlace,$EndPlace,$LabelModel,$NG){
	// add by zx 20100512
	$shipto="";
	switch($LabelModel){
		case 5:
			$shipto="(via Expeditors)";
			break;
		case 6:
			$shipto="(via Dachser)";
			break;			
	}
	//初始化
	$TrackingNO="&nbsp";
	//$NG=sprintf("%.2f",$WG-1);
	$SpecStr=substr($BoxSpec,0,-2);$Spec=explode("*",$SpecStr);	
	
	if($BoxCode!=""){
		$Field=explode("|",$BoxCode);$BoxCode0=$Field[0];$BoxCode1=$Field[1];
		if(is_numeric($BoxCode0)){	
			if (strpos($BoxCode1, ",") !== false )  $BoxCode1=preg_match(",","<br>",$BoxCode1);
			$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='bottom'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='120' height='40'  src='../model/ean_13code.php?Code=$BoxCode0&lw=1&hi=25'></iframe></td></tr><tr><td height='32' valign='top' scope='col'><div align='center' class='codebig'>$BoxCode1</div></td></tr></table>";
			}
		else{
			if ( strpos($BoxCode0, ",") !== false ) $BoxCode0=preg_match(",","<br>",$BoxCode0);
                        if (strlen($BoxCode0)>22) $codeStyle="style='font-size:13px;'"; else $codeStyle="class='codebig'";
			$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td height='22' valign='bottom' scope='col'><div align='center'  $codeStyle>$BoxCode0</div></td></tr><tr><td align='center' valign='top'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='120' height='40'  src='../model/ean_13code.php?Code=$BoxCode1&lw=1&hi=25'></iframe></td></tr></table>";
			}
		}
	else{
		$BoxCodeTable="&nbsp;";}
	//$BoxCodeTable="&nbsp;";	
	$StartL=strlen($StartPlace);
	if($StartL<25){
		$AutoStart="AutoCompany31";
		}
	else{
		$AutoStart="AutoCompany24";
		}
	$EndL=strlen($EndPlace);
	if($EndL<25){
		$AutoEnd="AutoCompany31";
		}
	else{
		$AutoEnd="AutoCompany30";
		}
	$StrL=strlen($eCode);
			if($StrL<21){
				$AutoDiv="AutoDiv50";
				}
			else{
				if($StrL<27){    //<26
					$AutoDiv="AutoDiv40";  //AutoDiv45
					}
				else{
					if($StrL<34){    //<36
						$AutoDiv="AutoDiv35";  // AutoDiv40
						}
					else{
						if($StrL<39){     //<46
							$AutoDiv="AutoDiv30";   //AutoDiv35
							}
						else{
							if($StrL<51){
								$AutoDiv="AutoDiv26";   //AutoDiv30
								}
							else{
								if($StrL<57){
									$AutoDiv="AutoDiv26";
									}
								else{
									$AutoDiv="AutoDiv24";
									}
								}
							}
						}
					}
				}

	//模板:标准	2-ECHO专用
	//echo "$eCode";
	/*
	if ($BoxPcs==8 || $BoxPcs==4)
	{
		$BoxPcs=20;
	}*/
	switch($LabelModel){
		case 2:		//ECHO专用标签模板
		$StrL=strlen($eCode);
		if($StrL<18){
				$E0101="E0101";
				}
			else{
				$E0101="E0101_X";
			}
		echo"
			<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=286 cellSpacing=0 cellPadding=0 width=590 border=0>
			  <TBODY>
				  <TR>
					<TD class=Dtablline align='center' valign='top'><TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=100% cellSpacing=0 cellPadding=0 width=588 border=0>
					  <TBODY>
						<TR bgColor=#ffffff>
						  <TD height='22' colSpan=2 class=E0100>&nbsp;Shipper:&nbsp;&nbsp;$StartPlace</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' colSpan=2 class=E0100>&nbsp;Consignee: &nbsp;$EndPlace</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' colSpan=2 class=E0100>&nbsp;Address:&nbsp; Les planes ,2-4- Poligono Fontsanta 08970 Sant Joan Despi-Barcelona</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD width='230' height='22' class=E0101>&nbsp;Attention:&nbsp; Mary Recio</TD>
						  <TD width='348' class=E0101>&nbsp;Número de caja:&nbsp;&nbsp;&nbsp;$PreWord$i&nbsp; / &nbsp;$PreWord$BoxTotal </TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Pedido Número:&nbsp;&nbsp; PO#$OrderPO</TD>
						  <TD class='$E0101'>&nbsp;Referencia:&nbsp;&nbsp;$eCode</TD>
						</TR>
						<TR vAlign=top bgColor=#ffffff>
						  <TD class=E0100 style='WORD-BREAK: break-all' colSpan=2 height=44>&nbsp;Descripción:&nbsp;&nbsp;$Description</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Cantidad :&nbsp; $BoxPcs $PackingUnit</TD>
						  <TD vAlign=center align=center rowSpan=6> $BoxCodeTable</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Medidas:&nbsp;$BoxSpec</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Peso bruto:&nbsp; $WG Kilos</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Peso neto: &nbsp;$NG Kilos</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Date: &nbsp;$Udate</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0001>&nbsp;Cod.barras:</TD>
						</TR>
					  </TBODY>
					</TABLE></TD>
				  </TR>
			  </TBODY>
			</TABLE>";
			break;
		default:	//标准标签模板
			//echo $StrL."-".$AutoDiv;
			//TESCO
			if($LabelModel==3){$numberTemp=$PackingRemark*$BoxPcs;$SPR=" SPR($numberTemp PCS)";}
		   $arrDate=explode(" ", $Udate);  //取得英文日期中日后面的英文字母
			if (count($arrDate)==3){
			   $Udate=preg_replace( '/[^\d]/ ', '',$arrDate[0]);
			   $mDate=preg_replace( '/[\d]/ ', '',$arrDate[0]);
			   $sDate=$arrDate[1] . " ". $arrDate[2];
			   }
			   
		 	$eCode=trim($eCode);//判断标题的长度,长度超过时自动缩小字体
			$eLen=strlen($eCode); 
		 if ($eLen>13){
			$sumLen=0;
			$sCount=0;
			$LettersArry=array("I","J","(",")");
			$iCount=0;
			while(list($key,$str) = each($LettersArry))
			{
			  $iCount=$iCount+substr_count($eCode, $str);
			}
			$sumLen=$sumLen+$iCount*6.25;
			$sCount=$sCount+$iCount;
			
			$mCount=0;
			$mCount=$mCount+substr_count($eCode, "M");
			$mCount=$mCount+substr_count($eCode, "W");
			$sumLen=$sumLen+$mCount*12.5;
			$sCount=$sCount+$mCount;
			
			$tempCode=preg_replace( '/\d/', '',$eCode);
			$tempLen=strlen($tempCode);
			$dCount=$eLen-$tempLen;
			$sumLen=$sumLen+$dCount*8.25;
			$sCount=$sCount+$dCount;
			$nCount=$eLen-$sCount;
			$sumLen=$sumLen+($nCount)*11;
			if ($sumLen>145){
				$n=0;$sumLen=$sumLen-$eLen*0.25;
				$eSize=42.18;
			  do {
				  $eSize=$eSize-1;
				  $sumLen=$sumLen-$iCount*0.175-$mCount*0.225-$dCount*0.18-$nCount*0.25;
				 }while($sumLen>145);
				 //判断字母大小写
			    $tempCode=preg_replace( '/[a-z]/', '',$eCode);
			    $tempLen=$eLen-strlen($tempCode);
				$eSize=$eSize+$tempLen*0.15;
			}
			else{
				$eSize=42.18;
			}
		}
		else{$eSize=42.18;}
			//  echo $eSize;
			  $boxLen=strlen($PreWord)+strlen($i);
			  $Box_width=38+($boxLen-1)*25;
			   if ($boxLen>3){
				  $BoxSize=28;
			  }
			  else{
			      $BoxSize=32;
			  }
			  $qtyLen=strlen($BoxPcs);
			  $qty_width=32+($qtyLen-1)*12;
			  $qty_tdwidth= $qty_width+10;
			  $qty_nextwidth=184-$qty_width;
			  
			   if (strlen($BoxSpec)<15){$BoxSpec="&nbsp;" . $BoxSpec;}
			   $BoxSpec=preg_replace( "/\*/","<span class='Font_val9'>×</span>",$BoxSpec);
		echo"
<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='292' cellSpacing=0 cellPadding=0 width=580 border=0>
     <TR height='14'> 
          <TD class='td_Topleft' width='14'></TD>
           <TD class='td_Hortop' rowspan='2' align='center' width='552'>
            <div style='text-align:center;padding-top:6px;'>
			   <span class='eCodetext' style='font-size:$eSize pt;'>$eCode</span>
			 <div>
          </TD>
          <TD class='td_Topright' width='14'></TD>
     </TR>
     <TR height='48'>
       <TD class='td_Verleft'  width='14'></TD>
	   <TD class='td_Verright'  width='14'></TD>
     </TR>  
     <TR height='216'> 
          <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
          <TD  class='td_botline' width='552'>
             <TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' cellSpacing=0 cellPadding=0 height='216'  width=552 border=0>  
               <TR height='47'>
                     <TD  align='center' width='278'>
                       <TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'  height='24'  cellSpacing=0 cellPadding=0  width=268 border=0>
				      <TR>
                        <TD class='td_smallleft' width='6'></TD> 
                        <TD  align='center' width='266' style='background-color:#000'>
             <span class='Placetext'>$StartPlace</span>
                        </TD>
                         <TD class='td_smallright' width='6'></TD>     
                      </TR>
			        </TABLE>
                   </TD>
                   <TD width='8'></TD> 
			       <TD width='56' align='Left'><span class='Box_title'>&nbsp;&nbsp;BOX </span></TD>       
                   <TD  width='100' align='center' valign='middle'>
                      <div class='div_h40' style='width:$Box_width px;_width:$Box_width px'>
					  <span style='font-size:$BoxSize pt;'>$PreWord$i</span></div>
                  </TD>
			       <TD  width='40' align='center'><span class='Box_title'>&nbsp;OF </span></TD>
                   <TD  align='center' style='text-align:center;' width='70'>
				       <div class='div_line'>
				       <span class='Box_textB'>$PreWord$BoxTotal</span>
					   </div>
                   </TD>
                </TR>
          	  <TR height='150'>
                 <TD  width='278' align='center'  valign='Top'>
                     <TABLE style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0 width='275' height='147' border=0>
                        <TR height='25'>
				          <TD class='td_line' width='60'><span class='Font_title'>&nbsp;Date:</span></TD>
				          <TD class='td_line' colspan='2'>
						   <span class='Font_val'>$Udate</span><span class='Font_date'>$mDate</span> 
						   <span class='Font_val'>$sDate</span>
						  </TD>
			            </TR>
                         <TRheight='45' >
				            <TD class='td_line50' width='60'><span class='Font_title'>&nbsp;Qty:</span></TD>
				            <TD class='td_line50' width='$qty_tdwidth'>
							   <div class='div_h32' style='width:$qty_width px;_width:$qty_width px;'>
							     <span>$BoxPcs</span></div>
							 <TD class='td_line50' width='$qty_nextwidth'>
						         <span class='Font_title'>$PackingUnit $SPR</span>
                             </TD>
			             </TR>   
                          <TR height='25'>
				             <TD colspan='3' class='td_line'><span class='Font_title'>&nbsp;CENTIMETERS: </span><span class='Font_val'> $BoxSpec</span>
                             </TD> 
			             </TR>
                          <TR height='25'>
				             <TD class='td_line' width='60'><span class='Font_title'>&nbsp;GW:</span></TD>
				             <TD class='td_line' colspan='2'>
                                <span class='Font_val'>$WG</span>&nbsp;&nbsp;&nbsp;<span class='Font_title'>KGS</span>
                             </TD>
			              </TR>   
                           <TR height='25'>
				              <TD class='td_line' width='60'><span class='Font_title'>&nbsp;NW:</span></TD>
				              <TD class='td_line'colspan='2'>
                                    <span class='Font_val'>$NG</span>&nbsp;&nbsp;&nbsp;<span class='Font_title'>KGS</span>
                              </TD>
			              </TR>     
                       </TABLE>   
                 </TD>
                 <TD width='8' class='td_right'>&nbsp;</TD> 
                 <TD  colspan='4'  width='266' align='center' valign='Top'>
                    <TABLE style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0 width='266' height='150' border=0>
                       <TR height='40'>
					       <TD width='8'>&nbsp;</TD>
				           <TD class='td_line' width='94' valign='bottom'><span class='Font_title'>&nbsp;P/O NO:</span></TD>
				           <TD class='td_line' width='164' valign='bottom'><span class='Font_val'>$OrderPO</span></TD>
			           </TR>         
                        <TR height='25'>
						    <TD width='8'>&nbsp;</TD>
				            <TD class='td_line' width='94'><span class='Font_title'>&nbsp;INVOICE:</span></TD>
				            <TD class='td_line' width='164'><span class='Font_val'>$InvoiceNO</span></TD>
			           </TR>          
                         <TR height='85'>
				             <TD  rowspan='4' colspan='4' align=center>
                                 <div calss='div_boxTable'>$BoxCodeTable</div>
                            </TD>
			            </TR>                
		             </TABLE>
                  </TD>        
               </TR> 
               <TR height='22'>
                 <TD colspan='6' align='center'  valign='bottom'>
                      <div calss='div_row'>
				       <div class='div_shiptext'><span class='shiptext'>SHIP TO</span></div>
					   <div class='div_shipval'><span class='ship_val'>$EndPlace</span></div>
					</div>
                </TD>
              </TR>
            </TABLE>       
           </TD>
          <TD  class='td_Verright' width='14'>&nbsp;</TD> 
     </TR>
      <TR height='14'> 
          <TD class='td_Bottomleft' width='14'></TD>
          <TD class='td_Horbottom' width='562'></TD>
          <TD class='td_Bottomright' width='14'></TD>
     </TR>
  </TABLE>";
		break;
		}
		if($LableID<$LableSUM){echo"<div style='PAGE-BREAK-AFTER: always'></div>";}
	}
//外箱条码输出
function EAN_13($code,$lw,$hi) { 
	//$lw =1; //条码宽
  	//$hi = 30; //条码高
  	//左资料码编码规则：根据国家不同有所不同
  	//									3法国	4 德国		5英国   6中国
  	$Guide = array(1=>'AAAAAA','AABBAB','AABBBA','ABAABB','ABBAAB','ABBBAA','ABABAB','ABABBA','ABBABA'); 
  	//左边线
  	$Lstart ='101'; 
  	//左侧编码格式，有两种码
 	 $Lencode = array("A" => array('0001101','0011001','0010011','0111101','0100011','0110001','0101111','0111011','0110111','0001011'), 
                   "B" => array('0100111','0110011','0011011','0100001','0011101','0111001','0000101','0010001','0001001','0010111')); 
  	//右边编码格式
 	$Rencode = array('1110010','1100110','1101100','1000010','1011100', 
                   '1001110','1010000','1000100','1001000','1110100');
	//中线
	$center = '01010';    
	//右边线
	$ends = '101'; 
	if ( strlen($code)!=13){//条码位数不是13，则条码有错
		die("条码必须是13位!");} 
	$lsum =0; 
	$rsum =0; 
  	for($i=0;$i<(strlen($code)-1);$i++){ 
    	if($i % 2){
			$lsum +=(int)$code[$i];}
		else{
			$rsum +=(int)$code[$i];} 
   		} 
	$tsum = $lsum*3 + $rsum; 
	/*
	//条码校准码
    if($code[12] != (10-($tsum % 10))){ 
		die("校检码不正确!"); 
    	}  
	*/
  	$barcode = $Lstart; 
	for($i=1;$i<=6;$i++){ 
		$barcode .= $Lencode [$Guide[$code[0]][($i-1)]] [$code[$i]];
		} 
	$barcode .= $center; 
	for($i=7;$i<13;$i++){
		$barcode .= $Rencode[$code[($i)]];
		} 
	$barcode .= $ends; 
	$img = ImageCreate($lw*95+10,$hi+15); //输出x*y的空白图像ImageCreate($lw*95+60,$hi+30)
	$fg = ImageColorAllocate($img,0,0,0); //给空白图象填色
	$bg = ImageColorAllocate($img, 255, 255, 255); //给空白图象填色
	//int imagefilledrectangle ( resource image, int x1, int y1, int x2, int y2, int color ). 
	//ImageFilledRectangle($img, 0, 0, $lw*95+60, $hi+30, $bg)
	ImageFilledRectangle($img, 0, 0, $lw*95+40, $hi+15, $bg); 
	//在image图像中画一个用color颜色填充了的矩形，其左上角坐标为x1\y1,右下角坐标为x2/y2。0/0是图像的最左上角。
	$shift=10; 
	for ($x=0;$x<strlen($barcode);$x++) { 
		if(($x<4) || ($x>=45 && $x<50) || ($x >=92)){
			$sh=10;} 
		else{
			$sh=0; 
			} 
		if ($barcode[$x]=='1'){  
			$color = $fg;} 
		else{  
			$color = $bg;  
			} 
		// ImageFilledRectangle($img, ($x*$lw)+30,5,($x+1)*$lw+29,$hi+5+$sh,$color); 
   		ImageFilledRectangle($img, ($x*$lw)+10,0,($x+1)*$lw+9,$hi+0+$sh,$color); 
		} 
	/* Add the Human Readable Label */
  	//
  	ImageString($img,$lw+1,0,$hi+3,$code[0],$fg); 
  	for ($x=0;$x<6;$x++) { 
 		// int imagestring(int im, int font, int x, int y, string s, int col);
 		//ImageString($img,5,$lw*(8+$x*6)+30,$hi+5,$code[$x+1],$fg); 
   		ImageString($img,$lw+1,$lw*(8+$x*6)+10,$hi+3,$code[$x+1],$fg); 
   		ImageString($img,$lw+1,$lw*(53+$x*6)+10,$hi+3,$code[$x+7],$fg); 
  		}  
		//header("Content-Type: image/png"); 
	ImagePNG($img); 
	}
?>