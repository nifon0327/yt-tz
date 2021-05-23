<?php 
//电信-zxq 2012-08-01
//$DataPublic.cardata / $DataPublic.cartype 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 标签打印机管理列表");
$funFrom="printer";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|30|楼层|60|设备编号|100|标签打印机名称|200|IP|100|端口号|50|状态|30";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6";

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";					  
    echo"<select name='WorkAdd' id='WorkAdd' onchange='document.form1.submit()'>";
	$camResult = mysql_query("SELECT DISTINCT C.WorkAdd,W.Name  
	    FROM  $DataPublic.ot5_printer C 
	    LEFT JOIN $DataPublic.staffworkadd W ON C.WorkAdd=W.Id ",$link_id);
	echo"<option value=''>全部</option>";
	if($camRow = mysql_fetch_array($camResult)){
	   do{
	         $tmpWorkAdd=$camRow["WorkAdd"];
	         $WrokAddName=$camRow["Name"];
	       if($WorkAdd==$tmpWorkAdd){
		   echo"<option value='$tmpWorkAdd' selected>$WrokAddName</option>";
	         $SearchRows.=" and C.WorkAdd='$tmpWorkAdd'";
				}
			else{
				echo"<option value='$tmpWorkAdd'>$WrokAddName</option>";					
				}
	
		}while ($camRow = mysql_fetch_array($camResult));
		}
    echo "</select>&nbsp;";
}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT C.*,W.Name AS WorkAddName 
    FROM $DataPublic.ot5_printer C 
    LEFT JOIN $DataPublic.staffworkadd W ON C.WorkAdd=W.Id
   WHERE 1 $SearchRows ORDER BY C.WorkAdd";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	  do{
	    $m=1;
		$Id=$myRow["Id"];
		$Floor=$myRow["WorkAddName"] . "-" . $myRow["Floor"];
		
		$Name=$myRow["Name"];
		$IP=$myRow["IP"]==""?"&nbsp;":$myRow["IP"];
		$Port=$myRow["Port"]==""?"&nbsp;":$myRow["Port"];
		$Identifier=$myRow["Identifier"];
		
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:
				$Estate="<div class='redB'>×</div>";
				break;
			case 1:
				$Estate="<div class='greenB'>√</div>";
				break;
		}
		$ValueArray=array(
			array(0=>$Floor,1=>"align='center'"),
			array(0=>$Identifier,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$IP,1=>"align='center'"),
			array(0=>$Port,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'")
			);
		
		   $checkidValue=$Id;
	      include "../model/subprogram/read_model_6.php";
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