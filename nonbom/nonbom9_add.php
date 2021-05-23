<?php
include "../model/modelhead.php";
//include "../model/testsearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
ChangeWtitle("$SubCompany 非bom配件转入");//需处理
$nowWebPage =$funFrom."_add";
$toWebPage  =$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=990;$tableMenuS=700;
           $CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="980" border="0" align="center" cellspacing="0"  >
		<tr>
			<td height="30"  align="right" valign="middle" scope="col">非BOM配件名称</td>
			<td  valign="middle" scope="col"><input name="GoodsName" type="text" id="GoodsName"  style="width: 340px;" >
<!--                <input type="button" name="Search" id="Search" value="查找" onclick="checkGoods('nonbom4','nonbom9','1','8','event')"></td>-->
		<input type="button" name="Search" id="Search" value="查找" onclick="chooseGoods()"></td>
		</tr>
		<tr>
		  <td align="right" height="30" >编号</td>
		  <td ><input name="GoodsId" type="text" id="GoodsId" style="width: 380px;"  readonly="readonly"><input id="PropertySign" name="PropertySign" type="hidden"><input  type="hidden" id="SIdList" name="SIdList"><input  id="defaultCkId" name="defaultCkId" type="hidden"></td>
	    </tr>
		<tr>
		  <td  align="right" height="30" >单位</td>
		  <td ><input name="Unit" type="text" id="Unit" style="width: 380px;"  value="" readonly="readonly" ></td>
	    </tr>

        <tr>
			<td height="30"  align="right" valign="middle" scope="col">转入数量</td>
			<td  valign="middle" scope="col"><input name="Qty" type="text" id="Qty" style="width: 380px;"  onblur="GetDataInfo()"></td>
		</tr>
        <tr>
          <td align="right" valign="top">转入备注</td>
          <td ><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" ></textarea></td>
        </tr>

	</table>
</td></tr></table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td width="30" class="A0010">&nbsp;</td><td colspan="8" height="40"><span class='redB'>固定资产配件入库资料输入</span></td>
 <td width="30" class="A0001">&nbsp;</td></tr>
<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="30" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="30" align="center">序号</td>
		<td class="A1101" width="50" align="center">配件编号</td>
		<td class="A1101" width="260" align="center">非bom配件名称</td>
		<td class="A1101" width="50" align="center">本次转入</td>
		 <td class="A1101" width="30" align="center">序号</td>
          <td class="A1101" width="140" align="center">资产编号</td>
          <td class="A1101" width="90" align="center">入库地点</td>
          <td class="A1101" width="280" align="center">上传图片</td>
		<td width="30" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="30" class="A0010" height="180">&nbsp;</td>
		<td colspan="8" align="center" class="A0111" id="ShowInfo">
		</td>
		<td width="30" class="A0001">&nbsp;</td>
	</tr>
</table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
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
                  message="请输入数量!";
                 }
           if(Remark==""){
                  message="请输入原因!";
                 }
            if(message!=""){
                alert(message);return false;
                }
      if(PropertySign==1){
             var ShowTable= document.getElementById("ShowTable");
            var GoodsNumArray=document.getElementsByName("GoodsNum[]");
            var CkIdArray=document.getElementsByName("CkId[]");
           // var PictureArray=document.getElementsByName("Picture[]");
            var endSign=0;
	    	for(i=0;i<ShowTable.rows.length;i++){
                if(GoodsNumArray[i].value==""){endSign=1;break;}
                if(CkIdArray[i].value==""){endSign=2;break;}
		    	}
               if(endSign==1){
                      alert("此配件为固定资产，未填写完固定资产编号!");return false;
                  }
               if(endSign==2){
                      alert("请选择入库地点!");return false;
                  }
          }
				//	document.form1.SIdList.value=DataSTR;
					document.form1.action="nonbom9_save.php";
					document.form1.submit();
	}


function  GetDataInfo(){
		var Qty=document.getElementById("Qty").value;
        var PropertySign=document.getElementById("PropertySign").value;
            if( PropertySign==1 && Qty>0){
         var ShowInnerHTML="<div style='width:980;height:100%;overflow-x:hidden;overflow-y:scroll'><table width='100%' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='ShowTable'>";
                 k=1;
                  var GoodsId=document.getElementById("GoodsId").value;
                  var GoodsName=document.getElementById("GoodsName").value;
                           ShowInnerHTML+="<tr><td class='A0101' width='30' height='25' align='center' rowspan='"+Qty+"'>"+k+"</td><td class='A0101' width='50' align='center' rowspan='"+Qty+"'>"+GoodsId+"</td><td class='A0101' width='260' rowspan='"+Qty+"' >"+GoodsName+"</td><td class='A0101' width='50' align='center' rowspan='"+Qty+"'>"+Qty+"</td>";
               for(j=0;j<Qty;j++){
                          tempK=j+1;
                         ShowInnerHTML+="<td class='A0101' width='30' align='center' >"+tempK+"</td><td class='A0101' width='140' align='center' ><input  type='text' name='GoodsNum[]' id='GoodsNum' size='16'></td><td class='A0101' width='90' align='center'><select name='CkId[]' id='CkId' style='width: 80px;' ><option value='' 'selected'>请选择</option>";
			           	<?PHP
		                  $mySql="SELECT Id,Name,Remark FROM $DataPublic.nonbom0_ck  WHERE Estate=1 order by  Remark";
	                      $result = mysql_query($mySql,$link_id);
                          if($myrow = mysql_fetch_array($result)){
	   	                  do{
			                    $FloorId=$myrow["Id"];
				                $FloorRemark=$myrow["Remark"];
				                $FloorName=$myrow["Name"];
			     	            $echoInfo.= "<option value='$FloorId'>$FloorName</option>";
			                  }while ($myrow = mysql_fetch_array($result));
		                  }
			           	?>
                          ShowInnerHTML=ShowInnerHTML+"<?PHP echo $echoInfo; ?>"+"</select></td><td class='A0100' width='280' ><input name='Picture[]' type='file' id='Picture'></td></tr>";
                      }
                      k++;
                 ShowInnerHTML+="</table></div>";
                   document.getElementById("ShowInfo").innerHTML=ShowInnerHTML;

                    var defaultCkId=document.getElementById("defaultCkId").value; //默认入库地点
                    var CkId=document.getElementsByName("CkId[]");
                   for(n=0;n<CkId.length;n++){
                           CkId[n].value=defaultCkId;
                      }
            }

   }


 function checkGoods(tSearchPage,fSearchPage,SearchNum,Action,Oevent){
	var r=Math.random();
	var theName="";
	if(! window.event){  //firfox
	  event =Oevent; //处理兼容性，获得事件对象
	  theName=event.target.getAttribute('name');
	  event ="";
	}
	else {
		theName=event.srcElement.getAttribute('name');
	}
	var e=eval("document.form1."+theName);
	var BackData=window.open(tSearchPage+"_s1.php?r="+r+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum+"&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if(BackData){
		var CL=BackData.split("^^");
		document.form1.GoodsId.value=CL[0];
		document.form1.GoodsName.value=CL[1];
		document.form1.Unit.value=CL[2];
		document.form1.PropertySign.value=CL[3];
		document.form1.defaultCkId.value=CL[4];
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
        content: 'nonbom4_s1.php?Action=8'+ val,
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
                document.form1.defaultCkId.value=CL[4];
            }

            layer.close(index);
        },
        btn2: function (index) {//layer.alert('aaa',{title:'msg title'});  ////点击取消回调
            layer.close(index);
        }

    });
}
</script>