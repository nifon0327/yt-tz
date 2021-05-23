/*
 Navicat Premium Data Transfer

 Source Server         : 建筑测试
 Source Server Type    : MySQL
 Source Server Version : 50640
 Source Host           : 106.15.180.165:3306
 Source Schema         : ac2_test

 Target Server Type    : MySQL
 Target Server Version : 50640
 File Encoding         : 65001

 Date: 08/04/2019 22:17:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for maintan_kiln_bits
-- ----------------------------
DROP TABLE IF EXISTS `maintan_kiln_bits`;
CREATE TABLE `maintan_kiln_bits` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `TemperatureValue` varchar(20) DEFAULT NULL COMMENT '温度',
  `HumidityValue` varchar(20) DEFAULT NULL COMMENT '湿度',
  `LineNo` varchar(2) DEFAULT NULL COMMENT '列',
  `KRowNo` varchar(2) DEFAULT NULL COMMENT '行',
  `WorkshopdataId` int(20) DEFAULT NULL COMMENT '产线id',
  `KType` int(1) DEFAULT NULL COMMENT '1 前 2 后',
  `Status` int(11) DEFAULT NULL COMMENT '1 正常  2 不可使用  0异常',
  `MaintanOrderId` int(20) DEFAULT '0' COMMENT '窑位订单id  0表示未使用',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=201 DEFAULT CHARSET=utf8 COMMENT='窑位表\n';

-- ----------------------------
-- Table structure for maintan_order
-- ----------------------------
DROP TABLE IF EXISTS `maintan_order`;
CREATE TABLE `maintan_order` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `KilnId` int(20) DEFAULT NULL COMMENT '窑位id',
  `TrolleyNo` int(20) DEFAULT NULL COMMENT '台车id',
  `Status` int(2) DEFAULT NULL COMMENT '状态   0待养护 1 养护待确认  2养护中  3 出窑待确认   4出窑成功',
  `MaintanTime` datetime DEFAULT NULL COMMENT '入窑时间',
  `Operator` varchar(20) DEFAULT NULL COMMENT '操作人openid',
  `Remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `OutMaintanTime` datetime DEFAULT NULL COMMENT '出窑时间',
  `LiningMouldId` int(10) DEFAULT NULL COMMENT 'liningmouldid',
  `Created` datetime DEFAULT NULL COMMENT '创建时间',
  `Creator` varchar(255) DEFAULT NULL,
  `Modifier` varchar(255) DEFAULT NULL,
  `Modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='养护表';

-- ----------------------------
-- Table structure for maintan_order_product_mapping
-- ----------------------------
DROP TABLE IF EXISTS `maintan_order_product_mapping`;
CREATE TABLE `maintan_order_product_mapping` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `MainOrderId` int(20) DEFAULT NULL COMMENT '窑位订单id',
  `POrderId` varchar(20) DEFAULT NULL COMMENT 'POrderId',
  `Operator` varchar(20) DEFAULT NULL COMMENT '操作人openid',
  `Remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `Created` datetime DEFAULT NULL COMMENT '创建时间',
  `Creator` varchar(255) DEFAULT NULL,
  `Modifier` varchar(255) DEFAULT NULL,
  `Modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='养护表与构件映射表';

SET FOREIGN_KEY_CHECKS = 1;
