<?php
class DbConnect{
    public $conn;

    public function __construct(){
        $config = parse_ini_file("config.ini");
        $this->conn = new mysqli(
            $config["resources.database.dev.hostname"],
            $config["resources.database.dev.username"],
            $config["resources.database.dev.password"],
            $config["resources.database.dev.database"],
            $config["resources.database.dev.port"]);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
	//方法重载
	public function __call($name,$arguments){
		ret( 0 , "action方法不存在");
    }
	//遍历
    public function format($sql){
        $res = $this->conn->query($sql);
        $result = array();
        if($res){
            while($row = $res->fetch_assoc()){
                $result[] = $row;
            }
            return $result;
        }else{
            return null;
        }
    }
    //产线列表
    public function productionLine($param){
        $sql = "select id,name from workshopdata order by Id";
        ret( 1 , '成功' , $this->format($sql));
    }
	//生产过程检验入口页面 确定
	public function confirm($param){
		$recordNumber = $param['recordNumber'] ;
		$product = $param['product'] ;
		$productionLine = $param['productionLine'] ;
		$sql = "select * from production_test_record where record_number=$recordNumber";
		$row = $this->format($sql);
		if($row){
			$exist = 1;
			$id = intval($row[0]['id']);
		}else{
			$exist = 0;
			/*
			$sql = "select companyid from trade_object where forshort='$item'";
			$row = $this->format($sql);
			if($row){
				$company_id = $row[0]['companyid'];
			}else{
				ret( 0 , "item failed 产品名称不正确，请重新输入！" );
				return ;
			}
			$sql = "insert into production_test_record (record_number,company_id,production_line) values ($recordNumber,$company_id,$productionLine)";
			$res = $this->conn->query($sql);
			if($res){
				$id = $this->conn->insert_id;
			}else{
				ret( 0 , "insert failed 记录编号创建失败！" );
				return ;
			}
			*/
			$sql = "insert into production_test_record (record_number,product,production_line) values ($recordNumber,'$product',$productionLine)";
			$res = $this->conn->query($sql);
			if($res){
				$id = $this->conn->insert_id;
			}else{
				ret( 0 , "insert failed 记录编号创建失败！" );
				return ;
			}
		}
		ret( 1 , "成功" , array( "exist" => $exist , "id" => $id ) );
	}
	//生产过程检验主页面 列表显示接口
	public function productionTest($param){
		$id = $param['id'];
		$sql = "select p.record_number,p.product,w.name 
			from production_test_record as p
			left join workshopdata as w on p.production_line=w.id
			where p.id=$id";
		$row = $this->format($sql);
		$record_number = $row[0]['record_number'];
		$product = $row[0]['product'];
		$production_line = $row[0]['name'];
		$sql = "select gs.id as id,t.forshort as item,pd.cname as component,case gs.estate when 0 then '检验合格' when 2 then '未检验' else '不合格' end status
			from gys_shsheet as gs
			inner join production_test_record as ptr on gs.production_test_record_id=ptr.id
			left join cg1_stocksheet as cs on gs.stockid=cs.stockid
		    left join yw1_ordersheet as yo on cs.porderid=yo.porderid
			left join productdata as pd on yo.productid=pd.productid
			left join yw1_scsheet as ys on yo.porderid = ys.porderid 
			left join yw1_ordermain as yom on ys.ordernumber=yom.ordernumber
			left join trade_object as t on yom.company_id=t.companyid
			where ptr.id=$id";
		$row = $this->format($sql);
		$result = array(
			"recordNumber" => $record_number,
			"product" => $product,
			"productionLine" => $production_line,
			"list" => $row,
		);
		ret( 1 , "成功" , $result );
	}
	//openid对应的用户名
	public function username($param){
		$openid = $param['openid'];
        $sql = "select s.name from usertable u inner join staffmain as s on u.number=s.number where u.openid='$openid';";
        $row = $this->format($sql);
		ret( 1 , "成功" , $row[0]['name'] );
    }
	//生产过程检验主页面 扫描添加
	public function productionScanAdd($param){
		$id = $param['id'];
		$sid = $param['sid'];
		$sql = "select * from gys_shsheet as gs
			inner join cg1_stocksheet as cs on gs.stockid=cs.stockid
			inner join yw1_ordersheet as yo on cs.porderid=yo.porderid
			inner join yw1_scsheet as ys on yo.porderid=ys.porderid
			inner join production_test_record as ptr on ys.workshopid=ptr.production_line
			where ptr.id=$id and gs.id=$sid and gs.production_test_record_id=0";
		if($this->conn->query($sql)){
			$sql = "update gys_shsheet set production_test_record_id=$id where id=$sid";
			if($this->conn->query($sql)){
				ret( 1 , "添加成功" );
			}else{
				ret( 0 , "添加失败" );
			}
		}else{
			ret( 0 , "exist , 已存在不能添加" );
		}
	}
	//生产过程检验 添加页面 项目筛选列表
	public function productionAddItem($param){
        $sql = "select t.id,t.forshort as name,ti.tradeno from trade_object as t inner join trade_info as ti on t.id=ti.tradeid";
        $row = $this->format($sql);
		ret( 1 , "成功" , $row );
    }
	//生产过程检验 添加页面 楼栋筛选列表
	public function productionAddBuilding($param){
		$item_id = $param['itemId'];
        $sql = "select buildingno as id,buildingno as name from trade_drawing where tradeid=$item_id group by buildingno";
        $row = $this->format($sql);
		ret( 1 , "成功" , $row );
    }
	//生产过程检验 添加页面 楼层筛选列表
	public function productionAddFloor($param){
		$item_id = $param['itemId'];
		$building_id = $param['buildingId'];
        $sql = "select floorno as id,floorno as name from trade_drawing where tradeid=$item_id and buildingno=$building_id group by floorno";
        $row = $this->format($sql);
		ret( 1 , "成功" , $row );
    }
	//生产过程检验 添加页面 构件类型筛选列表
	public function productionAddType($param){
		$item_id = $param['itemId'];
		$building_id = $param['buildingId'];
		$floor_id = $param['floorId'];
        $sql = "select p.typeid as id,td.cmpttype as name from trade_drawing as td inner join producttype as p on td.cmpttypeid=p.typeid
            where td.tradeid=$item_id and td.buildingno=$building_id and td.floorno=$floor_id group by td.cmpttypeid";
        $row = $this->format($sql);
		ret( 1 , "成功" , $row );
    }
	//生产过程检验 添加页面 列表显示
	public function productionAdd($param){
		$id = $param['id'];
		$item_id = $param['itemId'];
		$building_id = $param['buildingId'];
		$floor_id = $param['floorId'];
		$type_id = $param['typeId'];
		$building_floor = $building_id ."-". $floor_id ;
		$sql = "select gs.id as id,t.forshort as item,p.cname as component,case gs.estate when 0 then '检验合格' when 2 then '未检验' else '不合格' end status
			from gys_shsheet as gs
			inner join cg1_stocksheet as cs on gs.stockid=cs.stockid
			inner join yw1_ordersheet as yo on cs.porderid=yo.porderid
			inner join yw1_scsheet as ys on yo.porderid=ys.porderid
			inner join production_test_record as ptr on ys.workshopid=ptr.production_line
			inner join yw1_ordermain as yom on ys.ordernumber=yom.ordernumber
			inner join trade_object as t on yom.company_id=t.companyid
			inner join productdata as p on yo.productid=p.productid
			left join cg1_stocksheet as cs on gs.stockid=cs.stockid
			left join yw1_scsheet as ys on yo.porderid = ys.porderid 
			where ptr.id=$id and t.id=$item_id and p.cname like '{$building_floor}%' and p.typeid=$type_id";
		$row = $this->format($sql);
		ret( 1 , "成功" , $row );
	}
	//生产过程检验 添加页面 保存
	public function productionAddSave($param){
		$id = $param['id'];
		$sid = $param['sid'];
		$sid_arr = explode(',',$sid);
		foreach($sid_arr as $k=>$v){
			$sql = "update gys_shsheet set production_test_record=$id where id=$v";
			$res = $this->conn->query($sql);
			if(!$res){
				ret( 0 , "保存失败" );
				return;
			}
		}
		ret( 1 , "成功" );
	}
	//生产过程检验 主页面 删除
	public function productionDelete($param){
		$id = $param['id'];
		$did = $param['did'];
		$did_arr = explode(',',$did);
		foreach($did_arr as $k=>$v){
			$sql = "update gys_shsheet set production_test_record_id=0 where id=$v";
			$res = $this->conn->query($sql);
			if(!$res){
				ret( 0 , "删除失败" );
				return;
			}
		}
		ret( 1 , "成功" );
	}
	//生产过程检验 主页面 质检合格
	public function productionTestOk($param){
		$id = $param['id'];
		$tid = $param['tid'];
		$tid_arr = explode(',',$tid);
		foreach($tid_arr as $k=>$v){
			$sql = "update gys_shsheet set estate=0 and locks=0 where id=$v";
			$res = $this->conn->query($sql);
			if(!$res){
				ret( 0 , "质检合格失败" );
				return;
			}
		}
		ret( 1 , "成功" );
	}
}
