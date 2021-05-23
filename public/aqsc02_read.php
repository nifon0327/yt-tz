<?php
//2013-10-14 ewen
include "../model/modelhead.php";
$ColsNumber=7;				
$tableMenuS=600;
$From=$From==""?"read":$From;
$funFrom="aqsc02";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
ChangeWtitle("$SubCompany 安全管理制度文档");
$Th_Col="选项|45|序号|45|文档分类|200|文档标题|400|附件|80|更新日期|80|操作员|60";
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows ="";
	/*/类型
    $TypeResult = mysql_query("SELECT * FROM  $DataPublic.aqsc01 WHERE Estate='1' ORDER BY Id",$link_id);
	if($TypeRow = mysql_fetch_array($TypeResult)) {
		echo"<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
       echo"<option value='' selected>全部</option>";	
		do{			
              $thisTypeId=$TypeRow["Id"];
              $thisName=$TypeRow["Name"];
			  if($TypeId==$thisTypeId){
				     echo"<option value='$thisTypeId' selected>$thisName</option>";
				     $SearchRows.=" and B.Id='$thisTypeId' ";
				     }
			  else{
				      echo"<option value='$thisTypeId'>$thisName</option>";					
				    }
			}while ($TypeRow = mysql_fetch_array($TypeResult));
		echo"</select>&nbsp;";
		}*/
	}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.Caption,A.Attached,A.Date,A.Locks,B.Name AS Type,A.Operator
FROM $DataPublic.aqsc02 A
LEFT JOIN $DataPublic.aqsc01 B ON B.Id=A.TypeId 
WHERE 1 $SearchRows ORDER BY A.TypeId,A.Caption ASC,A.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Caption=$myRow["Caption"];
		$Attached=$myRow["Attached"];
		//echo "$Attached";
		$d=anmaIn("download/aqsc/",$SinkOrder,$motherSTR);		
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$FileType=substr($Attached,-3,3);
			if(strtolower($FileType)=='mp4'){
                $Attached="<a href=\"../m_videos/videoview.php?d=$d&f=$f&Type=mp4&Action=6\" target=\"_blank\">View</a>";
				}
			else{
				$Attached="<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\" style='CURSOR: pointer; color:#FF6633'>View</a>";
				}
			}
		else{
			$Attached="-";
			}
		$Date=$myRow["Date"];
		$Today=date("Y-m-d");
		$Type=$myRow["Type"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Type),
			array(0=>$Caption),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),	
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
  	echo '</div>';
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