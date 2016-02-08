<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params		= $this->item->params;
$images = json_decode($this->item->images);
$urls = json_decode($this->item->urls);
$canEdit	= $this->item->params->get('access-edit');
$user		= JFactory::getUser();

$GLOBALS["userName"] = $this->writer->name;

?>
<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>

<script type="text/javascript">

	var comment_img2 = "<?php echo ideary::getUserImagePath($user->id) ?>";
	
    var text_id = <?php echo $this->item->id ?>;
    var writer_id = <?php echo $this->item->created_by ?>;
	var commenter_id= <?php echo $user->id ?>;
    var home = <?php echo (isset($_GET['home']))? 'true' : 'false'; ?>;
    var alias = '<?php echo $this->item->alias ?>';
    var created_by = '<?php echo $this->item->created_by ?>';
    var modified_by = '<?php echo $this->item->modified_by ?>';
    var checked_out = '<?php echo $this->item->checked_out ?>';
    var contrast_state = 1;
    var text_content_font_increase = 0;
    var text_content_font_size = '<?php echo ($this->item->editor_font_size)? $this->item->editor_font_size : '14px' ?>';
	
    <?php $comments_limit_chars = 150; ?>
    var comments_limit_chars = <?php echo $comments_limit_chars ?>;
	var loggedin=<?php echo $user->id; ?>;
	var authorid=<?php echo $this->writer->id; ?>;

    <?php if($this->item->allow_comments): ?>
        var count_comments = <?php echo (isset($this->comments))? count($this->comments) : 0 ?>;
        var count_comments_text = "<?php echo $this->comment_text_of ?>";

        function setCountCommentsText(){
            //var str = count_comments_text.replace("{X}", count_comments);
            var count_comments_string = '';
            if(count_comments == 1){
                count_comments_string = ' ('+XCOMMENT.replace("{x}", count_comments)+')';
            }
            else if(count_comments > 1){
                count_comments_string = ' ('+XCOMMENTS.replace("{x}", count_comments)+')';
            }
            var str = count_comments_text+count_comments_string;
            $('#comment-text').text(str);
        }

        function truncateComments(){

            $('.comment-data').each(function(){
                var comment_data = $(this);
                var comment_text_truncated = $(this).find('.comment-text-truncated');

                $(comment_text_truncated).dotdotdot({
                    wrap: 'letter',
					height: 300,
                    callback: function( isTruncated, orgContent ) {
                        if(isTruncated){
                            $(comment_data).find('.follow-reading-container').show();
                        }
                    }
                });
            });

        }

		userList = [<?
			$users = ideary::getUsersForCommentTagging();
			for($i = 0; $i < count($users); $i++) {
				if ($i > 0)
					echo ", ";
				echo "{id: " . $users[$i]->id . ", name: '" . str_replace("'", "\\'", trim($users[$i]->name)) . "'}\n";
			}
		?>];
		
		function getFilteredUserList(needle) {
			return $.grep(userList, function(e){
				return e.name.substring(0, needle.length).toLowerCase() == needle.toLowerCase()
			});
		}
		
		function tagUser() {
			var $user = $("#comment-tag-popup .results ul li.selected");
				
			if ($user.length != 1)
				return;
			
			var user = {
				id: $user.attr("data-id"),
				name: $user.text()
			};
			
			tinymce.execCommand('mceFocus',false,'comment-textarea');
			tinymce.activeEditor.selection.setContent("<input type='button' disabled='disabled' value='" + user.name + "' class='comment-author-tag' data-id='" + user.id + "'></input>&nbsp;");
				
		}
		
		$(document).ready(function(){
			$("#comment-tag-popup .results ul").delegate("li", "hover", function(event) {
				$("#comment-tag-popup .results ul li").removeClass("selected");
				$(this).addClass("selected");
			});
			
			$("#comment-tag-popup .results ul").delegate("li", "mousedown", function(event) {
				event.preventDefault();
				tagUser();
			});
			
			$("#comment-tag-popup .search input").keyup(function(event) {
				//console.debug(event.keyCode);
				if (event.keyCode <= 40 && event.keyCode >= 37)
					return;
				$("#comment-tag-popup .results ul").empty();
				
				if ($("#comment-tag-popup .search input").val().length < 2)
					return;
					
				var filteredUserList = getFilteredUserList($("#comment-tag-popup .search input").val());
								
				for(var i = 0; i < filteredUserList.length && i < 4; i++) {
					var selected = i == 0 ? "selected" : "";
					$("#comment-tag-popup .results ul").append("<li data-id='" + filteredUserList[i].id + "' class='" + selected + "'>" + filteredUserList[i].name + "</li>");
				}
				
			});
			
			$("#comment-tag-popup .search input").keydown(function(event) {
				//console.debug(event.keyCode);
				if (event.keyCode == 13) {
					event.preventDefault();
					tagUser();
					
				}
				if (event.keyCode == 27) {
					$("#comment-tag-popup").css("visibility", "hidden");
					tinymce.execCommand('mceFocus',false,'comment-textarea');
				}
				if (event.keyCode == 38) {
					event.preventDefault();
					var index = $("#comment-tag-popup .results ul li").index($("#comment-tag-popup .results ul li.selected"));
					var length = $("#comment-tag-popup .results ul li").length;
					if(length == 0)
						return;
					if (index > 0)
						index--;
					else
						index = length -1;
					$("#comment-tag-popup .results ul li").removeClass("selected");
					$("#comment-tag-popup .results ul li")[index].addClass("selected");
				}
				if (event.keyCode == 40) {
					event.preventDefault();
					var index = $("#comment-tag-popup .results ul li").index($("#comment-tag-popup .results ul li.selected"));
					var length = $("#comment-tag-popup .results ul li").length;
					if(length == 0)
						return;
					if (index < length -1)
						index++;
					else
						index = 0;
					$("#comment-tag-popup .results ul li").removeClass("selected");
					$("#comment-tag-popup .results ul li")[index].addClass("selected");
				}
			});
			
			$("#comment-tag-popup .search input").blur(function(event){
				$("#comment-tag-popup").css("visibility", "hidden");
				tinymce.execCommand('mceFocus',false,'comment-textarea');
			});
		
			$.get("<?=$this->baseurl?>/index.php?option=com_content&task=get_recommendations", {
				"article_id": <?=$this->item->id?>
			}).done(function(data){
				var recommendation = JSON.parse(data);
				
				$("#recommended-texts span.article a").attr("href", recommendation.url);
				$("#recommended-texts span.article a").text(recommendation.recommendation.title);
				$("#recommended-texts span.author").text(recommendation.recommendation.username);
				$("#recommended-texts").css("visibility", "visible").hide().fadeIn("slow");
			});
			
		});

    <?php endif ?>
	
	function updateCharCount() {
		var comment = $.trim(tinyMCE.activeEditor.getContent({format: "text"}));
		comment = comment.replace(/\s/gi, "");
		var commentLength = comment.length;
		
		$('#char-count').text(commentLength);
		
		if (commentLength < 1500) {
			$('#char-box').fadeOut();
		} else {
			$('#char-box').fadeIn();
		}
		
		if (commentLength <= 2000) {
			$('#comment-button').removeClass('disabled');
			$('#char-count').removeClass('insufficient-chars-count');
			//$('#char-count').addClass('enough-chars-count');
		} else {
			$('#comment-button').addClass('disabled');
			//$('#char-count').removeClass('enough-chars-count');
			$('#char-count').addClass('insufficient-chars-count');
		}
	
	}
	
    $(document).ready(function(){
		tinymce.init({
			selector:'textarea#comment-textarea', 
			menubar : false, toolbar: false, statusbar: false, 
			valid_elements : "p,strong/b,em/i,br,input[data-id|class='comment-author-tag'|disabled|value|type]",
			content_css: "templates/beez_20/css/tinymce_comments.css",
			setup : function(ed) {
				ed.on('keypress', function(e) {
					if(e.charCode == 64) {
						e.preventDefault();
						$("#comment-tag-popup .search input").val("");
						$("#comment-tag-popup").css("visibility", "visible");
						$("#comment-tag-popup .search input").focus();
						return;
					}
					
				});
				
				ed.on("keypress", function(e) {
					updateCharCount();
				});
				
				ed.on("keyup", function(e) {
					updateCharCount();
				});
				
				ed.on("keydown", function(e) {
					if (e.keyCode == 13 && e.ctrlKey) {
						e.preventDefault();
						publishComment();
					}
					
					updateCharCount();
					
				});

			}
		});
		
        if(Tools.readCookie("text-content-font-size") != null){
            text_content_font_size = Tools.readCookie("text-content-font-size");
        }

        if(Tools.readCookie("text-content-font-increase") != null){
            text_content_font_increase = Tools.readCookie("text-content-font-increase");
        }


        $("#text-view-text-content").css("font-size", text_content_font_size);

        <?php if($this->item->allow_comments): ?>
            setCountCommentsText();
            truncateComments();
        <?php endif ?>

        //$('#comment-button').addClass('disabled');
        $('#char-count').text('0');
        $('#char-count').removeClass('enough-chars-count');
        $('#char-count').addClass('insufficient-chars-count');

        $('.cancel-button').click(function(){


            if(home){

                <?php $x_scroll = (isset($_GET['x-scroll']))? $_GET['x-scroll'] : 0; ?>
                <?php $offset = (isset($_GET['offset']))? $_GET['offset'] : 20; ?>
                <?php $search = (isset($_GET['search']))? '&search='.$_GET['search'] : ''; ?>

                window.location.href = '<?php echo JRoute::_(JURI::base()."index.php?x-scroll=".$x_scroll."&offset=".$offset.$search); ?>';

            }
            else{

                if(history.length > 1){
                    window.history.back();
                }
                else{
                    window.location.href = '<?php echo JRoute::_(JURI::base()."index.php"); ?>';
                }

            }


        });

        $('#text-view-text-a-plus').click(function(){

            text_content_font_increase++;
            if(text_content_font_increase <= 3){
                $("#text-view-text-content").css("font-size", "+=1");
                Tools.createCookie("text-content-font-increase", text_content_font_increase);
                Tools.createCookie("text-content-font-size", $("#text-view-text-content").css("font-size"));
            }
            else{
                text_content_font_increase--;
            }

        });

        $('#text-view-text-a-minus').click(function(){

            text_content_font_increase--;
            if(text_content_font_increase >= -3){
                $("#text-view-text-content").css("font-size", "-=1");
                Tools.createCookie("text-content-font-increase", text_content_font_increase);
                Tools.createCookie("text-content-font-size", $("#text-view-text-content").css("font-size"));
            }
            else{
                text_content_font_increase++;
            }

        });

        $("body").delegate('.follow-reading', 'click', function(){

            var commentId = $(this).data('commentId');

            var comment_text_truncated = $('.comment-text-truncated[data-comment-id="'+commentId+'"]');
            $(comment_text_truncated).hide();

            var comment_text = $('.comment-text[data-comment-id="'+commentId+'"]');
            $(comment_text).show();

            var read_less = '<div class="read-less more-less-reading" data-comment-id="'+commentId+'">'+READ_LESS+'</div>';

            $('.follow-reading-container[data-comment-id="'+commentId+'"]').html(read_less);

        });

        $("body").delegate('.read-less', 'click', function(){

            var commentId = $(this).data('commentId');

            var comment_text = $('.comment-text[data-comment-id="'+commentId+'"]');
            $(comment_text).hide();

            var comment_text_truncated = $('.comment-text-truncated[data-comment-id="'+commentId+'"]');
            $(comment_text_truncated).show();

            var follow_reading = '<div class="follow-reading more-less-reading" data-comment-id="'+commentId+'">'+CONTINUE_READING+'</div>';

            $('.follow-reading-container[data-comment-id="'+commentId+'"]').html(follow_reading);

        });


        $("body").delegate('.hand-up', 'click', function(){

            if($(this).hasClass('disabled')){
                return false;
            }

            if(USER_LOGGED){

                var comment_id = $(this).data('commentId');
                var commenter_id = $(this).data('commenterId');
                $('.hand-up[data-comment-id="'+comment_id+'"]').addClass('disabled');
                $('.hand-down[data-comment-id="'+comment_id+'"]').addClass('disabled');
                $('.hand-up[data-comment-id="'+comment_id+'"]').parent().addClass('votedUp');

                $.post(
                    comment_vote_up_url,
                    {
                        comment_id: comment_id,
                        commenter_id: commenter_id,
                        text_id: text_id
                    },
                    function(data) {

                        if(data.success){
							data.votes_up_count = parseInt(data.votes_up_count);
							data.votes_down_count = parseInt(data.votes_down_count);
							var aggregatedVotes = data.votes_up_count - data.votes_down_count;
							
                            var hand_count = $('.hand-count[data-comment-id="'+comment_id+'"]');
                            $(hand_count).text(aggregatedVotes);
							
							hand_count.removeClass('noVotes');
							if (aggregatedVotes < 0) {
								hand_count.addClass('negativeVotes');
							} else {
								hand_count.removeClass('negativeVotes');
							}
                        }

                    }
                    , "json"
                ).fail(function() {
                    $('.hand-up[data-comment-id="'+comment_id+'"]').removeClass('disabled');
                    $('.hand-down[data-comment-id="'+comment_id+'"]').removeClass('disabled');
                });

            }
            else{
                /*$('#login-required-popup').lightbox_me({
                    centered: true,
                    closeSelector: '#close-login-required-popup'
                });*/
				
				document.getElementById("message_not_logged_in_action").style.display="block";
				$('#register_form').lightbox_me({
					centered: true,
					closeSelector: '#close-register_form-required-popup',
                    onClose: function(){
                        $('.register-form-error').hide();
                    }
				});		

				
            }

        });


        $("body").delegate('.hand-down', 'click', function(){

            if($(this).hasClass('disabled')){
                return false;
            }

            if(USER_LOGGED){

                var comment_id = $(this).data('commentId');
                $('.hand-up[data-comment-id="'+comment_id+'"]').addClass('disabled');
                $('.hand-down[data-comment-id="'+comment_id+'"]').addClass('disabled');
                $('.hand-up[data-comment-id="'+comment_id+'"]').parent().addClass('votedDown');

                $.post(
                    comment_vote_down_url,
                    { comment_id: comment_id },
                    function(data) {

                        if(data.success){

							data.votes_up_count = parseInt(data.votes_up_count);
							data.votes_down_count = parseInt(data.votes_down_count);
							var aggregatedVotes = data.votes_up_count - data.votes_down_count;
							
                            var hand_count = $('.hand-count[data-comment-id="'+comment_id+'"]');
                            hand_count.text(aggregatedVotes);

							
							hand_count.removeClass('noVotes');
							if (aggregatedVotes < 0) {
								hand_count.addClass('negativeVotes');
							} else {
								hand_count.removeClass('negativeVotes');
							}
							
							
                        }

                    }
                    , "json"
                ).fail(function() {
                    $('.hand-up[data-comment-id="'+comment_id+'"]').removeClass('disabled');
                    $('.hand-down[data-comment-id="'+comment_id+'"]').removeClass('disabled');
                });

            }
            else{
              /*  $('#login-required-popup').lightbox_me({
                    centered: true,
                    closeSelector: '#close-login-required-popup'
                });
				*/
				
				document.getElementById("message_not_logged_in_action").style.display="block";
				$('#register_form').lightbox_me({
					centered: true,
					closeSelector: '#close-register_form-required-popup',
                    onClose: function(){
                        $('.register-form-error').hide();
                    }
				});	
            }

        });
		
        $('#comment-button').click(function(){
			publishComment();
		});
		
		function publishComment() {
            if($(this).hasClass('disabled')){
                return false;
            }

            var comment = $.trim(tinyMCE.activeEditor.getContent());
			var commentLength = $(comment).text().length;
			
            if(commentLength > 2000){
                $('#comment-textarea').focus();
				
            } else{
                var result = checkUserLogged();

                if(result){
                    $('#comment-button').addClass('disabled');
                    $('#comment-ajax-loading').show();
                    $.post(
                        comment_text_url,
                        {
                            text_id: text_id,
                            commenter_id: USER_ID,
                            writer_id: writer_id,
                            comment: comment
                        },
                        function(data) {
                            result = data.success;
                            if(result){

                                var author_link = "<?php echo JRoute::_('index.php?option=com_users&view=profile&user_id='.$user->get('id')); ?>";

                                var comment_class = $('<div class="comment"></div>');

                                var commentator_img = $('<div class="commentator-img"></div>');
                                var img_link = $('<a href="'+author_link+'"></a>');
                                var img = $('<img>');
                                $(img).attr('src', comment_img2);
                                $(img).appendTo($(img_link));
                                $(img_link).appendTo($(commentator_img));
                                $(commentator_img).appendTo($(comment_class));

                                var comment_data = $('<div class="comment-data"></div>');
                                if(contrast_state == 3){
                                    $(comment_data).addClass('contrast-state-3-b');
                                }

                                var author_name_link = $('<a href="'+author_link+'" class="author-name-link"></a>');
                                var author_name = $('<div class="author-name"></div>');
                                $(author_name).text(USER_NAME);
                                $(author_name).appendTo($(author_name_link));
                                $(author_name_link).appendTo($(comment_data));

                                var comment_text_truncated = $('<div class="comment-text-truncated" data-comment-id="'+data.comment_id+'"></div>');
                                //$(comment_text_truncated).text(comment.truncate(comments_limit_chars).toString());
                                $(comment_text_truncated).html(comment);
                                $(comment_text_truncated).appendTo($(comment_data));

                                var comment_text = $('<div class="comment-text" data-comment-id="'+data.comment_id+'"></div>');
                                $(comment_text).html(comment);
                                $(comment_text).appendTo($(comment_data));

                                var follow_reading_hands = $('<div class="follow-reading-hands"></div>');


                                var follow_reading_container = $('<div class="follow-reading-container" data-comment-id="'+data.comment_id+'"></div>');
                                var follow_reading = $('<div class="follow-reading more-less-reading" data-comment-id="'+data.comment_id+'"></div>');
                                $(follow_reading).text(CONTINUE_READING);
                                $(follow_reading).appendTo($(follow_reading_container));


                                $(follow_reading_container).appendTo($(follow_reading_hands));

                                var hands_container = $('<div class="hands-container"></div>');
								
								var hand_count = $('<div class="hand-count noVotes" data-comment-id="'+data.comment_id+'"></div>');
                                $(hand_count).appendTo($(hands_container));
								
                                var hand_up = $('<div class="hand-up" data-comment-id="'+data.comment_id+'" data-commenter-id="'+USER_ID+'"></div>');
                                $(hand_up).appendTo($(hands_container));
								
                                var hand_down = $('<div class="hand-down" data-comment-id="'+data.comment_id+'"></div>');
                                $(hand_down).appendTo($(hands_container));
								
                                $(hands_container).appendTo($(follow_reading_hands));

                                var clear_both1 = $('<div style="clear: both;"></div>');
                                $(clear_both1).appendTo($(follow_reading_hands));

                                $(follow_reading_hands).appendTo($(comment_data));

                                $(comment_data).appendTo($(comment_class));

                                var clear_both2 = $('<div style="clear: both;"></div>');
                                $(clear_both2).appendTo($(comment_class));

                                $(comment_class).prependTo($('#comments'));

                                $(comment_text_truncated).dotdotdot({
                                    wrap: 'letter',
                                    callback: function( isTruncated, orgContent ) {
                                        if(isTruncated){
                                            $(follow_reading_container).show();
                                        }
                                    }
                                });

                                $('#comment-textarea').val('').focus();

                                count_comments++;
                                setCountCommentsText();


                            }
                        }
                        , "json"
                    ).done(function() {
							tinyMCE.activeEditor.setContent("")
							updateCharCount();
                            $('#comment-ajax-loading').hide();
                            $('#comment-button').removeClass('disabled');
                    }).fail(function() {
                            $('#comment-ajax-loading').hide();
                            $('#comment-button').removeClass('disabled');
                    });

                }

                return result;
            }
        }

        $('#applaud-bg-not-applauded').click(function(){

            var result = checkUserLogged();
		
			if (loggedin !=authorid)	{	
				if(result){
					$.post(
						clap_url,
						{
							text_id: text_id,
							writer_id: writer_id
						},
						function(data) {
							result = data.success;
							if(result){
								var html = '<div id="applaud-bg-applauded">'+THANKS+'!</div>';
								$('#applaud-bg').html(html);
								$('#applaud-bg').addClass('selected');
							}
						}
						, "json"
					);

				}
			}
            return result;

        });

        $("body").delegate('#favorite-icon', 'click', function(){

            if(USER_LOGGED){

                $.post(
                    add_to_favorites_url,
                    { text_id: text_id },
                    function(data) {

                        if(data.success){

                            var html = '<div id="no-favorite-icon" title="'+UNDO_ADD_TO_FAVORITES+'"></div>';
                            $('#favorite-icon-container').html(html);

                        }

                    }
                    , "json"
                );

            }
            else{
               document.getElementById("message_not_logged_in_action").style.display="block";
				$('#register_form').lightbox_me({
					centered: true,
					closeSelector: '#close-register_form-required-popup',
                    onClose: function(){
                        $('.register-form-error').hide();
                    }
				});		
            }

        });

        $("body").delegate('#no-favorite-icon', 'click', function(){

            if(USER_LOGGED){

                $.post(
                    undo_add_to_favorites_url,
                    { text_id: text_id },
                    function(data) {

                        if(data.success){

                            var html = '<div id="favorite-icon" title="'+ADD_TO_FAVORITES+'"></div>';
                            $('#favorite-icon-container').html(html);

                        }

                    }
                    , "json"
                );

            }
            else{
              document.getElementById("message_not_logged_in_action").style.display="block";
				$('#register_form').lightbox_me({
					centered: true,
					closeSelector: '#close-register_form-required-popup',
                    onClose: function(){
                        $('.register-form-error').hide();
                    }
				});		
            }

        });


        $("body").delegate('#save-button', 'click', function(){

            processSaveEvent(text_id, 'save-button-box', 'unarchive-button');

        });

        $("body").delegate('#unarchive-button', 'click', function(){

            processUnarchiveEvent(text_id, 'save-button-box', 'save-button');

        });

        $('#denounce').click(function(){

            if(USER_LOGGED){

                $.post(
                    denounce_url,
                    { text_id: text_id },
                    function(data) {

                        if(data.success){
                            var html = '<span id="denounced">'+YOU_ALREADY_REPORTED_THIS_TEXT+'</span>';
                            $('#denounce-button2').html(html);

                            if(data.send_email){

                                $.post(
                                    send_denounce_mail,
                                    { text_id: text_id },
                                    function(data) {

                                    }
                                    , "json"
                                );

                            }

                        }

                    }
                    , "json"
                );

            }
            else{
              document.getElementById("message_not_logged_in_action").style.display="block";
				$('#register_form').lightbox_me({
					centered: true,
					closeSelector: '#close-register_form-required-popup',
                    onClose: function(){
                        $('.register-form-error').hide();
                    }
				});		
            }

        });

        $("body").delegate('#follow-button2', 'click',function(){

            if(USER_LOGGED){

                $('#follow-button2-progress').width($(this).outerWidth());
                $('#follow-button2-progress').removeClass('unfollow');
                $('#follow-button2-progress').addClass('follow');
                $(this).hide();
                $('#follow-button2-progress').show();

                $.post(
                    follow_url,
                    { writer_id: writer_id },
                    function(data) {

                        if(data.success){
                            var html = '<div id="follow-button2-progress" class="dot-progress-button"></div>';
                            html += '<div id="unfollow-button2" class="write-text-form-button follow-button-class">'+FOLLOWING+'</div>';
                            $('#follow-button2-container').html(html);
                        }

                    }
                    , "json"
                );

            }
            else{
                document.getElementById("message_not_logged_in_action").style.display="block";
				$('#register_form').lightbox_me({
					centered: true,
					closeSelector: '#close-register_form-required-popup',
                    onClose: function(){
                        $('.register-form-error').hide();
                    }
				});		
            }
        });

        $("body").delegate('#unfollow-button2', 'click', function(){

            if(USER_LOGGED){

                $('#follow-button2-progress').width($(this).outerWidth());
                $('#follow-button2-progress').removeClass('follow');
                $('#follow-button2-progress').addClass('unfollow');
                $(this).hide();
                $('#follow-button2-progress').show();

                $.post(
                    unfollow_url,
                    { writer_id: writer_id },
                    function(data) {

                        if(data.success){
                            var html = '<div id="follow-button2-progress" class="dot-progress-button"></div>';
                            html += '<div id="follow-button2" class="write-text-form-button follow-button-class">'+FOLLOW_AUTHOR+'</div>';
                            $('#follow-button2-container').html(html);
                        }

                    }
                    , "json"
                );

            }
            else{
                document.getElementById("message_not_logged_in_action").style.display="block";
				$('#register_form').lightbox_me({
					centered: true,
					closeSelector: '#close-register_form-required-popup',
                    onClose: function(){
                        $('.register-form-error').hide();
                    }
				});		
            }

        });

        /*$('#message-icon').click(function(){

            $('#message-popup').lightbox_me({
                centered: true,
                closeSelector: '#close-message-popup',
                onClose: function(){
                    $('#subject-field').val('');
                    $('#message-field').val('');
                }
            });

        });*/

        $("body").delegate('#send-button', 'click', function(){

            var subject = $('#subject-field').val();
            var message = $('#message-field').val();
            processSendMessageEvent(writer_id, subject, message, 'send-button-box', 'message-loading', 'message-popup', 'subject-field', 'message-field');

        });

        $('#text-view-text-contrast').click(function(){

            contrast_state++;
            $('#text-view-text-content').removeClass('contrast-state-2');
            $('#text-view').removeClass('contrast-state-3');
            $('#text-view-div-line').removeClass('contrast-state-3-a');
            $('.comment-data').removeClass('contrast-state-3-b');

            switch(contrast_state)
            {
                case 1:

                    break;
                case 2:
                    $('#text-view-text-content').addClass('contrast-state-2');
                    break;
                case 3:
                    $('#text-view').addClass('contrast-state-3');
                    $('#text-view-div-line').addClass('contrast-state-3-a');
                    $('.comment-data').addClass('contrast-state-3-b');
                    break;
                default:
                    contrast_state = 1;
            }


        });

    });

