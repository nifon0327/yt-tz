<?php 
/*
$DataPublic.adminitype
$DataPublic.currencydata
*/
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：//需处理
ChangeWtitle("$SubCompany 添加行政费用");
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
	<table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
            <td width="180" height="25" align="right" scope="col">费用分类</td>
            <td scope="col">
			<select name="TypeId" id="TypeId" style="width:380px" onchange="changeAmount()" dataType="Require" msg="未选择分类">
			<option value="" selected>请选择</option>
			<?php 
			$result = mysql_query("SELECT * FROM $DataPublic.adminitype WHERE Estate=1 order by Letter,Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
                    $thisRemark = $myrow["Remark"];
					echo"<option value='$myrow[TypeId]|$myrow[Amount]|$thisRemark'>$myrow[Letter] - $myrow[Name]</option>";//
					} while ($myrow = mysql_fetch_array($result));
				}
			?>		
		 	</select></td>
      </tr>
     <tr ><td  style="padding-left:190px;display:none;"  id="showRemark"  colspan="2" >&nbsp;</td></tr>
     
        <tr>
            <td scope="col" align="right">所属公司</td>
            <td scope="col">
              <?php 
              include "../model/subselect/cSign.php";
			  ?>
			</td></tr>
			
     <tr>
		  <td height="29" align="right" scope="col">登记日期</td>
		  <td scope="col"><input name="theDate" type="text" id="theDate" onfocus="WdatePicker({minDate:'%y-%M-{%d}'})" value="<?php  echo date("Y-m-d");?>" style="width:380px" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">金&nbsp;&nbsp;&nbsp;&nbsp;额</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" style="width:380px" dataType="Double" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  	<td height="24" align="right" scope="col">货&nbsp;&nbsp;&nbsp;&nbsp;币</td>
		  	<td scope="col">
			<select name="Currency" id="Currency" style="width:380px" dataType="Require"  msg="未选择货币">
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
		  <td scope="col"><textarea name="Content" cols="50" rows="3" id="Content" dataType="Require" Msg="未填写说明"></textarea></td>
		</tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">单 &nbsp;&nbsp;&nbsp;据</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="50" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">款项收回类型</td>
		  <td scope="col"><input name="Property" type="radio" value="1" ><span class="redB">其他收入</span>
                      <input name="Property"  type="radio" value="2" ><span class="blueB">Invoice</span>
                      <input name="Property" type="radio" value="3" ><span class="yellowB">薪资</span>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">款项是否收回</td>
		  <td scope="col"><input type="hidden" id="OtherId" name="OtherId"><input name="OtherName" type="text" id="OtherName" style="width:380px" onclick="SearchRecord(this)" readonly></td>
	    </tr>


        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function SearchRecord(e){
	var r=Math.random();
    var tempK=0;
    var temp=document.getElementsByName("Property");
         for (i=0;i<temp.length;i++){
              if(temp[i].checked){
                    tempK=temp[i].value;
                 }
            }
  switch(tempK){
           case "1":
	            var BackData=window.showModalDialog("cw_otherin_s1.php?r="+r+"&tSearchPage=cw_otherin&fSearchPage=cw_adminicost&SearchNum=1&Action=1","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
               break;
          case "2":
	            var BackData=window.showModalDialog("cw_orderin_s1.php?r="+r+"&tSearchPage=cw_otherin&fSearchPage=cw_adminicost&SearchNum=1&Action=3","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
               break;
          case "3":
               break;
            default:
               alert("请选择款项收回类型!");return false;
               break;
          }
	if(BackData){
		var CL=BackData.split("^^");
		document.form1.OtherId.value=CL[0];//记录产品ID
		e.value=CL[1];	//文本框显示产品名称
		}
	}

function changeAmount(){
	var TypeId=document.getElementById('TypeId');
    var index = TypeId.selectedIndex;
    var text = TypeId.options[index].text;
    var value = TypeId.options[index].value;
	if(value!=""){
		//分解内容
		var Split_ValueStr=value.split("|");
		document.form1.Amount.value=Split_ValueStr[1];
      if(Split_ValueStr[2]!=""){
               document.getElementById("showRemark").style.display="";
	        	document.getElementById("showRemark").innerHTML="<div style='color:red'>"+Split_ValueStr[2]+"</div>";
           }
       else{
                   document.getElementById("showRemark").style.display="none";
                   document.getElementById("showRemark").innerHTML ="";
                }
		}
	else{
		document.form1.Amount.value="";
		}
	}
function getRelatedata(type){
    var url="adminicost_relate_ajax.php?Type="+type;
    var ajax=InitAjax();
    ajax.open("GET",url,true);
    ajax.onreadystatechange =function(){
    if(ajax.readyState==4){
              document.getElementById("RelateDiv").innerHTML =ajax.responseText;
        }
    }
    ajax.send(null);
}
</script>
