<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)}
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)}
/* 为 DIV 加阴影 */
.out {position:relative;background:#EEEEEE;margin:10px auto;}
.in {
    background:#FFFFFF;
   border:2px solid #555;
   padding:10px 5px;
   position:relative;
   top:-5px;
   left:-5px;
 border-color: #B03060;
 }
-->
</style>
<?php
//EWEN 2013-03-29 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增供应商资料");//需处理
$nowWebPage =$funFrom."_add";
$toWebPage  =$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="850" border="0" align="center" cellspacing="5">
<tr>
<td width="91" align="right">选择购买分类</td>
<td colspan="3" scope="col"><textarea   id="TypeName" name="TypeName" onclick="updateJq(this,1)" style="width:380px"></textarea><input  type="hidden" id="TypeId" name="TypeId" ></td>
</tr>
		<tr>
            <td scope="col" align="right">结付方式</td>
            <td colspan="3" scope="col">
				<select name="PayMode" id="PayMode" style="width:380px" dataType="Require" msg="未选择">
				<option value="">请选择</option>
				<option value="0">月结</option>
				<option value="1">现金</option>
				</select></td>
          </tr>
		 <tr>
            <td scope="col" align="right">结付货币</td>
            <td colspan="3" scope="col">
			<?php
			include "../model/subselect/Currency.php";
			?>
            </td></tr>
		 <tr>
		   	<td scope="col" align="right">所在省市</td>
		   	<td colspan="3" scope="col">
		   		<select name="Prov" id="Prov" style="width:180" onChange="provChanged(selectedIndex)"></select>&nbsp;&nbsp;&nbsp;&nbsp;
             	<select name="City" id="City" style="width:180"></select>
			</td>
	      </tr>
              <script>
			var ar =
				[[["广东省"],["上海市","潮州市","东莞市","佛山市","广州市","河源市","惠州市","江门市","揭阳市","茂名市","梅州市","清远市","汕头市","汕尾市","韶关市","阳江市","云浮市","湛江市","肇庆市","中山市","珠海市"]],[["安徽省"],["安庆市","蚌埠市","巢湖市","池州市","滁州市","阜阳市","毫州市","合肥市","淮北市","淮南市","黄山市","六安市","马鞍山市","宿州市","铜陵市","芜湖市","宣州市"]],[["澳门"],["澳门"]],[["北京市"],["北京市"]],[["福建省"],["福州市","龙岩市","南平市","宁德市","莆田市","泉州市","三明市","厦门市","漳州市"]],[["甘肃省"],["白银市","定西地区","甘南自治州","嘉峪关市","金昌市","酒泉地区","兰州市","临夏自治州","陇南地区","平凉地区","庆阳地区","天水市","武威市","张掖地区"]],[["广西"],["百色地区","北海市","防城港市","桂林地区","桂林市","贵港市","河池地区","柳州地区","柳州市","南宁市","南宁地区","钦州市","贺州地区","梧州市","玉林市"]],[["贵州省"],["安顺市","毕节地区","贵阳市","六盘水市","黔东南自治州","黔南自治州","黔西南自治州","铜仁市","遵义市"]],[["海南省"],["海口市","三亚市"]],[["河北省"],["保定地区","保定市","沧州地区","沧州市","承德地区","承德市","邯郸市","衡水市","廊坊市","秦皇岛市","深州市","石家庄市","唐山市","邢台地区","邢台市","张家口地区","张家口市"]],[["河南省"],["安阳市","鹤壁市","焦作市","开封市","洛阳市","南阳市","平顶山市","三门峡市","商丘市","新乡市","信阳市","许昌市","郑州市","周口市","驻马店市","漯河市","濮阳市"]],[["黑龙江"],["大庆市","大兴安岭","哈尔滨市","鹤岗市","黑河地区","黑河市","鸡西市","佳木斯市","牡丹江市","七台河市","齐齐哈尔市","双鸭山市","松花江地区","绥化市","伊春市"]],[["湖北省"],["鄂州市","恩施自治州","黄冈市","黄石市","荆门市","荆州市","十堰市","随州市","武汉市","咸宁市","襄樊市","孝感地区","孝感市","宜昌地区","宜昌市","郧阳地区"]],[["湖南省"],["常德市","长沙市","郴州地区","张家界市","衡阳市","怀化市","永州市","娄底市","邵阳市","湘潭市","湘西自治区","益阳市","岳阳市","株洲市"]],[["吉林省"],["白城地区","白城市","白山市","长春市","浑江市","吉林市","辽源市","四平市","松原市","通化市","延边自治区"]],[["江苏省"],["常州市","淮阴市","连云港市","南京市","南通市","苏州市","宿迁市","泰州市","无锡市","徐州市","盐城市","扬州市","镇江市"]],[["江西省"],["抚州市","赣州市","吉安市","景德镇市","九江市","南昌市","萍乡市","上饶市","新余市","宜春市","鹰潭市"]],[["辽宁省"],["鞍山市","本溪市","朝阳市","大连市","丹东市","抚顺市","阜新市","葫芦岛市","锦州市","辽阳市","盘锦市","沈阳市","铁岭市","营口市"]],[["内蒙古"],["阿拉善盟","巴彦淖尔盟","包头市","赤峰市","呼和浩特市","呼伦贝尔市","乌海市","乌兰察布盟","锡林郭勒盟","兴安盟","鄂尔多斯市","通辽市"]],[["宁夏"],["固原市","石嘴山市","银川市","吴忠市"]],[["青海省"],["果洛自治州","海北自治州","海东地区","海南自治州","海西自治州","黄南自治州","西宁市","玉树自治州"]],[["山东省"],["诸城市","滨州市","德州市","东营市","菏泽地区","济南市","济宁市","莱芜市","聊城市","临沂市","青岛市","日照市","泰安市","威海市","潍坊市","烟台市","枣庄市","淄博市"]],[["山西省"],["长治市","大同市","晋城市","晋中市","临汾市","吕梁地区","朔州市","太原市","忻州市","雁北地区","阳泉市","运城市"]],[["陕西省"],["安康市","宝鸡市","汉中市","商洛市","铜川市","渭南市","西安市","咸阳市","延安市","榆林市"]],[["上海市"],["上海市"]],[["四川省"],["阿坝自治州","巴中市","成都市","达州市","德阳市","甘孜自治州","广安市","广元市","乐山市","凉山自治州","眉山市","绵阳市","南充市","内江市","攀枝花市","遂宁市","雅安市","宜宾市","自贡市","泸州市","资阳市"]],[["台湾"],["高雄市","高雄县","花莲县","基隆市","嘉义市","嘉义县","苗栗县","南投县","澎湖县","屏东县","台北市","台北县","台东县","台南市","台南县","台中市","台中县","桃园县","新竹市","新竹县","宜兰县","云林县","彰化市","彰化县"]],[["天津市"],["天津市"]],[["西藏"],["阿里地区","昌都地区","拉萨市","林芝地区","那曲地区","日喀则地区","山南地区"]],[["上海"],["上海"]],[["新疆"],["阿克苏地区","阿勒泰地区","巴音郭楞州","博尔塔拉州","昌吉自治州","哈密地区","和田地区","喀什地区","克拉玛依市","克孜勒州","石河子市","塔城地区","吐鲁番地区","乌鲁木齐市","伊犁地区"]],[["云南省"],["保山市","楚雄自治州","大理自治州","德宏自治州","迪庆自治州","东川市","红河自治州","昆明市","丽江地区","临沧地区","怒江自治州","曲靖市","思茅地区","文山自治州","西双版纳州","玉溪市","昭通市"]],[["浙江省"],["杭州市","湖州市","嘉兴市","诸暨市","金华市","丽水市","宁波市","绍兴市","台州市","温州市","舟山市","衢州市","慈溪市"]],[["重庆市"],["重庆市"]]];
			var prov=document.getElementById('Prov');
                        for (var i=0;i<ar.length;i++)
				prov.options[prov.options.length]=new Option(ar[i][0],ar[i][0])
			function provChanged(i){
                                city=document.getElementById('City');
				city.innerHTML=""
				for (var j=0;j<ar[i][1].length;j++)
					city.options[form1.City.options.length]=new Option(ar[i][1][j],ar[i][1][j])
				}
			provChanged(0);
			</script>
          <tr>
            <td align="right">公司名称</td>
            <td colspan="3"><input name="Company" type="text" id="Company" style="width:380px;" dataType="Limit" max="50" min="2" msg="必须在2-50个字之内"></td>
          </tr>
          <tr>
            <td align="right">公司简称</td>
            <td colspan="3"><input name="Forshort" type="text" id="Forshort" style="width:380px;" dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"></td>
          </tr>
    <tr>
            <td valign="top" align="right">增值税率</td>
            <td colspan="3"><select name="AddTaxValue" id="AddTaxValue" style="width:380px;" dataType="Require" msg="未选择">
				<option value="" selected>请选择</option>
              <?php
			  $AddTaxResult = mysql_query("SELECT Id, Name,Value FROM $DataIn.provider_addtax WHERE  Estate=1 order by Id",$link_id);
			  if($AddTaxRow = mysql_fetch_array($AddTaxResult)){
				$i=1;
				do{
				    $AddTaxId=$AddTaxRow["Id"];
					$AddTaxName=$AddTaxRow["Name"];
					$AddTaxValue=$AddTaxRow["Value"];
					echo"<option value='$AddTaxId' >$AddTaxName</option>";
					$i++;
					}while ($AddTaxRow = mysql_fetch_array($AddTaxResult));
				}
			?>
				</select></td>
          </tr>

          <tr>
            <td align="right">公司电话</td>
            <td colspan="3"><input name="Tel" type="text" id="Tel" style="width:380px;"></td>
          </tr>
          <tr>
            <td align="right">公司传真</td>
            <td colspan="3"><input name="Fax" type="text" id="Fax" style="width:380px;"></td>
          </tr>
          <tr>
            <td align="right">网&nbsp;&nbsp;&nbsp;&nbsp;址</td>
            <td colspan="3"><input name="Website" type="text" id="Website" style="width:380px;" require="false" dataType="Url" msg="非法的Url,http://"></td>
          </tr>
          <tr>
            <td align="right">邮政编码</td>
            <td colspan="3"><input name="ZIP" type="text" id="ZIP" style="width:380px;" require="false" dataType="Custom" regexp="^[1-9]\d{5}$" msg="邮政编码不存在"></td>
          </tr>
          <tr>
            <td align="right">通信地址</td>
            <td colspan="3"><input name="Address" type="text" require="false" id="Address" style="width:380px;" ataType="Limit" max="50" msg="必须在50个字之内"></td>
          </tr>


          <tr>
            <td valign="top" align="right">银行帐户</td>
            <td colspan="3"><textarea name="Bank" style="width:380px;" id="Bank"></textarea></td>
          </tr>
          <tr>
            <td valign="top" align="right">企业法人</td>
            <td colspan="3"><input name="LegalPerson" type="text" id="LegalPerson" style="width:380px;"></td>
          </tr>
          <tr>
            <td valign="top" align="right">营业执照有效期</td>
            <td colspan="3"><input name="BLdate" type="text" id="BLdate" maxlength="20" style="width:380px;"><img onclick="WdatePicker({el:'BLdate'})" src="../model/DatePicker/skin/datePicker.gif" width="16" height="22" align="absmiddle"></td>
          </tr>
          <tr>
            <td valign="top" align="right">税务登记证有效期</td>
            <td colspan="3"><input name="TRCdate" type="text" id="TRCdate" maxlength="20" style="width:380px;"><img onclick="WdatePicker({el:'TRCdate'})" src="../model/DatePicker/skin/datePicker.gif" width="16" height="22" align="absmiddle"></td>
          </tr>
          <tr>
            <td valign="top" align="right">生产许可证有效期</td>
            <td colspan="3"><input name="PLdate" type="text" id="PLdate" maxlength="20" style="width:380px;"><img onclick="WdatePicker({el:'PLdate'})" src="../model/DatePicker/skin/datePicker.gif" width="16" height="22" align="absmiddle"></td>
          </tr>
          <tr>
            <td valign="top" align="right">公司简介</td>
            <td colspan="3"><textarea name="Description" type="text" id="Description" style="width:380px;" rows='3' ></textarea></td>
          </tr>
           <tr>
            <td valign="top" align="right">已获资质</td>
            <td colspan="3"><textarea name="Aptitudes" type="text" id="Aptitudes" style="width:380px;"></textarea></td>
          </tr>
           <tr>
            <td valign="top" align="right">质量能力</td>
            <td colspan="3"><textarea name="EAQF" type="text" id="EAQF" style="width:380px;"></textarea></td>
          </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td colspan="3"><textarea name="Remark" style="width:380px;" id="Remark"></textarea></td>
          </tr>
          <tr>
            <td valign="top" align="right">FSC证书编号</td>
            <td colspan="3"><input name="FscNo" type="text" id="FscNo" style="width:380px;" /></td>
          </tr>
          <tr>
            <td colspan="4" align="center">默认联系人信息</td>
          </tr>
          <tr>
            <td align="right">联 系 人</td>
            <td><input name="Linkman" type="text" id="Linkman" style="width:150px;" dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"></td>
            <td width="55" align="right">性&nbsp;&nbsp;&nbsp;&nbsp;别</td>
            <td>
              <select name="Sex" id="Sex" style="width:150px;"  dataType="Require" msg="未选择">
			  <option value="">请选择</option>
			  <option value="0">女</option>
              <option value="1">男</option>
              </select></td>
          </tr>
          <tr>
            <td align="right">职&nbsp;&nbsp;&nbsp;&nbsp;务</td>
            <td width="164"><input name="Headship" type="text" id="Headship" style="width:150px;"  maxlength="20"></td>
            <td align="right">昵&nbsp;&nbsp;&nbsp;&nbsp;称</td>
            <td width="409"><input name="Nickname" type="text" id="Nickname" style="width:150px;"  maxlength="20"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" style="width:150px;" ></td>
            <td align="right">固定电话</td>
            <td><input name="Tel2" type="text" id="Tel2" style="width:150px;" ></td>
          </tr>
          <tr>
            <td align="right">MSN</td>
            <td colspan="3"><input name="MSN" type="text" id="MSN" style="width:380px;" require="false" dataType="Email" msg="MSN格式不正确"></td>
          </tr>
          <tr>
            <td align="right">SKYPE</td>
            <td colspan="3"><input name="SKYPE" type="text" id="SKYPE" style="width:380px;"></td>
          </tr>
          <tr>
            <td align="right">邮件地址</td>
            <td colspan="3"><input name="Email" type="text" id="Email" style="width:380px;" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          <tr>
            <td align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
            <td colspan="3"><textarea name="Remark2" style="width:380px;" id="Remark2"></textarea></td>
          </tr>
  </table></td></tr></table>
<?php
//步骤5：
echo"<div id='Jp' style='position:absolute;width:400px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
			<div class='in' id='infoShow'>
			</div>
	</div>";
include "../model/subprogram/add_model_b.php";
?>
<script language = "JavaScript">
function updateJq(e,toObj){
	var InfoSTR="";
	var buttonSTR="";
	var runningNum="";

	var theDiv=document.getElementById("Jp");
	var infoShow=document.getElementById("infoShow");

	var ObjId=document.form1.ObjId.value;
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId ){
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:
				<?PHP
				   $echoInfo="";$rows=0;
					$TypeResult = mysql_query("SELECT Id,TypeName FROM $DataPublic.nonbom2_subtype WHERE  Estate=1 ORDER BY Id",$link_id);
		          if($TypeRow = mysql_fetch_array($TypeResult)){
				  do{
				        $rows++;
					    $echoInfo.="&nbsp;<input type='checkbox' name='TypeIdCheckId[]'  id='TypeIdCheckId' value='$TypeRow[Id]|$TypeRow[TypeName]'>&nbsp;&nbsp;$TypeRow[TypeName] </br>";
					  } while($TypeRow = mysql_fetch_array($TypeResult));
			      }
				?>
				infoShow.style.width=252;
				 infoShow.style.height=<?php echo $rows; ?>*25+20;

				 theDiv.style.width=252;
				 theDiv.style.height=<?php echo $rows; ?>*25+20;

				 InfoSTR="<?php echo $echoInfo; ?>"+"<br>";
				break;

			}

		  var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='确  定' onclick=' setValue("+toObj+")'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取  消' onclick='CloseDiv()'>";

		  infoShow.innerHTML=InfoSTR+buttonSTR;
		  theDiv.style.top=event.clientY -5+'px';
	      theDiv.style.left= document.body.scrollLeft+600+'px';
		  theDiv.style.visibility = "";
		  theDiv.style.display="";


          var TypeId=document.getElementById("TypeId").value;
          var  TypeArray=TypeId.split("@");
	       var TypeIdCheckId=document.getElementsByName("TypeIdCheckId[]");
           for(n=0;n<TypeIdCheckId.length;n++){
                    for(k=0;k<TypeArray.length;k++){
                            var TempIdArray=TypeIdCheckId[n].value.split("|");
                                  if(TempIdArray[0]==TypeArray[k])TypeIdCheckId[n].checked=true;
                        }
             }
		}
}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");
	//theDiv.className="moveLtoR";
	theDiv.style.visibility = "hidden";
	infoShow.innerHTML="";
	}

function setValue(toObj){
    switch(toObj){
	    case 1:
	       var returnId="";
           var returnName="";
	       var TypeIdCheckId=document.getElementsByName("TypeIdCheckId[]");
	        for(var j=0;j<TypeIdCheckId.length;j++){
	            if (TypeIdCheckId[j].checked){
                      var tempArray=TypeIdCheckId[j].value.split("|");
		              returnId+=returnId==""?tempArray[0]:"@"+tempArray[0];
		              returnName+=returnName==""?tempArray[1]:"@"+tempArray[1];
	            }
	        }
	       document.getElementById("TypeId").value=returnId;
	       document.getElementById("TypeName").value=returnName;
	       break;
    }
     CloseDiv();
}
</script>