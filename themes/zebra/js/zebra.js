(function(w, Zebra, $, undefined) {

    var pageEls = Zebra.Elements || {};
    
    $(function() {

        pageEls.link   = $("#link");
        pageEls.text   = $("#text");
        pageEls.topics = $("#topics");

        $(".story-voting").on("click", "a", function(e) {

            e.preventDefault();
        });

        if (pageEls.link.length) {

            pageEls.link.on("blur", function() {

                var $this = $(this);

                if ($.trim($this.val()) == '')
                {
                    pageEls.text.fadeIn("fast").removeAttr('disabled');
                }
                else
                {
                    pageEls.text.fadeOut("fast").attr('disabled', 'disabled');   
                }


            });

            pageEls.link.on("keyup", function() {

                var $this = $(this);

                if ($.trim($this.val()) == '')
                {
                    pageEls.text.fadeIn("fast").removeAttr('disabled');
                }
                else
                {
                    pageEls.text.fadeOut("fast").attr('disabled', 'disabled');   
                }

            });

        }

        if (pageEls.text.length) {

            pageEls.text.on("blur", function() {

                var $this = $(this);

                if ($.trim($this.val()) == '')
                {
                    pageEls.link.fadeIn("fast").removeAttr('disabled');
                }
                else
                {
                    pageEls.link.fadeOut("fast").attr('disabled', 'disabled');   
                }


            });

            pageEls.text.on("keyup", function() {

                var $this = $(this);

                if ($.trim($this.val()) == '')
                {
                    pageEls.link.fadeIn("fast").removeAttr('disabled');
                }
                else
                {
                    pageEls.link.fadeOut("fast").attr('disabled', 'disabled');   
                }

            });

        }

    });

})(window, window.Zebra = window.Zebra || {}, jQuery);

(function(w, Zebra, $, undefined) {

    Zebra.Vote = function() {

        var processing = false;

        return {

            up: function(story_id) {
                if (base_url) {

                    if (processing == false) {
                        processing = true;
                        $.post(base_url + 'ajax/vote', { action: "upvote", story_id: story_id }, function(response) {
                            processing = false;
                            return response;
                        });
                    } else {
                        alert('Slow down, you can only vote one story at a time!');
                    }

                } else {
                    alert('Base URL is missing!');
                }
            },

            down: function(story_id, reason_id) {

                if (base_url) {

                    if (processing == false) {
                        processing = true;
                        $.post(base_url + 'ajax/vote', { action: "downvote", story_id: story_id, downvote_reason: reason_id }, function(response) {
                            processing = false;
                            return response;
                        });
                    } else {
                        alert('Slow down, you can only vote one story at a time!');
                    }

                } else {
                    alert('Base URL is missing!');
                }

            }

        }

    }();

})(window, window.Zebra = window.Zebra || {}, jQuery);