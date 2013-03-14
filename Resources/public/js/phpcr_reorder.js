function getQueryStringByProperty(selector, property, cell){
	return jQuery.makeArray(jQuery(selector).map(function() {
		var obj = {};
		obj[cell] = jQuery(this).attr(property);
  		return jQuery.param(obj) ;
	  })).join('&');
}

function createSortable(selector, blockId, selectorIds, propertyIds){
	$(selector).sortable({
	    update : function(event, ui) {
	    	 mydata = getQueryStringByProperty(selectorIds, propertyIds, 'names[]');
	    	 console.log(mydata);
	    	jQuery.ajax({
	            url:  '/reorder-node/doorder/'+blockId,
	            type: "POST",
	            data: mydata,

	            success: function(data){
	            	 
	            }
	    	});
	    }
	});
	
}
