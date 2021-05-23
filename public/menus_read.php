<?php 
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=600;
ChangeWtitle("$SubCompany 系统菜单列表");
$funFrom="menus";
$From=$From==""?"read":$From;
if($level==2){
    $Th_Col="选项|40|序号|40|一级菜单|120|二级菜单ID|60|二级菜单|120|action|180|回呼|180|标章|150|图标|80|图标类型|80|位置|70|绝对位置|70|排序|50|状态|40|更新日期|80|操作|60";
}else{
    $Th_Col="选项|40|序号|40|一级菜单ID|60|一级菜单|120|action|180|回呼|180|标章|150|图标|80|图标类型|80|位置|70|绝对位置|70|排序|50|状态|40|更新日期|80|操作|60";
}
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8";

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
    $level=$level==""?1:$level;
	$levelSTR="level".strval($level); 
	$$levelSTR="selected";
	echo"<select name='level' id='level' onchange='ResetPage(this.name)'>";
		echo"<option value='1' $level1>一级菜单</option>
		           <option value='2'  $level2>二级菜单</option>
	                </select>&nbsp;";
     if($level>0){
                $SearchRows.=" AND A.level=$level";
           }
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataIn.ac_menus A  WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$SharingShow="Y";//显示共享
	do{
		$m=1;
		$Id=$myRow["id"];
        $ModuleId=$myRow["ModuleId"];
		$parent_id=$myRow["parent_id"];
		$typeid=$myRow["typeid"];
		$menuname=$myRow["name"];
		$action=$myRow["action"]==""?"&nbsp;":$myRow["action"];
		$callback=$myRow["callback"]==""?"&nbsp;":$myRow["callback"];
		$badges=$myRow["badges"]==""?"&nbsp;":$myRow["badges"];
		$icon=$myRow["icon"];
		$icon_type=$myRow["icon_type"];
		$row=$myRow["row"];
		$col=$myRow["col"];
        $site =  $row."行".$col."列";
		$order=$myRow["order"];
		$abs=$myRow["abs"];
		$level=$myRow["level"];
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$cSignFrom=$myRow["csign"];
		include"../model/subselect/cSign.php";
		$Locks=$myRow["Locks"];
		if($level==1){
		         $typeRow=mysql_fetch_array(mysql_query("SELECT name FROM $DataIn.ac_menutypes  A  WHERE  id=$typeid",$link_id));
		        $typename =$typeRow["name"];
				$ValueArray=array(
					array(0=>$ModuleId,1=>"align='center'"),
					array(0=>$typename."--".$menuname),
					array(0=>$action),
					array(0=>$callback),
					array(0=>$badges),
					array(0=>$icon,1=>"align='center'"),
					array(0=>$icon_type,1=>"align='center'"),
					array(0=>$site,1=>"align='center'"),
					array(0=>$abs,1=>"align='center'"),
					array(0=>$order,1=>"align='center'"),
					array(0=>$Estate,1=>"align='center'"),
					array(0=>$Date,1=>"align='center'"),
					array(0=>$Operator,1=>"align='center'")
					);
		}else{
		       $parentRow=mysql_fetch_array(mysql_query("SELECT name FROM $DataIn.ac_menus A  WHERE  id=$parent_id",$link_id));
		         $parentname =$parentRow["name"];
				$ValueArray=array(
					array(0=>$parentname,1=>"align='center'"),
					array(0=>$ModuleId,1=>"align='center'"),
					array(0=>$menuname),
					array(0=>$action),
					array(0=>$callback),
					array(0=>$badges),
					array(0=>$icon),
					array(0=>$icon_type,1=>"align='center'"),
					array(0=>$site,1=>"align='center'"),
					array(0=>$abs,1=>"align='center'"),
					array(0=>$order,1=>"align='center'"),
					array(0=>$Estate,1=>"align='center'"),
					array(0=>$Date,1=>"align='center'"),
					array(0=>$Operator,1=>"align='center'")
					);
		   }

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