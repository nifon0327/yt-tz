#!/usr/bin/env Python
#coding=utf-8

import sys
import wx
from wx.lib.pubsub import pub
from wx.lib.stattext import GenStaticText
import ScannerListener
import SynListener
import ShippingList
from CodeChecker import Checker
import urllib
import urllib2
import StringIO
from ProductPanel import ProductPanel
from LabelPanel import LabelPanel
from LabelFactory import LabelFactory

WIDTH = 1080
HEIGHT = 1920

class CheckApp(wx.App):
    def OnInit(self):
        self.frame = wx.Frame(parent=None, title='喷码内容检查', size=(WIDTH, HEIGHT))
        #self.frame.SetSize(wx.DisplaySize())
        #self.frame.SetPosition((0,0))
        self.panel = wx.Panel(self.frame)
        self.panel.Bind(wx.EVT_KEY_DOWN, self.OnKeyDown)

        self.productPanel = ProductPanel(self.panel, -1, pos=(0,0), size=(WIDTH, HEIGHT*0.3))
        self.productPanel.SetBackgroundColour((230, 241, 247))

        self.frame.Show()
        self.frame.ShowFullScreen(True)

        #同步类型处理
        pub.subscribe(self.synMessage, "synMessage")
        #处理PO同步结果
        pub.subscribe(self.synResult, "synResult")
        #处理返回的读码的结果
        pub.subscribe(self.checkCode, "getCode")

        self.scanListen = ScannerListener.ScannerListener('172.16.1.100', 9004)
        self.synListen = SynListener.SynListener(30010)
        self.synListen.start()

        return True

    def OnKeyDown(self, event):
        keyCode = event.GetKeyCode()
        if keyCode == wx.WXK_ESCAPE:
            self.synListen.setKeepListen()
            self.synListen.killAlive()
            exit()
        else:
            event.Skip()

    def synMessage(self, msg):
        messages = msg.split('|')
        handler = messages[0]
        if handler == 'syn':
            synType = messages[1] #要load的类型-是已出还是模拟
            shipId = messages[2]
            self.companyId = messages[3]
            po = messages[4]
            self.totleBox = messages[5]
            self.productPanel = ProductPanel(self.panel, -1, pos=(0,0), size=(WIDTH, HEIGHT*0.3))
            self.productPanel.SetBackgroundColour((230, 241, 247))
            self.shipList = ShippingList.ShippingList()
            self.shipList.getShipItem(synType, shipId, self.companyId, po, self.totleBox)
            #正面标签panel
            self.frontLabelPanel = LabelPanel(self.panel, -1, pos=(0,HEIGHT*0.35), size=(WIDTH, HEIGHT*0.3), sidename='正面')
            #侧面标签panel
            self.sideLabelPanel = LabelPanel(self.panel, -1, pos=(0,HEIGHT*0.67), size=(WIDTH, HEIGHT*0.3), sidename='侧面')
            #生成标签页面
            labels = LabelFactory.createLabel(self.companyId, self.frontLabelPanel, self.sideLabelPanel)

            self.frontLabelPanel.setLabel(labels['front'])
            self.sideLabelPanel.setLabel(labels['side'])

            print 'finish load'
        elif handler == 'error':
            errorMsg = message[1]
        elif handler == 'test':
            target = self.shipList.getTargetItem(messages[1])
            self.setProductPanel(target)

    def checkCode(self, codes):
        result = Checker.analysisCode(codes)
        target = self.shipList.getTargetItem(result['qr'])
        if(self.frontLabelPanel.labelZone != None):
            #do something
            isFrontCorrect = False
            if result['front'] == result['qr']:
                isCorrect = True
            self.frontLabelPanel.labelZone.setInfo(target, isFrontCorrect, result['boxId'], self.totleBox)

        if(self.sideLabelPanel.labelZone != None):
            isSideCorrect = False
            if(result['side'] == result['qr']):
                isSideCorrect = True
            print target
            self.sideLabelPanel.labelZone.setInfo(target,isSideCorrect, result['boxId'], self.totleBox)

        #call for showing current product
        self.setProductPanel(target)

    def setProductPanel(self, target):

        self.productPanel.bindProduct(target)

    def synResult(self, result):
        self.productPanel.setResult(result)


if __name__ == '__main__':
    app = CheckApp()
    app.MainLoop()
