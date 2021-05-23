<HTML>
<HEAD>
<TITLE>日历表</TITLE>
<META http-equiv=Content-Type content="text/html; charset="utf-8">
<script language='javascript' type='text/javascript' src='wnlDate.js'></script>
<META content="MSHTML 6.00.2800.1505" name=GENERATOR>

<style type="text/css">
   body{BACKGROUND-COLOR:#eff0f2;}
   select{FONT-SIZE: 9pt;BACKGROUND-COLOR:#eff0f2;}
   .todyaColor {BACKGROUND-COLOR: slategray;}
   p {fONT-FAMILY: 思源黑体; FONT-SIZE: 9pt;line-height:12pt:color:#000000;}
   TD {fONT-FAMILY: 思源黑体,simsun; FONT-SIZE: 9pt}
   a:link{ color:#000000; text-decoration:none;}     
   a:visited{COLOR: #000000; TEXT-DECORATION: none} 
   a:active{color:green;text-decoration:none}
   a:hover{color:red;text-decoration:underline}
</style>
</HEAD>
<BODY leftMargin=0 topMargin=15 onload=initial()>
<script language=JavaScript>
lck=0;
function r(hval)
{
   if ( lck == 0 ){document.bgColor=hval;}
}
</script>
<DIV id=detail style="POSITION: absolute"></DIV>
<CENTER>
<FORM name=CLD>
	<FONT id=Clock face=Arial color=#000080 size=5 align="center"></FONT> 
      <input name="TZ" id="TZ" value="+0800 北京、重庆、黑龙江" type="hidden" size="8">
  </br>
<TABLE  border=1>
  <TBODY>
          <TD bgColor=slategray colSpan=7 height="40">
		  <FONT color=orange size=3>公历<SELECT  onchange=changeCld() name=SY id='SY'> 
              <SCRIPT language=JavaScript><!--
                      for(i=1900;i<2050;i++) document.write('<option>'+i)
            //--></script>
            </SELECT>年<SELECT  onchange=changeCld()  name=SM id='SM'> 
             <SCRIPT language=JavaScript><!--
                      for(i=1;i<13;i++) document.write('<option>'+i)
            //--></script>
            </SELECT >月 </FONT>
			<b><FONT id=GZ face=标楷体 color=orange size=3></FONT></b><BR></TD></TR>
        <TR align=middle bgColor=#e0e0e0>
          <TD width=54><FONT color=red>日</FONT></TD>
          <TD width=54>一</TD>
          <TD width=54>二</TD>
          <TD width=54>三</TD>
          <TD width=54>四</TD>
          <TD width=54>五</TD>
          <TD width=54><FONT color=red>六</FONT></TD></TR>
        <SCRIPT language=JavaScript><!--
            var gNum
            for(i=0;i<6;i++) {
               document.write('<tr align=center>')
               for(j=0;j<7;j++) {
                  gNum = i*7+j
                document.write('<td id="GD' + gNum +'" onMouseOver="mOvr(' + gNum +')" onMouseOut="mOut()"><font id="SD' + gNum +'" size=5 face="Arial Black"')
                  if(j == 0 || j == 6) document.write(' color=red')     
                  document.write(' TITLE=""> </font><br><font id="LD' + gNum + '" size=2 style="font-size:9pt"> </font></td>')
               }
               document.write('</tr>')
            }
            //--></script>
        </TBODY></TABLE>
   		<TR align=middle><TD colspan="2">&nbsp;</TD>
		   <TD>
		   <p></p>
      	   <BUTTON style="FONT-SIZE: 9pt" onClick="pushBtm('MU')">上一月↑</BUTTON>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		   <BUTTON style="FONT-SIZE: 9pt" onClick="pushBtm('CD')">今 日</BUTTON>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		   <BUTTON style="FONT-SIZE: 9pt" onClick="pushBtm('MD')">下一月↓</BUTTON>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            </TD>
	    </TR>
		</TBODY></TABLE>
   </FORM>
</BODY></HTML>
