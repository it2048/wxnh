var Util = {
	//提示
	showTips: function(status, text) {
		var $siteTips = $('.tips');
		if ($siteTips.length) {
			$siteTips.find('p').text(text);
			$siteTips.addClass('tips-show');
			return;
		}
		var tipsStr =
			'<div class="tips tips-show"><div>' +
			'<i class="icon icon-' + status + '"></i>' +'<p>' + text + '</p>' +
			'</div></div>';
		$('body').append(tipsStr);

		setTimeout(function() {
			$('.tips').removeClass('tips-show').remove();
		}, 2000);
	},
	//modal
	showModal: function(elem) {
   	$(elem).addClass('active');
 		$('body')
 			.scrollTop($('body').scrollTop() + 1)
 			.addClass('noScroll');
	},
	hideModal: function(elem) {
	  $(elem).removeClass('active');
	  $('body').removeClass('noScroll');
	},
	//字符串过滤
	uHtml: function(str) {
		var farr = [
			//过滤 <script>等可能引入恶意内容或恶意改变显示布局的代码,如果不需要插入flash等,还可以加入<object>的过滤
			/<(\/?)(script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/g,
			//过滤javascript的on事件
			/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/g
		];
		for (var i = 0; i < farr.length; i++) {
			var str = str.replace(farr[i], '');
		}
		return str;
	},
	// 解析相对 URL 为绝对 URL
	convertToAbsURL: function(url) {
		var anchor = document.createElement('a');
		anchor.href= url;
		return anchor.href;
	}
};