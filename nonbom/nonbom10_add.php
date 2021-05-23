<?php
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 非bom配件报废");//需处理
$nowWebPage =$funFrom."_add";
$toWebPage  =$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=950;$tableMenuS=650;
//$SaveSTR="NO";
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="940" border="0" align="center" cellspacing="0" id="NoteTable" >
		<tr>
			<td height="25"  align="right" valign="middle" scope="col">非BOM配件名称</td>
			<td valign="middle" scope="col"><input name="GoodsName" type="text" id="GoodsName"   style="width: 320px;" >
<!--                <input type="button" name="Search" id="Search" value="查     找" onclick="checkGoods('nonbom4','nonbom10','1','10','event')"> -->
                <input type="button" name="Search" id="Search" value="查     找" onclick="chooseGoods()">
            </td>
		</tr>
		<tr>
		  <td height="25" align="right">编号</td>
		  <td ><input name="GoodsId" type="text" id="GoodsId" style="width: 380px;" readonly="readonly"/><input id="PropertySign" name="PropertySign" type="hidden"><input  type="hidden" id="SIdList" name="SIdList"></td>
	    </tr>
		<tr>
		  <td height="25" align="right">单位</td>
		  <td ><input name="Unit" type="text" id="Unit" style="width: 380px;"  value="" readonly="readonly" /></td>
	    </tr>
        <tr>
			<td  height="25" align="right" valign="middle" scope="col">在库</td>
			<td  valign="middle" scope="col"><input name="wQty" type="text" id="wQty" style="width: 380px;" value="" readonly="readonly"/></td>
		</tr>
        <tr>
			<td height="25" align="right" valign="middle" scope="col">采购库存</td>
			<td  valign="middle" scope="col"><input name="oQty[]" type="text" id="oQty" style="width: 380px;"  value="" readonly="readonly"/></td>
		</tr>
        <tr >
          <td  height="25" align="right" valign="middle" >最低库存</td>
          <td ><input name="Qty3" type="text" id="Qty3" style="width: 380px;"   value="" readonly="readonly"/></td>
        </tr>
        <tr>
			<td  height="25" align="right" valign="middle" scope="col">报废数量</td>
			<td  valign="middle" scope="col"><input name="Qty" type="text" id="Qty" style="width: 380px;"   onblur="checkThisPage()"></td>
		</tr>
        <tr>
          <td  height="25" align="right" valign="middle"  >报废备注</td>
          <td ><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" ></textarea></td>
        </tr>
	</table>
</td></tr></table>
   <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
     		<tr><td width="30"  height="30" class="A0010">&nbsp;</td>	<td colspan="6" ><span class='redB'>固定资产报废明细</span></td>		<td width="30" class="A0001">&nbsp;</td></tr>
   <tr >
		<td width="30"  bgcolor="#FFFFFF" height="25" class="A0010">&nbsp;</td>
		 <td class="A1111" width="60" align="center">操作</td>
		 <td class="A1101" width="40" align="center">序号</td>
          <td class="A1101" width="120" align="center">条码</td>
          <td class="A1101" width="150" align="center">资产编号</td>
          <td class="A1101" width="260" align="center">报废图片</td>
          <td class="A1101" width="260" align="center">报废说明</td>
		<td width="30"  bgcolor="#FFFFFF" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="30"  height="250" class="A0010">&nbsp;</td>
		<td colspan="6" align="center" class="A0111" id="ShowInfo">
                    <div style='width:880;height:100%;overflow-x:hidden;overflow-y:scroll'>
                   <table width='100%' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='ListTable'>
                 </table></div>
		</td>
		<td width="30" class="A0001" >&nbsp;</td>
	</tr>
</table>
<?php
include "../model/subprogram/add_model_b.php";
include "../model/subprogram/read_model_menu.php";
?>
<script src="../plugins/layer/layer.js" type=text/javascript></script>
<script>
function CheckForm(){
          var message="";
           var DataSTR="";
            var Qty=document.getElementById("Qty").value;
            var GoodsId=document.getElementById("GoodsId").value;
            var Remark=document.getElementById("Remark").value;
           var PropertySign=document.getElementById("PropertySign").value;
           if(GoodsId==""){
                  message="请选择配件!";
                 }
           if(Qty<=0){
                  message="请输入报废数量!";
                 }
           if(Remark==""){
                  message="请输入报废原因!";
                 }
            if(message!=""){
                alert(message);return false;
                }

      if(PropertySign==1){
             if(Qty!=ListTable.rows.length){
                    alert("此配件为固定资产，报废的数量与明细不一致!");return false;
                  }
           //  var ShowTable= document.getElementById("ShowTable");
            var bfRemarkArray=document.getElementsByName("bfRemark[]");
          //  var PictureArray=document.getElementsByName("Picture[]");
            var endSign=0;
	    	for(i=0;i<ListTable.rows.length;i++){
		    	/*if(DataSTR==""){
			    	    DataSTR=ListTable.rows[i].cells[2].innerText+"@"+bfRemarkArray[i].value+"@"+PictureArray[i].value;
			    	}
		    	else{
			          	DataSTR=DataSTR+"|"+ListTable.rows[i].cells[2].innerText+"@"+bfRemarkArray[i].value+"@"+PictureArray[i].value;
			    	   }*/
		        	}
              }

		   document.form1.action="nonbom10_save.php";
		   document.form1.submit();
	}

