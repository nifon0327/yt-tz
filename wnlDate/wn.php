<HTML>
<HEAD>
<TITLE>日历表</TITLE>
<META http-equiv=Content-Type content="text/html; charset="utf-8">
<style type="text/css">

p {fONT-FAMILY: 思源黑体; FONT-SIZE: 9pt;line-height:12pt;color:#000000}

TD {fONT-FAMILY: 思源黑体,simsun; FONT-SIZE: 9pt}
    
a:link{ color:#000000; text-decoration:none}     
a:visited{COLOR: #000000; TEXT-DECORATION: none} 
a:active{color:green;text-decoration:none}
a:hover{color:red;text-decoration:underline}  
 </style>
<script language='javascript' type='text/javascript' src='wnlDate.js'></script>
<STYLE>.todyaColor {
    fONT-FAMILY: Arial Black;
	 BACKGROUND-COLOR: silver
}
.changeColor{
    BACKGROUND-COLOR: royalblue
}
.changeWorkColor{
    BACKGROUND-COLOR: limegreen
}
.addWorkColor{
    BACKGROUND-COLOR: yellow
}
.changeColorA{
    fONT-FAMILY: Arial Black;
	FONT-SIZE:25pt;
    BACKGROUND-COLOR: royalblue
}
.changeWorkColorA{
    fONT-FAMILY: Arial Black;
	FONT-SIZE:25pt;
    BACKGROUND-COLOR: limegreen
}
.addWorkColorA{
    fONT-FAMILY: Arial Black;
	FONT-SIZE:25pt;
    BACKGROUND-COLOR: yellow
}
</STYLE>
<META content="MSHTML 6.00.2800.1505" name=GENERATOR>
<script language=JavaScript>

lck=0;
function r(hval)
{
if ( lck == 0 )
{
document.bgColor=hval;
}
}
</script>

</HEAD>
<BODY leftMargin=0 topMargin=15 onload=initial()>
<DIV id=detail style="POSITION: absolute"></DIV>
<FORM name=CLD>
	<br/>
	<P align="center">
		  <FONT id=Clock face=Arial color=#000080 size=8 align="center"></FONT>
      <input name="TZ" id="TZ" value="+0800 北京、重庆、黑龙江" type="hidden" size="8">
     <!-- <DIV style="Z-INDEX: -1; POSITION: absolute; TOP: 70px">
      	<FONT id=YMBG style="FONT-SIZE: 80pt; COLOR: #f0f0f0; FONT-FAMILY: '黑体'">&nbsp;0000<BR>&nbsp;JUN</FONT> 
      </DIV> -->
  </P>
    <input name="JDATE" id="JDATE" value="" type="hidden" size="8"> 
<TABLE border=2>
  <TBODY>
    <TR>
      <TD vAlign=top width="520" align=middle  rowspan="2" style="background:lightblue;">
   <div id="news"><iframe src="../wnlDate/wn_readnews.php"  name="wnnewsFrm"  frameborder="0" width="100%" height="420" margwidth="0" margheight="0" scrolling="no" allowTransparency="true"></iframe>	</div>
 </TD>
     <!-- <img src="images/wnlnew.gif">--> 
     <TD width="460" height="358" align=center style="background:lightblue;">
      <TABLE border=0 style="background:#FFF;">
        <TBODY>
        <TR>
          <TD height=30 colSpan=7 align=center style="background:#03C;border=2;"><FONT style="FONT-SIZE: 12pt;" 
            color=#ffffff>公历&nbsp;<SELECT style="FONT-SIZE: 12 pt" 
            onchange=changeCld() name=SY id='SY'> 
              <SCRIPT language=JavaScript><!--
            for(i=1900;i<2050;i++) document.write('<option>'+i)
            //--></script>
            </SELECT>&nbsp;年&nbsp;<SELECT id=SM style="FONT-SIZE: 12pt" onchange=changeCld() 
            name=SM> 
              <SCRIPT language=JavaScript><!--
            for(i=1;i<13;i++) document.write('<option>'+i)
            //--></script>
            </SELECT>&nbsp;月&nbsp;</FONT>
            <span style='display:none;' name=GZ id=GZ type="hidden"  value=""></span><BR></TD></TR>
        <TR align=middle bgColor=#e0e0e0>
          <TD width=54><FONT color=red>日</FONT></TD>
          <TD width=54>一</TD>
          <TD width=54>二</TD>
          <TD width=54>三</TD>
          <TD width=54>四</TD>
          <TD width=54>五</TD>
          <TD width=54><FONT color=green>六</FONT></TD></TR>
        <SCRIPT language=JavaScript><!--
            var gNum
            for(i=0;i<6;i++) {
               document.write('<tr align=center>')
               for(j=0;j<7;j++) {
                  gNum = i*7+j
                  document.write('<td id="GD' + gNum +'" onMouseOver="mOvr(' + gNum +')" onMouseOut="mOut()" style="text-align:center;"><font id="SD' + gNum +'" style="fONT-FAMILY:Arial Black;font-size:26px"')
                  if(j == 0) document.write(' color=red')
                  if(j == 6)
                    document.write(' color=red')        
                  document.write(' TITLE=""> </font><br><font id="LD' + gNum + '" size=2 style="fONT-FAMILY:Arial;font-size:11px"> </font></td>')
               }
               document.write('</tr>')
            }
            //--></script>
        </TBODY></TABLE></TD>
      </TR>
   		<TR align=middle style="background:lightblue;">
		   <TD width="460">
      	   <BUTTON style="FONT-SIZE: 9pt" onClick="pushBtm('MU')">上一月↑</BUTTON>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		   <BUTTON style="FONT-SIZE: 9pt" onClick="pushBtm('')">今 日</BUTTON>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		   <BUTTON style="FONT-SIZE: 9pt" onClick="pushBtm('MD')">下一月↓</BUTTON>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
          </TD>
	    </TR>
		</TBODY></TABLE>
   </FORM>
</BODY></HTML>
