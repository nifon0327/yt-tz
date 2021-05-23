<?php 

		$StartPlace=trim($StartPlace)==""?"Ash Cloud Co.,Ltd.Shenzhen":$StartPlace;
	        $EndPlace=trim($EndPlace)==""?"&nbsp;":$EndPlace;
			echo"
<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='292' cellSpacing=0 cellPadding=0 width=580 border=0>
     <TR height='14'> 
          <TD class='td_Topleft' width='14'></TD>
           <TD class='td_Hortop' rowspan='2' align='center' width='552'>
            <div  style='text-align:center;padding-top:6px;'>
			   <span  class='eCodetext' style='font-size:$eSize pt;'>$eCode</span>
			 </div>
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
             <TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' cellSpacing=0 cellPadding=0  width=552 height='216' border=0>  
               <TR height='47'>
                     <TD  align='center' width='278'>
                       <TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'  height='24'  cellSpacing=0 cellPadding=0  width=268 border=0>
				      <TR>
                        <TD class='td_smallleft' width='6'></TD> 
                        <TD  align='center' width='266' style='background-color:#000'>
             <span class='Placetext' style='font-size:$spSize pt;'>$StartPlace</span>
                        </TD>
                         <TD class='td_smallright' width='6'></TD>     
                      </TR>
			        </TABLE>
                   </TD>
                   <TD width='8'></TD> 
			       <TD width='56' align='Left'><span class='Box_title'>&nbsp;&nbsp;BOX </span></TD>       
                   <TD  width='100' align='center'>
                      <div class='div_h40' style='width:$Box_width px;_width:$Box_width px;'>
					       <span style='font-size:$BoxSize pt;'>$PreWord$i</span>
					  </div>
                  </TD>
			       <TD  width='40' align='center'><span class='Box_title'>&nbsp;OF </span></TD>
                   <TD  align='center' style='text-align:center;' width='70'>
				       <div class='div_line'>
				       <span class='Box_textB'>$PreWord$BoxTotal</span>
					   </div>
                   </TD>
                </TR>
          	  <TR height='183'>
                 <TD  width='278' align='center'  valign='Top'>
                     <TABLE style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0 width='275' height='180' border=0>
                        <TR height='30'>
				          <TD class='td_line' width='60'><span class='Font_title'>&nbsp;Date:</span></TD>
				          <TD class='td_line' colspan='2'>
						   <span class='Font_val'>$Udate</span><span class='Font_date'>$mDate</span> 
						   <span class='Font_val'>$sDate</span>
						  </TD>
			            </TR>
                         <TR height='50'>
				            <TD class='td_line50' width='60'><span class='Font_title'>&nbsp;Qty:</span></TD>
				            <TD class='td_line50' width='$qty_tdwidth'>
							   <div class='div_h32' style='width:$qty_width px;_width:$qty_width px;'>
							     <span>$BoxPcs</span></div>
							 <TD class='td_line50' width='$qty_nextwidth'>
						         <span class='Font_title'>$PackingUnit $SPR</span>
                             </TD>
			             </TR>   
                          <TR height='30'>
				             <TD colspan='3' class='td_line'><span class='Font_title'>&nbsp;CENTIMETERS: </span><span class='Font_val'> $BoxSpec</span>
                             </TD> 
			             </TR>
                          <TR height='30'>
				             <TD class='td_line' width='60'><span class='Font_title'>&nbsp;GW:</span></TD>
				             <TD class='td_line' colspan='2'>
                                <span class='Font_val'>$WG</span>&nbsp;&nbsp;&nbsp;<span class='Font_title'>KGS</span>
                             </TD>
			              </TR>   
                           <TR height='30'>
				              <TD class='td_line' width='60'><span class='Font_title'>&nbsp;NW:</span></TD>
				              <TD class='td_line'colspan='2'>
                                    <span class='Font_val'>$NG</span>&nbsp;&nbsp;&nbsp;<span class='Font_title'>KGS</span>
                              </TD>
			              </TR> 
                       </TABLE>   
                 </TD>
                 <TD width='8' class='td_right'>&nbsp;</TD> 
                 <TD  colspan='4'  width='266' align='center' valign='Top'>
                    <TABLE style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0 width='266' height='180' border=0>
                       <TR height='25'>
					       <TD width='8'>&nbsp;</TD>
				           <TD class='td_line' width='94'><span class='Font_title'>&nbsp;P/O NO:</span></TD>
				           <TD class='td_line' width='164'><span class='Font_val'>$OrderPO</span></TD>
			           </TR>         
                        <TR height='25'>
						    <TD width='8'>&nbsp;</TD>
				            <TD class='td_line' width='94'><span class='Font_title'>&nbsp;INVOICE:</span></TD>
				            <TD class='td_line' width='164'><span class='Font_val'>$InvoiceNO</span></TD>
			           </TR>          
         	            <TR height='15'>
			            	<TD  colspan='3'  align=center >
                                 <span  class='cNametext' style='font-size:$cNameSize px;'>$cName</span>
                            </TD>
                        </TR>
                         <TR height='75'>
				             <TD colspan='3' align=center valign=middle>
                                 <div calss='div_boxTable'>$BoxCodeTable</div>
                            </TD>
			            </TR>   
				      <TR height='40'>
			            	<TD   colspan='3'  align=center valign=bottom>
							 <div style='border: 2px dashed #000;height:30px;width:150px;text-align:center;vertical-align:middle;'>
                                 <span  class='ship_val' style='font-size:20px;'>Made in China</span></div>
                            </TD>
                        </TR>
		             </TABLE>
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
  </TABLE>
";

?>