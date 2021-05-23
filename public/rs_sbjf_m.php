<?php  //电信-ZX  2012-08-01
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=15;
$tableMenuS=500;
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 保险(社保,公积金,意外险)缴费待审核列表");
$funFrom="rs_sbjf";
$Th_Col="选项|40|序号|40|所属公司|60|类型|60|员工姓名|60|部门|60|职位|60|入职日期|80|在职时间|70|缴费月份|70|个人缴费|60|公司缴费|60|小计|60|单据|60|结付状态|60|登记日期|100|操作员|80";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,17,15";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过
$sumCols="10,11,12";		//求和列
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
	$SearchRows =" and S.Estate=2";	
if($From!="slist"){
//缴费类型
$TypeResult=mysql_query("SELECT S.TypeId FROM $DataIn.sbpaysheet S WHERE 1 $SearchRows  GROUP BY S.TypeId",$link_id);
if($TypeRow=mysql_fetch_array($TypeResult)){
   echo"<select name='TypeId' id='TypeId' onchange='document.form1.submit()'>";
   do{
            $thisTypeId =$TypeRow["TypeId"];
            switch($thisTypeId){
                    case 1: $TypeName="社保";break;
                    case 2: $TypeName="公积金";break;
                    case 3: $TypeName="意外险";break;
                    case 4: $TypeName="商业险";break;
            }
            $TypeId=$TypeId==""?$thisTypeId:$TypeId;
            if($TypeId==$thisTypeId){
                    echo"<option value='$thisTypeId' selected>$TypeName</option>";
                     $SearchRows.=" AND S.TypeId='$thisTypeId'";
                    }
            else{
                    echo"<option value='$thisTypeId' >$TypeName</option>";
                    }
        }while($TypeRow=mysql_fetch_array($TypeResult));
    }
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.BranchId,S.JobId,S.Number,S.Month,S.mAmount,S.cAmount,S.Locks,S.Date,
S.Operator,S.Estate,S.TypeId,P.Name,P.ComeIn,J.Picture,S.cSign
FROM $DataIn.sbpaysheet S
LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
LEFT JOIN $DataPublic.rs_sbjf_picture  J ON J.Mid=S.Id
WHERE 1 $SearchRows ORDER by S.id  ";
//S.Month DESC,convert(P.Name using gbk) asc
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
			$m=1;
			$Id=$myRow["Id"];
			$Name=$myRow["Name"];			
			$Number=$myRow["Number"];
			$Month =$myRow["Month"];
			$mAmount =$myRow["mAmount"];
			$cAmount =$myRow["cAmount"];
			$Amount=sprintf("%.2f",$mAmount +$cAmount);
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
			$BranchId=$myRow["BranchId"];				
			$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id));
			$Branch=$B_Result["Name"];				
			$JobId=$myRow["JobId"];
			$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
			$Job=$J_Result["Name"];
			$Operator=$myRow["Operator"];
			include "../model/subprogram/staffname.php";
            switch($myRow["TypeId"]){
                    case 1: $TypeName="社保";break;
                    case 2: $TypeName="公积金";break;
                    case 3: $TypeName="意外险";break;
                    case 4: $TypeName="商业险";break;
              }
			$Estate=$myRow["Estate"];
			switch($Estate){
				case "2":
					$Estate="<div align='center' class='yellowB'>请款中</div>";
					break;
				}
         $ComeIn=$myRow["ComeIn"];
		 $ComeInYM=substr($ComeIn,0,7);
		 include "subprogram/staff_model_gl.php";
		
		if ($myRow["TypeId"]==3){
	        $CheckPictureResult = mysql_fetch_array(mysql_query("SELECT Picture FROM $DataIn.rs_casualty_picture  WHERE Mid=$Id",$link_id));
	        $Picture=$CheckPictureResult["Picture"];
	        if($Picture!=""){
		    $Dir=anmaIn("download/Casualty/",$SinkOrder,$motherSTR);
				$Bill=anmaIn($Picture,$SinkOrder,$motherSTR);
				$Picture="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
            }
            else $Picture="&nbsp;";
       }
       else{
	        $Picture=$myRow["Picture"];
	        //echo "Picture:$Picture";
	        if($Picture!=""){
			    $Dir=anmaIn("download/sbjf_List/",$SinkOrder,$motherSTR);
				$Bill=anmaIn($Picture,$SinkOrder,$motherSTR);
				$Picture="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>$Month</span>";
	            }
        }
        $cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign,		1=>"align='center'"),
			array(0=>$TypeName,		1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Branch,	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$ComeIn,		1=>"align='center'"),
			array(0=>$Gl_STR,		1=>"align='center'"),
			array(0=>$Month,	1=>"align='center'"),
			array(0=>$mAmount,	1=>"align='center'"),
			array(0=>$cAmount,	1=>"align='center'"),
			array(0=>$Amount, 	1=>"align='center'"),
			array(0=>$Picture, 	1=>"align='center'"),
			array(0=>$Estate, 	1=>"align='center'"),
			array(0=>$Date, 	1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
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