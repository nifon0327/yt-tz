<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=600;
$sumCols="7";		//求和列
ChangeWtitle("$SubCompany 员工体检费审核");
$funFrom="rs_stafftj";
$nowWebPage=$funFrom."_m";
$Th_Col="选项|40|序号|40|体检类型|60|员工姓名|60|部门|60|职位|60|入职日期|70|金额|60|结付|80|备注|200|凭证|30|合格与否|50|登记日期|100|操作员|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,17,15";	
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT Month FROM $DataIn.cw17_tjsheet WHERE 1 AND Estate=2 GROUP BY Month order by Month DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows=" and S.Month='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	$B_Result=mysql_query("SELECT B.Id,B.Name FROM $DataIn.cw17_tjsheet S 
	      LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
          LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
          WHERE 1 AND S.Estate=2 $SearchRows  GROUP BY B.Id",$link_id);
	if($B_Row = mysql_fetch_array($B_Result)) {
	     echo"<select name='BranchId' id='BranchId' onchange='document.form1.submit()'>";
		echo "<option value='' selected>全部</option>";
		do{
			$B_Id=$B_Row["Id"];
			$B_Name=$B_Row["Name"];
			if($BranchId==$B_Id){
				echo "<option value='$B_Id' selected>$B_Name</option>";
				$SearchRows.=" AND P.BranchId='$BranchId'";
				}
			else{
				echo "<option value='$B_Id'>$B_Name</option>";
				}
			}while ($B_Row = mysql_fetch_array($B_Result));
		}
	echo"</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
	S.Id,S.Number,S.Month,S.Amount,S.Locks,S.Date,S.Operator,S.Estate,S.Mid,P.Name,B.Name AS BranchName,J.Name AS JobName,S.Remark,S.Attached ,S.tjType,P.ComeIn,S.CheckT,S.HG
	 FROM $DataIn.cw17_tjsheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
    LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	WHERE 1 AND S.Estate=2 $SearchRows ";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$GDnumber= array("①","①","②","③","④","⑤","⑥","⑦","⑧","⑨","⑩");
	do{
			$m=1;
			$Id=$myRow["Id"];			
			$Number=$myRow["Number"];		
			$Name=$myRow["Name"];
			$Month =$myRow["Month"];
			$Amount =$myRow["Amount"];
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
			$BranchName=$myRow["BranchName"];				
			$JobName=$myRow["JobName"];
			$Mid=$myRow["Mid"];
            $ComeIn=$myRow["ComeIn"];
			$Operator=$myRow["Operator"];
			include "../model/subprogram/staffname.php";
			$Estate="<span class='yellowB'>请款中</span>";
            $Remark=$myRow["Remark"];
            $tjType=$myRow["tjType"];
            $CheckT=$myRow["CheckT"];
            $CheckTime=$GDnumber[$CheckT];
            switch($tjType){
                case "1":  $tjType="岗前体检".$CheckTime;  break;
                case "2":  $tjType="岗中体检".$CheckTime;  break;
                case "3":  $tjType="离职体检".$CheckTime;  break;
                }
            $Attached=$myRow["Attached"];
        if($Attached!="" && $Attached!=0){
		     $f1=anmaIn($Attached,$SinkOrder,$motherSTR);
		     $d1=anmaIn("download/tjfile/",$SinkOrder,$motherSTR);		
             $Attached="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
          }
           $HG=$myRow["HG"];
            switch($HG){
                 case 1: 
                      $HG="<span class='greenB'>合格</span>";
                       break;
                 case 0:
                      $HG="<span class='redB'>不合格</span>";
                       break;
               }
		$ValueArray=array(
			array(0=>$tjType,		1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$BranchName, 	1=>"align='center'"),
			array(0=>$JobName,	1=>"align='center'"),
			array(0=>$ComeIn,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Estate, 	1=>"align='center'"),
			array(0=>$Remark,	1=>"align='left'"),
			array(0=>$Attached, 	1=>"align='center'"),
			array(0=>$HG, 	1=>"align='center'"),
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