<?php 
//CG专用
			//echo $StrL."-".$AutoDiv;
			//TESCO
			//if($LabelModel==3){$numberTemp=$PackingRemark*$BoxPcs;$SPR=" SPR($numberTemp PCS)";}
			//$StartPlace="Ash cloud co.,Ltd.shenzhen";
echo"
 <TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='290' cellSpacing=0 cellPadding=0 width=580 border=0>
     <TR height='14'> 
          <TD class='td_Topleft' width='14'></TD>
           <TD class='td_Hortop' rowspan='2' align='center' width='552'>
            <div  style='text-align:center;padding-top:6px;'>
			   <span  class='eCodetext' style='font-size:$eSize pt;'>$eCode</span>
			 <div>
          </TD>
          <TD class='td_Topright' width='14'></TD>
     </TR>
     <TR height='30'>
       <TD class='td_Verleft'  width='14'></TD>
	   <TD class='td_Verright'  width='14'></TD>
     </TR>  
     <TR height='212'>
          <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
          <TD  class='td_botline' width='552'>
             <TABLE  style='WORD-WRAP: break-word' cellSpacing=0 cellPadding=0  width=550 border=0>  
			   <TR height='10'><TD colspan='3'></TD></TR>
          	   <TR height='202'>
                 <TD  width='278' align='center'  valign='Top'>
                     <TABLE style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0 width='275' border=0>                     
                        <TR >
				          <TD class='td_line30' width='60'><span class='Font_title'>&nbsp;Date:</span></TD>
				          <TD class='td_line30' colspan='2'>
						   <span class='Font_val'>$Udate</span><span class='Font_date'>$mDate</span> 
						   <span class='Font_val'>$sDate</span>
						  </TD>
			            </TR>
                         <TR>
				            <TD class='td_line30' width='60'><span class='Font_title'>&nbsp;Qty:</span></TD>
 				             <TD class='td_line30' colspan='2'>
                                <span class='Font_val'>$BoxPcs</span>&nbsp;&nbsp;&nbsp;<span class='Font_title'>$PackingUnit $SPR</span>
                             </TD>                            
			             </TR>
                           
                          <TR>
				             <TD colspan='3' class='td_line30'><span class='Font_title'>&nbsp;BOX </span><span class='Font_val'>$PreWord$i OF $PreWord$BoxTotal</span>
                             </TD> 
			             </TR>
                                                   
                          <TR>
				             <TD colspan='3' class='td_line30'><span class='Font_title'>&nbsp;CENTIMETERS: </span><span class='Font_val'>$BoxSpec</span>
                             </TD> 
			             </TR>
                          <TR>
				             <TD class='td_line30' width='60'><span class='Font_title'>&nbsp;GW:</span></TD>
				             <TD class='td_line30' colspan='2'>
                                <span class='Font_val'>$WG</span>&nbsp;&nbsp;&nbsp;<span class='Font_title'>KGS</span>
                             </TD>
			              </TR>   
						   <TR>
				              <TD class='td_line30' width='60'><span class='Font_title'>P/O NO:</span></TD>
				              <TD class='td_line30'colspan='2'>
                                    <span class='Font_val'>$OrderPO</span>
                              </TD>
			              </TR>     
                           <TR>
                              <TD colspan='3' class='td_line30'><span class='Font_title'>&nbsp;Dept No.: </span><span class='Font_val'>7</span>
			              </TR>
                                                     
                          <TR>
				             <TD colspan='3' class='td_line30'><span class='Font_title'></span><span class='Font_title'>$Description</span>
                             </TD> 
			             </TR>
                                                   
                       </TABLE>   
                </TD>
                 <TD width='8' class='td_right'>&nbsp;</TD> 
                  <TD   width='266' align='center' valign='Top'>
                      <TABLE style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0 width='266' border=0>

                         <TR >
				             <TD  colspan='4' class='td_line30' >
                                 <span class='Font_val'>From:</span><span class='Font_title'>GHC Ventures Limited Unit 1903-05, 19/F Tower A, Regent Centre 63 Wo Yi Hop Road Kwai Chung Hong Kong</span>
                            </TD>
			            </TR> 
                         <TR  >
				             <TD  colspan='4' >
                                 <span class='Font_val'>TO:</span><span class='Font_title'>TJX Europe Buying (Deutschland) Limited 50 Clarendon Road Watford, Herts United Kingdom</span>
                            </TD>
			            </TR>                           
				         <TR height='10'>
				             <TD  colspan='4' align=center></TD>
			            </TR>   
				         <TR height='15'>
				             <TD   align=right>Pretickets</TD>
                             <TD   width='40'>
							 <table border='0' style='border-collapse:collapse;border-spacing:0;cellpadding:0px;cellspacing:0px'><tr style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;'>Yes</td><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>
							 </TD>
                             <TD  align=center>Or</TD>
                             <TD   width='80'>
							  <table border='0' style='border-collapse:collapse;border-spacing:0;cellpadding:0px;cellspacing:0px'><tr style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;'>&nbsp;No&nbsp;</td><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'>&nbsp;&nbsp;X&nbsp;&nbsp;</td></tr></table>
							 </TD>
			            </TR>  
						<TR height='8'>
				             <TD  colspan='4' align=center></TD>
			            </TR> 						
				         <TR height='15'>
				             <TD  align=right>Store Ready</TD>
                             <TD   width='40'>
							 <table border='0' style='border-collapse:collapse;border-spacing:0;cellpadding:0px;cellspacing:0px'><tr style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;'>Yes</td><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>
							 </TD>
                             <TD  align=center>Or</TD>
                             <TD   width='40'>
							  <table border='0' style='border-collapse:collapse;border-spacing:0;cellpadding:0px;cellspacing:0px'><tr style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;'>&nbsp;No&nbsp;</td><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'>&nbsp;&nbsp;X&nbsp;&nbsp;</td></tr></table>
							 </TD>
			            </TR>
 
 				         <TR height='15'>
				             <TD  align=right>Heavy carton(>25KG)</TD>
                             <TD   width='40'>
							 <table border='0' style='border-collapse:collapse;border-spacing:0;cellpadding:0px;cellspacing:0px'><tr style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;'>Yes</td><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>
							 </TD>
                             <TD  align=center>Or</TD>
                             <TD   width='40'>
							  <table border='0' style='border-collapse:collapse;border-spacing:0;cellpadding:0px;cellspacing:0px'><tr style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;'>&nbsp;No&nbsp;</td><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'>&nbsp;&nbsp;X&nbsp;&nbsp;</td></tr></table>
							 </TD>
			            </TR>
				         <TR height='15'>
				             <TD  align=right>Fragile</TD>
                             <TD   width='40'>
							 <table border='0' style='border-collapse:collapse;border-spacing:0;cellpadding:0px;cellspacing:0px'><tr style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;'>Yes</td><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>
							 </TD>
                             <TD  align=center>Or</TD>
                             <TD   width='40'>
							  <table border='0' style='border-collapse:collapse;border-spacing:0;cellpadding:0px;cellspacing:0px'><tr style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;'>&nbsp;No&nbsp;</td><td style='border-width:1px;border-color:#000000;border-style:solid; padding:0px;margin:0px'>&nbsp;&nbsp;X&nbsp;&nbsp;</td></tr></table>
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