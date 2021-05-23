document.write("<SCRIPT  src='../model/IE_FOX.js' type=text/javascript></script>");  //加入IE,fireFox兼容的函数 add by zx 20110321
function br_replace(str)  //add by zx 20110321 除去空格，换行符，回车符等等
{
	if (typeof(str) == 'undefined' || str ==null || str == '' )
	{
		str="";
		return str;
	}
	str=str.replace(/(^\s*)|(\s*$)/g, "");
	str=str.replace(/<br>/gi,"");
	str=str.replace(/\n/g, ""); 
	str=str.replace(/\r\n/g, ""); 
	str=str.replace(/\r/g, "");
	str=str.replace(/<br[^>]*>/ig,'');			
	return str;
}


//业务专用
//英文格式日期
function myEngCheck(shipDate){
	var NewDateArray=shipDate.toString().split("-");//转英文月年
	var MonthArray=new Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	var MonthSub=Number(NewDateArray[1])-1;		
	return MonthArray[MonthSub]+NewDateArray[0].substr(2,2);
	}

/////////遮罩层函数/////////////
function showMaskDiv(WebPage,CompanyId){	//显示遮罩对话框
	//检查是否有选取记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				var NameTemp=e.name;
				var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
				if(e.checked && Name!=-1){
					UpdataIdX=UpdataIdX+1;
					break;
					} 
				}
			}
	//如果没有选记录
	if(UpdataIdX==0 || CompanyId==""){
		alert("没有选取记录或公司名称!");return false;
		}
	else{
		document.getElementById('divShadow').style.display='block';
		divPageMask.style.width = document.body.scrollWidth;
		divPageMask.style.height = document.body.scrollHeight>document.body.clientHeight?document.body.scrollHeight:document.body.clientHeight;
		document.getElementById('divPageMask').style.display='block';
		sOrhDiv(""+WebPage+"",CompanyId);
		}
	}

function closeMaskDiv(){	//隐藏遮罩对话框
	document.getElementById('divShadow').style.display='none';
	document.getElementById('divPageMask').style.display='none';
	}

//对话层的显示和隐藏:层的固定名称divInfo,目标页面,传递的参数
function sOrhDiv(WebPage,DeliveryValue){
	if(DeliveryValue!=""){			
		var url="../admin/"+WebPage+"_mask.php?DeliveryValue="+DeliveryValue; 
	　	//var show=eval("divInfo");
	　	var ajax=InitAjax(); 
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
	　		if(ajax.readyState==4){// && ajax.status ==200
				var BackData=ajax.responseText;					
				divInfo.innerHTML=BackData;
				}
			}
		ajax.send(null); 
		}
	}
/////////////////////////////////

//刷新页面
function RefreshPage(objWebPage){
	document.form1.action=objWebPage+".php";
	document.form1.submit();
	}
//查询返回
function ClearList(ListId){//清空多选框列表
	var The_Selectd = eval("window.document.form1."+ListId);
	for (loop=The_Selectd.options.length-1;loop>=0;loop--){
		The_Selectd.options[loop].selected=true;
		The_Selectd.remove(loop);
		}
	}
function s1ReBack(){
	var returnq="";
	var Message=""
	var j=1;
	var SearchNum=document.form1.SearchNum.value;
	for(var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			if(e.checked){
				if (j==1){
					returnq=e.value;j++;
					}
				else{
					returnq=returnq+"``"+e.value;j++;
					}
				if(SearchNum==1 && j>2){
					alert("只能选一条记录！");
					return false;
					}
				}//end if(e.checked)
			}//end if (e.type=="checkbox")
		}//end for(var i=0;i<form1.elements.length;i++)
	returnValue=returnq;
	this.close();
	}

//查询：到条件选择页面
function SearchToNext(Num){
	var tSearchPage=document.form1.tSearchPage.value;
	if(Num==2){
		document.form1.action=tSearchPage+"_s2.php";
		}
	else{
		document.form1.action="../model/subprogram/s3_model.php";
		}
	document.form1.submit();
	}

//操作时查询要操作的记录：如在新增时.参数：搜索页面，来自页面，返回记录个数:1只能1个，2可以多个
function SearchRecord(tSearchPage,fSearchPage,SearchNum,Action){
	var r=Math.random();
	var theType=event.srcElement.getAttribute('type');
	var theName=event.srcElement.getAttribute('name');	
	switch(theType){
		case "text":			//文本框
			var e=eval("document.form1."+theName);
			var BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+r+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum+"&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
			if(BackData){
				var CL=BackData.split("^^");
				e.value=CL[0];
				e.title=CL[1];
				}
		break;
		case "textarea":
			var e=eval("document.form1."+theName);
			var BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+r+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum+"&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
			if(BackData){
				var CL=BackData.split("^^");
				e.value=CL[0];
				e.title=CL[1];
				}
		break;
		case "select-multiple"://多选列表
			//其它参数：主要是类型限制
			var uTypeT=JobIdT=BranchIdT=KqSignT=MonthT="";			
			if(document.all("uType")!=null){
				uTypeT=document.getElementById('uType').value;
				uTypeT="&uType="+uTypeT;
				}
			if(document.all("JobId")!=null){
				var JobIdT=document.getElementById('JobId').value;
				JobIdT="&Jid="+JobIdT;
				}
			if(document.all("BranchId")!=null){
				var BranchIdT=document.getElementById('BranchId').value;
				BranchIdT="&Bid="+BranchIdT;
				}
			if(document.all("KqSign")!=null){
				var KqSignT=document.getElementById('KqSign').value;
				KqSignT="&Kid="+KqSignT;
				}
			if(document.all("newMonth")!=null){
				var MonthT=document.getElementById('newMonth').value;
				MonthT="&Month="+MonthT;
				}
			var BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+r+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum+"&Action="+Action+uTypeT+JobIdT+BranchIdT+MonthT+KqSignT,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
			//拆分BackData
			if(BackData){
				var The_Selectd = window.document.form1.ListId;
				var BL=BackData.split("``");
				var AddLength=The_Selectd.options.length;
				for(var i=0;i<BL.length;i++){
					var oldNum=0;
					var CL=BL[i].split("^^");
					for (loop=0;loop<AddLength;loop++){
						var oldTemp=The_Selectd.options[loop].value;
						if(CL[0]==oldTemp){
							oldNum=1;
							break;
							}
						}
					if(oldNum==1){
						alert("记录"+CL[1]+"已在列表,跳过继续！");
						}
					else{
						window.document.form1.ListId.options[document.form1.ListId.options.length]=new Option(CL[0]+' '+CL[1] ,CL[0]);
						}
					}
				}
			break;
		}//switch(theType)
	}

window.status='';
function SearchTo(toWebPage){
	document.form1.action="subprogram/"+toWebPage+".php";
	document.form1.submit();
	}

