;(function(w, Zebra, $, undefined) {
    
    Zebra.Api = function() {

    	return {

    		Story: {

    			Get: function(story_id, callback) {
	    			if (story_id) {
                        $.ajax({
                            dataType: "json",
                            url: base_url + 'api/story/story.json?id='+story_id+'',
                            data: {id: story_id},
                            success: function (response) {
                                // Make sure our callback is a function
                                if (typeof callback !== 'undefined') {
                                    callback(response);
                                } else {
                                    return response;
                                }
                            }
                        });
	    			}
    			},

                Post: function(data, callback) {
                    if (typeof data !== 'undefined' && data.length) {
                        $.ajax({
                            type: "POST",
                            url: base_url + 'api/story/story',
                            data: data,
                            success: function(response) {
                                if (typeof callback !== 'undefined') {
                                    callback(response);
                                } else {
                                    return response;
                                }
                            },
                            dataType: "json"
                        });
                    }
                }

    		}

    	};

    }();

})(window, Zebra = window.Zebra || {}, jQuery);