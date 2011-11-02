(function(){
	
	var wrapper,
		iframe,
		minHeight = 100,
		contentHeight,
		maxInitHeight;
	
	jQuery(document).ready(function(){
		$ = jQuery;
		wrapper = $("#inline_attachments .inside");
		iframe = $("#inline_attachments_iframe_wrapper iframe");
		$('#inline_attachments_iframe').load(function(){
			var iframeContents = $('#inline_attachments_iframe').contents();
			iframeContents.find(".toggle").click(function(){
				fitHeightToSlide($(this));
			});
			var headerHeight = iframeContents.find("#media-upload-header").height() + 10;
			var formHeight = iframeContents.find('form:first').height();
			contentHeight = headerHeight + formHeight + 20;
			if(contentHeight > maxInitHeight) contentHeight = maxInitHeight;
			animateHeight(contentHeight);
		})
		addResize();
		//tb_init('a.thickbox, area.thickbox, input.thickbox');
	})
	function fitHeightToSlide(toggle){
		if(toggle.hasClass("describe-toggle-on")){
			var slideHeight = toggle.nextAll(".slidetoggle").height() + toggle.siblings(".filename").height();
			if(contentHeight < slideHeight){
				adjustHeight(slideHeight + 10);
			}
		}
	}
	function addResize(){
		$("#inline_attachments_footer .resizeButton").click(function(e){
			e.preventDefault();
		}).mousedown(function(e){
			e.preventDefault();
			startResize();
		})
	}
	function startResize(){
		$("body").prepend("<div id='dragging-overlay'></div>");
		$("#dragging-overlay").css({
			"position": "absolute",
			"z-index": "1000",
			"top": "0px",
			"left": "0px",
			"height": $(document).height(),
			"width": $(document).width(),
			"background": "red",
			"opacity": "0"
		})
		
		$("body").mousemove(function(e){
			var h = e.pageY - wrapper.offset().top;
			adjustHeight(h);
		}).mouseup(function(){
			stopResize();
		});
	}
	function adjustHeight(h){
		
		if(h < minHeight) h = minHeight;
		
		wrapper.css("height", (h+17) + "px");
		iframe.css("height", h + "px");
	}
	function stopResize(){
		$("body").unbind("mousemove");
		$("#dragging-overlay").remove();
	}
	function animateHeight(h){
		
		wrapper.animate({
			height: (h + 17) + "px"
		}, 400);
		iframe.animate({
			height: (h) + "px"
		}, 400);
	}
})();