//简繁转换
function gbStr(){
return '啊阿埃挨哎唉哀皑癌蔼矮艾碍爱隘鞍氨安俺按暗岸胺案肮昂盎凹敖熬翱袄傲奥懊澳芭捌扒叭吧笆八疤巴拔跋靶把耙坝霸罢爸白柏百摆佰败拜稗斑班搬扳般颁板版扮拌伴瓣半办绊邦帮梆榜膀绑棒磅蚌镑傍谤苞胞包褒剥薄雹保堡饱宝抱报暴豹鲍爆杯碑悲卑北辈背贝钡倍狈备惫焙被奔苯本笨崩绷甭泵蹦迸逼鼻比鄙笔彼碧蓖蔽毕毙毖币庇痹闭敝弊必辟壁臂避陛鞭边编贬扁便变卞辨辩辫遍标彪膘表鳖憋别瘪彬斌濒滨宾摈兵冰柄丙秉饼炳病并玻菠播拨钵波博勃搏铂箔伯帛舶脖膊渤泊驳捕卜哺补埠不布步簿部怖擦猜裁材才财睬踩采彩菜蔡餐参蚕残惭惨灿苍舱仓沧藏操糙槽曹草厕策侧册测层蹭插叉茬茶查碴搽察岔差诧拆柴豺搀掺蝉馋谗缠铲产阐颤昌猖场尝常长偿肠厂敞畅唱倡超抄钞朝嘲潮巢吵炒车扯撤掣彻澈郴臣辰尘晨忱沈陈趁衬撑称城橙成呈乘程惩澄诚承逞骋秤吃痴持匙池迟弛驰耻齿侈尺赤翅斥炽充冲冲虫崇宠抽酬畴踌稠愁筹仇绸瞅丑臭初出橱厨躇锄雏滁除楚础储矗搐触处揣川穿椽传船喘串疮窗幢床闯创吹炊捶锤垂春椿醇唇淳纯蠢戳绰疵茨磁雌辞慈瓷词此刺赐次聪葱囱匆从丛凑粗醋簇促蹿篡窜摧崔催脆瘁粹淬翠村存寸磋撮搓措挫错搭达答瘩打大呆歹傣戴带殆代贷袋待逮怠耽担丹单郸掸胆旦氮但惮淡诞弹蛋当挡党荡档刀捣蹈倒岛祷导到稻悼道盗德得的蹬灯登等瞪凳邓堤低滴迪敌笛狄涤翟嫡抵底地蒂第帝弟递缔颠掂滇碘点典靛垫电佃甸店惦奠淀殿碉叼雕凋刁掉吊钓调跌爹碟蝶迭谍叠丁盯叮钉顶鼎锭定订丢东冬董懂动栋侗恫冻洞兜抖斗陡豆逗痘都督毒犊独读堵睹赌杜镀肚度渡妒端短锻段断缎堆兑队对墩吨蹲敦顿囤钝盾遁掇哆多夺垛躲朵跺舵剁惰堕蛾峨鹅俄额讹娥恶厄扼遏鄂饿恩而儿耳尔饵洱二贰发罚筏伐乏阀法珐藩帆番翻樊矾钒繁凡烦反返范贩犯饭泛坊芳方肪房防妨仿访纺放菲非啡飞肥匪诽吠肺废沸费芬酚吩氛分纷坟焚汾粉奋份忿愤粪丰封枫蜂峰锋风疯烽逢冯缝讽奉凤佛否夫敷肤孵扶拂辐幅氟符伏俘服浮涪福袱弗甫抚辅俯釜斧脯腑府腐赴副覆赋复傅付阜父腹负富讣附妇缚咐噶嘎该改概钙盖溉干甘杆柑竿肝赶感秆敢赣冈刚钢缸肛纲岗港杠篙皋高膏羔糕搞镐稿告哥歌搁戈鸽胳疙割革葛格蛤阁隔铬个各给根跟耕更庚羹埂耿梗工攻功恭龚供躬公宫弓巩汞拱贡共钩勾沟苟狗垢构购够辜菇咕箍估沽孤姑鼓古蛊骨谷股故顾固雇刮瓜剐寡挂褂乖拐怪棺关官冠观管馆罐惯灌贯光广逛瑰规圭硅归龟闺轨鬼诡癸桂柜跪贵刽辊滚棍锅郭国果裹过哈骸孩海氦亥害骇酣憨邯韩含涵寒函喊罕翰撼捍旱憾悍焊汗汉夯杭航壕嚎豪毫郝好耗号浩呵喝荷菏核禾和何合盒貉阂河涸赫褐鹤贺嘿黑痕很狠恨哼亨横衡恒轰哄烘虹鸿洪宏弘红喉侯猴吼厚候后呼乎忽瑚壶葫胡蝴狐糊湖弧虎唬护互沪户花哗华猾滑画划化话槐徊怀淮坏欢环桓还缓换患唤痪豢焕涣宦幻荒慌黄磺蝗簧皇凰惶煌晃幌恍谎灰挥辉徽恢蛔回毁悔慧卉惠晦贿秽会烩汇讳诲绘荤昏婚魂浑混豁活伙火获或惑霍货祸击圾基机畸稽积箕肌饥迹激讥鸡姬绩缉吉极棘辑籍集及急疾汲即嫉级挤几脊己蓟技冀季伎祭剂悸济寄寂计记既忌际妓继纪嘉枷夹佳家加荚颊贾甲钾假稼价架驾嫁歼监坚尖笺间煎兼肩艰奸缄茧检柬碱硷拣捡简俭剪减荐槛鉴践贱见键箭件健舰剑饯渐溅涧建僵姜将浆江疆蒋桨奖讲匠酱降蕉椒礁焦胶交郊浇骄娇嚼搅铰矫侥脚狡角饺缴绞剿教酵轿较叫窖揭接皆秸街阶截劫节茎睛晶鲸京惊精粳经井警景颈静境敬镜径痉靖竟竞净炯窘揪究纠玖韭久灸九酒厩救旧臼舅咎就疚鞠拘狙疽居驹菊局咀矩举沮聚拒据巨具距踞锯俱句惧炬剧捐鹃娟倦眷卷绢撅攫抉掘倔爵桔杰捷睫竭洁结解姐戒藉芥界借介疥诫届巾筋斤金今津襟紧锦仅谨进靳晋禁近烬浸尽劲荆兢觉决诀绝均菌钧军君峻俊竣浚郡骏喀咖卡咯开揩楷凯慨刊堪勘坎砍看康慷糠扛抗亢炕考拷烤靠坷苛柯棵磕颗科壳咳可渴克刻客课肯啃垦恳坑吭空恐孔控抠口扣寇枯哭窟苦酷库裤夸垮挎跨胯块筷侩快宽款匡筐狂框矿眶旷况亏盔岿窥葵奎魁傀馈愧溃坤昆捆困括扩廓阔垃拉喇蜡腊辣啦莱来赖蓝婪栏拦篮阑兰澜谰揽览懒缆烂滥琅榔狼廊郎朗浪捞劳牢老佬姥酪烙涝勒乐雷镭蕾磊累儡垒擂肋类泪棱楞冷厘梨犁黎篱狸离漓理李里鲤礼莉荔吏栗丽厉励砾历利傈例俐痢立粒沥隶力璃哩俩联莲连镰廉怜涟帘敛脸链恋炼练粮凉梁粱良两辆量晾亮谅撩聊僚疗燎寥辽潦了撂镣廖料列裂烈劣猎琳林磷霖临邻鳞淋凛赁吝拎玲菱零龄铃伶羚凌灵陵岭领另令溜琉榴硫馏留刘瘤流柳六龙聋咙笼窿隆垄拢陇楼娄搂篓漏陋芦卢颅庐炉掳卤虏鲁麓碌露路赂鹿潞禄录陆戮驴吕铝侣旅履屡缕虑氯律率滤绿峦挛孪滦卵乱掠略抡轮伦仑沦纶论萝螺罗逻锣箩骡裸落洛骆络妈麻玛码蚂马骂嘛吗埋买麦卖迈脉瞒馒蛮满蔓曼慢漫谩芒茫盲氓忙莽猫茅锚毛矛铆卯茂冒帽貌贸么玫枚梅酶霉煤没眉媒镁每美昧寐妹媚门闷们萌蒙檬盟锰猛梦孟眯醚靡糜迷谜弥米秘觅泌蜜密幂棉眠绵冕免勉娩缅面苗描瞄藐秒渺庙妙蔑灭民抿皿敏悯闽明螟鸣铭名命谬摸摹蘑模膜磨摩魔抹末莫墨默沫漠寞陌谋牟某拇牡亩姆母墓暮幕募慕木目睦牧穆拿哪呐钠那娜纳氖乃奶耐奈南男难囊挠脑恼闹淖呢馁内嫩能妮霓倪泥尼拟你匿腻逆溺蔫拈年碾撵捻念娘酿鸟尿捏聂孽啮镊镍涅您柠狞凝宁拧泞牛扭钮纽脓浓农弄奴努怒女暖虐疟挪懦糯诺哦欧鸥殴藕呕偶沤啪趴爬帕怕琶拍排牌徘湃派攀潘盘磐盼畔判叛乓庞旁耪胖抛咆刨炮袍跑泡呸胚培裴赔陪配佩沛喷盆砰抨烹澎彭蓬棚硼篷膨朋鹏捧碰坯砒霹批披劈琵毗啤脾疲皮匹痞僻屁譬篇偏片骗飘漂瓢票撇瞥拼频贫品聘乒坪苹萍平凭瓶评屏坡泼颇婆破魄迫粕剖扑铺仆莆葡菩蒲埔朴圃普浦谱曝瀑期欺栖戚妻七凄漆柒沏其棋奇歧畦崎脐齐旗祈祁骑起岂乞企启契砌器气迄弃汽泣讫掐洽牵扦钎铅千迁签仟谦乾黔钱钳前潜遣浅谴堑嵌欠歉枪呛腔羌墙蔷强抢橇锹敲悄桥瞧乔侨巧鞘撬翘峭俏窍切茄且怯窃钦侵亲秦琴勤芹擒禽寝沁青轻氢倾卿清擎晴氰情顷请庆琼穷秋丘邱球求囚酋泅趋区蛆曲躯屈驱渠取娶龋趣去圈颧权醛泉全痊拳犬券劝缺炔瘸却鹊榷确雀裙群然燃冉染瓤壤攘嚷让饶扰绕惹热壬仁人忍韧任认刃妊纫扔仍日戎茸蓉荣融熔溶容绒冗揉柔肉茹蠕儒孺如辱乳汝入褥软阮蕊瑞锐闰润若弱撒洒萨腮鳃塞赛三叁伞散桑嗓丧搔骚扫嫂瑟色涩森僧莎砂杀刹沙纱傻啥煞筛晒珊苫杉山删煽衫闪陕擅赡膳善汕扇缮墒伤商赏晌上尚裳梢捎稍烧芍勺韶少哨邵绍奢赊蛇舌舍赦摄射慑涉社设砷申呻伸身深娠绅神沈审婶甚肾慎渗声生甥牲升绳省盛剩胜圣师失狮施湿诗尸虱十石拾时什食蚀实识史矢使屎驶始式示士世柿事拭誓逝势是嗜噬适仕侍释饰氏市恃室视试收手首守寿授售受瘦兽蔬枢梳殊抒输叔舒淑疏书赎孰熟薯暑曙署蜀黍鼠属术述树束戍竖墅庶数漱恕刷耍摔衰甩帅栓拴霜双爽谁水睡税吮瞬顺舜说硕朔烁斯撕嘶思私司丝死肆寺嗣四伺似饲巳松耸怂颂送宋讼诵搜艘擞嗽苏酥俗素速粟僳塑溯宿诉肃酸蒜算虽隋随绥髓碎岁穗遂隧祟孙损笋蓑梭唆缩琐索锁所塌他它她塔獭挞蹋踏胎苔抬台泰酞太态汰坍摊贪瘫滩坛檀痰潭谭谈坦毯袒碳探叹炭汤塘搪堂棠膛唐糖倘躺淌趟烫掏涛滔绦萄桃逃淘陶讨套特藤腾疼誊梯剔踢锑提题蹄啼体替嚏惕涕剃屉天添填田甜恬舔腆挑条迢眺跳贴铁帖厅听烃汀廷停亭庭挺艇通桐酮瞳同铜彤童桶捅筒统痛偷投头透凸秃突图徒途涂屠土吐兔湍团推颓腿蜕褪退吞屯臀拖托脱鸵陀驮驼椭妥拓唾挖哇蛙洼娃瓦袜歪外豌弯湾玩顽丸烷完碗挽晚皖惋宛婉万腕汪王亡枉网往旺望忘妄威巍微危韦违桅围唯惟为潍维苇萎委伟伪尾纬未蔚味畏胃喂魏位渭谓尉慰卫瘟温蚊文闻纹吻稳紊问嗡翁瓮挝蜗涡窝我斡卧握沃巫呜钨乌污诬屋无芜梧吾吴毋武五捂午舞伍侮坞戊雾晤物勿务悟误昔熙析西硒矽晰嘻吸锡牺稀息希悉膝夕惜熄烯溪汐犀檄袭席习媳喜铣洗系隙戏细瞎虾匣霞辖暇峡侠狭下厦夏吓掀锨先仙鲜纤咸贤衔舷闲涎弦嫌显险现献县腺馅羡宪陷限线相厢镶香箱襄湘乡翔祥详想响享项巷橡像向象萧硝霄削哮嚣销消宵淆晓小孝校肖啸笑效楔些歇蝎鞋协挟携邪斜胁谐写械卸蟹懈泄泻谢屑薪芯锌欣辛新忻心信衅星腥猩惺兴刑型形邢行醒幸杏性姓兄凶胸匈汹雄熊休修羞朽嗅锈秀袖绣墟戌需虚嘘须徐许蓄酗叙旭序畜恤絮婿绪续轩喧宣悬旋玄选癣眩绚靴薛学穴雪血勋熏循旬询寻驯巡殉汛训讯逊迅压押鸦鸭呀丫芽牙蚜崖衙涯雅哑亚讶焉咽阉烟淹盐严研蜒岩延言颜阎炎沿奄掩眼衍演艳堰燕厌砚雁唁彦焰宴谚验殃央鸯秧杨扬佯疡羊洋阳氧仰痒养样漾邀腰妖瑶摇尧遥窑谣姚咬舀药要耀椰噎耶爷野冶也页掖业叶曳腋夜液一壹医揖铱依伊衣颐夷遗移仪胰疑沂宜姨彝椅蚁倚已乙矣以艺抑易邑屹亿役臆逸肄疫亦裔意毅忆义益溢诣议谊译异翼翌绎茵荫因殷音阴姻吟银淫寅饮尹引隐印英樱婴鹰应缨莹萤营荧蝇迎赢盈影颖硬映哟拥佣臃痈庸雍踊蛹咏泳涌永恿勇用幽优悠忧尤由邮铀犹油游酉有友右佑釉诱又幼迂淤于盂榆虞愚舆余俞逾鱼愉渝渔隅予娱雨与屿禹宇语羽玉域芋郁吁遇喻峪御愈欲狱育誉浴寓裕预豫驭鸳渊冤元垣袁原援辕园员圆猿源缘远苑愿怨院曰约越跃钥岳粤月悦阅耘云郧匀陨允运蕴酝晕韵孕匝砸杂栽哉灾宰载再在咱攒暂赞赃脏葬遭糟凿藻枣早澡蚤躁噪造皂灶燥责择则泽贼怎增憎曾赠扎喳渣札轧铡闸眨栅榨咋乍炸诈摘斋宅窄债寨瞻毡詹粘沾盏斩辗崭展蘸栈占战站湛绽樟章彰漳张掌涨杖丈帐账仗胀瘴障招昭找沼赵照罩兆肇召遮折哲蛰辙者锗蔗这浙珍斟真甄砧臻贞针侦枕疹诊震振镇阵蒸挣睁征狰争怔整拯正政帧症郑证芝枝支吱蜘知肢脂汁之织职直植殖执值侄址指止趾只旨纸志挚掷至致置帜峙制智秩稚质炙痔滞治窒中盅忠钟衷终种肿重仲众舟周州洲诌粥轴肘帚咒皱宙昼骤珠株蛛朱猪诸诛逐竹烛煮拄瞩嘱主著柱助蛀贮铸筑住注祝驻抓爪拽专砖转撰赚篆桩庄装妆撞壮状椎锥追赘坠缀谆准捉拙卓桌琢茁酌啄着灼浊兹咨资姿滋淄孜紫仔籽滓子自渍字鬃棕踪宗综总纵邹走奏揍租足卒族祖诅阻组钻纂嘴醉最罪尊遵昨左佐柞做作坐座';
}

