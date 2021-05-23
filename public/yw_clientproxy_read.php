<?php 
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 客户授权书");
$funFrom="yw_clientproxy";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|40|客户|120|授权文件名称|400|产品数量|80|授权截止|90|档案|80|上传者|60|状态|40";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,82";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.Caption,D.TimeLimit,D.Attached,D.Estate,D.Locks,D.Operator,C.Forshort 
FROM $DataIn.yw7_clientproxy D
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId 
WHERE 1 $SearchRows ORDER BY D.Estate DESC,C.Forshort,D.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Forshort=$myRow["Forshort"];
		$Caption=$myRow["Caption"];
		$TimeLimit=$myRow["TimeLimit"];
         if($TimeLimit=="0000-00-00"){
                  $TimeLimit="<div class='greenB'>无期限</div>";
             }
	       else{
	        	       $TimeLimit=$TimeLimit>date("Y-m-d")?"<div class='greenB'>$TimeLimit</div>":"<div class='redB'>$TimeLimit</div>";
                  }	
		$Attached=$myRow["Attached"];
		$d=anmaIn("download/clientproxy/",$SinkOrder,$motherSTR);		
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			//$Attached="<span onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;color:#FF6633'>View</span>";
			$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
			}
		else{
			$Attached="-";
			}
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
        $proxyResult = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS  proCount FROM $DataIn.yw7_clientproduct A
                               LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
                               LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
                              WHERE A.cId='$Id'",$link_id));
        $proCount=$proxyResult ["proCount"]==0?"&nbsp;":$proxyResult ["proCount"];
//动态读取
		if($proCount>0){$showPurchaseorder="<img onClick='Showproxy(proxyList$i,showtable$i,proxyList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示此客户的授权产品.' width='13' height='13' style='CURSOR: pointer'>";}
        else $showPurchaseorder="";
		$proxyTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='proxyList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showproxyTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$Forshort),
			array(0=>$Caption),
			array(0=>$proCount, 1=>"align='center'"),
			array(0=>$TimeLimit,1=>"align='center'"),
			array(0=>$Attached, 1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'"),
			array(0=>$Estate,   1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
        echo $proxyTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
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
