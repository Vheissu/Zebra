var Zebra = Zebra || {};

(function(w, Zebra, $, undefined) {

    var pageEls = Zebra.Elements || {};
    
    $(function() {

        pageEls.link        = $("#link");
        pageEls.text        = $("#text");
        pageEls.topics      = $("#topics");
        pageEls.downvoteBox = $("#downvotereason");

        $(".story-voting").on("click", "a", function(e) {

            var $this       = $(this);
            var $storyId    = $this.data('story-id');
            var $voteAction = $this.data('vote-action');

            if ($voteAction == "up" && !$this.hasClass('disabled'))
            {
                Zebra.Vote.Story.up($storyId);
            }

            if ($voteAction == "down" && !$this.hasClass('disabled'))
            {
                pageEls.downvoteBox.dialog({
                    resizable: false,
                    modal: true,
                    width: 500,
                    buttons: {
                        "Save": function() {
                            var downvoteReason = $("#downvote_reason");

                            if (!downvoteReason.children("option:selected").length)
                            {
                                alert('You must choose a reason to downvote');
                            }
                            else
                            {
                                Zebra.Vote.Story.down($storyId, downvoteReason.children("option:selected").val());
                                
                                $(this).dialog("close");
                            }
                        },
                        Cancel: function() {
                            $(this).dialog("close");    
                        }
                    }
                });
            }

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

})(window, Zebra, jQuery);

(function(w, Zebra, $, undefined) {

    Zebra.Vote = function() {

        var processing     = false;
        var slowDown       = "Slow down, you can only vote so fast";
        var missingBaseurl = "Base URL is missing!";

        return {

            Story: {

                up: function(story_id) {
                    if (base_url) {
                        if (processing == false) {
                            processing = true;
                            $.post(base_url + 'ajax/story_vote', { action: "upvote", story_id: story_id }, function(response) {
                                processing = false;
                                return response;
                            });
                        } else {
                            alert(slowDown);
                        }

                    } else {
                        alert(missingBaseurl);
                    }
                },

                down: function(story_id, reason_id) {

                    if (base_url) {

                        if (processing == false) {
                            processing = true;
                            $.post(base_url + 'ajax/story_vote', { action: "downvote", story_id: story_id, downvote_reason: reason_id }, function(response) {
                                processing = false;
                                return response;
                            });
                        } else {
                            alert(slowDown);
                        }

                    } else {
                        alert(missingBaseurl);
                    }

                }
            }
        }

    }();

    Zebra.Vote.Comment = function() {

        var processing     = false;
        var slowDown       = "Slow down, you can only vote so fast";
        var missingBaseurl = "Base URL is missing!";

        return {

            up: function(comment_id) {
                if (base_url) {
                    if (processing == false) {
                        processing = true;
                        $.post(base_url + 'ajax/comment_vote', { action: "upvote", comment_id: comment_id }, function(response) {
                            processing = false;
                            return response;
                        });
                    } else {
                        alert(slowDown);
                    }

                } else {
                    alert(missingBaseurl);
                }
            },

            down: function(comment_id, reason_id) {

                if (base_url) {

                    if (processing == false) {
                        processing = true;
                        $.post(base_url + 'ajax/comment_vote', { action: "downvote", comment_id: comment_id, downvote_reason: reason_id }, function(response) {
                            processing = false;
                            return response;
                        });
                    } else {
                        alert(slowDown);
                    }

                } else {
                    alert(missingBaseurl);
                }

            }

        }

    }();

})(window, Zebra, jQuery);