function bigStr(){
return '啊阿埃挨哎唉哀皚癌藹矮艾礙愛隘鞍氨安俺按暗岸胺案肮昂盎凹敖熬翺襖傲奧懊澳芭捌扒叭吧笆八疤巴拔跋靶把耙壩霸罷爸白柏百擺佰敗拜稗斑班搬扳般頒板版扮拌伴瓣半辦絆邦幫梆榜膀綁棒磅蚌鎊傍謗苞胞包褒剝薄雹保堡飽寶抱報暴豹鮑爆杯碑悲卑北輩背貝鋇倍狽備憊焙被奔苯本笨崩繃甭泵蹦迸逼鼻比鄙筆彼碧蓖蔽畢斃毖幣庇痹閉敝弊必辟壁臂避陛鞭邊編貶扁便變卞辨辯辮遍標彪膘表鼈憋別癟彬斌瀕濱賓擯兵冰柄丙秉餅炳病並玻菠播撥缽波博勃搏鉑箔伯帛舶脖膊渤泊駁捕蔔哺補埠不布步簿部怖擦猜裁材才財睬踩采彩菜蔡餐參蠶殘慚慘燦蒼艙倉滄藏操糙槽曹草廁策側冊測層蹭插叉茬茶查碴搽察岔差詫拆柴豺攙摻蟬饞讒纏鏟産闡顫昌猖場嘗常長償腸廠敞暢唱倡超抄鈔朝嘲潮巢吵炒車扯撤掣徹澈郴臣辰塵晨忱沈陳趁襯撐稱城橙成呈乘程懲澄誠承逞騁秤吃癡持匙池遲弛馳恥齒侈尺赤翅斥熾充衝沖蟲崇寵抽酬疇躊稠愁籌仇綢瞅醜臭初出櫥廚躇鋤雛滁除楚礎儲矗搐觸處揣川穿椽傳船喘串瘡窗幢床闖創吹炊捶錘垂春椿醇唇淳純蠢戳綽疵茨磁雌辭慈瓷詞此刺賜次聰蔥囪匆從叢湊粗醋簇促躥篡竄摧崔催脆瘁粹淬翠村存寸磋撮搓措挫錯搭達答瘩打大呆歹傣戴帶殆代貸袋待逮怠耽擔丹單鄲撣膽旦氮但憚淡誕彈蛋當擋黨蕩檔刀搗蹈倒島禱導到稻悼道盜德得的蹬燈登等瞪凳鄧堤低滴迪敵笛狄滌翟嫡抵底地蒂第帝弟遞締顛掂滇碘點典靛墊電佃甸店惦奠澱殿碉叼雕凋刁掉吊釣調跌爹碟蝶叠諜疊丁盯叮釘頂鼎錠定訂丟東冬董懂動棟侗恫凍洞兜抖鬥陡豆逗痘都督毒犢獨讀堵睹賭杜鍍肚度渡妒端短鍛段斷緞堆兌隊對墩噸蹲敦頓囤鈍盾遁掇哆多奪垛躲朵跺舵剁惰墮蛾峨鵝俄額訛娥惡厄扼遏鄂餓恩而兒耳爾餌洱二貳發罰筏伐乏閥法琺藩帆番翻樊礬釩繁凡煩反返範販犯飯泛坊芳方肪房防妨仿訪紡放菲非啡飛肥匪誹吠肺廢沸費芬酚吩氛分紛墳焚汾粉奮份忿憤糞豐封楓蜂峰鋒風瘋烽逢馮縫諷奉鳳佛否夫敷膚孵扶拂輻幅氟符伏俘服浮涪福袱弗甫撫輔俯釜斧脯腑府腐赴副覆賦複傅付阜父腹負富訃附婦縛咐噶嘎該改概鈣蓋溉幹甘杆柑竿肝趕感稈敢贛岡剛鋼缸肛綱崗港杠篙臯高膏羔糕搞鎬稿告哥歌擱戈鴿胳疙割革葛格蛤閣隔鉻個各給根跟耕更庚羹埂耿梗工攻功恭龔供躬公宮弓鞏汞拱貢共鈎勾溝苟狗垢構購夠辜菇咕箍估沽孤姑鼓古蠱骨谷股故顧固雇刮瓜剮寡挂褂乖拐怪棺關官冠觀管館罐慣灌貫光廣逛瑰規圭矽歸龜閨軌鬼詭癸桂櫃跪貴劊輥滾棍鍋郭國果裹過哈骸孩海氦亥害駭酣憨邯韓含涵寒函喊罕翰撼捍旱憾悍焊汗漢夯杭航壕嚎豪毫郝好耗號浩呵喝荷菏核禾和何合盒貉閡河涸赫褐鶴賀嘿黑痕很狠恨哼亨橫衡恒轟哄烘虹鴻洪宏弘紅喉侯猴吼厚候後呼乎忽瑚壺葫胡蝴狐糊湖弧虎唬護互滬戶花嘩華猾滑畫劃化話槐徊懷淮壞歡環桓還緩換患喚瘓豢煥渙宦幻荒慌黃磺蝗簧皇凰惶煌晃幌恍謊灰揮輝徽恢蛔回毀悔慧卉惠晦賄穢會燴彙諱誨繪葷昏婚魂渾混豁活夥火獲或惑霍貨禍擊圾基機畸稽積箕肌饑迹激譏雞姬績緝吉極棘輯籍集及急疾汲即嫉級擠幾脊己薊技冀季伎祭劑悸濟寄寂計記既忌際妓繼紀嘉枷夾佳家加莢頰賈甲鉀假稼價架駕嫁殲監堅尖箋間煎兼肩艱奸緘繭檢柬堿鹼揀撿簡儉剪減薦檻鑒踐賤見鍵箭件健艦劍餞漸濺澗建僵姜將漿江疆蔣槳獎講匠醬降蕉椒礁焦膠交郊澆驕嬌嚼攪鉸矯僥腳狡角餃繳絞剿教酵轎較叫窖揭接皆稭街階截劫節莖睛晶鯨京驚精粳經井警景頸靜境敬鏡徑痙靖竟競淨炯窘揪究糾玖韭久灸九酒廄救舊臼舅咎就疚鞠拘狙疽居駒菊局咀矩舉沮聚拒據巨具距踞鋸俱句懼炬劇捐鵑娟倦眷卷絹撅攫抉掘倔爵桔傑捷睫竭潔結解姐戒藉芥界借介疥誡屆巾筋斤金今津襟緊錦僅謹進靳晉禁近燼浸盡勁荊兢覺決訣絕均菌鈞軍君峻俊竣浚郡駿喀咖卡咯開揩楷凱慨刊堪勘坎砍看康慷糠扛抗亢炕考拷烤靠坷苛柯棵磕顆科殼咳可渴克刻客課肯啃墾懇坑吭空恐孔控摳口扣寇枯哭窟苦酷庫褲誇垮挎跨胯塊筷儈快寬款匡筐狂框礦眶曠況虧盔巋窺葵奎魁傀饋愧潰坤昆捆困括擴廓闊垃拉喇蠟臘辣啦萊來賴藍婪欄攔籃闌蘭瀾讕攬覽懶纜爛濫琅榔狼廊郎朗浪撈勞牢老佬姥酪烙澇勒樂雷鐳蕾磊累儡壘擂肋類淚棱楞冷厘梨犁黎籬狸離漓理李裏鯉禮莉荔吏栗麗厲勵礫曆利傈例俐痢立粒瀝隸力璃哩倆聯蓮連鐮廉憐漣簾斂臉鏈戀煉練糧涼梁粱良兩輛量晾亮諒撩聊僚療燎寥遼潦了撂鐐廖料列裂烈劣獵琳林磷霖臨鄰鱗淋凜賃吝拎玲菱零齡鈴伶羚淩靈陵嶺領另令溜琉榴硫餾留劉瘤流柳六龍聾嚨籠窿隆壟攏隴樓婁摟簍漏陋蘆盧顱廬爐擄鹵虜魯麓碌露路賂鹿潞祿錄陸戮驢呂鋁侶旅履屢縷慮氯律率濾綠巒攣孿灤卵亂掠略掄輪倫侖淪綸論蘿螺羅邏鑼籮騾裸落洛駱絡媽麻瑪碼螞馬罵嘛嗎埋買麥賣邁脈瞞饅蠻滿蔓曼慢漫謾芒茫盲氓忙莽貓茅錨毛矛鉚卯茂冒帽貌貿麽玫枚梅酶黴煤沒眉媒鎂每美昧寐妹媚門悶們萌蒙檬盟錳猛夢孟眯醚靡糜迷謎彌米秘覓泌蜜密冪棉眠綿冕免勉娩緬面苗描瞄藐秒渺廟妙蔑滅民抿皿敏憫閩明螟鳴銘名命謬摸摹蘑模膜磨摩魔抹末莫墨默沫漠寞陌謀牟某拇牡畝姆母墓暮幕募慕木目睦牧穆拿哪呐鈉那娜納氖乃奶耐奈南男難囊撓腦惱鬧淖呢餒內嫩能妮霓倪泥尼擬你匿膩逆溺蔫拈年碾攆撚念娘釀鳥尿捏聶孽齧鑷鎳涅您檸獰凝甯擰濘牛扭鈕紐膿濃農弄奴努怒女暖虐瘧挪懦糯諾哦歐鷗毆藕嘔偶漚啪趴爬帕怕琶拍排牌徘湃派攀潘盤磐盼畔判叛乓龐旁耪胖抛咆刨炮袍跑泡呸胚培裴賠陪配佩沛噴盆砰抨烹澎彭蓬棚硼篷膨朋鵬捧碰坯砒霹批披劈琵毗啤脾疲皮匹痞僻屁譬篇偏片騙飄漂瓢票撇瞥拼頻貧品聘乒坪蘋萍平憑瓶評屏坡潑頗婆破魄迫粕剖撲鋪仆莆葡菩蒲埔樸圃普浦譜曝瀑期欺棲戚妻七淒漆柒沏其棋奇歧畦崎臍齊旗祈祁騎起豈乞企啓契砌器氣迄棄汽泣訖掐洽牽扡釺鉛千遷簽仟謙乾黔錢鉗前潛遣淺譴塹嵌欠歉槍嗆腔羌牆薔強搶橇鍬敲悄橋瞧喬僑巧鞘撬翹峭俏竅切茄且怯竊欽侵親秦琴勤芹擒禽寢沁青輕氫傾卿清擎晴氰情頃請慶瓊窮秋丘邱球求囚酋泅趨區蛆曲軀屈驅渠取娶齲趣去圈顴權醛泉全痊拳犬券勸缺炔瘸卻鵲榷確雀裙群然燃冉染瓤壤攘嚷讓饒擾繞惹熱壬仁人忍韌任認刃妊紉扔仍日戎茸蓉榮融熔溶容絨冗揉柔肉茹蠕儒孺如辱乳汝入褥軟阮蕊瑞銳閏潤若弱撒灑薩腮鰓塞賽三三傘散桑嗓喪搔騷掃嫂瑟色澀森僧莎砂殺刹沙紗傻啥煞篩曬珊苫杉山刪煽衫閃陝擅贍膳善汕扇繕墒傷商賞晌上尚裳梢捎稍燒芍勺韶少哨邵紹奢賒蛇舌舍赦攝射懾涉社設砷申呻伸身深娠紳神沈審嬸甚腎慎滲聲生甥牲升繩省盛剩勝聖師失獅施濕詩屍虱十石拾時什食蝕實識史矢使屎駛始式示士世柿事拭誓逝勢是嗜噬適仕侍釋飾氏市恃室視試收手首守壽授售受瘦獸蔬樞梳殊抒輸叔舒淑疏書贖孰熟薯暑曙署蜀黍鼠屬術述樹束戍豎墅庶數漱恕刷耍摔衰甩帥栓拴霜雙爽誰水睡稅吮瞬順舜說碩朔爍斯撕嘶思私司絲死肆寺嗣四伺似飼巳松聳慫頌送宋訟誦搜艘擻嗽蘇酥俗素速粟僳塑溯宿訴肅酸蒜算雖隋隨綏髓碎歲穗遂隧祟孫損筍蓑梭唆縮瑣索鎖所塌他它她塔獺撻蹋踏胎苔擡台泰酞太態汰坍攤貪癱灘壇檀痰潭譚談坦毯袒碳探歎炭湯塘搪堂棠膛唐糖倘躺淌趟燙掏濤滔縧萄桃逃淘陶討套特藤騰疼謄梯剔踢銻提題蹄啼體替嚏惕涕剃屜天添填田甜恬舔腆挑條迢眺跳貼鐵帖廳聽烴汀廷停亭庭挺艇通桐酮瞳同銅彤童桶捅筒統痛偷投頭透凸禿突圖徒途塗屠土吐兔湍團推頹腿蛻褪退吞屯臀拖托脫鴕陀馱駝橢妥拓唾挖哇蛙窪娃瓦襪歪外豌彎灣玩頑丸烷完碗挽晚皖惋宛婉萬腕汪王亡枉網往旺望忘妄威巍微危韋違桅圍唯惟爲濰維葦萎委偉僞尾緯未蔚味畏胃餵魏位渭謂尉慰衛瘟溫蚊文聞紋吻穩紊問嗡翁甕撾蝸渦窩我斡臥握沃巫嗚鎢烏汙誣屋無蕪梧吾吳毋武五捂午舞伍侮塢戊霧晤物勿務悟誤昔熙析西硒矽晰嘻吸錫犧稀息希悉膝夕惜熄烯溪汐犀檄襲席習媳喜銑洗系隙戲細瞎蝦匣霞轄暇峽俠狹下廈夏嚇掀鍁先仙鮮纖鹹賢銜舷閑涎弦嫌顯險現獻縣腺餡羨憲陷限線相廂鑲香箱襄湘鄉翔祥詳想響享項巷橡像向象蕭硝霄削哮囂銷消宵淆曉小孝校肖嘯笑效楔些歇蠍鞋協挾攜邪斜脅諧寫械卸蟹懈泄瀉謝屑薪芯鋅欣辛新忻心信釁星腥猩惺興刑型形邢行醒幸杏性姓兄凶胸匈洶雄熊休修羞朽嗅鏽秀袖繡墟戌需虛噓須徐許蓄酗敘旭序畜恤絮婿緒續軒喧宣懸旋玄選癬眩絢靴薛學穴雪血勳熏循旬詢尋馴巡殉汛訓訊遜迅壓押鴉鴨呀丫芽牙蚜崖衙涯雅啞亞訝焉咽閹煙淹鹽嚴研蜒岩延言顔閻炎沿奄掩眼衍演豔堰燕厭硯雁唁彥焰宴諺驗殃央鴦秧楊揚佯瘍羊洋陽氧仰癢養樣漾邀腰妖瑤搖堯遙窯謠姚咬舀藥要耀椰噎耶爺野冶也頁掖業葉曳腋夜液一壹醫揖銥依伊衣頤夷遺移儀胰疑沂宜姨彜椅蟻倚已乙矣以藝抑易邑屹億役臆逸肄疫亦裔意毅憶義益溢詣議誼譯異翼翌繹茵蔭因殷音陰姻吟銀淫寅飲尹引隱印英櫻嬰鷹應纓瑩螢營熒蠅迎贏盈影穎硬映喲擁傭臃癰庸雍踴蛹詠泳湧永恿勇用幽優悠憂尤由郵鈾猶油遊酉有友右佑釉誘又幼迂淤于盂榆虞愚輿余俞逾魚愉渝漁隅予娛雨與嶼禹宇語羽玉域芋郁籲遇喻峪禦愈欲獄育譽浴寓裕預豫馭鴛淵冤元垣袁原援轅園員圓猿源緣遠苑願怨院曰約越躍鑰嶽粵月悅閱耘雲鄖勻隕允運蘊醞暈韻孕匝砸雜栽哉災宰載再在咱攢暫贊贓髒葬遭糟鑿藻棗早澡蚤躁噪造皂竈燥責擇則澤賊怎增憎曾贈紮喳渣劄軋鍘閘眨柵榨咋乍炸詐摘齋宅窄債寨瞻氈詹粘沾盞斬輾嶄展蘸棧占戰站湛綻樟章彰漳張掌漲杖丈帳賬仗脹瘴障招昭找沼趙照罩兆肇召遮折哲蟄轍者鍺蔗這浙珍斟真甄砧臻貞針偵枕疹診震振鎮陣蒸掙睜征猙爭怔整拯正政幀症鄭證芝枝支吱蜘知肢脂汁之織職直植殖執值侄址指止趾只旨紙志摯擲至致置幟峙制智秩稚質炙痔滯治窒中盅忠鍾衷終種腫重仲衆舟周州洲謅粥軸肘帚咒皺宙晝驟珠株蛛朱豬諸誅逐竹燭煮拄矚囑主著柱助蛀貯鑄築住注祝駐抓爪拽專磚轉撰賺篆樁莊裝妝撞壯狀椎錐追贅墜綴諄准捉拙卓桌琢茁酌啄著灼濁茲咨資姿滋淄孜紫仔籽滓子自漬字鬃棕蹤宗綜總縱鄒走奏揍租足卒族祖詛阻組鑽纂嘴醉最罪尊遵昨左佐柞做作坐座';
}
function toBIG(tempStr){//转繁体
	var strS='';
	for(var i=0;i<tempStr.length;i++){
		if(gbStr().indexOf(tempStr.charAt(i))!=-1){
			strS+=bigStr().charAt(gbStr().indexOf(tempStr.charAt(i)));}
		else{
			strS+=tempStr.charAt(i);}
		}
	return strS; 
	}

