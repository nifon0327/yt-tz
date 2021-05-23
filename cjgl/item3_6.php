<?php
//电信-zxq 2012-08-01
echo"<div style='width: 100%;'>
<table align='center' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;'>
	<tr style='background-color: #F0F5F8'>
	<td height='40px' width='200px' class='A1110'>$ClientList</td>
	<td width='300px' class='A1100'>&nbsp;</td>
	<td align='right' class='A1101'><input name='NowInfo' type='text' id='NowInfo' value='当前:登陆资料更新' class='text' disabled></td></tr>";
?>
  <tr>
    <td height="30px" align="right" class="A0110">原登陆帐号：</td>
    <td class="A0100"><input name="oUser" type="text" id="oUser" size="30" maxlength="16"></td>
  <td class="A0101">&nbsp;</td>
  </tr>
  <tr>
    <td  height="30px" align="right" class="A0110">原登陆密码：</td>
    <td class="A0100"><input name="oPsw" type="password" id="oPsw" size="30" maxlength="16"></td>
  <td class="A0101">&nbsp;</td>
  </tr>
  <tr>
    <td height="30px" align="right" class="A0110">新登录帐号：</td>
    <td class="A0100"><input name="nUser" type="text" id="nUser" size="30" maxlength="16"></td>
  <td class="A0101">&nbsp;</td>
  </tr>
  <tr>
    <td height="30px" align="right" class="A0110">新登陆密码：</td>
    <td class="A0100"><input name="nPsw" type="password" id="nPsw" size="30" maxlength="16"></td>
	<td class="A0101">&nbsp;</td>
  </tr>
<tr>
    <td height="30px" align="right" class="A0110">新登陆密码：</td>
    <td class="A0100"><input name="nPsws" type="password" id="nPsws" size="30" maxlength="16"></td>
    <td class="A0101">&nbsp;</td>
</tr>
  <tr>
    <td height="30px" class="A0110">&nbsp;</td>
    <td class="A0100" align="right"><input class='ButtonH_25' type="button" name="Submit" value="更新" onclick="UpdatedMyInfo();"></td>
	 <td class="A0101">&nbsp;</td>
  </tr>
</table>
</div>
</BODY>
</HTML>
<script>
function UpdatedMyInfo(){
	//处理参数值
	var oUser=document.getElementById("oUser").value;
	var oPsw=document.getElementById("oPsw").value;
	var nUser=document.getElementById("nUser").value;
	var nPsw=document.getElementById("nPsw").value;
    var nPsws=document.getElementById("nPsws").value;
	var url="item3_6_updated.php?oUser="+oUser+"&oPsw="+oPsw+"&nUser="+nUser+"&nPsw="+nPsw;
	var ajax=InitAjax();
    if(oUser=="" || oPsw=="" ){
        alert("请输入原始登录帐号或密码!");
        return;
    }else if (nUser == "" || nPsw == "" || nPsws == "") {
        alert("请输入新的登录帐号或密码!");
        return;
    }
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
			if(ajax.responseText=="Y"){
				//退出？ 注消
				alert("已更新,请重新登陆!");
				parent.location.href="/cjgl";
				}
			else{
				alert("更新失败!");
				}
			}
		}
　	ajax.send(null);
	}
</script>