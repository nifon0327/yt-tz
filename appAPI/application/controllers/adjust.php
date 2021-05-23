<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adjust extends MC_Controller {
	
		public function index() {
			$listArr = array();
			$records = array();
			
			$nums = 10;
			for ($i=0;$i<$nums;$i++) {
				$records[]=array(
					"tag"=>"adjust",
					"date1"=>"2016-03-18(sat)",
					"date2"=>"2016-03-27(fri)",
					"remark"=>"蔡圳开",
					"index"=>"".($i+1)
					
				);
			}


	
		    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$records);
		    $this->load->view('output_json',$data);
		}
		
		public function djRecord() {
			$listArr = array();
			$records = array();
			
			$nums = 10;
			for ($i=0;$i<$nums;$i++) {
				$records[]=array(
					"tag"=>"djRecord",
					"col1"=>"200",
					"col2"=>"12分钟前",
					"col3"=>"蔡圳",
					"index"=>"".($nums-$i)
					
				);
			}
 			$aSection = array("data"=>$records);
			$listArr[]=$aSection;
	
		    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$listArr);
		    $this->load->view('output_json',$data);
		}
	
		public function subList(){
		$listArr = array();
// 		sleep();
		$params = $this->input->post();
		
			$upTag =  element("upTag",$params,"--");
			
			$listTag = "--";
			switch ($upTag) {
				case "wtotal":$listTag = "week";break;
				case "week":$listTag = "dbl";break;
				case "dbl":$listTag = "order";break;
			}
			$numResign = 3;
		
			$timeTitle = date(":s");
			
		for ($i = 0; $i < $numResign; $i++) {
			//@"col2Sub",@"col2Right"
			$listArr[]=array("tag"=>"$listTag",
			"showArrow"=>$listTag == "order"?"0":"1",
			"hideLine"=>$i==0?"1":"0",
			$listTag == "order"?"nopen":"open"=>"0",
			"arrowImg"=>$listTag != "wtotal"?"UpAccessory_gray":"UpAccessory_blue",
			"hasCols"=>$i==1?"1":"0",
			"col1"=>"1,000",
			"col2"=>"1,110",
			"col2Sub"=>"999",
			"col2Right"=>"(4)",
			"col3"=>"891",
			"wtitle"=>"测试数据$timeTitle"."_$i",
			"hasTime"=>"1",
			"time"=>"13分钟前",
			"week"=>"42",
// 			"weekColor"=>"#FF0000",
			"topTitle"=>"slim-47($i)",
			"title"=>"测试数据$timeTitle"."_"."$i,／slim-47($i)",
			"col4"=>"¥2.81","weekDate"=>"2016-01-01",
			$i==1?"added":"qty"=>
			array(
				"isAttribute"=>"1",
				"attrDicts"=>array(
					array("text"=>"8","color"=>"#00aa00","fontSize"=>"12"),
					array("text"=>"/10","color"=>"#777777","fontSize"=>"10")
				)
			),
			"Id"=>"$i",
			"actions"=>array('打印|willPrint|#459fd1','补料|register|#00aa00','备注|remark|#ff0000','备料|multiBl|#000000'),
			"Process"=>$i==1?array(
			array("TypeId"=>"1","Qty"=>"916","GxQty"=>"916"),
			array("TypeId"=>"2","Qty"=>"916","GxQty"=>"900"),
			array("TypeId"=>"3","Qty"=>"916","GxQty"=>"9"),
			array("TypeId"=>"4","Qty"=>"916","GxQty"=>"1")
			):array()
			);
			
			if ($i==0)
			$listArr[]=array(
				"tag"=>"remarkNew",
				"headline"=>"测试测试:",
				"Record"=>"\n124321235235",
				"Recorder"=>"26分钟前 ｜ 谢雪梅",
				"bgcolor"=>"#FFFFFF",
				"left_sper"=>"15",
				"RID"=>"1"
			);
		}
		$listArr[$numResign]["deleteTag"] = $upTag;

	
		    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$listArr);
		    $this->load->view('output_json',$data);
	}
	public function menu(){
		$listArr = array();
		$params = $this->input->post();
		
		
			$numResign = 7;
			
		$titls = 	array("开料","皮套A","皮套B","贴钻",
		"丝印","移印","镭雕");
		$imgs = array("sc_kailiao","sc_pitaoA","sc_pitaoB","sc_tiezuan",
		
		"sc_siyin","sc_yiyin","sc_leidiao");
		for ($i = 0; $i < $numResign; $i++) {
			$listArr[]=array("CellType"=>"1",
			"headImage"=>$imgs[$i],
			"title"=>$titls[$i],
			"selected"=>"1",
			"Id"=>"$i"
			);
		}

	
		    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$listArr);
		    $this->load->view('output_json',$data);
	}
	
	public function top_seg() {
		$listArr = array();
		$params = $this->input->post();
		
		
			$numResign = 7;
			
		$titls = 	array("开料","皮套A","皮套B","贴钻",
		"丝印","移印","镭雕");
		$imgs = array("sc_kailiao","sc_pitaoA","sc_pitaoB","sc_tiezuan",
		
		"sc_siyin","sc_yiyin","sc_leidiao");
		for ($i = 0; $i < $numResign; $i++) {
			$img = $imgs[$i];
			$img_1 = $img."_s_1";
			$img_0 = $img."_s_0";
			$listArr[]=array(
			"img_0"=>"$img_0",
			"img_1"=>"$img_1",
			"title"=>$titls[$i],
			"Id"=>"$i",
			"cellType"=>""
			);
		}

	
		    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$listArr);
		    $this->load->view('output_json',$data);

	}
	public function top_top() {
		$listArr = array();
		$params = $this->input->post();
		
		
			$numResign = 7;
			
		$titls = 	array("开料","皮套A","皮套B","贴钻",
		"丝印","移印","镭雕");
		$imgs = array("10005","10009","11965","10019",
		
		"11093","12102","11010");
		for ($i = 0; $i < $numResign; $i++) {
			$img = $imgs[$i];
			$img_1 = $img."";
			$img_0 = $img."_s_0";
			

			
			$listArr[]=array(
			"img_0"=>"$img_0",
			"personImg"=> "http://www.middlecloud.com/download/staffPhoto/P"."$img_1".".png",
			"name"=>"姓名".($i+1),
			"Id"=>"$i",
			"maxNum"=>"88",
			"chatValues"=>
			$i==2?
			array(
				array("num"=>"88","color"=>"#00CC00"),
				array("num"=>"18","color"=>"#459fd1"),
				array("num"=>"7","color"=>"#d2691e")
				):
				($i==1?
			array(
				array("num"=>"20","color"=>"#00CC00"),
				array("num"=>"30","color"=>"#459fd1"),
				array("num"=>"15","color"=>"#d2691e")
				):
			array(
				array("num"=>"2","color"=>"#00CC00"),
				array("num"=>"35","color"=>"#459fd1"),
				array("num"=>"15","color"=>"#d2691e"),
				array("num"=>"5","color"=>"#ff0000"),
				array("num"=>"12","color"=>"#00aa00"),
				array("num"=>"5","color"=>"#123499"),
				array("num"=>"15","color"=>"#908800"),
				array("num"=>"5","color"=>"#ff0000"),
				array("num"=>"12","color"=>"#00aa00"),
				array("num"=>"5","color"=>"#123499"),
				array("num"=>"20","color"=>"#00CC00"),
				array("num"=>"30","color"=>"#459fd1"),
				array("num"=>"15","color"=>"#d2691e")
			))
			);
		}

	
		    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$listArr);
		    $this->load->view('output_json',$data);

	}
	
	public function segment() 
	{
		$listArr = array();
		$params = $this->input->post();
		
		$segmentIndex = intval( element("segmentIndex",$params,-1));
		$tagOfIndex = "dbl";
		$allTags = array("week","order","dbl");
		if ($segmentIndex >= 0) {
			$tagOfIndex = $allTags[$segmentIndex];
				$numResign = 2+$segmentIndex;
		for ($i = 0; $i < $numResign; $i++) {
			$listArr[]=array("tag"=>"$tagOfIndex",
			"showArrow"=>"1",
			"arrowImg"=>"UpAccessory_blue",
			"open"=>"0",
			"week"=>"53",
			"wtitle"=>"12/28-01/03",
			"col1"=>"2015-1$i",
			"col2"=>"1,000",
			"col2Right"=>"(12)",
			"col2Sub"=>"900",
			"col3"=>"100(12)","isTotal"=>"0");
		}
		
		
		} else {
				$numResign = 1;
		for ($i = 0; $i < $numResign; $i++) {
			$listArr[]=array("tag"=>"week");
		}
		
			$numResign = 2;
		for ($i = 0; $i < $numResign; $i++) {
			$listArr[]=array("tag"=>"order");
		}
			$numResign = 1;
		for ($i = 0; $i < $numResign; $i++) {
			$listArr[]=array("tag"=>"dbl");
		}
		
		
		}
	
		    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$listArr);
		    $this->load->view('output_json',$data);
		
		
	}
	
	public function main() 
	{
		
		// 		find 出差 
		$params = $this->input->post();
		
		$readTypes = element("types",$params,"all");
		$top_seg = element("top_seg",$params,"");
		
		$Types = explode(",", $readTypes);
		$numsOfTypes = 0;
		$titls = 	array("开料","皮套A","皮套B","贴钻",
		"丝印","移印","镭雕");
			$imgs = array("sc_kailiao","sc_pitaoA","sc_pitaoB","sc_tiezuan",
		
		"sc_siyin","sc_yiyin","sc_leidiao");
		if ($readTypes == "all") {
			$numsOfTypes = 7;
		} else {
			$numsOfTypes = count($Types);
		}
		$sectionList = array();
		$sectionList[]=array('hidden'=>'');
		// my apple watch came so soon we never wantted to user 
		$actions = array('备注|remark|#459fd1','登记|register|#00aa00');
		$firstOne = 0;
		if ($top_seg != "" && $readTypes !="all") {
			$firstOne = $top_seg;$numsOfTypes = $top_seg+1;
			$readTypes = "all";
			
		}
		for ($i = $firstOne; $i < $numsOfTypes; $i++) {
			$fixIndex = ($readTypes == "all"?$i:$Types[$i]);
			$sectionList[]=array(
			'data'=>array(),'hidden'=>'0',
			'indi'=>"-1",
			'tag'=>"nosc",
			"method"=>"segment",
			"weekDate"=>"2016-01-01",
			"name"=>"蔡圳",
			"title"=>$titls[$fixIndex],
			"titleImg"=>$imgs[$fixIndex],
			"number"=>"11965",
			"subtitle"=>"4人｜5天",
			"amount"=>array(
			array("290,909","#459fd1","24"),
			array("/12,890,900","#000000","11"),
			),
			"value1"=>"0,000",
			"value2"=>"0,000",
			"value3"=>"0,000",
			"monthValue"=>array(
			array("¥698,909","#ff0000","10"),
			array("/¥714,900","#bbbbbb","10"),
			),
			
		    "dayValue"=>array(
			array("¥28,909","#00aa00","10"),
			array("/¥14,900","#bbbbbb","10"),
			),
			
			"chartValue"=>array(
				array("75","#00cc00","27","","regular"),
				array("%","#00cc00","10")
			),
			"allPercents"=>array("0.3","0.925","1","1"),
			"per1"=>array(array("90","#459fd1","17"),array("%","#459fd1","10")),
			"per2"=>array(array("5","#459fd1","17"),array("%","#459fd1","10")),
			"per3"=>array(array("88","#00cc00","17"),array("%","#00cc00","10")),
			"per4"=>array(array("5","#fd0300","17"),array("%","#fd0300","10")),
			"per1Qty"=>"618,290,988",
			"per2Qty"=>"8,290,988",
			"per3Qty"=>"18,290,988",
			"per4Qty"=>"8,290,988",
			"pieValue"=>
			array(
				array("value"=>"90","color"=>"#459fd1"),
				array("value"=>"0","color"=>"clear")
			),
			"pieValue2"=>array(
				array("value"=>"175","color"=>"#00cc00"),
				array("value"=>"25","color"=>"clear")
			)
			
			
			);

		}
		
		/*
				
		$sectionList = array();
		$sectionList[]=array('hidden'=>'');
		// my apple watch came so soon we never wantted to user 
		$actions = array('备注|remark|#459fd1','登记|register|#00aa00');
		
		$numChuchai = 2;
		if ($numChuchai > 0) {
			
			$rowList = array();
			
			$headDict = array('Title'=>array('Text'=>"Biz-travel"),
							  'Col2'=>array('Text'=>"$numChuchai"));
			
			for ($i = 0; $i < $numChuchai; $i++) {
				$name = 'John KangNa '."$i";

				$rowList[]=array('tag'=>"order",'name'=>"$name",'job'=>"IOS Engineer",'desc'=>"08/12 15:00 ~",'actions'=>$actions,'added'=>"Buy something.(drive by himself)");
			}
			//@[@"name",@"subtitle",@"amount",@"value1",@"value2",@"value3"];
			$sectionList[]=array('data'=>$rowList,'hidden'=>'0','listnum'=>"$numChuchai",'indi'=>"-1",'tag'=>'scing',
			"method"=>"segment",
			"name"=>"蔡圳",
			"title"=>"开料",
			"number"=>"11965",
			"subtitle"=>"4人｜5天",
			"amount"=>array(
			array("290,909","#459fd1","24"),
			array("/12,890,900","#000000","11"),
			),
			"value1"=>"--",
			"value2"=>"--",
			"value3"=>"--",
			"monthValue"=>array(
			array("¥698,909","#ff0000","10"),
			array("/¥714,900","#bbbbbb","10"),
			),
			
			"dayValue"=>array(
			array("¥28,909","#00aa00","10"),
			array("/¥14,900","#bbbbbb","10"),
			),
			
			"chartValue"=>array(
				array("75","#00cc00","27","","regular"),
				array("%","#00cc00","10")
			),
			
			"pieValue"=>array(
				array("value"=>"90","color"=>"#459fd1"),
				array("value"=>"0","color"=>"clear")
			),
			"pieValue2"=>array(
				array("value"=>"175","color"=>"#00cc00"),
				array("value"=>"25","color"=>"clear")
			)
			
			
			);
		}
		
		
		$numqingjia = 1;
		if ($numqingjia > 0) {
			
			$rowList = array();
				
			$headDict = array('Title'=>array('Text'=>"Biz-travel"),
							  'Col2'=>array('Text'=>"$numqingjia"));
			for ($i = 0; $i < $numqingjia; $i++) {
				$name = 'John Litta '."$i";

				$rowList[]=array('tag'=>"week",'name'=>"$name",'actions'=>$actions,'job'=>"Net Engineer",'desc'=>"08/12 08:00 ~ 08/12 17:00",'added'=>"Go hospital.",);
			}
				$sectionList[]=array('data'=>$rowList,'hidden'=>'0','listnum'=>"$numChuchai",'indi'=>"-1",'tag'=>'nosc',
			"method"=>"segment",
			"name"=>"蔡圳",
			"title"=>"开料",
			"number"=>"11965",
			"subtitle"=>"4人｜5天",
			"amount"=>array(
			array("290,909","#ff0000","20"),
			array("/12,890,900","#000000","20"),
			),
			"value1"=>"--",
			"value2"=>"--",
			"value3"=>"--",
			"monthValue"=>"¥194,980,003"
			
			);
			//like we 
 
		}
		
		
		$numResign = 100;
		if ($numResign > 0) {
			
			$rowList = array();
				$headDict = array('Title'=>array('Text'=>"Biz-travel"),
							  'Col2'=>array('Text'=>"$numResign"));
			for ($i = 0; $i < $numResign; $i++) {
				$name = 'John Lee '."$i";
				$rowList[]=array('tag'=>"dbl",'name'=>"$name",'actions'=>$actions,'job'=>"Slime QC",'desc'=>"14/11/13 ~ 15/08/12",'added'=>"Normal Resignation, back home.");
			}
			$sectionList[]=array('data'=>$rowList,'hidden'=>'0','listnum'=>"$numqingjia",'type'=>"1",'tag'=>'nosc','head'=>$headDict);

	$sectionList[]=array('data'=>$rowList,'hidden'=>'0','listnum'=>"$numqingjia",'type'=>"1",'tag'=>'nosc','head'=>$headDict);

	$sectionList[]=array('data'=>$rowList,'hidden'=>'0','listnum'=>"$numqingjia",'type'=>"1",'tag'=>'nosc','head'=>$headDict);

	$sectionList[]=array('data'=>$rowList,'hidden'=>'0','listnum'=>"$numqingjia",'type'=>"1",'tag'=>'nosc','head'=>$headDict);

	$sectionList[]=array('data'=>$rowList,'hidden'=>'0','listnum'=>"$numqingjia",'type'=>"1",'tag'=>'nosc','head'=>$headDict);

 
		}
		

		*/
			
		    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>3,'rows'=>$sectionList);
		    $this->load->view('output_json',$data);
	}
	
	
	// : audit   attend   leave   travel 
	
	public function audit() {
// 		find 出差 
		$sectionList = array();
		// my apple watch came so soon we never wantted to user 
		$actions = array('Refuse|#FF0000','Approve|#0022FF');
		
		$numChuchai = 2;
		if ($numChuchai > 0) {
			
			$rowList = array();
			
			$headDict = array('Title'=>array('Text'=>"Biz-travel"),
							  'Col2'=>array('Text'=>"$numChuchai"));
			
			for ($i = 0; $i < $numChuchai; $i++) {
				$name = 'John KangNa '."$i";

				$rowList[]=array('tag'=>"attend",'name'=>"$name",'job'=>"IOS Engineer",'desc'=>"08/12 15:00 ~",'actions'=>$actions,'added'=>"Buy something.(drive by himself)");
			}
			$sectionList[]=array('list'=>$rowList,'hidden'=>'0','listnum'=>"$numChuchai",'type'=>"1",'view'=>'1','head'=>$headDict);
		}
		
		
		$numqingjia = 1;
		if ($numqingjia > 0) {
			
			$rowList = array();
				
			$headDict = array('Title'=>array('Text'=>"Biz-travel"),
							  'Col2'=>array('Text'=>"$numqingjia"));
			for ($i = 0; $i < $numqingjia; $i++) {
				$name = 'John Litta '."$i";

				$rowList[]=array('tag'=>"attend",'name'=>"$name",'actions'=>$actions,'job'=>"Net Engineer",'desc'=>"08/12 08:00 ~ 08/12 17:00",'added'=>"Go hospital.",);
			}
			$sectionList[]=array('list'=>$rowList,'hidden'=>'0','listnum'=>"$numqingjia",'type'=>"1",'view'=>'1','head'=>$headDict);
			//like we 
 
		}
		
		
		$numResign = 100;
		if ($numResign > 0) {
			
			$rowList = array();
				$headDict = array('Title'=>array('Text'=>"Biz-travel"),
							  'Col2'=>array('Text'=>"$numResign"));
			for ($i = 0; $i < $numResign; $i++) {
				$name = 'John Lee '."$i";
				$rowList[]=array('tag'=>"attend",'name'=>"$name",'actions'=>$actions,'job'=>"Slime QC",'desc'=>"14/11/13 ~ 15/08/12",'added'=>"Normal Resignation, back home.");
			}
			$sectionList[]=array('list'=>$rowList,'hidden'=>'0','listnum'=>"$numqingjia",'type'=>"1",'view'=>'1','head'=>$headDict);

 
		}
		
		
		    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>3,'rows'=>$sectionList);
		    $this->load->view('output_json',$data);
	}
	

		public function attend() {
// 		find 出差 
		$sectionList = array();
		// my apple watch came so soon we never wantted to user 
		$actions = array('Edit|#000000');
		
		$numChuchai = 20;
		if ($numChuchai > 0) {
			
			$rowList = array();
			
			for ($i = 0; $i < $numChuchai; $i++) {
				$name = 'John KangNa '."$i";

				$rowList[]=array('tag'=>"attend",'name'=>"$name",'job'=>"IOS Engineer",'desc'=>"08/12 15:00 ~",'actions'=>$i == 2 ? $actions : array(),'added'=>"Buy something.(drive by himself)");
			}
			$sectionList[]=array('list'=>$rowList,'listhide'=>'0','listnum'=>"$numChuchai",'title'=>"Biz-travel");
		}
		
				
		
		    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>3,'rows'=>$sectionList);
		    $this->load->view('output_json',$data);
	}
	
	
	
	
}