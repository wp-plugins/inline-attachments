(function(){
	var $,
		wrapper,
		iframe,
		minHeight = 100,
		contentHeight,
		maxInitHeight,
		iframeContents;
	
	jQuery(document).ready(function(){
		$ = jQuery;
		wrapper = $("#inline_attachments .inside");
		iframe = $("#inline_attachments_iframe");
		$("#inline_attachments h3:first").append("<img src='images/wpspin_light.gif' class='waiting' alt='' />")
		iframe.load(function(){
			iframeContents = iframe.contents();
			addIframeScaling();
			addSavePostHook();
			$("#inline_attachments h3 .waiting").hide("fast");
		});
		addResize();
		$("#open_attachments_lightbox").appendTo("#inline_attachments");
		//tb_init('a.thickbox, area.thickbox, input.thickbox');
	})
	function addSavePostHook(){
		$("#publish, #post-preview, #save-post").unbind("mousedown").mousedown(function(e){
			iframeContents.find("#save-all").click();
		})
		iframeContents.find("#tab-gallery a").unbind("mousedown").mousedown(function(e){
			iframeContents.find("#save-all").click();
		})
	}
	function addIframeScaling(){
		iframeContents.find(".toggle").click(function(){
			fitHeightToSlide($(this));
		});
		autoAnimateHeight();
	}
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
	function autoAnimateHeight(){
		var form = iframeContents.find('form.media-upload-form');
		contentHeight = form.position().top + form.height() + parseInt(form.css('margin-bottom')) + 30;
		animateHeight(contentHeight);
	}
})();