function toGB(tempStr){//转简体
	var strS='';
	for(var i=0;i<tempStr.length;i++){
		if(bigStr().indexOf(tempStr.charAt(i))!=-1){
			strS+=gbStr().charAt(bigStr().indexOf(tempStr.charAt(i)));}
		else{
			strS+=tempStr.charAt(i);}
		}
	return strS;
	}

//重置页面，页数、分页设回默认值;也可指定分页方式
function ResetPage(obj){
	if(document.all("Estate")!=null && obj!="Estate"){//如果Estate存在,且由其它元素重置页面,则Estate也重置
		document.forms["form1"].elements["Estate"].value="";		
		}
	if(document.all("chooseMonth")!=null && obj!="chooseMonth"){
		document.forms["form1"].elements["chooseMonth"].value="";
		}
	if(document.all("Page")!=null){
		document.form1.Page.value=1;
		}
	if(document.all("Pagination")!=null){
		document.forms["form1"].elements["Pagination"].value="";
		}
	if(document.all("ProductType")!=null && obj!="ProductType"){
		document.forms["form1"].elements["ProductType"].value="";
		}
	document.form1.submit();
	}

//考勤日期检查:三天之内且非未来日期
function kqAllowDate(DateValue,LimitValue){
	//转yyyy/mm/dd
	var d1 = new Date(Date.parse(DateValue.replace(/-/g, '/')));
	var d2 = new Date();
	var dTemp=(Date.parse(d2) - Date.parse(d1))/86400000;
	if(dTemp<0){					//未来日期，一律不允许
		return false;
		}
	else{
		if(LimitValue==0 && dTemp>4){//非最高权限，且超出前三天范围
			 return false;
			}
		else{
			return true;
			}
		}
	}

