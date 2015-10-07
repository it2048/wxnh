try{
	var arg = {
			link: location.href, title: document.title,
			img_width: 49, img_height: 55, desc: descContent,
			img_url: 'http://bszp.it2048.cn/img/logo.big.png'
	};
	// 微信分享
	document.addEventListener('WeixinJSBridgeReady',
		function onBridgeReady() {
			// 发送给好友
			WeixinJSBridge.on('menu:share:appmessage', function(argv) {
				WeixinJSBridge.invoke('sendAppMessage', arg, function(res) {
					_report('send_msg', res.err_msg);
				})
			});
			// 分享到朋友圈
			WeixinJSBridge.on('menu:share:timeline', function(argv) {
				WeixinJSBridge.invoke('shareTimeline', arg, function(res) {
					_report('timeline', res.err_msg);
				});
			});
		},
		false
	);
} catch(e) {
}