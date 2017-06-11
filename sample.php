<?php
require_once "jssdk.php";
$jssdk = new JSSDK("appid", "secret");
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>test</title>
  <link href="http://cdn.bootcss.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" rel="stylesheet">
  <script src="http://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
  <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
  <script src="base64.js"></script>
</head>
<body id="activity-detail" class="zh_CN ">  
    <div class="wdbll4" align="center" style="padding-left: 10px; padding-right: 10px;">
        <div style="width: 95%; margin: 0 auto; text-align: center">  
            <div class='btn aui-btn-info aui-btn-block' onclick="bleConn()">连接设备</div>  
        </div>  
    </div> 

    <div class="wdbll4" align="center" style="padding-left: 10px; padding-right: 10px;">  
        <div style="width: 95%; margin: 0 auto; text-align: center" id="scan">  
            <div class='aui-btn aui-btn-info aui-btn-block' onclick="sendData()">发送数据</div>  
        </div>  
    </div>  

    <div id="initBle"></div>  
</body> <script src="http://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
var dId;
  /*
   * 注意：
   * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
   * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
   * 3. 常见问题及完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
   *
   * 开发中遇到问题详见文档“附录5-常见错误及解决办法”解决，如仍未能解决可通过以下渠道反馈：
   * 邮箱地址：weixin-open@qq.com
   * 邮件主题：【微信JS-SDK反馈】具体问题
   * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
   */
  wx.config({
    beta : true,
    debug: true,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    
    jsApiList : [ //需要调用的接口，都得在这里面写一遍      
      "openWXDeviceLib" ,     
      "closeWXDeviceLib",//关闭设备库（只支持蓝牙设备）      
      "getWXDeviceInfos",//获取设备信息（获取当前用户已绑定的蓝牙设备列表）      
      "sendDataToWXDevice",//发送数据给设备      
      "startScanWXDevice",//扫描设备（获取周围所有的设备列表，无论绑定还是未被绑定的设备都会扫描到）      
      "stopScanWXDevice",//停止扫描设备      
      "connectWXDevice",//连接设备      
      "disconnectWXDevice",//断开设备连接      
      "getWXDeviceTicket",//获取操作凭证      

      //下面是监听事件：      
      "onWXDeviceBindStateChange",//微信客户端设备绑定状态被改变时触发此事件      
      "onWXDeviceStateChange",//监听连接状态，可以监听连接中、连接上、连接断开      
      "onReceiveDataFromWXDevice",//接收到来自设备的数据时触发      
      "onScanWXDeviceResult",//扫描到某个设备时触发      
      "onWXDeviceBluetoothStateChange",//手机蓝牙打开或关闭时触发      
    ]  
  });

function bleConn() {  
    wx.invoke('connectWXDevice', {  
            'deviceId' : dId,  
            'connType' : 'blue'  
        }, function(res) {  
            $("#initBle").append("<p>连接设备" + JSON.stringify(res) + "</p>");         
    });
}  

function sendData() {
  var data={"deviceId":dId,"base64Data": Base64.encode('0x 77 01 07 32 01 08 12 34 56 00 3a')};

  wx.invoke('sendDataToWXDevice', data, function(res){
      // 回调
      console.info('发消息到设备sendMsg');
      console.log(data);
      console.log(res);
      $("#initBle").append("<p>发送数据：" + JSON.stringify(res) + "</p>");
  });

  // wx.invoke('sendDataToWXDevice',  
  // {  
  //     'deviceId' : dId,  
  //     'connType' : 'blue',  
  //     'base64Data' : 'MDAwMEZGRjItMDAwMC0xMDAwLTgwMDAtMDA4MDVGOUIzNEZC'  
  // }, function(res) {  
  //     $("#initBle").append("<p>发送数据：" + JSON.stringify(res) + "</p>");
  // }); 
}

  wx.ready(function () {

    wx.invoke('openWXDeviceLib', {  
    // 'brandUserName' : '',  
    // 'connType':'blue'  
    }, function(res) {  
        $("#initBle").append("<p>初始化设备库：" + res.err_msg + "</p>");  
        if (res.bluetoothState == "off") {  
            alert("请先开启手机蓝牙");  
            $("#initBle").append("<p>请先开启手机蓝牙</p>");  

        }  
    });  

    wx.invoke('getWXDeviceInfos', {  
        'connType' : 'blue'  
    }, function(res) {  
      dId = res.deviceInfos[0].deviceId;
        $("#initBle").append("<p>获取我的设备：" + res.err_msg +'，设备号：'+ dId + "</p>");  
         wx.invoke('connectWXDevice', {  
                  'deviceId' : dId,  
                  'connType' : 'blue'  
              }, function(res) {  
                  $("#initBle").append("<p>连接设备" + JSON.stringify(res) + "</p>");         
          });
    });  

   

    wx.on('onScanWXDeviceResult', function(res) {  
        var ret_ = res.devices;  
        for (var i = 0; i < ret_.length; i++) {  
            var macid = JSON.stringify(res.devices[i].deviceId)  
                    .replace(/\"/g, "");  
            //给扫描到的设备添加点击事件  
            $("#initBle").append(  
                    "<button onclick=\"bindBle('" + macid  
                            + "')\">扫描到设备：" + macid + "</button>");  
        }  
    });  
    //手机蓝牙状态改变时触发 （这是监听事件的调用方法，注意，监听事件都没有参数）      
    wx.on('onWXDeviceBluetoothStateChange', function(res) {  
        //把res输出来看吧      
        $("#initBle").append(  
                "<p>蓝牙状态变更：" + JSON.stringify(res) + "</p>");  
    });  
    //设备绑定状态改变事件（解绑成功，绑定成功的瞬间，会触发）      
    wx.on('onWXDeviceBindStateChange', function(res) {  

        $("#initBle").append(  
                "<p>绑定状态变更：" + JSON.stringify(res) + "</p>");  
    });  
    //设备连接状态改变      
    wx.on('onWXDeviceStateChange', function(res) {  
        //有3个状态：connecting连接中,connected已连接,unconnected未连接      
        //每当手机和设备之间的状态改变的瞬间，会触发一次      

        $("#initBle").append("<p>设备连接状态：" + res.state + "</p>");  
    });  
    //接收到设备传来的数据   
    // var data={"deviceId":dId,"base64Data": Base64.encode('this is a test')};   
    wx.on('onReceiveDataFromWXDevice', function(res) {  
        $("#initBle").append(  
                "<p>收到设备数据：" + JSON.stringify(res) + "</p>");  
    });  
    wx.error(function(res) {  
        // alert("wx.error错误：" + JSON.stringify(res));  
        //如果初始化出错了会调用此方法，没什么特别要注意的      
    });  
  });
</script>
</html>
