<?php
//电信-zxq 2012-08-01
$Th_Col="序号|40|小组分类|60|小组班长|60|小组<br>编号|40|生产记录|60|人数|40";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$nowInfo="当前:生产记录查询";
$checkDay=$checkDay==""?date("Y-m-d"):$checkDay;


$path = $_SERVER["DOCUMENT_ROOT"];
include_once("$path/public/kqClass/Kq_dailyItem.php");

//步骤5：
echo"<table width='100%' border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#D9D9D9'>
	<td width='560' class='A1010' align='right'><input name='checkDay' type='text' id='checkDay' size='10' maxlength='10' value='$checkDay' onchange='document.form1.submit()'  onFocus='WdatePicker()' readonly />&nbsp;的生产登记记录</td>
	<td height='40px' align='right' class='A1001'><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td>
	</tr></table>";
echo"<table width='100%' border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td class='A1110' width='300' valign='top'>";

echo"<table id='ListGroup' border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";

	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A0101":"A0101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$i=1;
$GroupArray=array("7100","7080","7050");
$Count=count($GroupArray);
$Allsum=0;
for($j=0;$j<$Count;$j++){
           $mySql="SELECT G.GroupId,G.GroupLeader,G.GroupName,G.Estate,M.Name 
                   FROM $DataIn.staffgroup G 
                   LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader 
                   LEFT JOIN $DataPublic.branchdata B ON B.Id=G.BranchId 
                   WHERE 1 AND  B.TypeId=2  AND G.ESTATE=1 AND G.TypeId>0  AND G.TypeId='$GroupArray[$j]' ORDER BY G.GroupId";
          $myResult = mysql_query($mySql." $PageSTR",$link_id);
          if($myRow = mysql_fetch_array($myResult)){
	       $dGroupId=$myRow["GroupId"];
	       $SumNums=0;
	         do{
		          $m=1;
		          $GroupId=$myRow["GroupId"];
		          $GroupName=$myRow["GroupName"];
		          $Name=$myRow["Name"];

		          //检查当天的员工数
		          $checkNums= mysql_query("SELECT * FROM $DataIn.sc1_memberset WHERE Date='$checkDay' AND GroupId='$GroupId'",$link_id);
		          $Nums=@mysql_num_rows($checkNums);
		          $SumNums+=$Nums;
		          $Leader=$myRow["Leader"];
		          echo"<tr align='center' id='Row$i'><td class='A0101' height='25' >$i</td>";
		          echo"<td class='A0101'>$GroupName</td>";
		          echo"<td class='A0101'>$Name</td>";
		          echo"<td class='A0101'>$GroupId</td>";
		          echo"<td class='A0101' onclick='SetAction($i,2,$GroupId,0)'><div class='yellowB'>查看</div></td>";
		          echo"<td class='A0101'>$Nums</td>";
		          echo"</tr>";
		         $i++;
		       }while ($myRow = mysql_fetch_array($myResult));
	         echo"<tr align='center'><td class='A0101' height='25' colspan='4'>小组人数</td>
	                 <td class='A0101' onclick='SetAction($i,2,0,$GroupArray[$j])'><div class='yellowB'>查看</div></td>
	                 <td class='A0101'>$SumNums</td></tr>";
	         }
        $i++;
       $Allsum=$Allsum+$SumNums;
}
  echo"<tr align='center'><td class='A0101' height='25' colspan='4'>总人数</td>
	                 <td class='A0101' onclick='SetAction($i,2,0,0)'><div class='yellowB'>查看</div></td>
	                 <td class='A0101'>$Allsum</td></tr>";
if($i==1)echo"<tr><td colspan='7' align='center' height='30' class='A1011'><div class='redB'>没有小组资料!</div></td></tr>";
echo "</table></td><td class='A1101' id='SheetInfo' align='center' valign='top'>&nbsp;</td></tr></table>";
?>
<input name="TempValue" type="hidden" id="TempValue">
</form>
</body>
</html>
<script>
SetAction(<?php    echo $i?>,2,<?php    echo $dGroupId?>);
function SetAction(theRow,Action,GroupId,TypeId){
	//消除行背景色
	for (var i=1; i<ListGroup.rows.length; i++){   //遍历行
		if(i==theRow){
			ListGroup.rows[i].bgColor="#D9D9D9";
			SheetInfo.bgColor="#D9D9D9";
			}
		else{
			ListGroup.rows[i].bgColor="";
			}
		}
	//动态读取数据
	//GroupId='502';
	var checkDay=document.form1.checkDay.value;
	var url="item3_5_ajax.php?GroupId="+GroupId+"&fromModuleId=120&checkDay="+checkDay+"&TypeId="+TypeId;
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
			var BackData=ajax.responseText;
			SheetInfo.innerHTML=BackData;
			}
		}
　	ajax.send(null);
	}
function Correct(e,Id,OrderQty){
	var NewQty=e.value;
	var OldValue=document.form1.TempValue.value;
	var CheckSTR=fucCheckNUM(NewQty,"");
	if(CheckSTR==0){
		alert("不是合法的数字!");
		e.value=OldValue;
		return false;
		}
	else{
		////////////////////////////////////
		var url="item3_5_updated.php?Id="+Id+"&Qty="+NewQty;
		var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
				if(ajax.responseText=="Y"){
					alert("操作成功!");
					document.form1.submit();
					}
				else{
					alert("操作不成功!");
					e.value=OldValue;
					}
				}
			}
	　	ajax.send(null);
		////////////////////////////////////
		}
	}
</script>