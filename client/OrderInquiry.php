<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php";
echo"<html>
<head>
<META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<script src='../model/pagefun.js' type=text/javascript></script>
</head>";
?>
<script language="javascript" type="text/javascript">
function ToSearch(){
	var KeyTemp=document.form1.KeyTemp.value;
	var URL="OrderInquiry_ajax.php?KeyWord="+KeyTemp+"";
	var show1=document.getElementById("Results");
	show1.innerHTML="Searching...";
　	var ajax1=InitAjax(); 
　	ajax1.open("GET",URL,true);
	ajax1.onreadystatechange =function(){
	　　if(ajax1.readyState==4 && ajax1.status ==200 && ajax1.responseText!="" ){
	　　　	show1.innerHTML=ajax1.responseText;
			}
		}
	ajax1.send(null);	
	}

//层数，记录行数，+号ID，参数，下层页面
function SandH(divNum,RowId,f,TempId,ToPage){
	var e=eval("HideTable_"+divNum+RowId);
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		e.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		e.myProperty=false;
		if(TempId!=""){			
			var url=""+ToPage+"_ajax.php?TempId="+TempId+"&RowId="+RowId;
		　	var show=eval("HideDiv_"+divNum+RowId);
		　	var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}

</script>
<body>
<form name="form1" method="post" action="">
  Keyword
  <input name="KeyTemp" type="text" id="KeyTemp" size="60">
  <input type="button" name="Submit" value="Seach" onClick="ToSearch()">
  <p>
  <div Id="Results"></div>
</form>
</body>
</html>