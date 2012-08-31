(function(w, Zebra, $, undefined) {
    
    $(function() {

        $(".story-voting").on("click", "a", function(e) {

            e.preventDefault();
        });

    });

})(window, window.Zebra = window.Zebra || {}, jQuery);

(function(w, Zebra, $, undefined) {

    Zebra.Vote = function() {

        var processing = false;

        return {

            up: function(story_id) {
                if (base_url) {
                    $.post(base_url + 'ajax/vote', { action: "upvote", story_id: story_id }, function(response) {
                        return response;
                    });
                } else {
                    alert('Base URL is missing!');
                }
            },

            down: function(story_id, reason_id) {
                if (base_url) {
                    $.post(base_url + 'ajax/vote', { action: "downvote", story_id: story_id, downvote_reason: reason_id }, function(response) {
                        return response;
                    });
                } else {
                    alert('Base URL is missing!');
                }
            }

        }

    }();

})();})(window, window.Zebra = window.Zebra || {}, jQuery);