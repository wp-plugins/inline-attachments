(function($){
	var $,
		galleryTabTimeout,
		ajaxBusy = false,
		currentMenuOrder,
		galleryLink;
	
	jQuery(document).ready(function(){
		$ = jQuery;
		galleryLink = phpGalleryLink;
		currentMenuOrder = getMenuOrder();
		// If there is no Gallery tab
		if($("#tab-gallery").length == 0){
			$("#sidemenu #tab-type").after('<li id="tab-gallery"></li>');
			$("#tab-gallery").append(galleryLink);
			$("#tab-gallery").css("display", "none");
			checkIfMoreThanZero();
		} else if($("#tab-gallery a").hasClass("current") && $(".media-item").length > 1) {
			// If we are on the gallery screen
			saveAjax();
			addAjaxOrderAutoSave();
			addAjaxFieldsAutoSave();
		}
	})
	// Checks for new uploaded files and adds the gallery tab, if there are more then 0 files uploaded
	function checkIfMoreThanZero(){
		if(parseInt($("#attachments-count").text()) > 0){
			$("#tab-gallery").css("display", "block");
		} else {
			galleryTabTimeout = setTimeout(checkIfMoreThanZero, 1000);
		}
	}
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
	// The AutoSave AJAX-Call
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
	// Get the menu order of the media items by their DOM order
	function getMenuOrder(){
		var mo = "";
		$(".media-item").each(function(){
			var id = $(this).attr("id").split("media-item-")[1];
			var menuOrder = $(this).find(".menu_order_input").attr("value");
			mo += id;
		})
		return mo;
	}
})();