function checkThisPage(){
	//数量检查
	var GoodsId=document.getElementById("GoodsId").value;
	var PropertySign=document.getElementById("PropertySign").value;
	var TempwQty=Number(document.getElementById("wQty").value);
	var TempoQty=Number(document.getElementById("oQty").value);
	var TempQty=Number(document.getElementById("Qty").value);
	if(TempQty<=TempwQty && TempQty<=TempoQty && TempQty>0){
             if(PropertySign==1){
                       AddGoods(4,GoodsId);
                         }
           }
	else{
	     	alert("数据不在允许范围或格式不对");
               document.getElementById("Qty").value="";
		}
}
function checkGoods(tSearchPage,fSearchPage,SearchNum,Action,Oevent){
	var r=Math.random();
	var theName="";
	if(! window.event){  //firfox
	  //alert("FirFox");
	  event =Oevent; //处理兼容性，获得事件对象
	  theName=event.target.getAttribute('name');
	  event ="";
	  //alert ("FirFox"+theName);
	}
	else {
		//alert("IE");
		theName=event.srcElement.getAttribute('name');
		//alert ("IE"+theName);
	}
	var e=eval("document.form1."+theName);
	var BackData=window.open(tSearchPage+"_s1.php?r="+r+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum+"&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if(BackData){
	    console.log(BackData);
		var CL=BackData.split("^^");
		document.form1.GoodsId.value=CL[0];
		document.form1.GoodsName.value=CL[1];
		document.form1.Unit.value=CL[2];
		document.form1.PropertySign.value=CL[3];
		document.form1.wQty.value=CL[4];
		document.form1.oQty.value=CL[5];
		document.form1.Qty3.value=CL[6];
		}
  }

function AddGoods(Action,GoodsId){
   document.getElementById('SafariReturnValue').value="";
	var num=Math.random();
	BackData=window.open("nonbom8_s1.php?r="+num+"&tSearchPage=nonbom8&fSearchPage=nonbom8&SearchNum=2&Action="+Action+"&GoodsId="+GoodsId,"BackData","dialogHeight =450px;dialogWidth=880px;center=yes;scroll=yes");

		if(!BackData){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
			//alert("return");
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
		}
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录
		var Rowslength=Rows.length;//数组长度

		if(document.getElementById("TempMaxNumber")){
			var TempMaxNumber=document.getElementById("TempMaxNumber");
			TempMaxNumber.value=TempMaxNumber.value*1+Rowslength*1;
		}


		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");//分拆记录中的字段
			//过滤相同的配件ID号
			for(var j=0;j<ListTable.rows.length;j++){
				var SIdtemp=ListTable.rows[j].cells[2].innerText;
				if(FieldArray[0]==SIdtemp){//如果流水号存在
					Message="固定资产配件: "+FieldArray[0]+" 已存在!跳过继续！";
					break;
					}
				}
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);

				//表格行数
				tmpNumQty=oTR.rowIndex;
				tmpNum=oTR.rowIndex+1;

				//第1列:序号
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="54";
				oTD.height="25";

				//第2列:
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";

				//第2列:
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"<input name='BarCode[]' type='hidden' id='BarCode"+tmpNumQty+"'  class='noLine' value='"+FieldArray[0]+"'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="120";
				//第3列
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="150";

				oTD=oTR.insertCell(4);
				oTD.innerHTML="<input name='Picture[]' type='file' id='Picture"+tmpNumQty+"'  class='noLine' value=''>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="260";
				//第5列:领用说明
				oTD=oTR.insertCell(5);
				oTD.innerHTML="<input name='bfRemark[]' type='text' id='bfRemark"+tmpNumQty+"' size='30' class='noLine' value=''>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="251";
				}
			else{
				alert(Message);
				}
			}//end for
			return true;
		}
	}

function deleteRow(rowIndex){
	            ListTable.deleteRow(rowIndex);
	            ShowSequence(ListTable);
	}

function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){
        var k=i+1;
		TableTemp.rows[i].cells[1].innerText=k;//
		var j=i-1;
		}
	}
	
	function chooseGoods() {
        var val = '';
        var GoodsName = jQuery("#GoodsName").val();
        if (GoodsName) {
            val += '&GoodsName=' + GoodsName;
        }
        layer.open({
            type: 2,
            title: '替换构件',
            area: ['800px', '500px'],
            btn: ['确定', '取消'],
            fixed: false, //不固定
            // maxmin: true,
            content: 'nonbom4_s1.php?Action=10'+ val,
            success: function (layero) {
                layero.find('.layui-layer-btn').css('text-align', 'center')
            },
            yes: function (index) {//

                var chooses = 0;
                var GName = '';
                var Field = '';
                var $table = layer.getChildFrame('table tbody', index);

                $table.find('input[id^="checkid"]:checkbox').each(function () {
                    if (jQuery(this).prop('checked') == true) {
                        chooses = chooses + 1;
                        if (chooses == 1) {
                            GName = jQuery(this).val();
                        }
                    }
                });


                if (chooses == 0) {
                    layer.msg("该操作要求选定记录！", function () {
                    });
                    return;
                }
                if (chooses > 1) {
                    layer.msg("该操作只能选取定一条记录!", function () {
                    });
                    return;
                }

                if (chooses == 1){
                    var CL=GName.split("^^");
                    document.form1.GoodsId.value=CL[0];
                    document.form1.GoodsName.value=CL[1];
                    document.form1.Unit.value=CL[2];
                    document.form1.PropertySign.value=CL[3];
                    document.form1.wQty.value=CL[4];
                    document.form1.oQty.value=CL[5];
                    document.form1.Qty3.value=CL[6];
                }

                layer.close(index);
            },
            btn2: function (index) {//layer.alert('aaa',{title:'msg title'});  ////点击取消回调
                layer.close(index);
            }

        });
    }
</script>