<?php 
include "../model/modelhead.php";
//步骤2：//需处理
ChangeWtitle("$SubCompany 添加离职员工补助费用");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理;
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="700" border="0" cellspacing="5" id="NoteTable">
		<tr>
		  	<td  width="180" height="29" align="right" scope="col">补助类型</td>
		  	<td scope="col">
			<select name="TypeId" id="TypeId" style="width:320px" dataType="Require"  msg="未选择补助类型"  onchange="changeType()">
			<option value="" selected>请选择</option>
			<option value="1" >离职补助</option>
			<option value="2" >辞退赔偿金</option>
		  	</select></td>
	    </tr>
     <tr ><td  style="padding-left:190px;display:none;"  id="showRemark"  colspan="2" >&nbsp;</td></tr>
		<tr>
		  <td align="right" scope="col">登记日期</td>
		  <td scope="col"><input name="theDate" type="text" id="theDate" onfocus="WdatePicker()" value="<?php  echo date("Y-m-d");?>" size="45" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">指定员工</td>
		  <td scope="col"><input name="Name" type="text" id="Name" size="45" dataType="Require" Msg="未填写" readonly  onclick="CheckStaffName(this)"><input  type="hidden" id="Number" name="Number"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">离职类型</td>
		  <td scope="col"><input name="TypeName" type="text" id="TypeName" size="45" readonly></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">月均工资</td>
		  <td scope="col"><input name="AveAmount" type="text" id="AveAmount" size="45" dataType="Double" Msg="未填写或格式不对" onblur="changeAmount()"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">补助比例</td>
		  <td scope="col"><input name="TotalRate" type="text" id="TotalRate" size="45" dataType="Double" Msg="未填写或格式不对"  onblur="changeAmount()"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">总&nbsp;&nbsp;金&nbsp;&nbsp;额</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" size="45" dataType="Double" Msg="未填写或格式不对"  readonly></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">一次性支付</td>
		  <td scope="col"><input name="PaySign"  id="PaySign" type="checkbox" value="1"></td>
	    </tr>

		<tr>
		  	<td height="24" align="right" scope="col">货&nbsp;&nbsp;&nbsp;&nbsp;币</td>
		  	<td scope="col">
			<select name="Currency" id="Currency" style="width:320px" dataType="Require"  msg="未选择货币">
			<option value="" selected>请选择</option>
		  	<?php 
		   	$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
		    if($cRow = mysql_fetch_array($cResult)){
				do{
					echo"<option value='$cRow[Id]'>$cRow[Name]</option>";
					}while ($cRow = mysql_fetch_array($cResult));
				}
          	?>
		  	</select></td>
	    </tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Content" cols="40" rows="3" id="Content" dataType="Require" Msg="未填写说明"></textarea></td>
		</tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">单 &nbsp;&nbsp;&nbsp;据</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
	
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function CheckStaffName(e){
	var r=Math.random();
  var BackData=window.showModalDialog("staff_s1.php?r="+r+"&tSearchPage=staff&fSearchPage=staffout_subsidy&SearchNum=1&Action=21","BackData","dialogHeight =600px;dialogWidth=1130px;center=yes;scroll=yes");
	if(BackData){
		var CL=BackData.split("^^");
		document.form1.Number.value=CL[0];
		e.value=CL[1];	
       document.form1.AveAmount.value=CL[2];	
       document.form1.Amount.value=CL[2];	
       document.form1.TypeName.value =CL[3];	
		}
	}
function changeAmount(){
 var TotalRate= document.getElementById("TotalRate").value;
 var AveAmount = document.getElementById("AveAmount").value;
  if(TotalRate>0){
             document.getElementById("Amount").value= AveAmount*TotalRate;
        }
}


function changeType(){
   var showRemark="";
	var TypeId=document.getElementById('TypeId');
    var index = TypeId.selectedIndex;
    var text = TypeId.options[index].text;
    var value = TypeId.options[index].value;
	if(value!=""){
               if(value==1)showRemark="模范员工及优秀员工离职公司按表现补助薪资";
               if(value==2)showRemark="解除劳动合同赔偿金(公司辞退员工的补偿款)";
               document.getElementById("showRemark").style.display="";
	        	document.getElementById("showRemark").innerHTML="<div style='color:red'>"+showRemark+"</div>";
           }
       else{
                   document.getElementById("showRemark").style.display="none";
                   document.getElementById("showRemark").innerHTML ="";
                }
	}
</script>
