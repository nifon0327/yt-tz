<?php 
//ewen 2013-03-20 OK
include "../model/modelhead.php";
echo "<script src='../model/palette.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增工作地点");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<style type="text/css">
<!--
a.g:link {
	text-decoration: none;
	color: #0000FF;
	font-size: 13px;
}
a.g:visited {
	text-decoration: none;
	color: #0000FF;
	font-size: 13px;
}
a.g:hover {
	text-decoration: none;
	color: #FF0000;
	font-size: 13px;
}

.gray{color:#666666}
.f12{font-size:12px}
.box{padding:2px;border:1px solid #CCC}
-->
</style>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="left" cellspacing="5">
		<tr>
            <td width="120" height="40" align="right" scope="col">地点名称</td>
            <td scope="col" ><input name="Name" type="text" id="Name" style="width:380px;" maxlength="20" title="可输入2-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="2" Msg="没有填写或字符不在2-20个字节内"></td>
		</tr>
        <tr>
            <td width="120" height="40" align="right" scope="col">排序</td>
            <td scope="col" ><input name="ExtNoFirst" type="text" id="ExtNoFirst" style="width:380px;" DataType="Number" Msg="没有填写或格式不对"></td>
		</tr>
        <tr>
            <td width="120" height="40" align="right" valign="top" scope="col">地址</td>
            <td scope="col" ><textarea name="Address" rows="4" id="Address" style="width:380px;" tdatatype="Require" msg="没有填写"></textarea></td>
		</tr>
      </table>
</td></tr></table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
    	<table border="0" cellpadding="0" cellspacing="0" class="colTab">
            <tr align="left" valign="top">
            <td width="120" >&nbsp;</td>
                <td>
                    	<table border="0" cellspacing="0" cellpadding="0">
                        	<tr>
                            	<td>
                           		 	颜色：
                                    <TABLE ID=ColorTable BORDER=0 CELLSPACING=2 CELLPADDING=0 style='cursor:pointer'>
                                    <SCRIPT LANGUAGE=JavaScript>
                                        function wc(r, g, b, n){
                                            r = ((r * 16 + r) * 3 * (15 - n) + 0x80 * n) / 15;
                                            g = ((g * 16 + g) * 3 * (15 - n) + 0x80 * n) / 15;
                                            b = ((b * 16 + b) * 3 * (15 - n) + 0x80 * n) / 15;
                                            document.write('<TD BGCOLOR=#' + ToHex(r) + ToHex(g) + ToHex(b) + ' height=8 width=12 onmouseover="ctOver(this)" onmouseout="ctOut(this)" onmousedown="ctClick(this)"></TD>');
                                            }
                                        var cnum = new Array(1, 0, 0, 1, 1, 0, 0, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1, 1, 0, 0);
                                        for(i = 0; i < 16; i ++){
                                            document.write('<TR>');
                                            for(j = 0; j < 30; j ++){
                                                n1 = j % 5;
                                                n2 = Math.floor(j / 5) * 3;
                                                n3 = n2 + 3;
                                                wc((cnum[n3] * n1 + cnum[n2] * (5 - n1)),
                                                (cnum[n3 + 1] * n1 + cnum[n2 + 1] * (5 - n1)),
                                                (cnum[n3 + 2] * n1 + cnum[n2 + 2] * (5 - n1)), i);
                                                }
                                        document.writeln('</TR>');
                                        }
                                    </script>
                                    </TABLE>
                        		</td>
                			</tr>
            			</table>
    			</td>
        		<td width=60 valign="top" align="center">
       	 			亮度
                                    <TABLE ID=GrayTable BORDER=0 CELLSPACING=0 CELLPADDING=0 style='cursor:pointer'>
                                    <SCRIPT LANGUAGE=JavaScript>
                                        for(i = 255; i >= 0; i -= 8.5) {
                                            document.write('<TR BGCOLOR=#' + ToHex(i) + ToHex(i) + ToHex(i) + '><TD TITLE=' + Math.floor(i * 16 / 17) + ' height=5 width=20 onmouseover="gtOver(this)" onmouseout="gtOut()" onmousedown="gtClick(this)"></TD></TR>');
                                            }
                                    </script>	
                                    </TABLE>
    			</td>
    			<td width="60" valign="top">
    				选中颜色：
        			<table ID=ShowColor width="50" height="24" cellspacing="0" cellpadding="0"><tr><td></td></tr></table>
    			</td>
    			<td valign="top">&nbsp;
					颜色代码：<br> 
    				<INPUT TYPE=TEXT class="colInp" name="SelColor" ID="SelColor" value="#FFFFFF" SIZE=7 onKeyUp="inpCol(this)">
				</td>
             </tr>
    	</table><script>EndColor();</script>
 	</td></tr>
 </table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>

