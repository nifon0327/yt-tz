<?php 
//电信-zxq 2012-08-01
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|50|序号|50|Product Name|400|Image|50|状态|50|更新日期|80|操作员|60";
$ColsNumber=17;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
//非必选,过滤条件
$Parameter.=",Bid,$Bid";
//if($From!=slist){$CompanyIdSTR=" AND CompanyId=$Bid";}
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT * FROM $DataIn.muvit_productdata WHERE 1 $sSearch AND Id NOT IN (SELECT Pid FROM $DataIn.muvit_pands GROUP BY Pid ORDER BY Pid) AND Estate=1 ORDER BY Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Name=$myRow["Name"];
		$Bdata=$Id."^^".$Name;
		$Img=$myRow["Img"];
		$Dir=anmaIn("download/muvitimg/",$SinkOrder,$motherSTR);
		if($Img==1){
			$Img="P".$Id.".jpg";
			$Img=anmaIn($Img,$SinkOrder,$motherSTR);
			$Img="<span onClick='OpenOrLoad(\"$Dir\",\"$Img\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Img="-";
			}
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		//操作员姓名
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Name),
			array(0=>$Img,		1=>"align='center'",	2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";		}while ($myRow = mysql_fetch_array($myResult));
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