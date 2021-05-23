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

 Date: 04/03/2019 09:54:00
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for inventory_data_finished_product
-- ----------------------------
DROP TABLE IF EXISTS `inventory_data_finished_product`;
CREATE TABLE `inventory_data_finished_product` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `StackID` int(10) unsigned DEFAULT NULL COMMENT '垛号',
  `ProductID` mediumint(9) DEFAULT NULL COMMENT '构件产品ID',
  `Status` int(11) DEFAULT NULL COMMENT '0: 待入库、 1：已入库、 2：已出库',
  `Creator` varchar(255) DEFAULT NULL COMMENT '微信OpenID（与创建当前垛的用户OpenID相同）',
  `CreateDT` datetime DEFAULT NULL COMMENT '创建日期',
  `IsDelete` int(1) DEFAULT NULL COMMENT '逻辑删除   1 已删除 0  未删除',
  `StorageNO` varchar(255) DEFAULT NULL COMMENT '入库编号',
  `POrderId` varchar(255) DEFAULT NULL,
  `cjtjId` int(10) DEFAULT NULL COMMENT 'cjtjId',
  `PreEstate` int(2) DEFAULT NULL COMMENT 'ch1_shipsplit estate临时存储',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COMMENT='成品入库暂存数据';


-- ----------------------------
-- Table structure for inventory_stackinfo_finished_product
-- ----------------------------
DROP TABLE IF EXISTS `inventory_stackinfo_finished_product`;
CREATE TABLE `inventory_stackinfo_finished_product` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增类型',
  `StackNo` varchar(255) DEFAULT NULL COMMENT '垛号',
  `SeatId` varchar(15) DEFAULT NULL COMMENT '库位号\r\n',
  `Creator` varchar(255) DEFAULT NULL COMMENT '微信创建者的OpenID',
  `CreateDT` datetime DEFAULT NULL COMMENT '垛号创建时间',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='成品入库垛号和库位对应表';

SET FOREIGN_KEY_CHECKS = 1;
