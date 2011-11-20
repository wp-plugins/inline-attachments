(function(){
	var $,
		galleryTabTimeout,
		ajaxBusy = false,
		currentMenuOrder,
		galleryLink;
	
	function checkIfMoreThanZero(){
		if(parseInt($("#attachments-count").text()) > 0){
			$("#tab-gallery").css("display", "block");
		} else {
			galleryTabTimeout = setTimeout(checkIfMoreThanZero, 1000);
		}
	}
	jQuery(document).ready(function(){
		$ = jQuery;
		galleryLink = phpGalleryLink;
		currentMenuOrder = getMenuOrder();
		if($("#tab-gallery").length == 0){
			$("#sidemenu").append('<li id="tab-gallery"></li>');
			$("#tab-gallery").append(galleryLink);
			$("#tab-gallery").css("display", "none");
			checkIfMoreThanZero();
		} else if($(".media-item").length > 1) {
			saveAjax();
			addAjaxOrderAutoSave();
			addAjaxFieldsAutoSave();
		}
	})
	// Autosaving of changed fields on blur
	function addAjaxFieldsAutoSave(){
		$(".slidetoggle input, .slidetoggle textarea").change(function(){
			$(".updated-media-item").removeClass("updated-media-item");
			$(this).parents(".media-item").addClass("updated-media-item");
			saveAjax();
		})
	}
	// Autosaving of the menu_order of the attachments
	function addAjaxOrderAutoSave(){
		$(".media-item").mouseup(function(){
			$(".updated-media-item").removeClass("updated-media-item");
			$(this).addClass("updated-media-item");
			setTimeout(function(){
				if(currentMenuOrder != getMenuOrder()){
					saveAjax();
				}
			}, 50);
		})
	}
	function saveAjax(){
		if(!ajaxBusy){
			ajaxBusy = true;
			currentMenuOrder = getMenuOrder();
			$('.updated-media-item .menu_order input').hide().after('<img alt="processing" src="images/wpspin_light.gif" class="waiting" style="margin: 0px;" />');
			var items = new Array();
			$(".media-item").each(function(index){
				$(this).find(".menu_order_input").attr("value", index+1);
				var itemId = parseInt($(this).attr("id").split("media-item-")[1]);
				items.push(itemId);
			})
			$.post($("#gallery-form").attr("action"), $("#gallery-form").serialize(), function(data){
				$('.media-item img.waiting').remove();
				$('.menu_order input').show();
				ajaxBusy = false;
			});
		}
	}
	function getMenuOrder(){
		var mo = "";
		$(".media-item").each(function(){
			var id = $(this).attr("id").split("media-item-")[1];
			var menuOrder = $(this).find(".menu_order_input").attr("value");
			mo += id + "-" + menuOrder + ",";
		})
		return mo;
	}
})();