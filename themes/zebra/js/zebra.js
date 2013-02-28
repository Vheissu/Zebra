;(function(w, Zebra, $, undefined) {

    var pageEls = Zebra.Elements || {};
    
    $(function() {

        pageEls.link          = $("#link");
        pageEls.text          = $("#text");
        pageEls.contentVoting = $(".content-voting");
        pageEls.topics        = $("#topics");
        pageEls.downvoteBox   = $("#downvotereason");

        if (pageEls.contentVoting.length) {

            pageEls.contentVoting.on("click", "a", function(e) {

                var $this       = $(this);
                var $storyId    = $this.data('story-id');
                var $commentId  = $this.data('comment-id');
                var $voteAction = $this.data('vote-action');
                var $entryRow   = $("#entry-"+$storyId);
                var $storyVotes = $(".content-upvotes", $entryRow);
                var voteValue   = parseInt($storyVotes.text());

                if ($storyId) {

                    if ($voteAction == "up" && !$this.hasClass('disabled')) {
                        Zebra.Vote.Story.up($storyId, function(response) {
                            if (response) {
                                var response_str = response.split("|");

                                var status       = response_str[0];
                                var voteType     = response_str[1];
                                var storyId      = response_str[2];
                                
                                voteValue = voteValue+1;

                                if (status == "success") {
                                    pageEls.contentVoting.children('a').removeClass('disabled');
                                    $this.addClass('disabled');

                                    $storyVotes.text(voteValue);
                                } else if (status == "error") {
                                    alert(voteType);
                                }  
                            }
                        });
                    }

                    if ($voteAction == "down" && !$this.hasClass('disabled')) {
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
                                        Zebra.Vote.Story.down($storyId, downvoteReason.children("option:selected").val(), function(response) {
                                            if (response) {
                                                var response_str = response.split("|");

                                                var status       = response_str[0];
                                                var voteType     = response_str[1];
                                                var storyId      = response_str[2];

                                                voteValue = voteValue-1;

                                                if (status == "success") {
                                                    pageEls.contentVoting.children('a').removeClass('disabled');
                                                    $this.addClass('disabled');

                                                    $storyVotes.text(voteValue);
                                                }  
                                            }
                                        });
                                        
                                        $(this).dialog("close");
                                    }
                                },
                                Cancel: function() {
                                    $(this).dialog("close");    
                                }
                            }
                        });
                    }

                } else if ($commentId) {

                    if ($voteAction == "up" && !$this.hasClass('disabled')) {
                        Zebra.Vote.Comment.up($storyId, function(response) {
                            if (response) {
                                var response_str = response.split("|");

                                var status       = response_str[0];
                                var voteType     = response_str[1];
                                var storyId      = response_str[2];
                                
                                voteValue = voteValue+1;

                                if (status == "success") {
                                    pageEls.contentVoting.children('a').removeClass('disabled');
                                    $this.addClass('disabled');

                                    $storyVotes.text(voteValue);
                                } else if (status == "error") {
                                    alert(voteType);
                                }  
                            }
                        });
                    }

                    if ($voteAction == "down" && !$this.hasClass('disabled')) {
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
                                        Zebra.Vote.Comment.down($storyId, downvoteReason.children("option:selected").val(), function(response) {
                                            if (response) {
                                                var response_str = response.split("|");

                                                var status       = response_str[0];
                                                var voteType     = response_str[1];
                                                var storyId      = response_str[2];

                                                voteValue = voteValue-1;

                                                if (status == "success") {
                                                    pageEls.contentVoting.children('a').removeClass('disabled');
                                                    $this.addClass('disabled');

                                                    $storyVotes.text(voteValue);
                                                }  
                                            }
                                        });
                                        
                                        $(this).dialog("close");
                                    }
                                },
                                Cancel: function() {
                                    $(this).dialog("close");    
                                }
                            }
                        });
                    }

                }

                e.preventDefault();
            });

        }

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

})(window, Zebra = window.Zebra || {}, jQuery);

(function(w, Zebra, $, undefined) {

    Zebra.Vote = function() {

        var processing     = false;
        var slowDown       = "Slow down, you can only vote so fast";
        var missingBaseurl = "Base URL is missing!";

        return {

            Story: {

                up: function(story_id, callback) {
                    if (base_url) {
                        if (processing == false) {
                            processing = true;
                            $.post(base_url + 'ajax/story_vote', { action: "upvote", story_id: story_id }, function(response) {
                                processing = false;
                                callback(response);
                            });
                        } else {
                            alert(slowDown);
                        }

                    } else {
                        alert(missingBaseurl);
                    }
                },

                down: function(story_id, reason_id, callback) {

                    if (base_url) {

                        if (processing == false) {
                            processing = true;
                            $.post(base_url + 'ajax/story_vote', { action: "downvote", story_id: story_id, downvote_reason: reason_id }, function(response) {
                                processing = false;
                                callback(response);
                            });
                        } else {
                            alert(slowDown);
                        }

                    } else {
                        alert(missingBaseurl);
                    }

                }
            },

            Comment: function() {

                return {

                    up: function(comment_id, callback) {
                        if (base_url) {
                            if (processing == false) {
                                processing = true;
                                $.post(base_url + 'ajax/comment_vote', { action: "upvote", comment_id: comment_id }, function(response) {
                                    processing = false;
                                    callback(response);
                                });
                            } else {
                                alert(slowDown);
                            }

                        } else {
                            alert(missingBaseurl);
                        }
                    },

                    down: function(comment_id, reason_id, callback) {

                        if (base_url) {

                            if (processing == false) {
                                processing = true;
                                $.post(base_url + 'ajax/comment_vote', { action: "downvote", comment_id: comment_id, downvote_reason: reason_id }, function(response) {
                                    processing = false;
                                    callback(response);
                                });
                            } else {
                                alert(slowDown);
                            }

                        } else {
                            alert(missingBaseurl);
                        }

                    }

                }

            },
        }

    }();

})(window, Zebra = window.Zebra || {}, jQuery);