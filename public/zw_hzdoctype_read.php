<?php 
//电信-joseph
//代码、数据库共享-zx	
include "../model/modelhead.php";
//$sumCols="5";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=7;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 行政资料分类列表");
$funFrom="zw_hzdoctype";
$nowWebPage=$funFrom."_read";
//$Th_Col="选项|50|序号|50|一级分类名称|140|二级分类名称|140|备注|300|状态|50|更新日期|80|操作|60";
$Th_Col="选项|50|序号|50|分类名称|140|备注|300|状态|50|排序|60|更新日期|80|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

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
$mySql="SELECT A.Id,A.Name,A.SubName,A.Remark,A.Estate,A.Locks,A.Date,A.Operator,A.SortId 
FROM $DataPublic.zw2_hzdoctype A 
WHERE 1 $SearchRows ORDER BY A.SortId ASC,A.Name";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		//$SubName=$myRow["SubName"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
        $SortId=$myRow["SortId"]==999?"":$myRow["SortId"];
        $Sortstr="<input type='text' id='sortId' name='sortId' size='6' value='$SortId' onblur='TypeSort(this,$Id,1)'>";
       // $Sortclick="onblur='TypeSort(this)'";
		$ValueArray=array(
			array(0=>$Name,1=>"align='Left'"),
			//array(0=>$SubName,1=>"align='Left'"),
			array(0=>$Remark,3=>"..."),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Sortstr,1=>"align='center'"),
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
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
function TypeSort(e,Id,Action){
     var sortId=e.value;
     var url="zw_hzdocsort_ajax.php?Id="+Id+"&Action="+Action+"&sortId="+sortId;
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
</script>