//模似表单元素的单击事件
function SimulateOnClick(linkId, e){   
	var fireOnThis = document.getElementById(linkId)
    if (document.createEvent){
		var evObj = document.createEvent('MouseEvents');
        evObj.initEvent('click', true, false);
        fireOnThis.dispatchEvent(evObj);
		}
	else
		if(document.createEventObject){
			fireOnThis.fireEvent('onclick');
    	}
	}

//强制关闭窗口
function WindowClose(){
    if(document.all){
        if(parseFloat(window.navigator.appVersion.substr(window.navigator.appVersion.indexOf("MSIE")+5, 3)) < 5.5){
            var str  = '<object id=meizzMax classid="clsid:ADB880A6-D8FF-11CF-9377-00AA003B7A11">'
                str += '<param name="Command" value="Close"></object>';
            document.body.insertAdjacentHTML("beforeEnd", str);
            document.all.meizzClose.Click();
        }
        else{
            window.opener = "meizz";
            window.close();
        }
    }
    else    window.close();
}

//星期几
function CheckWeek(DateValue){
	var day = new Date(Date.parse(DateValue.replace(/-/g, '/'))); //将值格式化
    return day.getDay();
   }
   
function toTempValue(textValue){//onfocus='toTempValue(this.value)'
	document.form1.TempValue.value=textValue;
	}
//hhii格式检查
function hhiiCheck(str){
    var re = new RegExp("^(([01][0-9])|20|21|22|23):[012345][0-9]$");
	return re.test(str);
	}
function ymdCheck(str){
		var m, year, month, day;
		m = str.match(new RegExp("^((\\d{4})|(\\d{2}))([-./])(\\d{1,2})\\4(\\d{1,2})$"));
		if(m == null ) return false;
		day = m[6];
		month = m[5]*1;
		year =  (m[2].length == 4) ? m[2] : GetFullYear(parseInt(m[3], 10));
		if(!parseInt(month)) return false;
		month = month==0 ?12:month;
		var date = new Date(year, month-1, day);
        return (typeof(date) == "object" && year == date.getFullYear() && month == (date.getMonth()+1) && day == date.getDate());		
	}

//yyyymm格式检查    
function yyyymmCheck(str){
	var res = true;
	var StrL=str.length;
	if (StrL!=7){
			res=false;
			}
	else{
		var Today = new Date();
		var tY = Today.getFullYear();
		var tM = Today.getMonth()+1;//注意月份从下标0开始
		var re = new RegExp("^([0-9]{4})-([0-9]{1,2})$");//yyyy-mm
		var ar;
		
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
		}
	return res;
	}

//////mcright///////
//带动态的显示菜单
function menuShow(obj,maxh,obj2){
    if(obj.style.pixelHeight<maxh){
      obj.style.pixelHeight+=maxh/20;
      obj.filters.alpha.opacity+=5;
      obj2.background="images/title_bg_hide.gif";
      if(obj.style.pixelHeight==maxh/10)
        obj.style.display='block';
      myObj=obj;
      myMaxh=maxh;
      myObj2=obj2;
      setTimeout('menuShow(myObj,myMaxh,myObj2)','5');
    }
  }
//带动态的隐藏菜单
function menuHide(obj,maxh,obj2){
    if(obj.style.pixelHeight>0){
      if(obj.style.pixelHeight==maxh/20)
        obj.style.display='none';
      obj.style.pixelHeight-=maxh/20;
      obj.filters.alpha.opacity-=5;
      obj2.background="images/title_bg_show.gif";
      myObj=obj;
      myMaxh=maxh
      myObj2=obj2;
      setTimeout('menuHide(myObj,myMaxh,myObj2)','5');
    }
    else
      if(whichContinue)
        whichContinue.click();
  }

  function menuChange(obj,maxh,obj2)
  {
    if(obj.style.pixelHeight)
    {
      menuHide(obj,maxh,obj2);
      whichOpen='';
      whichcontinue='';
    }
    else
      if(whichOpen)
      {
        whichContinue=obj2;
        whichOpen.click();
      }
      else
      {
        menuShow(obj,maxh,obj2);
        whichOpen=obj2;
        whichContinue='';
      }
  }
	
//打开或关闭右框架
var KeyWord="open;"
function openOrclose(){
	if (KeyWord=="open"){ 
        KeyWord="close";
        parent.frmMain.cols='*,8';
		arrowhead.src="images/arrowhead1.gif";
    	} 
    else{ 
        KeyWord="open";
        parent.frmMain.cols='*,152';//top.frmMain.cols='*,138';
		arrowhead.src="images/arrowhead2.gif";
    	} 
	}	
///////////////

//取消分页
function CencelPage(){
	document.form1.aciotn="?Page=1";
	document.form1.submit();
	}
//返回浏览页面（从查询结果返回,是否分页）
function ToReadPage(nowWebPage,Sign){
	if(document.all("Page")!=null){
		document.form1.Page.value=1;
		}
	if(document.all("From")!=null){
		document.form1.From.value="";
		}
	if(document.all("Pagination")!=null){
		if(Sign!=0){
			document.forms["form1"].elements["Pagination"].value="1";
			}
		else{
			document.forms["form1"].elements["Pagination"].value="0";
			}
		//document.forms["form1"].elements["Pagination"].selectedIndex=1; 
		}
	document.form1.action=nowWebPage+".php";
	document.form1.submit();
	}
//yyyy-mm-dd格式检查
function yyyymmddCheck(str){
    var re = new RegExp("^([0-9]{4})[.-]{1}([0-9]{1,2})[.-]{1}([0-9]{1,2})$");
    var ar;
    var res = true;
    if ((ar = re.exec(str)) != null){
        var i;
        i = parseFloat(ar[3]);//01-12-2006:1
		
        // verify dd
        if (i <= 0 || i > 31){
            res = false;
        }
        i = parseFloat(ar[2]);
        // verify mm
        if (i <= 0 || i > 12){
            res = false;
        }
    }else{
        res = false;
    }
	return res;
	}

function mySearch(FromPage,ALType){
	document.form1.action=FromPage+"_select.php?Action=select&"+ALType;
	document.form1.submit();
	}

