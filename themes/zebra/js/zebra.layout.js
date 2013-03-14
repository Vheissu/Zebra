;(function(w, Zebra, $, undefined) {

    var pageEls = Zebra.Elements || {};
    
    $(function() {
    });

    Zebra.Layout = function() {

    	return {

    		Story: {

    			Get: function(story_id, callback) {
	    			if (story_id) {
	    				$.get(base_url + 'api/story/story.json?id='+story_id+'', function(response) {
	    					// Make sure our callback is a function
	    					if (typeof callback !== 'undefined') {
	    						callback(response);
	    					} else {
	    						return response;
	    					}
	    				});
	    			}
    			}

    		}

    	};

    }();

})(window, Zebra = window.Zebra || {}, jQuery);