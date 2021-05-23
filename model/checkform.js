function ActionTo(actionId,chooseNum,ToPage,ToWin,ToWarn){
	var Message="";
	var upId="";
	var ToAction=1;
	var funFrom=document.form1.funFrom.value;
     if(actionId==154 || actionId==155){
           //alert(form1.elements.length);
     }

    //alert (actionId+","+chooseNum+","+ToPage+","+ToWin+","+ToWarn);
	if(chooseNum>0){//检查已选取的项目
		var choosedRow=0;
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
            if(actionId==154 || actionId==155){
		    	var Name=NameTemp.search("checkqkid") ;//全额请款和分批请款
            }
          else{
			    var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
             }
			if (e.type=="checkbox" && Name!=-1){
				if(e.checked){
					choosedRow=choosedRow+1;
					if(chooseNum==1)
						upId="&Id="+e.value;
					if(choosedRow>1)
						break;
					}
				}
			}
		if(choosedRow==0){
			Message="该操作要求选定记录！";
			}
		else{
			if(chooseNum==1 && choosedRow>chooseNum)
				Message="该操作只能选取定一条记录！";
			}
		}
	//检查是否需要输入原因

   	if(document.getElementById("ReturnReasons")!=null  && actionId==15){
	   if(document.form1.ReturnReasons.value==""){
		   Message="没有输入退回原因！";
		  }
	   }
	  //如果是结付，要检查有没有选择银行
	  if(document.getElementById("BankId")!=null  && actionId==18){
	   if(document.form1.BankId.value==""){
		   Message="没有选择结付银行！";
		  }
	   }

	  //如果是非BOM请款，要检查有没有选择请款月份
	  if(document.getElementById("qkMonth")!=null  && (actionId==154)){
	   if(document.form1.qkMonth.value==""){
		   Message="没有填写请款月份！";
		  }
	   }


	if(Message!=""){
		alert(Message);return false;
		}
	else{
		if(ToWarn==1){//需警告
			var message=confirm("确定要进行此操作吗？");
			if(message==false){
				ToAction=0;
				}
			}
		if(ToAction==1){
			//解锁
			if(chooseNum>0){
				for (var i=0;i<form1.elements.length;i++){
					var e=form1.elements[i];
					if(e.type=="checkbox")
						e.disabled=false;
					}
				}
			if(ToPage=="linkman"){
				var funFrom=document.form1.funFrom.value;
				document.form1.action=ToPage+"_read.php?ComeFrom=otherPage";
				}
			else{
				if(funFrom=='nonbom5'&&ToPage=='tomain'){
					//检查nonbom add by ckt 2018-01-11
					var nonbom_bol_ud = false;
					var nonbom_check_ud =jQuery(document.form1).find('input[type="checkbox"]:checked');
					var forShortUdTemp = null;
					var mainTypeUdTemp = null;

					if(!confirm('您是否确认要生成采购订单！')){
                        return false;
					}
					nonbom_check_ud.each(function(){
						//判断是否审批通过
						var tr = jQuery(this).parent().parent();
						var num = tr.find('td:eq(1)').text();


						// if(tr.find('input[input-name="StaffNoUd[]"]').attr('iden')!=1){
						// 	alert('第'+num+'条记录非本人采购，不能生成采购单！');
						// 	nonbom_bol_ud = true;
						// 	return false;
						// }
						if(tr.find('input[input-name="AuditStateUd[]"]').val()!=1){
							alert('第'+num+'条记录未通过审核，不能生成采购单！');
							nonbom_bol_ud = true;
							return false;
						}
						//判断是否是同一供应商
						var forShortUd = tr.find('input[input-name="forShortUd[]"]').val();
						if(forShortUdTemp===null){
							forShortUdTemp = forShortUd;
						}else{
							if(forShortUd!=forShortUdTemp){
								alert('非同一供应商，不能生成采购单！');
								nonbom_bol_ud = true;
								return false;
							}
						}
						//判断供应商是否为空  by.lwh
                        if(forShortUdTemp===null){
                            alert('供应商为空，不能生成采购单！');
                            nonbom_bol_ud = true;
                            return false;
                        }
						//判断是否是同一分类
						var mainTypeUd = tr.find('input[input-name="mainTypeUd[]"]').val();
						if(mainTypeUdTemp===null){
							mainTypeUdTemp = mainTypeUd;
						}else{
							if(mainTypeUd!=mainTypeUdTemp){
								alert('非同一分类，不能生成采购单！');
								nonbom_bol_ud = true;
								return false;
							}
						}
					});
					if(nonbom_bol_ud){
						return false;
					}
					nonbom_check_ud.each(function(){
						var tr = jQuery(this).parent().parent();
						tr.find('input[input-name]').each(function(){
							jQuery(this).attr('name', jQuery(this).attr('input-name'));
						})
					})
                }
				document.form1.action=funFrom+"_"+ToPage+".php?ActionId="+actionId+upId;
			}
			document.form1.target=ToWin;
			document.form1.submit();
			document.form1.target="_self";
			document.form1.action="";
			}
		else{
			return false;
			}
		}


}
//表单检查类
/**
新加入:
PreTerm	：带to属性，指定决定源（即是否需要检查的参数）,没有指定决源则需要检查
Month	:格式yyyy-mm的检查
*/
Validator = {
	Require : /.+/,
	Email : /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,
	Phone : /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/,
	Mobile : /^1[35][13567890](\d{8})$/,
	Url : /^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/,
	IdCard : "this.IsIdCard(value)",
	Currency : /^\d+(\.\d+)?$/,
	Number : /^\d+$/,
	Zip : /^[1-9]\d{5}$/,
	QQ : /^[1-9]\d{4,8}$/,
	Integer : /^[-\+]?\d+$/,
	Double : /^[-\+]?\d+(\.\d+)?$/,
	English : /^[A-Za-z]+$/,
	Chinese :  /^[\u0391-\uFFE5]+$/,
	Username : /^[a-z]\w{3,}$/i,
	UnSafe : /^(([A-Z]*|[a-z]*|\d*|[-_\~!@#\$%\^&\*\.\(\)\[\]\{\}<>\?\\\/\'\"]*)|.{0,5})$|\s/,
	IsSafe : function(str){return !this.UnSafe.test(str);},
	SafeString : "this.IsSafe(value)",
	Filter : "this.DoFilter(value, getAttribute('Accept'),getAttribute('name'),getAttribute('id'),getAttribute('size'),getAttribute('Msg'),getAttribute('Row'),getAttribute('Cel'))",
	FilterB : "this.DoFilter(value, getAttribute('Accept'),getAttribute('name'),getAttribute('id'),getAttribute('size'),getAttribute('Msg'),getAttribute('Row'),getAttribute('Cel'))",

	Limit : "this.fLimit(value.length,getAttribute('min'),  getAttribute('max'))",
	LimitB : "this.fLimit(this.LenB(value), getAttribute('min'), getAttribute('max'))",
	Date : "this.IsDate(value, getAttribute('min'), getAttribute('format'))",
	Repeat : "value == document.getElementsByName(getAttribute('to'))[0].value",
	PreTerm:"this.IsList(getAttribute('id'),getAttribute('to'))",
	unSame:"this.IsunSame(getAttribute('id'),getAttribute('toId'))",
	Range : "getAttribute('min') < (value|0) && (value|0) < getAttribute('max')",
	Compare : "this.compare(value,getAttribute('operator'),getAttribute('to'))",
	Custom : "this.Exec(value, getAttribute('regexp'))",
	Group : "this.MustChecked(getAttribute('name'), getAttribute('min'), getAttribute('max'))",
	Month:"this.IsMonth(value)",
	Time:"this.IsTime(value)",
	autoList:"this.IsautoList(getAttribute('id'))",
	DPNameCheck:"this.IsDPName(value, getAttribute('name'), getAttribute('DPNameCheck'), getAttribute('DPNameCheckExcept'))",//增加ajax重名判断 by ckt 2017-12-29
	ErrorItem :
		[document.forms[0]],
	ErrorMessage :
		["以下原因导致提交失败：\t\t\t\t"],

	Validate :
		function(thisFromId,mode,toWebPage,thisAction,thisExtra,FromDir){
			var obj = thisFromId || event.srcElement;
			var count = obj.elements.length;
			this.ErrorMessage.length = 1;
			this.ErrorItem.length = 1;
			this.ErrorItem[0] = obj;
			for(var i=0;i<count;i++){
				if(obj.elements[i].disabled==true)//如果禁用中，则不处理
					continue;
				with(obj.elements[i]){
					//判断重名
					if(getAttribute("DPNameCheck")){
						if(!eval(this.DPNameCheck)){
							this.AddError(i, '重名');
						};
					}
					var _DataType = getAttribute("datatype");
					if(obj.elements[i].type=="text")//加入这一句，否则chrome下上传文件出错 ewen 2013-07-24
					obj.elements[i].value=toGB(obj.elements[i].value);//强制转换为简体
					if(typeof(_DataType) == "object" || typeof(this[_DataType]) == "undefined")
						continue;
					this.ClearState(obj.elements[i]);
					if(getAttribute("require") == "false" && value == "")
						continue;
					switch(_DataType){
					    case "FilterB":
					      if (value==""){
						     this.AddError(i, getAttribute("msg"));
						     break;
					      }
						case "CharType":
						case "IdCard" :
						case "Date" :
						case "Month":
						case "Time":
						case "Repeat" :
						case "PreTerm":
						case "unSame":
						case "Range" :
						case "Compare" :
						case "Custom" :
						case "Group" :
						case "Limit" :
						case "autoList":
						case "LimitB":
						case "SafeString":
						case "Filter":
							if(!eval(this[_DataType])){
								this.AddError(i, getAttribute("msg"));
							}
							break;
						default :
							if(!this[_DataType].test(value)){
								this.AddError(i, getAttribute("msg"));
								}
							break;
						}//end switch(_DataType)
					}//end with(obj.elements[i])
				}//end for(var i=0;i<count;i++)

			if(this.ErrorMessage.length >1){
				mode = mode || 1;
				var errCount = this.ErrorItem.length;
				switch(mode){		//报警模式
					case 2 :
						for(var i=1;i<errCount;i++)
							this.ErrorItem[i].style.color = "red";
					case 1 :
						alert(this.ErrorMessage.join("\n"));
						this.ErrorItem[1].focus();
						break;
					case 3 :
						for(var i=1;i<errCount;i++){
							try{
								var span = document.createElement("SPAN");
								span.id = "__ErrorMessagePanel";
								span.style.color = "red";
								this.ErrorItem[i].parentNode.appendChild(span);
								span.innerHTML = this.ErrorMessage[i].replace(/\d+:/,"*");
								}//end try
							catch(e){
								alert(e.description);
								}
							}//end for(var i=1;i<errCount;i++)
						this.ErrorItem[1].focus();
						break;
					default :
						alert(this.ErrorMessage.join("\n"));
						break;
					}//end switch(mode)
				return false;
				}
			//ewen 2013-02-23更新
			switch(thisAction){
				case 0://返回来源页的checkThisPage函数,特殊应用
					checkThisPage();
					break;
				case 1:
					if(thisExtra==1){
						document.form1.action="../model/subprogram/"+toWebPage+".php?FromDir="+FromDir;
						}
					else{
						if(thisExtra==2){
							//var tSearchPage=document.form1.tSearchPage.value;
							//document.form1.action=tSearchPage+"_s2.php";
							}
						else{
							document.form1.action="../model/subprogram/s3_model.php?FromDir="+FromDir;
							}
						}
					document.form1.submit();
					break;
				case 2:
					for (var i=0;i<form1.elements.length;i++){
						var e=form1.elements[i];
						if(e.type=="checkbox")
						e.disabled=false;
						}
                    document.form1.action=toWebPage+".php";
                    document.form1.submit();
                    break;
				case 3:
					return true;
				default:
					document.form1.action=toWebPage+".php";
					document.form1.submit();
					break;
				}
			/*如果没有错误，则前往指定页面
			if(thisAction==1){
				if(thisExtra==1){
					document.form1.action="../model/subprogram/"+toWebPage+".php?FromDir="+FromDir;
					}
				else{
					if(thisExtra==2){
						//var tSearchPage=document.form1.tSearchPage.value;
						//document.form1.action=tSearchPage+"_s2.php";
						}
					else{
						document.form1.action="../model/subprogram/s3_model.php?FromDir="+FromDir;
						}
					}
				}
			else{
				if(thisAction==2){//列表页直接提交, 遮罩
					for (var i=0;i<form1.elements.length;i++){
						var e=form1.elements[i];
						if(e.type=="checkbox")
						e.disabled=false;
						}
					}
				//alert (	toWebPage);
				document.form1.action=toWebPage+".php";
				}
			document.form1.submit();
			*/
			},
	IsDPName:
		function(value, field, table, where){
			var result;
			where = where?' and Id !='+where:'';
			$.ajax({
				url:'/model/DPNameCheck.php',
				type:"post",
				data:{value:value, field:field, table:table, where:where},
				async : false,
				success : function(data) {
					if(data==1)
						result = false;
					else
						result = true;
				}
			});
			return result;
		},
	AddError :			//添加出错提示
		function(index, str){
			this.ErrorItem[this.ErrorItem.length] = this.ErrorItem[0].elements[index];
			this.ErrorMessage[this.ErrorMessage.length] = this.ErrorMessage.length + ":" + str;
			},
	CharChange:			//简繁转换
		function(elem,GandB){

			},
	ClearState :		//消除出错提示
		function(elem){
			with(elem){
				if(style.color == "red")
					style.color = "";
				var lastNode = parentNode.childNodes[parentNode.childNodes.length-1];
				if(lastNode.id == "__ErrorMessagePanel")
					parentNode.removeChild(lastNode);
				}
			},
	Compare : 			//字串比较
		function(op1,operator,op2){
			switch (operator) {
				case "NotEqual":
					return (op1 != op2);
				case "GreaterThan":
					return (op1 > op2);
				case "GreaterThanEqual":
					return (op1 >= op2);
				case "LessThan":
					return (op1 < op2);
				case "LessThanEqual":
					return (op1 <= op2);
				default:
					return (op1 == op2);
				}
			},
	Exec :				//以指定的正则检查字串
		function(op, reg){
			return new RegExp(reg,"g").test(op);
			},
	IsunSame:
		function(name,toname){
			var res = true;
			var groups1 = document.getElementById(name);
			var groups2 = document.getElementById(toname);
			if(groups1.value==groups2.value){
				res=false;
				}
			return res;
			},
	IsTime:
		function(str){
			var re = new RegExp("^(([01][0-9])|20|21|22|23):[012345][0-9]$");
			return re.test(str);
			},
	IsMonth:
		function(str){
			var Today = new Date();
			var tY = Today.getFullYear();
			var tM = Today.getMonth()+1;//注意月份从下标0开始
			var re = new RegExp("^([0-9]{4})-([0-9]{1,2})$");
			var ar;
			var res = true;
			if ((ar = re.exec(str)) != null){
				var i;
				i = ar[1]*1;
				if(i<2007 || i>tY){
					 res = false;
					}
				j = ar[2]*1;
				if (j <= 0 || j > 12){
					res = false;
					}

				if(i==tY && j>tM){
					  res = false;
					}
				}
			else{
				res = false;
				}
			return res;
			},
IsIdCard : function(number){

		if(number.length == 10)
		{
			return 15;
		}

		var date, Ai;
		var verify = "10x98765432";
		var Wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
		var area = ['','','','','','','','','','','','北京','天津','河北','山西','内蒙古','','','','','','辽宁','吉林','黑龙江','','','','','','','','上海','江苏','浙江','安微','福建','江西','山东','','','','河南','湖北','湖南','广东','广西','海南','','','','重庆','四川','贵州','云南','西藏','','','','','','','陕西','甘肃','青海','宁夏','新疆','','','','','','台湾','','','','','','','','','','上海','澳门','','','','','','','','','国外'];
		var re = number.match(/^(\d{2})\d{4}(((\d{2})(\d{2})(\d{2})(\d{3}))|((\d{4})(\d{2})(\d{2})(\d{3}[x\d])))$/i);
		if(re == null) return false;
		if(re[1] >= area.length || area[re[1]] == "") return false;
		if(re[2].length == 12){
			Ai = number.substr(0, 17);
			date = [re[9], re[10], re[11]].join("-");
		}
		else{
			Ai = number.substr(0, 6) + "19" + number.substr(6);
			date = ["19" + re[4], re[5], re[6]].join("-");
		}
		if(!this.IsDate(date, "ymd")) return false;
		var sum = 0;
		for(var i = 0;i<=16;i++){
			sum += Ai.charAt(i) * Wi[i];
		}
		Ai +=  verify.charAt(sum%11);
		return (number.length ==15 || number.length == 18 && number == Ai);
	},
IsDate : function(op, formatString){
		formatString = formatString || "ymd";
		var m, year, month, day;
		switch(formatString){
			case "ymd" :
				m = op.match(new RegExp("^((\\d{4})|(\\d{2}))([-./])(\\d{1,2})\\4(\\d{1,2})$"));
				if(m == null ) return false;
				day = m[6];
				month = m[5]*1;
				year =  (m[2].length == 4) ? m[2] : GetFullYear(parseInt(m[3], 10));
				break;
			case "dmy" :
				m = op.match(new RegExp("^(\\d{1,2})([-./])(\\d{1,2})\\2((\\d{4})|(\\d{2}))$"));
				if(m == null ) return false;
				day = m[1];
				month = m[3]*1;
				year = (m[5].length == 4) ? m[5] : GetFullYear(parseInt(m[6], 10));
				break;
			default :
				break;
		}
		if(!parseInt(month)) return false;
		month = month==0 ?12:month;
		var date = new Date(year, month-1, day);
        return (typeof(date) == "object" && year == date.getFullYear() && month == (date.getMonth()+1) && day == date.getDate());
		function GetFullYear(y){return ((y<30 ? "20" : "19") + y)|0;}
		},
	IsautoList:
		function(thisName){
			var thisList =eval("document.form1."+thisName);
			if(thisList.length>0){
				for (loop=0;loop<thisList.length;loop++){
					thisList.options[loop].selected=true;
					}
				}
			return true;
			},
	IsList:
		function(thisName,theName){//需检查的列表Id  及是否许可检查的决定源1/0	单选组
			var res=true;
			if(theName==""){
				var groups = document.getElementsByName(theName);
				for(var i=groups.length-1;i>=0;i--){
					if(groups[i].checked){
						var theSign=groups[i].value;//取得单选组选定的值
						}
					}
				}
			else{//没有决定源，则强制检查
				var theSign=1;
				}
			if(theSign==1){					//单选组的值为1则要检查
				var thisList =eval("document.form1."+thisName);
				if(thisList.length==0){
					res = false;
					}
				else{//全选
					for (loop=0;loop<thisList.length;loop++){
						thisList.options[loop].selected=true;
						}
					}
				}
			return res;
			},
	DoFilter : 			//上传文件格式过滤	(不指定)默认可以：jpg,gif,png,rar,zip,pdf,ai,psd,eps,titf,doc,xsl
		function(input, theFilter,theName,theId,theSize,theMsg,theRow,theCel){
			if(theCel=="")theCel=1;

			var groups = document.getElementsByName(name);
			var filter=theFilter;
			if(input){
				if(filter=="")
					filter="jpg,gif,png,rar,zip,pdf,ai,psd,eps,titf,doc,xsl";
				var upInfo=new RegExp("^.+\.(?=EXT)(EXT)$".replace(/EXT/g, filter.split(/\s*,\s*/).join("|")), "gi").test(input);
				//alert(upInfo);
				if(upInfo==false){
					
					var row=NoteTable.rows[theRow];
					if(row==undefined){
						return upInfo;
					}
					var cell=NoteTable.rows[theRow].cells[theCel];
					if(cell==undefined){
						return upInfo;
					}
					NoteTable.rows[theRow].cells[theCel].innerHTML=" <input name="+theName+" type='file' id="+theId+" style='width:420px' size="+theSize+" DataType='Filter' Accept="+theFilter+" Msg="+theMsg+" Row="+theRow+" Cel="+theCel+">";
			    }
				return upInfo;
				}
			else{
				return true;
				}
			},
	fLimit : 			//字串长度限制
		function(len,min, max){
			min = min || 0;
			max = max || Number.MAX_VALUE;
			return min <= len && len <= max;
			},
	LenB : 				//字串字节长度限制
		function(str){
			return str.replace(/[^\x00-\xff]/g,"**").length;
			},
	MustChecked : 		//元素选定的数目是否在指定范围
		function(name, min, max){
			var groups = document.getElementsByName(name);
			var hasChecked = 0;
			min = min || 1;
			max = max || groups.length;
			for(var i=groups.length-1;i>=0;i--)
				if(groups[i].checked) hasChecked++;
			return min <= hasChecked && hasChecked <= max;
			}
	}//end Validator = {