function unUseKey(){
	if((window.event.altKey)&&   
      ((window.event.keyCode==37)||       //屏蔽   Alt+   方向键   ←   
    	(window.event.keyCode==39))){     //屏蔽   Alt+   方向键   →   
        	alert("不准你使用ALT+方向键前进或后退网页！");   
            event.returnValue=false;   
            }   
	
	if((event.keyCode==116)||                                   //屏蔽   F5   刷新键   
       (event.keyCode==112)||                                   //屏蔽   F1   刷新键   
       (event.ctrlKey   &&   event.keyCode==82)){   //Ctrl   +   R   
       		event.keyCode=0;   
            event.returnValue=false;   
            }
	if(event.srcElement.type   !=   'text'   &&   event.srcElement.type   !=   'textarea'){
		if (event.keyCode==8){//屏蔽退格删除键 
			event.keyCode=0;   
			event.returnValue=false;  
			}
		}
	else{
		if(event.srcElement.readOnly==true){
			if (event.keyCode==8){//屏蔽退格删除键 
				event.keyCode=0;   
				event.returnValue=false;  
				}
			}
		}
	if((event.ctrlKey)&&(event.keyCode==78))       //屏蔽   Ctrl+n   
    	event.returnValue=false;   
    if((event.shiftKey)&&(event.keyCode==121))   //屏蔽   shift+F10   
    	event.returnValue=false;   
    if(window.event.srcElement.tagName   ==   "A"   &&   window.event.shiftKey)     
    	window.event.returnValue   =   false;     //屏蔽   shift   加鼠标左键新开一网页   
    if((window.event.altKey)&&(window.event.keyCode==115)){   //屏蔽Alt+F4   
    	window.showModelessDialog("about:blank","","dialogWidth:1px;dialogheight:1px");   
        return   false;
		}
	}   

//主明列表方式的更新
function upMainData(Page,Mid){
	document.form1.action=Page+".php?Mid="+Mid;
	document.form1.submit();
	}

//去除空格
String.prototype.Trim = function(){ 
	return this.replace(/(^\s*)|(\s*$)/g, ""); 
	}	

String.prototype.LTrim = function(){ 
	return this.replace(/(^\s*)/g, ""); 
	} 

String.prototype.RTrim = function(){ 
	return this.replace(/(\s*$)/g, ""); 
	} 