</script>

<style type="text/css">
#all
{
	padding-top:35px !important;
}
#topbar{
    position: fixed !important;
}
</style>

<div id="text-view">

    <div id="close-icon" class="cancel-button" title="<?php echo JText::_('JCANCEL') ?>"></div>
	
    <div id="text-view-text-header" >
		<div id="text-view-category-color" style="background: <?= $this->item->first ? "#E02424" : "#C1C2C2" ?>;"></div>
        
		<?
			if($user->id!=$this->writer->id){ 
				$userProfileUrl = "index.php?option=com_contact&view=public&id=" . $this->writer->id;
			}else{
				$userProfileUrl = "index.php?option=com_users&view=profile";
			}
		?>
		<a href="<?=$userProfileUrl?>">
		<img src="<?=ideary::getUserImagePath($this->writer->id, "200")?>" style="
			float: left; height: 88px; width: 88px; margin-right: 12px; border: 2px solid lightgray;
		" />
		</a>
		
        <div id="text-view-text-title"><?=$this->item->title?></div>
        <div id="text-view-text-author">
			<?=JText::_('BY')?> 
			<a href="<?=$userProfileUrl?>"><?=$this->writer->name?></a>
			
			<?php if($this->item->created != '0000-00-00 00:00:00'): ?>
            <div id="text-view-text-date" style="text-transform: lowercase;" ><?php echo ideary::textDate($this->item->created) ?></div>
			<?php endif ?>
		</div>
    </div>

   <div id="text-view-text-adjustments">
        <div id="text-view-text-a-plus"></div>
        <div id="text-view-text-a-minus"></div>
        <div id="text-view-text-contrast"></div>
    </div>

    <div id="text-view-text-content"><?php echo nl2br($this->item->introtext) ?></div>

    <?php if($this->has_tags): ?>
        <div id="text-view-text-tags"><?php echo $this->tags_text ?></div>
    <?php endif ?>

    <div id="social-icons-container">

        <?php if($this->is_applauded): ?>
        <div id="applaud-bg" class="selected">
            <div id="applaud-bg-applauded">
                <?php echo JText::_('THANKS').'!' ?>
            </div>
        </div>
        <?php else: ?>
        <div id="applaud-bg">
            <div id="applaud-bg-not-applauded">

                <?php echo JText::_('JCLAP') ?>!
            </div>
        </div>
        <?php endif; ?>


        <div id="social-icons">

            <div id="favorite-icon-container" class="social-icon">
                <?php if($this->is_favorite): ?>
                    <div id="no-favorite-icon" title="<?php echo JText::_('UNDO_ADD_TO_FAVORITES') ?>"></div>
                <?php else: ?>
                    <div id="favorite-icon" title="<?php echo JText::_('ADD_TO_FAVORITES') ?>"></div>
                <?php endif; ?>
            </div>

		
			<a title="Compartir en Facebook" id="fb-icon" class="social-icon" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);?>" target="_blank">
					<div></div>
			</a> 
		
			<a title="Compartir en Twitter" href="https://twitter.com/share" target="_blank" id="tw-icon" class="social-icon"  data-via="ideary" data-lang="es" data-count="none" data-dnt="true">
			</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
			<div style="clear: both;"></div>
        </div>
		
		<? if (!$this->item->pinned) { ?>
		
        <div id="denounce-button2">
            <?php if($this->is_reported): ?>
                <span id="denounced"><?php echo JText::_('YOU_ALREADY_REPORTED_THIS_TEXT') ?></span>
            <?php else: ?>
                <span id="denounce"><?php echo JText::_('DENOUNCE') ?></span>
            <?php endif; ?>
        </div>
		
		<div id="recommended-texts" style="visibility: hidden; " >
			
			<div>Si te gustó este ideary, tal vez te guste... </div>
			
			<div class="recommendation" >
				<span class="article"><a href=""></a></span><span>por</span><span class="author"></span>
				
			</div>
		</div>
		
		<? } ?>
		
    </div>

    <div id="text-view-div-line"></div>

    <?php if($this->item->allow_comments): ?>
    <div id="comment-container">
        <a name="comments-section"></a>
        <div id="comment-text"></div>

        <div id="comment-textarea-container">
            <textarea id="comment-textarea"></textarea>
			<div id="comment-tag-popup">
				<div class="search"><input type="text" placeholder="<?=$user->name?>" ></div>
				<div class="results">
					<ul></ul>
				</div>
			</div>
			
            <div id="comment-button-chars">
                <div id="comment-button" class="write-text-form-button"><?php echo JText::_('COMMENT') ?></div>
                <div id="comment-ajax-loading"></div>
                <div id="char-box" style="display: none; " >
                    <span id="char-count">0</span> / 2000
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>

        <div id="comments">

            <?php foreach($this->comments as $comment): ?>
				<?
					$comment->comment = trim($comment->comment);
					
					$comment->comment = preg_replace_callback(
						"/(?:http\:\/\/)?(?:www\.)?ideary\.co(?:m\.ar)?\/([a-zA-Z-]*\/)?([^ \.,;\n\r\t<>]*)/i",
						function ($matches) {
							$category = $matches[1];
							$title = ucfirst(str_replace("-", " ", $matches[2]));
							return "<a class='idearyLink' href='http://www.ideary.co/$category{$matches[2]}'>$title</a>";
						} ,
						$comment->comment
					);
					
					$comment->comment = preg_replace(
						"/((?:http\:\/\/)?(?:www\.)?ideary\.co(?:m\.ar)?\/index\.php(?:[^ \.,;\n\r\t<>]*))/i",
						"<a class='idearyLink' href='$0'>$0</a>",
						$comment->comment
					);
					
					$comment->comment = str_replace("\r\n", "<br>", $comment->comment);
					$comment->comment = str_replace("\r", "<br>", $comment->comment);
					$comment->comment = str_replace("\n", "<br>", $comment->comment);
				
                    $authorLink = ($comment->user_id != $user->id) ?
						JRoute::_('index.php?option=com_contact&view=public&id='.$comment->user_id) :
						JRoute::_('index.php?option=com_users&view=profile&user_id='.$comment->user_id);
					
                ?>

                <div class="comment">
                    <a name="comment-<?php echo $comment->id ?>"></a>
                    <div class="commentator-img">
						<a href="<?php echo $authorLink; ?>">
							<?php echo Ideary::getUserImage($comment->user_id,"50",$comment->author_name) ;?>
						</a>
                    </div>

                    <div class="comment-data">
                        <a href="<?php echo $authorLink; ?>" class="author-name-link">
                            <div class="author-name"><?php echo $comment->author_name ?></div>
                        </a>
                        <div class="comment-text-truncated" data-comment-id="<?php echo $comment->id ?>">
                            <?php echo $comment->comment ?>
                        </div>
                        <div class="comment-text" data-comment-id="<?php echo $comment->id ?>">
                            <?php echo $comment->comment ?>
                        </div>

                        <div class="follow-reading-hands">


                            <div class="follow-reading-container" data-comment-id="<?php echo $comment->id ?>">
                                <div class="follow-reading more-less-reading" data-comment-id="<?php echo $comment->id ?>"><?php echo JText::_('CONTINUE_READING') ?></div>
                            </div>

							<?
								$disabled_class = ($comment->comment_id)? 'disabled' : '';
								
								if ($comment->comment_id)
									$voted_class = ($comment->voted_up) ? "votedUp" : "votedDown";
								else
									$voted_class = "";
								$total_votes = $comment->votes_up - $comment->votes_down;
								$handCountClass = "";
								if ($total_votes < 0) 
									$handCountClass = "negativeVotes";
								else if ($total_votes === 0) 
									$handCountClass = "noVotes";
								
							?>
                            <div class="hands-container <?=$voted_class?>">
                                <div class="hand-count <?=$handCountClass?>" data-comment-id="<?php echo $comment->id ?>"><?php echo $total_votes ?></div>
                                <div class="hand-up <?php echo $disabled_class ?>" data-comment-id="<?php echo $comment->id ?>" data-commenter-id="<?php echo $comment->user_id ?>"></div>
                                <div class="hand-down <?php echo $disabled_class ?>" data-comment-id="<?php echo $comment->id ?>"></div>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>

                    <div style="clear: both;"></div>
                </div>

            <?php endforeach ?>

        </div>
    </div>
    <?php endif ?>

    <div id="author-container">

        <div id="author-info">
            <div id="author-data">
                <div id="author-name">
					<?php if($user->id!=$this->writer->id){ ?>
						<a class="article-author-name" href="<?php echo JRoute::_('index.php?option=com_contact&view=public&id=' . $this->writer->id);?>"><?php echo $this->writer->name ?></a>
					<?php }else{?>
						<a class="article-author-name" href="<?php echo JRoute::_('index.php?option=com_users&view=profile');?>"><?php echo $this->writer->name ?></a>
					<?php }?>
				</div>
                <div id="author-nationality">Argentina</div>
            </div>

            <div id="author-photo">
				<?php if($user->id!=$this->writer->id){ ?>
					<a href="<?php echo JRoute::_('index.php?option=com_contact&view=public&id=' . $this->writer->id);?>">
						<?php echo Ideary::getUserImage($this->writer->id,"50",$this->writer->name) ;?>				
					</a>
				<?php }else{?>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=profile');?>">
						<?php echo Ideary::getUserImage($this->writer->id,"50",$this->writer->name) ;?>				
					</a>
				<?php }?>
            </div>
            <div style="clear: both;"></div>
        </div>

        <?php if(($this->item->created_by != $this->user->get('id')) && ($this->user->get('id') != 0)): ?>
        <div id="author-buttons-container">
            <div id="follow-button2-container">
                <div id="follow-button2-progress" class="dot-progress-button"></div>
                <?php if($this->is_writer_followed): ?>
                    <div id="unfollow-button2" class="write-text-form-button follow-button-class"><?php echo JText::_('FOLLOWING') ?></div>
                <?php else: ?>
                    <div id="follow-button2" class="write-text-form-button follow-button-class"><?php echo JText::_('FOLLOW_AUTHOR') ?></div>
                <?php endif; ?>
            </div>

            <!--<div id="message-icon" class="send-message-clickable" title="<?php //echo JText::_('SEND_MESSAGE') ?>"></div>-->

            <div style="clear: both;"></div>
        </div>
        <?php endif; ?>

        <div style="clear: both;"></div>
		<?php if(!empty($this->writerFiveRecentTexts)):?>
        <div id="other_items"><?= str_replace("{X}", $this->writer->name, JText::_('OTHER_ARTICLES_BY_AUTHOR')) ?></div>

        <div id="items">
            <?php foreach($this->writerFiveRecentTexts as $writerRecentText): ?>
            <div class="item-text">
                <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($writerRecentText->id, $writerRecentText->catid)) ?>">
                    <?php echo $writerRecentText->title ?>
                </a>
            </div>
            <?php endforeach ?>
        </div>
		<?php endif;?>
    </div>

    <div style="clear: both;"></div>

</div>

<?php //echo JHtml::_('icon.edit', $this->item, $params); ?>
<div id="message-popup">

    <div id="close-message-popup" class="close-button" title="<?php echo JText::_('CLOSE') ?>">
        <img src="<?php echo JURI::base() . "templates/beez_20/images/close-button.png"; ?>"/>
    </div>

    <div id="message-to-box"><h1><?php echo JText::_('MESSAGE_TO')." ".$author; ?></h1></div>

    <div id="subject-box"><?php echo JText::_('SUBJECT') ?></div>

    <div id="subject-field-box"><input type="text" id="subject-field"></div>

    <div id="message-box"><?php echo JText::_('MESSAGE') ?></div>

    <div id="message-field-box"><textarea id="message-field"></textarea></div>

    <div id="send-button-box"><button type="button" id="send-button"><?php echo JText::_('SEND') ?></button></div>

    <div id="message-loading">
        <img src="<?php echo JURI::base() . "templates/beez_20/images/loading3.gif"; ?>"/>
    </div>
</div>
