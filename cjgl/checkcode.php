<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">    
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="0"> 
<title>(扫描)条码检验</title>
<style type="text/css">
<!--
.t1{color:#555555;margin-left:2px;font-size:15px;font-weight: bold;}
-->
</style>
</head>
<body style="overflow-x:hidden;overflow-y:hidden" oncontextmenu="event.returnValue=false" onhelp="return false;" oncut = "return false" oncopy = "return false" onpaste = "return false" onselectstart="return false">
<table  border='0' cellspacing='0' style='width:98%;TABLE-LAYOUT: fixed; WORD-WRAP: break-word;text-align:center;'>
    <tr bgcolor="#66CCFF">
        <td colspan="2" height="30"  align="center" style="font-size:18px;font-weight: bold;">条码扫描检测</td>
    </tr>
    <tr bgcolor="#CCFFFF">
        <td height="30" align="center"><input name="ValidityId" type="text" id="ValidityId" size="16" tabindex="1"><input name="CodeLen" type="hidden" id="CodeLen"></td>
        <td><input name="okBtn" id="okBtn" type="button" value="设定检验值" onclick="setValidityId(this)" tabindex="2"/></td>
    </tr>
    
    <tr bgcolor="#CCFFFF">
        <td height="30" align="center"><input name="TempId" type="text" id="TempId" size="16" onpropertychange="inputChange()" onblur="getFocus(this)" tabindex="3"></td>
        <td  align="left"><b class="t1">正确率:</b><span name="checkPre" id="checkPre"></span></td>
    </tr>
    <tr bgcolor="#CCFFFF">
        <td height="30" align="left"><b class="t1">已检数:</b><span name="checkNum" id="checkNum">0</span></td>
        <td align="left"><b class="t1">错误数:</b><span name="errorNum" id="errorNum" style="color:#FF0000;">0</span></td>
    </tr>
</table>
<embed id="soundControl" src="../sound/BEEP.WAV" mastersound hidden="true" loop="false" autostart="false"></embed>
<div id="content" name="content" class="content" style="width:98%;height:98%;overflow:hidden;margin-top:5px;font-size:12px;"></div>

</body>
</html>

<!-- 以下是javascript代码 -->
<script language="JavaScript">
      var validityLen=13;
      var validitySTR="";
      var checkError=0;
      var checkSum=0;
      document.getElementById("TempId").readOnly=true;
      document.getElementById("ValidityId").focus();
      
      function inputChange(){
          var tempSTR=document.getElementById("TempId").value;
          
          if (tempSTR.length==validityLen) {
               checkSum+=1;
               document.getElementById("checkNum").innerHTML=checkSum;
               
                var content=document.getElementById("content");
                if (tempSTR!=validitySTR){
                    //alert(tempSTR+"|"+validitySTR);
                    tempSTR="<div style='color:#FF0000;'>"+tempSTR+"</div>";
                    content.innerHTML=tempSTR+content.innerHTML;
                    checkError+=1;
                    document.getElementById("errorNum").innerHTML=checkError;
                    playSound("play");
                }
                else{
                   content.innerHTML=tempSTR+"</br>"+content.innerHTML+"</br>"; 
                }

                document.getElementById("TempId").value=""; 
                document.getElementById("TempId").focus();
                
                var checkPre=Math.round(((checkSum-checkError)/checkSum)*10000)/100;
                document.getElementById("checkPre").innerHTML=checkPre +"%";
         }
      }
      
      function getFocus(e){
          if (validitySTR!=""){
               e.focus();
          } 
      }
      
      function setValidityId(e)
      {
          var ValidityId=document.getElementById("ValidityId");
          var TempId=document.getElementById("TempId");
          
          if (ValidityId.readOnly==true){
              e.value="设定检验值";
              validitySTR="";
              validityLen=13;
              ValidityId.readOnly=false;
              TempId.readOnly=true;
              ValidityId.value="";
              ValidityId.focus(); 
              document.getElementById("content").innerHTML="";
              return;
          }
          else{
                validitySTR=ValidityId.value; 
                validityLen=validitySTR.length;
                if (validityLen>0){
                    e.value="重新设定";
                    checkError=0;
                    checkSum=0;
                    ValidityId.readOnly=true;
                    TempId.readOnly=false;
                    TempId.focus();
                    document.getElementById("TempId").focus();
                    document.getElementById("checkNum").innerHTML="0";
                    document.getElementById("errorNum").innerHTML="0";
                    document.getElementById("checkPre").innerHTML="";
                }
                else{
                    ValidityId.focus(); 
                }
          }
      }
      
     function playSound(action){
          var soundControl = document.getElementById("soundControl");
            if(action == "play")
          {
                soundControl.play();
            }
            if(action == "stop")
            {
                soundControl.stop();
            }
}
</script>