//去除全部空格
function funallTrim(TempStr){
	while (TempStr.indexOf(" ",0) != -1){
		TempStr=TempStr.replace(" ","")}
	return TempStr;
}
function ViewDoc(From,Record){
	win=window.open("OpenDocFile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
	}
//检查请款月份格式
function checkAskMonth(theMonth){
	var d= new Date(); 
	var nowYear=d.getYear();
	var nowMonth=d.getMonth();
	var Message="";
	var theMonthlength=theMonth.length;
	if(theMonthlength!=6){
		Message="请款月份格式不正确.";
		}
	else{
		var theYear=theMonth.substring(0,4);
		if(nowYear-theYear==0 || nowYear-theYear==1){
			var theMonth=theMonth.substring(4,6);
			if(theMonth>13){
				Message="月份格式不正确.";				
				}
			else{
				if(nowYear-theYear==0 && theMonth*1>nowMonth+1){
					Message="输入了未来月份！";
					}
				}
			}
		else{
			Message="年份格式或年份(只允许今年和上一年)不正确.";
			}
		}
	return Message;
	}

//转到更新保存页面:11月确认
var marked_row = new Array;
//19
function openUrl(url){ 
	var objxml=new ActiveXObject("Microsoft.XMLHttp") 
	objxml.open("GET",url,false); 
	objxml.send(); 
	//var retInfo=objxml.responseText; 使用此句系统错误1072896658 需要字符一致
	if (objxml.status=="200"){ 
		return "0"; //		return retInfo;
		} 
	else{ 
		return "-2"; 
	} 
} 

//18
function webpage_read(){
	}
function OpenOrLoad(d,f,Action,Type){//Action下载6
	var newnow = new Date().getTime();
	win=window.open("../admin/openorload.php?d="+d+"&f="+f+"&Action="+Action+"&Type="+Type,newnow,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
	//win.close(); 
	}
function OpenPhotos(d,f,s){
	var newnow = new Date().getTime();	
	win=window.open("openphotos.php?d="+d+"&f="+f+"&s="+s,newnow,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
	//win.close(); 
	}

//17
function View(From,Record){
	switch(From){
		case "openphoto":
			win=window.open("openphoto.php?photoid="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "clientspec":
			win=window.open("admin/openphoto.php?photoid="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "OpenDocFile":
			win=window.open("OpenDocFile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "ViewBulletin":
			win=window.open("../ViewBulletin.php?Id="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "casepicture":
			win=window.open("casepicturemove.php?photoid="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "clientdata":
			win=window.open("clientdata_view.php?CompanyId="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "ProviderData":
			win=window.open("ProviderData_view.php?CompanyId="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;		
		case "shippingdata":
			win=window.open("shippingdata_view.php?Id="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "teststandard":
			win=window.open("../admin/opendocfile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;			
		case "forwarddata":
			win=window.open("forwarddata_view.php?CompanyId="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "freightdata":
			win=window.open("freightdata_view.php?CompanyId="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "staff":
			win=window.open("staff_view.php?RecordId="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "supplierpo":
			win=window.open("supplierpo_view.php?RecordId="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "errorcase":
			win=window.open("../admin/opendocfile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "deliverybill":
			win=window.open("OpenDocFile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		default:
			win=window.open("OpenDocFile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
	}
}

function InitAjax(){ 
	var ajax=false;
	try{   
　　	ajax=new ActiveXObject("Msxml2.XMLHTTP");
		}
	catch(e){   
　　	try{   
　　　		ajax=new ActiveXObject("Microsoft.XMLHTTP");
			}
		catch(E){   
　　　		ajax=false;
			}   
　		} 
　	if(!ajax && typeof XMLHttpRequest!='undefined'){
		ajax=new XMLHttpRequest();
		}   
　	return ajax;
	}

//16	显示或隐藏配件采购单列表
function ShowOrHide(e,f,Order_Rows,theDate,RowId,FromT){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(theDate!=""){			
			var url="../admin/Sc_DayAnalyse_ajax.php?Date="+theDate+"&RowId="+RowId+"&FromT="+FromT; 
			//var url="../admin/Sc_DayAnalyse_ajax.php"; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					var DataArray=BackData.split("`");
					show.innerHTML=DataArray[0];
					//eval("ListTable"+RowId).rows[0].cells[4].innerText=DataArray[1];  
					/*
					switch(DataArray[1]){
						case "1"://白色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#FFFFFF";
							break;
						case "2"://黄色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#FFCC00";
							break;
						case "3"://绿色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#339900";
							break;
						}*/
					}
				}
			ajax.send(null); 
			}
		}
	}



//16	显示或隐藏配件采购单列表
function PubblicShowOrHide(e,f,Order_Rows,URL,theParam,RowId,FromT,FromDir){
	//alert(FromDir);
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(theParam!=""){	
		    if(FromDir !=null && FromDir!="" ){
				var url="../"+FromDir+"/"+URL+"?"+theParam+"&RowId="+RowId+"&FromT="+FromT; 
			}
			else {
				var url="../admin/"+URL+"?"+theParam+"&RowId="+RowId+"&FromT="+FromT; 
			}
			//alert (url);
			//var url="../admin/Sc_DayAnalyse_ajax.php"; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					var DataArray=BackData.split("`");
					show.innerHTML=DataArray[0];
					//eval("ListTable"+RowId).rows[0].cells[4].innerText=DataArray[1];  
					/*
					switch(DataArray[1]){
						case "1"://白色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#FFFFFF";
							break;
						case "2"://黄色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#FFCC00";
							break;
						case "3"://绿色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#339900";
							break;
						}*/
					}
				}
			ajax.send(null); 
			}
		}
	}



function sOrhOrder(e,f,Order_Rows,ShipId,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(ShipId!=""){			
			var url="../admin/ch_shiporder_ajax.php?ShipId="+ShipId+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;					
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}


//15 
//函数名：fucCheckNUM
//功能介绍：检查是否为数字
//参数说明：要检查的数字
//返回值：1为是数字，0为不是数字
//Objects:检查整数还是价格
function fucCheckNUM(NUM,Objects)
{
 var i,j,strTemp;
 if (Objects!="Price"){
 strTemp="0123456789";}
 else{
	strTemp=".0123456789"; 
	 }
 if ( NUM.length== 0)
  return 0
 for (i=0;i<NUM.length;i++)
 {
  j=strTemp.indexOf(NUM.charAt(i)); 
  if (j==-1)
  {
  //说明有字符不是数字
   return 0;
  }
 }
 //说明是数字
 return 1;
}

//14	焦点自动下移
function init(){ 
	document.onkeydown=keyDown ; 
	} 
function keyDown(e) {  
	if(event.keyCode==13) 
	{ 
		event.keyCode=9 ;
		} 
	}
//13	格式化数字,直接使用toFixed()
function FormatNumber(srcStr,nAfterDot){
　　var srcStr,nAfterDot;
　　var resultStr,nTen;
　　srcStr = ""+srcStr+"";
　　strLen = srcStr.length;
	//小数点位置
　　dotPos = srcStr.indexOf(".",0);
　　//-1为无小数点
	if (dotPos == -1){
		//整数则在后加.00
　　　　resultStr = srcStr+".";
　　　　for (i=0;i<nAfterDot;i++){
　　　　　　resultStr = resultStr+"0";
　　　　}
　　　　return resultStr;
　　}
　　else{
		// 如果小数点后的数字多于要保留的的位数
　　　　if ((strLen - dotPos - 1) >= nAfterDot){
　　　　　　nAfter = dotPos + nAfterDot + 1;
　　　　　　nTen =1;
　　　　　　for(j=0;j<nAfterDot;j++){
　　　　　　　　nTen = nTen*10;
　　　　　　}
　　　　　　resultStr = Math.round(parseFloat(srcStr)*nTen)/nTen;
			//三种结果：123.45   123.4   123
			//处理尾数是0的情况，要补0
		　　resultStr = ""+resultStr+"";
		　　strLen = resultStr.length;
			dotPos = resultStr.indexOf(".",0);
			//带一位小数123.4的补"0";带两位小数的略过
			if ((strLen - dotPos - 1)< nAfterDot){
				resultStr=resultStr+"0";
				}
			//不带小数123的加".00"
			if (dotPos=="-1"){
					resultStr=resultStr+".00";
					}
　　　　　　return resultStr;
　　　　}
　　　　else{
			// 如果小数点后的数字少于要保留的的位数
　　　　　　resultStr = srcStr;
　　　　　　for (i=0;i<(nAfterDot - strLen + dotPos + 1);i++){
　　　　　　　　resultStr = resultStr+"0";
　　　　　　}
　　　　　　return resultStr;
　　　　}
　　}
} 

//12 	Select_Records
function Select_Records(WebPage,From){
	location.href=WebPage+"_select.php?From="+From;
	}

//11	返回
function ComeBack(WebPage,ALType){//返回的页面和参数
	if (ALType!=""){
		ALType="?"+ALType;		
		}
	location.href=WebPage+".php"+ALType;
	}

//11	重新打开页面
function ReOpen(WebPage){//返回的页面和参数
	document.form1.action=WebPage+".php";
	document.form1.submit();
	}


//10 				当前行无素  行数	 鼠标动作		非选定色		鼠标经过色	选定色(不使用)   着色列数
function setPointer(theRow, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor,  theMerge ,theMergeUN){
    IE_FireFox();  //加入IE,fireFox兼容的函数 add by zx 2011032 IE_FOX.js
	var theCells = null;
    if ((thePointerColor == '' && theMarkColor == '')|| typeof(theRow.style) == 'undefined') {
        return false;
    	}

    // 2. Gets the current row and exits if the browser can't get it
    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
   		}
    else 
		if (typeof(theRow.cells) != 'undefined') {
        	theCells = theRow.cells;
    		}
    	else {
        return false;
    	}

    // 3. Gets the current color...
    var rowCellsCnt  = theCells.length;
	var lastCellsCnt =rowCellsCnt-1;
    var domDetect    = null;
    var currentColor = null;
    var newColor     = null;
    // 3.1 ... with DOM compatible browsers except Opera that does not return
    //         valid values with "getAttribute"
    if (typeof(window.opera) == 'undefined'
        && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[lastCellsCnt].getAttribute('bgcolor');
        domDetect    = true;
    }
    // 3.2 ... with other browsers
    else {
        currentColor = theCells[lastCellsCnt].style.backgroundColor;
        domDetect    = false;
    } // end 3

    // 4. Defines the new color
    // 4.1 Current color is the default one
    if (currentColor ==null || currentColor == ''
        || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {
        if (theAction == 'over' && thePointerColor != '') {
            newColor              = thePointerColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
			
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    // 4.1.2 Current color is the pointer one
    else if (currentColor.toLowerCase() == thePointerColor.toLowerCase()
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newColor              = theDefaultColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    // 4.1.3 Current color is the marker one
    else if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        if (theAction == 'click') {			
            newColor              = (thePointerColor != '')
                                  ? thePointerColor
                                  : theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                                  ? true
                                  : null;
        }
    } // end 4

    // 5. Sets the new color...
    if (newColor) {
        var c = null;
        // 5.1 ... with DOM compatible browsers except Opera
        if (domDetect) {
            for (c = 0; c < rowCellsCnt; c++) {
                //判断是否有并行，如果有，则首行前几列，不变色
				if(theMerge<rowCellsCnt){	//有并行
					var MergeCles=rowCellsCnt-theMerge;//并行的列
					if(c>=MergeCles && c!=theMergeUN){
						theCells[c].setAttribute('bgcolor', newColor, 0);
						}					
					}
				else{						//无并行
					theCells[c].setAttribute('bgcolor', newColor, 0);
					}
            } // end for
        }
        // 5.2 ... with other browsers
        else {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].style.backgroundColor = newColor;
            }
        }
    } // end 5

    return true;
} // end of the 'setPointer()' function

//9		下拉选框
function OutputSelects(Character,Default_Str,Length)
{
    var Split_Character=Character.split("~");
    var Length_Character=Split_Character.length;
    if(Split_Character[Length_Character-1]==""){
		Length_Character--;}
    var i=0;
	while(i<Length_Character){
    var j=1;
	ValueStr="";
	while(j<Length){
		ValueStr=ValueStr+Split_Character[i+j];
		j++
		}
	document.write("<option value=\""+ValueStr+"\""+((Default_Str==ValueStr)?" selected":"")+">"+Split_Character[i]);
       i=i+Length;
    	}
	}
//下拉列表OutputSelect(选项值字符串，默认项字符串)
function OutputSelect(ValueStr,Default_Str)
{
    var Split_ValueStr=ValueStr.split("~");
    var Length_ValueStr=Split_ValueStr.length;
    if(Split_ValueStr[Length_ValueStr-1]==""){
		Length_ValueStr--;}
    var i=0;
    while(i<Length_ValueStr){
        document.write("<option value=\""+Split_ValueStr[i]+"\""+((Default_Str==Split_ValueStr[i])?" selected":"")+">"+Split_ValueStr[i]);
        i++;
    	}
	}

//8		转其它更新功能页面
function Update_Other(From,ALType){
	document.form1.action=From+"_other.php?Action=other"+ALType;
	document.form1.submit();
	}

//7		转到相应的记录删除页面:删除多条记录,Ids是多条记录标志 WebPage:目标页面 From：目标页面的前导页面 ALType：目标页面分类过滤
function Del_ManyRecords(WebPage,From,ALType){
	// 检查是否没有选记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				var NameTemp=e.name;
				var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
				if(e.checked && Name!=-1){
					UpdataIdX=UpdataIdX+1;
					break;
					} 
				}
			}
	//如果没有选记录
	if(UpdataIdX==0){
		alert("没有选取记录!");
		}
	else{		
		var message=confirm("你确定要删除此记录吗？");
		if (message==true){
			//选项解锁为可用
			for (var i=0;i<form1.elements.length;i++){
				var e=form1.elements[i];
				var NameTemp=e.name;
				var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
				if (e.type=="checkbox" && Name!=-1){
					e.disabled=false;
					} 
				}
			if (From!=""){
				From="&From="+From;}
			document.form1.action="../admin/"+WebPage+"_del.php?Id=Ids"+From+ALType;
			document.form1.submit();
			}
		else{
			return false;
			}
		}
	}

//6		转到更新记录页面
function Update_OneRecord(WebPage,From,ALType){// 检查是否多选或没有选记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				var NameTemp=e.name;
				var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
				if(e.checked && Name!=-1){
					UpdataIdX=UpdataIdX+1;
					Id=e.value;
					} 
				}
			if (UpdataIdX>1){
				UpdataIdX=form1.elements.length;
				break;
				}
			}
	if (UpdataIdX!=1){
		alert("多选或未选记录,本操作只针对一条记录!");
		return (false);
		}
	else{
		document.form1.action=WebPage+"_update.php?Id="+Id+ALType;
		document.form1.submit();
		}
	}

//5		转新增记录页面
function Add_Record(WebPage,ALType){//WebPage:功能项目;ALType:参数
	document.form1.action=WebPage+"_add.php?"+ALType;
	document.form1.submit();
	}

//4		全选记录行												着色列数
function All_elects(theDefaultColor,thePointerColor,theMarkColor,theMerge,theMergeUN){
	IE_FireFox();  //加入IE,fireFox兼容的函数 add by zx 2011032 IE_FOX.js
	$Rows=document.form1.IdCount.value;
	var bgcolor=null;
	for(var i=1;i<=$Rows;i++){
		var theTable=eval("ListTable"+i);
		var bgcolor=theTable.rows[0].cells[4].getAttribute('bgcolor');
		//if(theTable.rows[0].cells[4].getAttribute('bgcolor').toUpperCase()!=theMarkColor){
		if(bgcolor==null || bgcolor=='' || bgcolor.toUpperCase()!=theMarkColor){	//modify by zx 20110321	
			if(document.all("checkid"+i)!=null){
				eval("document.form1.checkid"+i).checked=true;
				}
			//改变底色:	对象		行	事件		非选定色     鼠标经过色      选定色        着色列数
			chooseRow(theTable,i,"click",theDefaultColor,thePointerColor,theMarkColor,"",theMerge,theMergeUN);
			}
		}
	}
	
//3		反选记录行
function Instead_elects(theDefaultColor,thePointerColor,theMarkColor,theMerge,theMergeUN){
	IE_FireFox();  //加入IE,fireFox兼容的函数 add by zx 2011032 IE_FOX.js
	var Rows=document.form1.IdCount.value;
	for(var i=1;i<=Rows;i++){
		var theTable=eval("ListTable"+i);
		if(document.all("checkid"+i)!=null){
			if(eval("document.form1.checkid"+i).checked){
				eval("document.form1.checkid"+i).checked=false;
				}
			else{
				eval("document.form1.checkid"+i).checked=true;
				}
			}
		chooseRow(theTable,i,"click",theDefaultColor,thePointerColor,theMarkColor,"",theMerge,theMergeUN);
		}
	}
function ClickKeyCheck(theTable, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor, theFrom,theMerge,theMergeUN){
    IE_FireFox();  //加入IE,fireFox兼容的函数 add by zx 2011032 IE_FOX.js
    var thisevt= window.event;
	//alert (thisevt.button);  
	if ( thisevt.button == 1 || thisevt.button == 0) {
		//chooseRow(theTable, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor, theFrom,theMerge);
		chooseRow(theTable, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor, theFrom,theMerge,theMergeUN);
	}		
	/*
	if (event.button==1)
		chooseRow(theTable, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor, theFrom,theMerge,theMergeUN);
	*/	
}
//2	改变选定行状态	表格	  行号（表格数目） 鼠标动作		非选定色	  鼠标经过色		选定色		   着色列数
function chooseRow(theTable, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor, theFrom,theMerge,theMergeUN){
	//求和两种形式：1、列表框选择求和的列	2、直接指定求和的列
	//分解求和列数据,以，分开
	var MergeRows=document.form1.MergeRows.value;
	var sumCols=document.form1.sumCols.value;
	var SumCelLenght=0;
	if(sumCols!=""){
		var SumCelArray=sumCols.split(",");
		SumCelLenght=SumCelArray.length;
		}
	var theRow=eval("theTable").rows[0];
	var theCells = null;
    if ((thePointerColor == '' && theMarkColor == '')|| typeof(theRow.style) == 'undefined') {
        return false;
    	}
    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
    	}
    else if (typeof(theRow.cells) != 'undefined') {
        theCells = theRow.cells;
    }
    else {
        return false;
    }
    var rowCellsCnt  = theCells.length;
	var lastCellsCnt =rowCellsCnt-1;
    var domDetect    = null;
    var currentColor = null;
    var newColor     = null;
    if(typeof(window.opera) == 'undefined' && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[lastCellsCnt].getAttribute('bgcolor');//注意应该取最后一列的底色做判断
        domDetect    = true;
    	}
    else{
        currentColor = theCells[lastCellsCnt].style.backgroundColor;//注意应该取最后一列的底色做判断
        domDetect    = false;
    	}
	//if (currentColor == '' || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {//全选
	if (currentColor ==null || currentColor == '' || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {//全选
        newColor = theMarkColor;
        marked_row[theRowNum] = true; 
		//求和num.toFixed(2
		if (SumCelLenght>0){//在TableHead表的单元格读取和存储值
			//当前列
			var InfoSum="";
			for(var L=0;L<SumCelLenght;L++){
				var NowColValue=SumCelArray[L];								//当前列
				var NowColName=Number(NowColValue)+Number(MergeRows);					//列名所在列；用于区分有并行和无并行的情况
				var ColName=TableHead.rows[0].cells[NowColName].innerText; //列名
				
				ColName=br_replace(ColName);  //把空格，回车符，换行符去掉  add by zx 20110322
				
				var OldSumAmount=TableHead.rows[0].cells[NowColValue].data;
				if(typeof(OldSumAmount)=="undefined")
				OldSumAmount=0;
				//var sumAmount=Number(OldSumAmount)+Number(theRow.cells[NowColValue].innerText);	//累加值
				var CurrentValue=theRow.cells[NowColValue].innerText;
				CurrentValue=br_replace(CurrentValue); 
				CurrentValue=CurrentValue==""?0:CurrentValue;
				var sumAmount=Number(OldSumAmount)+Number(CurrentValue);	//累加值
				
				sumAmount=sumAmount.toFixed(2);
				TableHead.rows[0].cells[NowColValue].data=sumAmount;
				InfoSum+=ColName+"="+sumAmount+"   ";
				}
			   //window.status=InfoSum!=""?"选定行求和：   "+InfoSum:"";
			  showWindowStatus(InfoSum);
			}
		}
    else 
	if (currentColor.toLowerCase() == thePointerColor.toLowerCase()  && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
		
		if (theAction == 'click' && theMarkColor != '') {	//单击
			if((theFrom!="")&&(theFrom!="undefined")){
				//选定记录:底色为选定色
				if(document.all("checkid"+theRowNum)!=null){
					eval("document.form1.checkid"+theRowNum).checked=true;
					}
				eval(theFrom)(theRowNum,1);				
				//求和
				if (SumCelLenght>0){//在TableHead表的单元格读取和存储值
					//当前列
					var InfoSum="";
					for(var L=0;L<SumCelLenght;L++){
						var NowColValue=SumCelArray[L];								//当前列
						var NowColName=Number(NowColValue)+Number(MergeRows);					//列名所在列；用于区分有并行和无并行的情况
						var ColName=TableHead.rows[0].cells[NowColName].innerText; //列名
						ColName=br_replace(ColName);  //把空格，回车符，换行符去掉  add by zx 20110322
						//ColName=ColName.replace("<br>","");
						var OldSumAmount=TableHead.rows[0].cells[NowColValue].data;
						if(typeof(OldSumAmount)=="undefined")
						OldSumAmount=0;
						//var sumAmount=Number(OldSumAmount)+Number(theRow.cells[NowColValue].innerText);	//累加值
						var CurrentValue=theRow.cells[NowColValue].innerText;
						CurrentValue=br_replace(CurrentValue); 
						CurrentValue=CurrentValue==""?0:CurrentValue;
						var sumAmount=Number(OldSumAmount)+Number(CurrentValue);	//累加值
						
						sumAmount=sumAmount.toFixed(2);
						TableHead.rows[0].cells[NowColValue].data=sumAmount;
						InfoSum+=ColName+"="+sumAmount+"   ";
						}
					//window.status=InfoSum!=""?"选定行求和：   "+InfoSum:"";
					 showWindowStatus(InfoSum);
					}
				}
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        	}
		}
    else 
		if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        	if (theAction == 'click') {			
				if((theFrom!="")&&(theFrom!="undefined")){//取消选定：底色为非选定色，如果鼠标在其上，则为鼠标经过色
					if(document.all("checkid"+theRowNum)!=null){//如果存在则
						eval("document.form1.checkid"+theRowNum).checked=false;
						}
					eval(theFrom)(theRowNum,-1);
					if (SumCelLenght>0){//在TableHead表的单元格读取和存储值
						//当前列
						var InfoSum="";
						for(var L=0;L<SumCelLenght;L++){
							var NowColValue=SumCelArray[L];								//当前列
							var NowColName=Number(NowColValue)+Number(MergeRows);		//列名所在列；用于区分有并行和无并行的情况
							var ColName=TableHead.rows[0].cells[NowColName].innerText;  //列名
							ColName=br_replace(ColName);  //把空格，回车符，换行符去掉  add by zx 20110322		
							//ColName=ColName.replace("<br>","");
							var OldSumAmount=TableHead.rows[0].cells[NowColValue].data;
							if(typeof(OldSumAmount)=="undefined")
							OldSumAmount=0;
							//var sumAmount=Number(OldSumAmount)-Number(theRow.cells[NowColValue].innerText);	//累加值
							var CurrentValue=theRow.cells[NowColValue].innerText;
							CurrentValue=br_replace(CurrentValue); 
							CurrentValue=CurrentValue==""?0:CurrentValue;
							var sumAmount=Number(OldSumAmount)-Number(CurrentValue);	//累加值							
							
							sumAmount=sumAmount.toFixed(2);
							TableHead.rows[0].cells[NowColValue].data=sumAmount;
							InfoSum+=ColName+"="+sumAmount+"   ";
							}
						//window.status=InfoSum!=""?"选定行求和：   "+InfoSum:"";
					    showWindowStatus(InfoSum);
						}
					}
			else{		//反选非点击事件
				if (SumCelLenght>0){//在TableHead表的单元格读取和存储值
					//当前列
					var InfoSum="";
					for(var L=0;L<SumCelLenght;L++){
						var NowColValue=SumCelArray[L];								//当前列
						var NowColName=Number(NowColValue)+Number(MergeRows);		//列名所在列；用于区分有并行和无并行的情况
						var ColName=TableHead.rows[0].cells[NowColName].innerText; //列名
						
						ColName=br_replace(ColName);  //把空格，回车符，换行符去掉  add by zx 20110322
						//ColName=ColName.replace("<br>","");
						var OldSumAmount=TableHead.rows[0].cells[NowColValue].data;
						if(typeof(OldSumAmount)=="undefined")
						OldSumAmount=0;
						//var sumAmount=Number(OldSumAmount)-Number(theRow.cells[NowColValue].innerText);	//累加值
						var CurrentValue=theRow.cells[NowColValue].innerText;
						CurrentValue=br_replace(CurrentValue); 
						CurrentValue=CurrentValue==""?0:CurrentValue;
						var sumAmount=Number(OldSumAmount)-Number(CurrentValue);	//累加值						
						
						sumAmount=sumAmount.toFixed(2);
						TableHead.rows[0].cells[NowColValue].data=sumAmount;
						InfoSum+=ColName+"="+sumAmount+"   ";
						}
					//window.status=InfoSum!=""?"选定行求和：   "+InfoSum:"";
					 showWindowStatus(InfoSum);
					}
				}
           	newColor = (thePointerColor != '')? thePointerColor: theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])? true: null;
		  }
   	 }
	 
if (newColor) {
        var c = null;
        if (domDetect) {
			
		   for (c = 0; c < rowCellsCnt; c++) {
		   
                //判断是否有并行，如果有，则首行前几列，不变色
				if(theMerge<rowCellsCnt){	//有并行
					var MergeCles=rowCellsCnt-theMerge;//并行的列
					if(c>=MergeCles && c!=theMergeUN){
						theCells[c].setAttribute('bgcolor', newColor, 0);
						}					
					}
				else{						//无并行
					theCells[c].setAttribute('bgcolor', newColor, 0);
					}
					
				}
      		}
        else {
            for (c = 0; c < rowCellsCnt; c++) {
				theCells[c].style.backgroundColor = newColor;
            }
        }
    }
    return true;
} 

function showWindowStatus(InfoSum){
   
   var totalStatusBar=document.getElementById("TotalStatusBar");
   
   if(isSafari=navigator.userAgent.indexOf("MSIE")>0 || totalStatusBar=='undefined' || totalStatusBar == null){
		window.status=InfoSum!=""?"选定行求和：   "+InfoSum:""; 
	 }
	 else{
	    if (InfoSum!=""){
		    totalStatusBar.innerHTML="选定行求和：   "+InfoSum;
		    totalStatusBar.style.display="";
	    }else{
		    totalStatusBar.style.display="none";
	    } 
	 }
}


//1		转更新标记页面：可用/禁用；锁定/解锁
function Update_Sign(WebPage,Action,ALType){
		//需检查是否有选记录，如果没有则返回
	//选项解锁为可用
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		var NameTemp=e.name;
		var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
		if (e.type=="checkbox" && Name!=-1){
			e.disabled=false;
			} 
		}
	// 检查是否没有选记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
			if (e.type=="checkbox" && Name!=-1){
				if(e.checked){
					UpdataIdX=UpdataIdX+1;
					break;
					} 
				}
			}
	//如果没有选记录
	if(UpdataIdX==0){
		alert("没有选取记录");
		//选项改回禁用
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
			if (e.type=="checkbox" && Name!=-1){
				e.disabled=true;
				} 
			}
		return false;
		}
	else{		
		if (Action!=""){
			Action="Id="+Action;}
		document.form1.action="../admin/"+WebPage+"_Updated.php?"+Action+ALType;
		document.form1.submit();
		}
	}
//********************
//11月已确认的函数结束
//********************