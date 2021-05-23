<?php 
include "../model/modelhead.php";
$ColsNumber=7;				
$tableMenuS=600;
$From=$From==""?"read":$From;
$funFrom="zw_hzdoc";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
switch($TypeId){
      case "33":
        include "zw_hzdoc_read2.php";
        break;
      default:
        include "zw_hzdoc_read1.php";
      break;
}

List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script language="JavaScript" type="text/JavaScript">
function TypeSort(e,Id,Action,DB){
     var sortId=e.value;
     var url="zw_hzdocsort_ajax.php?Id="+Id+"&Action="+Action+"&sortId="+sortId+"&DB="+DB;
	 var ajax=InitAjax();
　  ajax.open("GET",url,true);
	 ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
			var BackData=ajax.responseText;
			       //更新该单元格底色和内容
             if(BackData=="Y"){
                      e.value=sortId;
                     }
              else{
                      e.value="";
                     }
			}
		}
　	ajax.send(null);
}

function zhtj(obj){

	document.form1.action="zw_hzdoc_read.php";
	document.form1.submit();
}	
function Showproxy(e,f,Order_Rows,proxyId,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(proxyId!=""){			
			var url="../public/yw_clientproxy_ajax.php?proxyId="+proxyId+"&RowId="+RowId; 
		　	var show=eval("showproxyTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			    ajax.onreadystatechange=function(){
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