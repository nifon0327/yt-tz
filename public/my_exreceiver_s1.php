<script language="javascript" type="text/javascript">

function setsearch()
{
	alert("Here");
	document.getElementById('search').disabled="";

}

</script>

<?php 
//步骤1 $DataPublic.my3_exadd  二合一已更新电信---yang 20120801
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|联系人|100|公司名称|300|联系地址|400";
$ColsNumber=16;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$SearchSTR=0;//不需查询功能
//步骤3：
/*
//注意：只有加了 <SCRIPT type=text/javascript>window.name='win_test'  </script><BASE target=_self>
 <form name='form1' enctype='multipart/form-data' method='post' action='' target='win_test'>
 
 window.name='win_test'这是关键，相当于给窗口取个固定名字，强行把重 target='win_test'指到指定窗口
*/
/*
echo "
<script type=text/javascript>window.name='win_test'</script><BASE target=_self>
<form name='form1' enctype='multipart/form-data' method='post' action='' target='win_test'>
<input name='From' type='hidden' id='From'  value='$From'>";
*/
echo "
<script type=text/javascript>window.name='win_test'</script><BASE target=_self>
<form name='form1' enctype='multipart/form-data' method='post' action='my_exreceiver_s1.php' >
<input name='From' type='hidden' id='From'  value='$From'>";

include "../model/subprogram/s1_model_3.php";



 $searchtable="my3_exadd|E|Company|0|0"; // 表名|别名|字段|1  1表示带Estate字段,其它|值无前导百分号，1表示有，其
//关系到lookup.js, readmodel_3.php if ($FromSearch=="FromSearch") { 
echo "	
  Keyword
  <input name='search' type='text' id='search'  autocomplete='off' disabled='disabled' /> 
  <input type='button' name='Submit' value='Seach' onClick='this.form.submit();'>
  <input name='searchtable' type='hidden' id='searchtable' value='$searchtable'>
  <input name='FromSearch' type='hidden' id='FromSearch' value='$FromSearch'>";

/*
echo "	
  Keyword
  <input name='search' type='text' id='search'  autocomplete='off' disabled='disabled' /> 
  <input type='button' name='Submit' value='Seach' onClick='ToNewSearch()'>
  <input name='searchtable' type='hidden' id='searchtable' value='$searchtable'>
  <input name='FromSearch' type='hidden' id='FromSearch' value='$FromSearch'>";
 */
 /* 
echo "
 <script language='javascript' type='text/javascript'>
 	window.oninit=InitQueryCode('search','querydiv'); 
	//window.onload=setsearch();
	//clearDiv();
 </script> ";  //
*/
//if ($search=="")
if($From!="slist"){
	$CencalSstr="";	
}
else
{
	
	//echo "search :$search";
	$Arraysearch=explode("|",$searchtable);
	$TAsName=$Arraysearch[1];
	$TField=$Arraysearch[2];
	$SearchRows=" AND ($TAsName.$TField like '$search%' ) ";
	
	$CompanySTR ="";
	//$CencalSstr="<input name='CencalS' type='checkbox' id='CencalS' value='1' checked onclick='javascript:ToReadPage(\"orderstatus\",\"$Pagination\")'><LABEL for='CencalS'>Return</LABEL>";
}
echo "$CencalSstr";



//步骤4：可选，其它预设选项
//echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//echo "&nbsp;";
//echo $CencalSstr;
//步骤5：
$ActioToS="4";

include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
//echo "search:$search";
if (trim($search)!=""){
	$SearchRows.=" AND  (E.Name like '%$search%' OR E.Company like '%$search%' OR E.Address like '%$search%' )  ";
}
else {
	$Operator=$Login_P_Number;
	$SearchRows.=" AND E.Operator='$Operator' ";
}
$mySql="SELECT E.Id,E.Name,E.Company,E.PayerNo,E.Address,E.ZIP,E.Country,E.Tel,E.Mobile,E.Email
	FROM $DataPublic.my3_exadd E
	WHERE 1 AND E.Estate='1' $sSearch $SearchRows ORDER BY E.Company,E.Name";


	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Company=$myRow["Company"];
		$PayerNo=$myRow["PayerNo"];
		$Address=$myRow["Address"];
		$Address1=preg_replace("/'/","$$",$Address);
		$ZIP=$myRow["ZIP"];
		$Country=$myRow["Country"];
		$Tel=$myRow["Tel"];
		$Mobile=$myRow["Mobile"]==""?"&nbsp;":$myRow["Mobile"];
		$Mail=$myRow["Mail"]==""?"&nbsp;":"<a href='mailto:$myRow[Mail]'><img src='../images/email.gif' alt='$myRow[Mail]' width='18' height='18' border='0'></a>";
		$Locks=1;
		$checkidValue=$Id."^^".$PayerNo."^^".$Name."^^".$Company."^^".$Country."^^".$ZIP."^^".$Address1."^^".$Tel."^^".$Mobile;
		$ValueArray=array(
			array(0=>$Name),
			array(0=>$Company),
			array(0=>$Address)
			);
		include "../model/subprogram/s1_model_6.php";
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
echo"</form>";
?>



<script language="javascript" type="text/javascript">



function ToNewSearch(){
	var searchT=document.getElementById("search").value;
	document.form1.From.value="slist"; 
	document.form1.FromSearch.value="FromSearch"; 	
	//var searchs=searchT.value;
	/*
	"<meta http-equiv=\"Refresh\" content='0;url=../".$tSearchPage."_s1.php?From=slist&tSearchPage=$tSearchPage&fSearchPage=$fSearchPage&SearchNum=$SearchNum&Action=$Action&uType=$uType&Bid=$Bid&Jid=$Jid&Kid=$Kid&Month=$Month'>";
	*/
	document.form1.action="my_exreceiver_s1.php?search="+searchT;
	document.form1.submit();
	//window.location.reload(); 
    
}

document.getElementById('search').disabled="";

</script>