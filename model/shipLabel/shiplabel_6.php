<?php 
// CEL专用模板
	     $EndPlace=trim($EndPlace)==""?"&nbsp;":$EndPlace;
		 $qtyLen=strlen($BoxPcs);
		 $qty_width=20+($qtyLen-1)*8;
		 $qty_tdwidth= $qty_width+6;
		 $cNameLen=strlen($cName);
		 $cNameSize=11;
		  if ($cNameLen>20){
			  $cNameSize=10;
			  if ($cNameLen>30){
					$cNameSize=7;
			   }
			  else{
				 if ($cNameLen>25) $cNameSize=8;
			  }
		  } 
 
               $boxLen=strlen($PreWord)+strlen($i);
			  $Box_width=26+($boxLen-1)*16;
			  if ($boxLen>3){
                                 if (strlen($PreWord)>0){
                                     $BoxSize=20;
                                 }else{
				     $BoxSize=22;
                                 }
			  }
			  else{
                              if (strlen($PreWord)>0 && $boxLen=3){
                                  if ($PreWord=="D" || $PreWord=="G")
                                  {
                                    $BoxSize=21;  
                                  }
                                  else{
                                    $BoxSize=22;   
                                  }
                              }
                              else{
			         $BoxSize=24;
                              }
			  }

