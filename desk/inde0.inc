<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>登录页面</title>
<style>
	*{
		list-style:none;
		margin:0px;
		padding:0px;
		}
	.float{
			float:left;
			}
	.clear:after{
					height:0;
 					display:block;
 					clear:both;
 					content:"";
 					visibility:hidden;
					}
	.clear{display:inline-block;}
	.clear {display:block;}
	.trans{
				width:80px;
				height:80px;
				background:url(data:image/png;base64,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) repeat-x scroll 0 0;
			}
	.shadow{
	/*firefox*/
	-moz-box-shadow: 2px 2px 3px #555;
	/*chrome & safari*/
	-webkit-box-shadow: 2px 2px 3px #555;
	/*ie*/
  filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#555555');
  -ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#555555')";
	}

	body{
				 overflow:hidden;
				/*FireFox*/
				background-image: -moz-linear-gradient( top, #62ace4, #153469 );
				/*Chrome & Safari*/
				background-image: -webkit-gradient(linear , left bottom , left top , color-stop( 0, #153469 ) , color-stop( 1, #62ace4 ));
				/*IE6 & IE7*/
				filter: progid:DXImageTransform.Microsoft.gradient( GradientType= 0 , startColorstr = '#62ace4', endColorstr = '#153469' );
				/*IE8*/
				-ms-filter: "progid:DXImageTransform.Microsoft.gradient( GradientType = 0,startColorstr = '#62ace4', endColorstr = '#153469' )";
			}

	.sqButton{
						width:80px;
						height:80px;
						text-align:center;
						font-size:18px;
						color:#FFF;
						margin:20px;
						border:1px solid black;
						cursor:pointer;
						font-weight:700;
						text-shadow: 0 -1px 3px rgba(0,0,0,0.8);
	}
	.twoLine{
					padding-top:20px;
				}

	#main{
				width:100%;
			}
	#picOr{
				color:#fff;
				width:800px;
				height:200px;
				margin-left:auto;
				margin-right:auto;
				position:relative;
				top:70px;
				font-size:90px;
				text-shadow: 0 -1px 3px rgba(0,0,0,0.8);
			  }
	#chiCom{
					font-size:20px;
				}
	#selectLogin{
						width:100%;
						position:relative;
						top:80px;
						}
	#staff{
				background:#F16765;
			}
	#gongyi{
					background:#F90;
				}
	#customer{
						background:#89CA88;
					}
	#shengc{
					background:#FDCC66;
				}
	#qiege{
					background:#334DB2;
			  }
	#main{
					margin-left:auto;
					margin-right:auto;
			 }
	#navigator{
						width:650px;
						margin-left:auto;
						margin-right:auto;
					}
	#login{
				color:#fff;
				width:400px;
				position:relative;
				top:200px;
				left:200px;
		     }
</style>
<script type="text/javascript" src="tempJQ/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="tempJQ/jquery.corner.js"></script>
</head>

<body>
	<div id="main">
    	<div id="picOr">Ash Cloud<br />
        	<span id="chiCom">上海市研砼包装生产有限公司</span>
       </div>
        <div id="selectLogin" class="clear">
        	<ul class="clear" id="navigator">
            	<li class="float sqButton shadow" id="staff"><div class="trans"><div class="twoLine">内部员工登 录</div></div><input type="hidden" value="ewens;jiajia%@)"></li>
                <li class="float sqButton shadow" id="gongyi"><div class="trans twoLine">供应商<br />登 录</div><input type="hidden" value="hhx001;1qaz2wsx"></li>
                <li class="float sqButton shadow" id="customer"><div class="trans twoLine">客  户<br />登 录</div><input type="hidden" value="cel001;vulcan"></li>
                <li class="float sqButton shadow" id="shengc"><div class="trans twoLine">生产任务系 统</div><input type="hidden" value="ewens;jiajia%@)"></li>
                <li class="float sqButton shadow" id="qiege"><div class="trans twoLine">切割任务系 统</div><input type="hidden" value="ewens;jiajia%@)"></li>
            </ul>
        </div>
       <div>
       		<form method="post" id="goPage" >
            	<input type="hidden" name="Name" id="Name" />
                <input type="hidden" name="UserName" id="UserName"/>
                <input type="hidden" name="Password" id="Password" />
                <input type="hidden" name="F" id="F" value="1">
            </form>
       </div>
    </div>
</body>
</html>

<script type="text/javascript">
	$("body")
		.css({
					"width":"100%",
					"height":"1250px"
				})
	$("#staff").click(submitForm).corner();
	$("#gongyi").click(submitForm).corner();
	$("#customer").click(submitForm).corner();
	$("#shengc").click(submitForm).corner();
	$("#qiege").click(submitForm).corner();
	$(".trans").click(submitForm).corner();

	function submitForm()
		{
			var streamOrg = $(this).attr("Id");
			var stream = $("#"+streamOrg+" > input").val();
			stream = stream.split(";");
			var targetPhp;
			switch (streamOrg)
				{
					case "staff" :
					case "gongyi" :
					case "customer":
						targetPhp = "checklogin.php";
					break;
					case "shengc":
						targetPhp = "cjgl/checklogin.php";
					break;
					case "qiege":
						targetPhp = "cutManage/checklogin.php";
					break;
				}
			$("#goPage").attr("action",targetPhp);
			$("#Name").val(stream[0]);
			$("#UserName").val(stream[0]);
			$("#Password").val(stream[1]);
			$("#goPage").submit();
		}

</script>