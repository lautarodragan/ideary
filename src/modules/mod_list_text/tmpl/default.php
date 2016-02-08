<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_list_text
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<style type="text/css">
    #contentarea{
        padding: 0 !important;
    }

    #wrapper2{
        width: 100% !important;
    }

    .h-scroll {
        width: 100% !important;
    }

    #main{
        padding: 0 !important;
    }


    #wrapper2{
        padding: 0 !important;
    }

    #breadcrumbs {
        margin: 0 !important;
    }

    #header{
        padding: 0 !important;
        height: 38px !important;
    }

</style>

<script type="text/javascript">

    var texts = new Array();
    //var x_scroll = <?php //echo (isset($_GET['x-scroll']))? $_GET['x-scroll'] : 0 ?>;

    var category_post = '<?php echo $category_post ?>';
    var period_post = '<?php echo $period_post ?>';
    var text_search_post = '<?php echo $text_search_post ?>';
    var userId = '<?php echo $userId ?>';
    var offset = <?php echo $offset ?>;
    var can_process_scroll_forward = true;
    var count_all_texts = <?php echo $countAlltexts ?>;
    var more_texts_bar_showed = false;
    var can_scroll_foward = true;
    var let_scroll_foward = true;
	var old_num_rows = 0;

    var count_texts = <?php echo count($texts); ?>;
    var count_users = <?php echo count($users); ?>;

    function resize(){
		var browser_height = $(window).height();
        var topbar_height = $("#topbar").height();

        if(texts.length > 0){

            var user_search_results_height = $('#user-search-results').outerHeight(true);
            user_search_results_height = (user_search_results_height == null)? 0 : user_search_results_height;

            var h_scroll_users_height = $('#h-scroll-users').outerHeight(true);
            h_scroll_users_height = (h_scroll_users_height == null)? 0 : h_scroll_users_height;

            var text_search_results_height = $('#text-search-results').outerHeight(true);
            text_search_results_height = (text_search_results_height == null)? 0 : text_search_results_height;

            var remaining_height = browser_height - (topbar_height + user_search_results_height + h_scroll_users_height + text_search_results_height);

            var h_scroll_container_margin_top = $('#h-scroll-container').css('margin-top').replace("px","");
            $('#h-scroll-container').css("height", (remaining_height-h_scroll_container_margin_top)+"px");

            var num_rows = Math.floor(remaining_height/238);
            num_rows = (num_rows == 0) ? 1 : num_rows;
			
			if (old_num_rows != num_rows) {
				old_num_rows = num_rows;
			
			
				var rows = new Array();
				for (i = 0; i < num_rows; i++)
					rows[i] = new Array();

				var current_row = 0;
				for (i = 0; i < texts.length; i++){

					rows[current_row].push(texts[i]);
					
					if(current_row == num_rows - 1)
						current_row = 0;
					else
						current_row++;
					
				}

				var h_scroll = $('<div class="h-scroll"></div>');
				$(h_scroll).css('height', (num_rows*238)+"px");
				h_scroll.scroll(function(event){
					window.updateScrollbarPosition();
				});
				$('#h-scroll-container').html($(h_scroll));

				var li;
				for (i = 0; i < num_rows; i++){
					var ul_width = 0;
					var ul = $('<ul class="ul-text-list"></ul>');
					$(ul).css('width', (rows[i].length*928)+"px");

					$(h_scroll).append($(ul));

					for (j = 0; j < rows[i].length; j++){

						li = $('<li class="li-text"></li>');
						$(ul).append($(li));

						var text = $(rows[i][j]);

						$(li).html($(text));

						/*var text_data = $(text).find('.text-data');
						var text_content = $(text).find('.text-content');
						$(text_content).css('width', $(text_data).width()+"px");
						$(text_content).show();*/
						//$(text_content).dotdotdot();

						/*var text_bottom = $(text).find('.text-bottom');
						$(text_bottom).css('width', $(text_data).width()+"px");*/

					}

				}
				
				window.updateScrollbarPosition();
				
				$('.text-content').dotdotdot({
					ellipsis: ''
				});

                $('.more-applauded-text-title').dotdotdot({
                    watch: true,
                    wrap: 'letter'
                });

                setTextAuthorDivWidth();

				resizeMoreTextsBar();

				$('#more-texts-bar').hide();
				more_texts_bar_showed = false;
				
				
			}
        }

    }

    function resizeMoreTextsBar(){

        $('#more-texts-bar').css('height', $('.h-scroll').height()-20);
        var more_texts_bar_height = $('#more-texts-bar').height();
        var more_texts_bar_center_height = $('#more-texts-bar-center').height();
        var more_texts_bar_center_margin = Math.floor((more_texts_bar_height - more_texts_bar_center_height)/2);
        $('#more-texts-bar-center').css('margin-top', more_texts_bar_center_margin+'px');

    }

    function appendMoreTexts(texts){

        if(texts.length > 0){

            var ul_text_lists = $('.h-scroll ul.ul-text-list');

            var num_rows = ul_text_lists.length;

            var li;
            var current_row = 0;
            for (i = 0; i < texts.length; i++){

                li = $('<li class="li-text"></li>');
                var text = $(texts[i]);
                $(li).html($(text));
                $(ul_text_lists[current_row]).css('width', '+=928px');
                $(ul_text_lists[current_row]).append($(li));

                var text_content = $(text).find('.text-content');
                $(text_content).dotdotdot({
                    ellipsis: ''
                });

                if(current_row == (num_rows-1)){
                    current_row = 0;
                }
                else{
                    current_row++;
                }
            }

            can_scroll_foward = true;
            let_scroll_foward = true;

            setTextAuthorDivWidth();
			
			window.updateScrollbarPosition();
        }
    }

    function getMoreTexts(){

        can_process_scroll_forward = false;
        can_scroll_foward = false;

        $.post(
            get_more_texts_url,
            {
                category_post: category_post,
                period_post: period_post,
                text_search_post: text_search_post,
                userId: userId,
                offset: offset
            },
            function(data) {

                if(data.texts_array_html.length > 0){

                    $('#more-texts-bar').hide();
                    more_texts_bar_showed = false;

                    for(i = 0; i < data.texts_array_html.length; i++){
                        texts.push(data.texts_array_html[i]);
                    }

                    appendMoreTexts(data.texts_array_html);
                    offset += data.texts_array_html.length;

                }

            }
            , "json"
        ).done(function(){
            can_process_scroll_forward = true;
            $('#more-texts-bar').removeClass('disabled');
            resizeMoreTextsBar();
        }).fail(function(){
            can_process_scroll_forward = true;
            $('#more-texts-bar').removeClass('disabled');
            resizeMoreTextsBar();
        });

    }


    function processScrollForward(distance){

		if (typeof distance === "undefined")
			distance = 700;
			
        if(let_scroll_foward){

            var ul_text_lists = $('.h-scroll .ul-text-list');

            var max_ul_text_list_width = 0;
            var max_ul_last_text = $(ul_text_lists[0]).find('.text:last');

            for(i = 0; i < ul_text_lists.length; i++){
                var last_text = $(ul_text_lists[i]).find('.text:last');
                var last_text_right_position = $(last_text).position().left + $(last_text).outerWidth();

                if(last_text_right_position > max_ul_text_list_width){
                    max_ul_text_list_width = last_text_right_position;
                    max_ul_last_text = last_text;
                }
            }

            var max_ul_right_position = $(max_ul_last_text).offset().left + $(max_ul_last_text).outerWidth();
            var screen_width = $(window).width();
            var remaining_width = max_ul_right_position - screen_width;

            var more_texts_bar_width = $('#more-texts-bar').outerWidth(true);
            more_texts_bar_width += parseInt($('#more-texts-bar').css('right').replace("px",""));

            scrollLeft = $('.h-scroll').scrollLeft();

            if(remaining_width < 700){
                scrollLeft += remaining_width + more_texts_bar_width;
                let_scroll_foward = false;
            }
            else{
                scrollLeft += distance;
            }

            $('.h-scroll').stop().animate({scrollLeft: scrollLeft}, 500, function(){

                var last_text_right_position = $(max_ul_last_text).offset().left + $(max_ul_last_text).outerWidth();
                var screen_width = $(window).width();
                if(((last_text_right_position - screen_width) < 0) && can_process_scroll_forward && (count_all_texts > offset)){

                    $('#more-texts-bar').fadeIn('slow');
                    more_texts_bar_showed = true;

                }

            });

        }
    }

    function processScroll(delta, distance){
        if (typeof distance === "undefined")
			distance = 700;
		
        if(!can_scroll_foward){
            return false;
        }

        var val = $('.h-scroll').scrollLeft() - (delta * distance);

        if(delta > 0){
            if(more_texts_bar_showed){
                $('#more-texts-bar').hide();
                more_texts_bar_showed = false;
            }
            let_scroll_foward = true;
            $('.h-scroll').stop().animate({scrollLeft:val}, 500);
        }
        else{
            if(more_texts_bar_showed){
                $('#more-texts-bar').addClass('disabled');
                resizeMoreTextsBar();
                getMoreTexts();
            }
            else{
                processScrollForward(distance);
            }
        }
    }

    function processUsersScroll(delta){
        var val = $('#h-scroll-users').scrollLeft() - (delta * 700);
        $('#h-scroll-users').stop().animate({scrollLeft:val}, 1000);
    }

	function idearyScroll(delta) {
		if(count_texts > 0){
			var offset = 700;
			
			if ($(".h-scroll > ul").length == 1) {				
				var elements = $("#h-scroll-container > div > ul > li > div");
				var filteredElements = elements.filter(function(index){return $(elements[index]).offset().left > 10});

				offset = $(filteredElements).offset().left - 5;
			}
			
			if(count_users > 0){
				var user_results = $(event.target).parents('#user-results');
				var topbar = $(event.target).parents('#topbar');

				if((user_results.length != 0) || ($(event.target).is('#user-results')) || (topbar.length != 0) || ($(event.target).is('#topbar')))
					processUsersScroll(delta);
				else
					processScroll(delta, offset);
				

			}else{
				processScroll(delta, offset);
			}
		}else{
			if(count_users > 0)
				processUsersScroll(delta);
			
		}
	}

    $(document).ready(function(){

        resize();

        //$('.h-scroll').scrollLeft(x_scroll);

        var more_texts_bar = '<div id="more-texts-bar">' +
            '<div id="more-texts-bar-center"></div>' +
            '</div>';

        $('#wrapper2').append(more_texts_bar);

        resizeMoreTextsBar();

        $(window).resize(resize);

        $("body").delegate('.user-box-follow-button', 'click',function(){

            if(USER_LOGGED){

                var writer_id = $(this).data('writerId');

                var user_box_follow_button_container = $(this).parents('.user-box-follow-button-container');
                var user_box_follow_progress = $(user_box_follow_button_container).find('.user-box-follow-progress');
                $(user_box_follow_progress).width($(this).outerWidth());
                $(user_box_follow_progress).removeClass('unfollow');
                $(user_box_follow_progress).addClass('follow');
                $(this).hide();
                $(user_box_follow_progress).show();

                $.post(
                    follow_url,
                    { writer_id: writer_id },
                    function(data) {

                        if(data.success){
                            var html = '<div class="user-box-follow-progress dot-progress-button"></div>';
                            html += '<div class="user-box-following-button write-text-form-button" data-writer-id="'+writer_id+'">'+FOLLOWING+'</div>';
                            $('.user-box-follow-button-container[data-writer-id="'+writer_id+'"]').html(html);
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

        $("body").delegate('.user-box-following-button', 'click', function(){

            if(USER_LOGGED){

                var writer_id = $(this).data('writerId');

                var user_box_follow_button_container = $(this).parents('.user-box-follow-button-container');
                var user_box_follow_progress = $(user_box_follow_button_container).find('.user-box-follow-progress');
                $(user_box_follow_progress).width($(this).outerWidth());
                $(user_box_follow_progress).removeClass('follow');
                $(user_box_follow_progress).addClass('unfollow');
                $(this).hide();
                $(user_box_follow_progress).show();

                $.post(
                    unfollow_url,
                    { writer_id: writer_id },
                    function(data) {

                        if(data.success){
                            var html = '<div class="user-box-follow-progress dot-progress-button"></div>';
                            html += '<div class="user-box-follow-button write-text-form-button" data-writer-id="'+writer_id+'">'+FOLLOW+'</div>';
                            $('.user-box-follow-button-container[data-writer-id="'+writer_id+'"]').html(html);
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

        $("body").delegate('.text-saved-icon', 'click', function(){

            if(USER_LOGGED){

                var text_id = $(this).data('textId');
                var text = $(this).parents('.text');
                var text_index = $(text).data('index');

                $.post(
                    unarchive_url,
                    { text_id: text_id },
                    function(data) {

                        if(data.success){
                            var html = '<div class="text-save-icon" title="'+SAVE+'" data-text-id="'+text_id+'"></div>';
                            $('.save-container[data-text-id="'+text_id+'"]').html(html);

                            var text_element = $(texts[text_index]);
                            $(text_element).find('.save-container[data-text-id="'+text_id+'"]').html(html);
                            texts[text_index] = $(text_element);
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

        $("body").delegate('.text-save-icon', 'click', function(){

            if(USER_LOGGED){

                var text_id = $(this).data('textId');
                var text = $(this).parents('.text');
                var text_index = $(text).data('index');

                $.post(
                    add_to_saved_url,
                    { text_id: text_id },
                    function(data) {

                        if(data.success){
                            var html = '<div class="text-saved-icon" title="'+UNARCHIVE+'" data-text-id="'+text_id+'"></div>';
                            $('.save-container[data-text-id="'+text_id+'"]').html(html);

                            var text_element = $(texts[text_index]);
                            $(text_element).find('.save-container[data-text-id="'+text_id+'"]').html(html);
                            texts[text_index] = $(text_element);

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

        $('.no-applauded').click(function(){

            var text_id = $(this).data('id');
            var writer_id = $(this).data('writerId');
            if(processClapEvent2(text_id, writer_id)){
                $(this).removeClass("no-applauded");
                $(this).addClass("already-applauded");
                $(this).attr('title', ALREADY_APPLAUDED);
            }

        });

        $("body").delegate('.save', 'click', function(){

            var text_id = $(this).data('id');
            if(processSaveEvent2(text_id)){
                $(this).removeClass("save");
                $(this).addClass("unarchive");
                $(this).attr('title', UNARCHIVE);
            }

        });

        $("body").delegate('.unarchive', 'click', function(){

            var text_id = $(this).data('id');
            if(processUnarchiveEvent2(text_id)){
                $(this).removeClass("unarchive");
                $(this).addClass("save");
                $(this).attr('title', SAVE);
            }

        });

        $("body").delegate('.fav', 'click', function(){

            var text_id = $(this).data('id');
            if(processAddFavoritesEvent2(text_id)){
                $(this).removeClass("fav");
                $(this).addClass("des-fav");
                $(this).attr('title', UNDO_ADD_TO_FAVORITES);
            }

        });

        $("body").delegate('.des-fav', 'click', function(){

            var text_id = $(this).data('id');
            if(processUndoAddFavoritesEvent2(text_id)){
                $(this).removeClass("des-fav");
                $(this).addClass("fav");
                $(this).attr('title', ADD_TO_FAVORITES);
            }

        });
		
        $('body').bind('mousewheel', function(event, delta) {
            idearyScroll(delta);
			event.preventDefault();
        });

		$('body').bind("touchmove", function(event){
			var t = event.originalEvent.touches[0];

			idearyScroll(t.pageX < document.touchScrollStartX ? -1 : 1);

			event.preventDefault();
		});

		$('body').bind("touchstart", function(event){
			var t = event.originalEvent.touches[0];
			document.touchScrollStartX = t.pageX;
		});

        /*$("body").delegate('.text-title, .text-img', 'mousedown', function(e){

            if(e.which != 3)
            {
                var x_position = $('.h-scroll').scrollLeft();
                var text = $(this).parents('.text');
                var save_container = $(text).find('.save-container');
                var text_id = $(save_container).data('textId');
                var url = 'index.php?option=com_content&view=article&id='+text_id+'&x-scroll='+x_position;

                switch(e.which)
                {
                    case 1:
                        window.location.href = url;
                        break;
                    case 2:
                        window.open(url, "_blank");
                        break;
                }


            }
            e.preventDefault();
        });*/

        $("body").delegate('#more-texts-bar', 'click', function(){

            if($(this).hasClass('disabled')){
                return false;
            }

            $(this).addClass('disabled');
            resizeMoreTextsBar();

            getMoreTexts();

        });


        /*$("body").delegate('.text-title, .text-img', 'click', function(e){

            var x_position = $('.h-scroll').scrollLeft();
            var text = $(this).parents('.text');
            var save_container = $(text).find('.save-container');
            var text_id = $(save_container).data('textId');
            var search = '<?php echo ($text_search_post)? "&search=".$text_search_post : ""?>';
            var url = '<?php echo JURI::base() ?>index.php?option=com_content&view=article&id='+text_id+'&x-scroll='+x_position+'&offset='+offset+search+'&home=true';

            window.location.href = url;

            e.preventDefault();

        });*/

        $('body').keyup(function(event){

            var target = $(event.target);

            var makeScroll = false;

            var delta = 0;
            if(event.which == 37){
                delta = 1;
                makeScroll = true;
            }
            else if(event.which==39){
                delta = -1;
                makeScroll = true;
            }

            if(makeScroll){
                if(!$(target).is('input')){

                    if(count_texts > 0){
                        processScroll(delta);
                    }
                    else{
                        if(count_users > 0){
                            processUsersScroll(delta);
                        }
                    }

                }
            }

        });


    });
</script>

<?php if(isset($text_search_post) && ($text_search_post != "")): ?>

<div id="user-results">

    <div id="user-search-results">
        <div id="user-search-results-icon"></div>
        <?php
            if(count($users)!=1){
                $user_search_results_text = JText::_('X_AUTHORS_FOUND_FOR_SEARCH');
            }else{
                $user_search_results_text = JText::_('X_AUTHORS_FOUND_FOR_SEARCH_ONE');
            }
            $user_search_results_text = str_replace('{X}', count($users), $user_search_results_text);
        ?>
        <div id="user-search-results-text"><?php echo $user_search_results_text ?> “<b><?php echo $_POST["text-search"] ?></b>”.</div>
    </div>

    <div style="clear: both;"></div>

    <?php if(!empty($users)): ?>

    <div id="h-scroll-users">

        <ul id="ul-users" style="width: <?php echo (273*(count($users))) ?>px;">
        <?php foreach($users as $user1): ?>
        <li class="li-users">
        <div class="user-box">

            <div class="user-box-image">

                <?php

                    if($user1->id == $user->get('id')){
                        $user_url = JRoute::_('index.php?option=com_users&view=profile');
                    }
                    else{
                        $user_url = JRoute::_('index.php?option=com_contact&view=public&id='.$user1->id);
                    }

                ?>

                <a href="<?php echo $user_url ?>" title="<?php echo $user1->name?>">
                    <?php echo Ideary::getUserImage($user1->id, "200", $user1->name, 'style="width:110px;height:110px;"'); ?>
                </a>
            </div>

            <div class="user-box-content">

                <div class="user-name">
                    <a href="<?php echo $user_url ?>" title="<?php echo $user1->name?>">
                        <?php echo $user1->name ?>
                    </a>
                </div>
                <!--<div class="user-ranking"><?php //echo $user1->ranking ?></div>-->

                <?php if($user1->id != $user->get('id')): ?>
                <div class="user-box-follow-button-container" data-writer-id="<?php echo $user1->id ?>">
                    <div class="user-box-follow-progress dot-progress-button"></div>
                    <?php if($user1->follower_id): ?>
                        <div class="user-box-following-button write-text-form-button" data-writer-id="<?php echo $user1->id ?>"><?php echo JText::_('FOLLOWING') ?></div>
                    <?php else: ?>
                        <div class="user-box-follow-button write-text-form-button" data-writer-id="<?php echo $user1->id ?>"><?php echo JText::_('FOLLOW') ?></div>
                    <?php endif ?>

                </div>
                <?php endif ?>

            </div>

        </div>
        </li>
        <?php endforeach ?>
        </ul>
    </div>

    <?php endif ?>

</div>

<?php endif ?>

<?php if(isset($text_search_post) && ($text_search_post != "")): ?>

    <div id="text-search-results">
        <div id="text-search-results-icon"></div>
        <?php
            if(count($texts)!=1){
				$text_search_results_text = JText::_('X_ARTICLES_FOUND_FOR_SEARCH');
			}else{
				$text_search_results_text = JText::_('X_ARTICLES_FOUND_FOR_SEARCH_ONE');
			}
            $text_search_results_text = str_replace('{X}', count($texts), $text_search_results_text);
        ?>
        <div id="text-search-results-text"><?php echo $text_search_results_text ?> “<b><?php echo $text_search_post ?></b>”.</div>
    </div>

    <div style="clear: both;"></div>
<?php endif ?>

<div class="blog" id="h-scroll-container">

    <?php if(empty($texts)): ?>
        <?php //echo JText::_("NO-TEXTS") ?>
    <?php endif ?>

</div>

<?php if(!empty($texts)): ?>



        <script type="text/javascript">

            //texts.push('<?php //echo addslashes(ideary::removeNewLinesFromString(ideary::generateMoreApplaudedTextsBox())) ?>');

            //texts.push('<?php //echo addslashes(ideary::removeNewLinesFromString(ideary::generateAuthorRanking())) ?>');

            <?php
            $app = JFactory::getApplication();
            $menu = $app->getMenu();    
            $lang = JFactory::getLanguage();
            ?>

            <?php if($menu->getActive() == $menu->getDefault($lang->getTag()) && !isset($_POST["text-search"])): ?>
                texts.push('<?php echo addslashes(ideary::removeNewLinesFromString(ideary::generateMoreApplaudedTextsBox())) ?>');
				<? if(JFactory::getUser()->get('id') > 0 && ideary::getUserImpact($user->id) > 10 ) { ?>
				texts.push('<?php echo addslashes(ideary::removeNewLinesFromString(ideary::generateMyStatsBox())) ?>');
				<? } ?>
            <?php endif ?>

			<? $followers = ideary::getUsersWhoIAmFollowingId($userId); ?>
			
            <?php foreach($texts as $index => $text): ?>
				<? $isFollower = in_array($text->created_by, $followers); ?>
			
                texts.push('<?php echo addslashes(ideary::removeNewLinesFromString(ideary::generateTextContent($text, $user->get('id'), $index,false,false,true, "default", $isFollower))) ?>');

                //texts.push('<?php //echo addslashes(ideary::removeNewLinesFromString(require JModuleHelper::getLayoutPath('mod_list_text', 'item'))) ?>');
            <?php endforeach ?>
			
        </script>



<?php endif ?>