$barcode39=$barcode39==""?"Ash01":$barcode39;
$barcode14=$barcode14==""?$BoxCode1:$barcode14;
if ($eCode=='FINECGALA716T') $barcode14='48018080260320';

   echo "<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='286' cellSpacing=0 cellPadding=0 width=580 border=0>
     <TR height='14'> 
         <TD class='td_Topleft' width='14'></TD>
           <TD class='td_Hortop' rowspan='2' align='center' width='552'>
            <div  style='text-align:left;padding-top:6px;'>
			   <span  class='eCodetext' style='font-size:32pt;'>$eCode</span>
			 </div>
          </TD>
          <TD class='td_Topright' width='14'></TD>
     </TR>
     <TR height='46'>
       <TD class='td_Verleft'  width='14'></TD>
	   <TD class='td_Verright'  width='14'></TD>
     </TR>  
     <TR height='212'>
          <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
		  <TD  class='td_botline1' width='552'>
         <div>
            <div class='div_cl1' >
		            <TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' cellSpacing=0 cellPadding=0  border=0>
				      <TR><TD align='left' valign='middle' colspan='2'  height='30'><span class='span_cel1'>$EndPlace</span></TD></TR>
                       <TR><TD align='left' valign='top' width='35%' height='30'><span class='Font_val'>$Udate</span><span class='Font_date'>$mDate</span> 
						   <span class='Font_val'>$sDate</span></TD><TD align='left' valign='top' width='65%'><span class='Font_title'>&nbsp;invoice</span><span class='Font_val'>&nbsp;&nbsp;$InvoiceNO</span></TD></TR>
				  </TABLE>
          </div>
            <div class='div_cl2' ><div calss='div_boxTable' style='margin-left:10px; max-width:100%; height:100%; '><img  src='../plugins/barcodegen39/barcode39.php?text=$barcode39'  style='margin-left:10px; margin-top:10px; min-height:90%;'></div></div>
        </div>
         <div>
            <div class='div_cl3' >
		            <TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' cellSpacing=0 cellPadding=0  border=0>
				      <TR><TD align='left' valign='top' ><span >BOX </span><span style='font-size:28px;margin-left:10px;'>$PreWord$i</span><span style='margin-left:5px;'>&nbsp;OF </span><span style='font-size:28px;margin-left:10px;'>$PreWord$BoxTotal</span></TD></TR>
                       <TR><TD align='left' valign='top'  ><span >Qty</span><span style='font-size:28px;margin-left:10px;'>$BoxPcs</span></TD></TR>
				  </TABLE>
            </div>
            <div class='div_cl4' ><div calss='div_boxTable' style='margin-top:10px'><img  src='../model/ean_13code.php?Code=$BoxCode1&lw=2&hi=50'  style='margin-left:10px;'></div></div>
        </div>
         <div>
            <div class='div_cl5'>
		            <TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' cellSpacing=0 cellPadding=0  border=0>
                       <TR><TD align='left' valign='bottom' width='50%' height='28'><span class='Font_title'>GW:</span><span class='Font_val'  style='font-size:18px'>$WG</span>&nbsp;<span class='Font_title'>KG</span></TD><TD align='left' valign='bottom' width='50%'><span class='Font_title' style='margin-left:5px;'>&nbsp;NW:</span><span class='Font_val' style='font-size:18px'>$NG</span>&nbsp;<span class='Font_title'>KG</span></TD></TR>
                       <TR><TD align='left' valign='middle' colspan='2' height='28'> <span class='Font_title'>PO NO:</span><span class='Font_val'>&nbsp;&nbsp;$OrderPO</span></TD></TR>
                       <TR><TD align='left' valign='middle' colspan='2' height='16'><span class='Font_val'>MADE IN CHINA</span></TD></TR>
				  </TABLE>
               </div>
            <div class='div_cl6'><div calss='div_boxTable' style='margin-top:10px'><img   src='../plugins/barcodegen/ITF_14Code.php?Code=$barcode14&lw=2&hi=40'    style=' height:70px; '></div></div>
        </div>
          </TD>
          <TD  class='td_Verright' width='14'></TD> 
     </TR>
      <TR height='14'> 
          <TD class='td_Bottomleft' width='14'></TD>
          <TD class='td_Horbottom' width='562'></TD>
          <TD class='td_Bottomright' width='14'></TD>
     </TR>
  </TABLE>
  ";  

           
	/*echo "<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='286' cellSpacing=0 cellPadding=0 width=580 border=0>
     <TR height='14'> 
          <TD class='td_Topleft' width='14'></TD>
           <TD class='td_Hortop' rowspan='2' align='center' width='552'>
            <div  style='text-align:center;padding-top:6px;'>
			   <span  class='eCodetext' style='font-size:$eSize pt;'>$eCode</span>
			 </div>
          </TD>
          <TD class='td_Topright' width='14'></TD>
     </TR>
     <TR height='46'>
       <TD class='td_Verleft'  width='14'></TD>
	   <TD class='td_Verright'  width='14'></TD>
     </TR>  
     <TR height='212'>
          <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
		  <TD  class='td_botline' width='552'>
		 <TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' cellSpacing=0 cellPadding=0  width=552 height='38' border=0>
		 <TR>
            <TD align='center' valign='middle'>
		       <div class='div_endPlace6'><span>$EndPlace</span></div>
		  </TD>
		  </TR>
		  </TABLE>
             <TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' cellSpacing=0 cellPadding=0  width=552 height='170' border=0>        
                <TR height='3'>
                     <TD align='center' width='268'></TD>
                     <TD width='8' class='td_right'></TD> 
		     <TD width='45' ></TD>
                     <TD width='70' ></TD>
	             <TD width='30' ></TD>
                     <TD width='55' class='td_right'></TD>
 		     <TD  width='76' rowspan='3' align='right' valign='middle'>
                         <img src='../model/codefun_vert.php?CodeTemp=$BoxPcs' style='margin-left:10px;'> 
                     </TD> 
                      
                </TR>
                          
                  <TR height='30'>
                     <TD  align='center' width='268'>
                       <TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;margin-top:5px;'  height='20'  cellSpacing=0 cellPadding=0  width=268 border=0>
		        <TR>
                        <TD class='td_smallleft6' width='6'></TD> 
                        <TD  align='center' width='256' style='background-color:#000'>
             <span class='Placetext' style='font-size:$cNameSize pt;'>$cName</span>
                        </TD>
                         <TD class='td_smallright6' width='6'></TD>     
                      </TR>
			        </TABLE>
                   </TD>
                   <TD width='8' class='td_right'>&nbsp;</TD> 
		   <TD width='50' align='Left'><span class='Box_title6'>&nbsp;&nbsp;BOX </span></TD>
                   <TD  width='70' align='center'>
                      <div class='div_h30' style='width:$Box_width px;_width:$Box_width px;'>
					       <span style='font-size:$BoxSize pt;margin-left:3px;'>$PreWord$i</span>
					  </div>
                  </TD>
	           <TD  width='30' align='center' style='text-align:center;'><span class='Box_title6'>&nbsp;OF </span></TD>
                   <TD  align='center' style='text-align:center;' width='50' class='td_right'>
				       <div class='div_line'>
				         <span class='Box_textB6'>$PreWord$BoxTotal</span>
					   </div>
                   </TD>
 			
                </TR>
          	  <TR height='102'>
                 <TD  width='268' align='center'  valign='Top'>
                     <TABLE style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0 width='265' height='80' border=0>
                        <TR height='16'>
				          <TD class='td_line' width='60'><span class='Font_title'>&nbsp;Date:</span></TD>
				          <TD class='td_line' width='205'>
						   <span class='Font_val'>$Udate</span><span class='Font_date'>$mDate</span> 
						   <span class='Font_val'>$sDate</span>
						  </TD>
			            </TR>
                          <TR height='16'>
				             <TD class='td_line' width='60'><span class='Font_title'>&nbsp;GW:</span></TD>
				             <TD class='td_line' width='205'>
                                <span class='Font_val'>$WG</span>&nbsp;&nbsp;&nbsp;<span class='Font_title'>KGS</span>
                             </TD>
			              </TR>   
                           <TR height='16'>
				              <TD class='td_line' width='60'><span class='Font_title'>&nbsp;NW:</span></TD>
				              <TD class='td_line' width='205'>
                                    <span class='Font_val'>$NG</span>&nbsp;&nbsp;&nbsp;<span class='Font_title'>KGS</span>
                              </TD>
			              </TR> 
                          </TABLE>
                      <TABLE style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0 width='275' height='46' border=0>
                           <TR height='16'>
				              <TD class='td_line' width='85'><span class='Font_title'>&nbsp;P/O NO:</span></TD>
				              <TD class='td_line' width='180'>
                                    <span class='Font_val'>&nbsp;&nbsp;$OrderPO</span>
                              </TD>
			              </TR>
                           <TR height='16'>
				              <TD class='td_line' width='85'><span class='Font_title'>&nbsp;INVOICE:</span></TD>
				              <TD class='td_line' width='180'>
                                    <span class='Font_val'>&nbsp;&nbsp;$InvoiceNO</span>
                              </TD>
			              </TR>
				 <TR height='2'><TD colspan='2'></TD></TR>
                       </TABLE>   
                 </TD>
                 <TD width='8' class='td_right'>&nbsp;</TD> 
                 <TD  colspan='4'  width='210' align='center' class='td_right'>
                        <div calss='div_boxTable' style='margin-top:10px'>$BoxCodeTable</div>
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
  ";   */
?>