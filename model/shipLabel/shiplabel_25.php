<?php 
//CG专用
$DiyName="&nbsp;";
$Color="&nbsp;";
$Field=explode("|",$Description);
$Count=count($Field);
if($Count==3){
	$DiyName=$Field[1];
	$Color=$Field[2];
}

$Field=explode("(",$OrderPO);  //把括号中的提取出来
$Count=count($Field);
if($Count==2){
	$OrderPO=$Field[1];	
	$Field=explode(")",$OrderPO);
	$OrderPO=$Field[0];
}
			
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
     <TR height='50'>
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
				          <TD class='td_line30' width='60'><span class='Font_title'>日期::</span></TD>
				          <TD class='td_line30' colspan='2'>
						   <span class='Font_val'>$Udate</span><span class='Font_date'>$mDate</span> 
						   <span class='Font_val'>$sDate</span>
						  </TD>
			            </TR>
               
                        <TR >
				          <TD class='td_line30' width='60'><span class='Font_title'>名称:</span></TD>
				          <TD class='td_line30' colspan='2'>
						  <span class='Font_title'>$DiyName</span> 
						  </TD>
			            </TR>
                                                
                         <TR>
				            <TD class='td_line52' width='60'><span class='Font_title'>数量:</span></TD>
				            <TD class='td_line52' width='$qty_tdwidth'>
							   <div class='div_h32' style='width:$qty_width px;_width:$qty_width px;'>
							     <span>$BoxPcs</span></div>
						   <TD class='td_line52' width='$qty_nextwidth'>
						         <span class='Font_val'>$PackingUnit $SPR</span>
                                 <span class='Font_title'>&nbsp;&nbsp;颜色: $Color</span>
                           </TD>
			             </TR>   
                          <TR>
				             <TD colspan='3' class='td_line30'><span class='Font_title'>外箱尺寸: </span><span class='Font_val'>$BoxSpec</span>
                             </TD> 
			             </TR>
                          <TR>
				             <TD class='td_line30' width='60'><span class='Font_title'>毛重:</span></TD>
				             <TD class='td_line30' colspan='2'>
                                <span class='Font_val'>$WG</span>&nbsp;&nbsp;&nbsp;<span class='Font_title'>KGS</span>
                             </TD>
			              </TR>   
                           <TR>
				              <TD class='td_line30' width='60'><span class='Font_title'>净重:</span></TD>
				              <TD class='td_line30'colspan='2'>
                                    <span class='Font_val'>$NG</span>&nbsp;&nbsp;&nbsp;<span class='Font_title'>KGS</span>
                              </TD>
			              </TR>    
						   <TR>
				              <TD class='td_line30' width='60'><span class='Font_title'>PO:</span></TD>
				              <TD class='td_line30'colspan='2'>
                                    <span class='Font_val'>$OrderPO</span>
                              </TD>
			              </TR>     
                       </TABLE>   
                </TD>
                 <TD width='8' class='td_right'>&nbsp;</TD> 
                  <TD   width='266' align='center' valign='Top'>
                      <TABLE style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0 width='266' border=0>
					  <TR height='60'>
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
                         <TR  height='87'>
				             <TD  colspan='4' align=center>
                                 <div class='$AutoEnd'>$BoxCodeTable</div>
                            </TD>
			            </TR>   
				         <TR height='65'>
				             <TD  colspan='4' align=center>
                                 <div class='dashed_div'><span class='Font_val19'>$EndPlace</span></div>
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
  </TABLE> ";
?>