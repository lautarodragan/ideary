<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');

	$sexo="";
	$educacion="";
	$ocupacion="";
	$pais="";
	$ciudad="";
	$provincia="";
	$frasepersonal="";
	$website="";
	$fechanac="";
	$selectedM="";
	$selectedF="";

?>
<?php foreach ($this->userExtraData as $campo):?>
				
	<?php switch ($campo->profile_key){
	
		case "profile.address1":
					$educacion=str_replace('"','',$campo->profile_value);
		break;
		case "profile.address2":
					$ocupacion=str_replace('"','',$campo->profile_value);
		break;
		case "profile.website":
					$website=str_replace('\/','/',str_replace('"','',$campo->profile_value));
		break;
		case "profile.aboutme":
					$frasepersonal=str_replace('"','',$campo->profile_value);
		break;					
		case "profile.country":
					$pais=str_replace('"','',$campo->profile_value);
		break;
		case "profile.region":
					$provincia=str_replace('"','',$campo->profile_value);
		break;					
		case "profile.city":
					$ciudad=str_replace('"','',$campo->profile_value);
		break;
		case "profile.dob":						
					$fechanac=str_replace('"','',$campo->profile_value);
		break;
		case "profile.phone":
			$sexo=str_replace('"','',$campo->profile_value);

			if($sexo=="M"){
				$selectedM="selected='selected'";
			}elseif($sexo=="F"){
				$selectedF="selected='selected'";
			}
		break;
	}?>
	
<?php endforeach;?>	

<style type="text/css">
    #contentarea{
        <?php echo ideary::getUserBackground($this->user->id) ?>
        padding: 0 !important;
        margin: 0 !important;
    }

    #breadcrumbs{
        margin: 0 !important;
    }

    #main{
        padding: 0 !important;
    }

    .shownocolumns {
        width: 100% !important;
    }

    .h-scroll{
        padding: 0 !important;
    }

    #h-scroll-container{
        margin: 20px 0 0 10px !important;
    }

</style>

<script type="text/javascript">

    var texts = new Array();
	var change_author_texts_shown=false;
    var no_text_box_id = "no-texts-box";

    var offset = <?php echo count($this->texts) ?>;
    var can_process_scroll_forward = true;
    var count_all_texts = <?php echo $this->countAlltexts ?>;
    var more_texts_bar_showed = false;
    var can_scroll_foward = true;
    var let_scroll_foward = true;
    var old_num_rows = 0;
    var text_type = '<?php echo $this->text_type ?>';

    function deselect_author_texts_links(){
        $('#published_texts_bottom').removeClass('white');
        $('#published_texts_bottom').addClass('greycolor');

        $('#draft_texts_bottom').removeClass('white');
        $('#draft_texts_bottom').addClass('greycolor');
    }

    function resize(no_text_box_id){

        resizePublicProfileLeftContainer();

        if(texts.length > 0){
            var browser_height = $(window).height();
            var browser_width = $(window).width();
            var topbar_height = $("#topbar").height();
            var public_profile_left_container_width = $("#public-profile-left-container").outerWidth();

            var remaining_height = browser_height - (topbar_height);
            var remaining_width = browser_width - (public_profile_left_container_width+40);

            var h_scroll_container_margin_top = $('#h-scroll-container').css('margin-top').replace("px","");
            $('#h-scroll-container').css("height", (remaining_height-h_scroll_container_margin_top)+"px");

            var num_rows = Math.floor(remaining_height/238);
            num_rows = (num_rows == 0) ? 1 : num_rows;

            var rows = new Array();

            for (i = 0; i < num_rows; i++){

                rows[i] = new Array();

            }

            var current_row = 0;
            for (i = 0; i < texts.length; i++){

                var text = texts[i];
                $(text).data('index', i);
                $(text).attr('data-index', i);
                texts[i] = $(text);

                rows[current_row].push(texts[i]);
                if(current_row == (num_rows-1)){
                    current_row = 0;
                }
                else{
                    current_row++;
                }
            }

            var h_scroll = $('<div class="h-scroll"></div>');
            $(h_scroll).css('height', (num_rows*238)+"px");
            $(h_scroll).css('width', remaining_width+"px");

            var li;
            for (i = 0; i < num_rows; i++){
                ul_width = 0;
                var ul = $('<ul class="ul-text-list"></ul>');
                $(ul).css('width', (rows[i].length*928)+"px");
                for (j = 0; j < rows[i].length; j++){
                    li = $('<li class="li-text"></li>');
                    $(li).html(rows[i][j]);
                    $(ul).append($(li));
                }

                $(h_scroll).append($(ul));

            }

            $('#h-scroll-container').html($(h_scroll));

            var text_data;
            $(".text-content").each(function() {
                text_data = $(this).parents(".text-data");
                $(this).css('width', $(text_data).width()+"px");
                $(this).show();
            });

            $(".text-bottom").each(function() {
                text_data = $(this).parents(".text-data");
                $(this).css('width', $(text_data).width()+"px");
            });

            setTextAuthorDivWidth();

            $('.text-content').dotdotdot({
                wrap: 'letter',
                watch: false
            });
        }
        else{
            var no_text_box = $('#'+no_text_box_id).clone();
            $('#h-scroll-container').html($(no_text_box));
            $('#h-scroll-container #'+no_text_box_id).show();
        }

        $('#more-texts-bar').hide();
        more_texts_bar_showed = false;
        let_scroll_foward = true;

    }


    /*function processScroll(delta){

        var val = $('.h-scroll').scrollLeft() - (delta * 700);

        if(delta == 1){
            $('.h-scroll').stop().animate({scrollLeft: val}, 500);
        }
        else{
            processScrollForward();
        }
    }*/

    function processScroll(delta){

        if(!can_scroll_foward){
            return false;
        }

        var val = $('.h-scroll').scrollLeft() - (delta * 700);

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
                processScrollForward();
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

        }
    }

    function getMoreTexts(){

        can_process_scroll_forward = false;
        can_scroll_foward = false;

        $.post(
            get_more_texts_of_author_url,
            {
                text_type: text_type,
                userId: USER_ID,
                offset: offset
            },
            function(data) {

                if(data.texts.length > 0){

                    $('#more-texts-bar').hide();
                    more_texts_bar_showed = false;

                    for(i = 0; i < data.texts.length; i++){
                        texts.push(data.texts[i]);
                    }

                    appendMoreTexts(data.texts);
                    offset += data.texts.length;

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

    function processScrollForward(){

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
                scrollLeft += 700;
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

    /*function processScrollForward(){

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

            scrollLeft = $('.h-scroll').scrollLeft();
            var duration = 500;

            if(remaining_width < 700){
                scrollLeft += remaining_width+50;
                duration = Math.floor((duration*(remaining_width+50))/700);
            }
            else{
                scrollLeft += 700;
            }

            $('.h-scroll').stop().animate({scrollLeft: scrollLeft}, duration);

    }*/


        $('#message-icon').click(function(){

            $('#message-popup').lightbox_me({
                centered: true,
                closeSelector: '#close-message-popup',
                onClose: function(){
                    $('#subject-field').val('');
                    $('#message-field').val('');
                }
            });
 
        });


		$("body").delegate('#send-button', 'click', function(){

            var subject = $('#subject-field').val();
            var message = $('#message-field').val();
            processSendMessageEvent(writer_id, subject, message, 'send-button-box', 'message-loading', 'message-popup', 'subject-field', 'message-field');

        });

    $(document).ready(function(){

        no_text_box_id = "no-texts-box";
        resize(no_text_box_id);

        var more_texts_bar = '<div id="more-texts-bar">' +
            '<div id="more-texts-bar-center"></div>' +
            '</div>';

        $('#wrapper2').append(more_texts_bar);

        resizeMoreTextsBar();

        $("body").delegate('#more-texts-bar', 'click', function(){

            if($(this).hasClass('disabled')){
                return false;
            }

            $(this).addClass('disabled');
            resizeMoreTextsBar();

            getMoreTexts();

        });

        $("body").delegate('.draft-delete', 'click', function(){

            $('#delete-draft-popup-confirm').attr('data-text-id', $(this).data('textId'));
            $('#delete-draft-popup-confirm').attr('data-text-index', $(this).parents('.text').data('index'));

            $('#delete-draft-popup-confirm').data('textId', $(this).data('textId'));
            $('#delete-draft-popup-confirm').data('textIndex', $(this).parents('.text').data('index'));

            $('#delete-draft-popup').lightbox_me({
                centered: true,
                closeSelector: ".delete-draft-popup-close"
            });

        });

        $("body").delegate('#delete-draft-popup-confirm', 'click', function(){


             var text_id = $(this).data('textId');
             var text_index = $(this).data('textIndex');
             $('#delete-draft-popup').trigger('close');

             $.post(
                delete_text_url,
                { text_id: text_id },
                 function(data) {

                 if(data.success){

                     $('.text[data-index="'+text_index+'"]').fadeOut('slow', function(){
                         texts.splice(text_index, 1);
                         count_all_texts--;
                         offset--;
                         no_text_box_id = "no-drafts-box";
                         resize(no_text_box_id);
                         $('#cant_draft').text(count_all_texts);
                     });

                     }

                 }
                , "json"
             );



        });



        $("body").delegate('.user-ranking-follow-button', 'click',function(){

            if(USER_LOGGED){
                var writer_id = $(this).data('authorId');

                $.post(
                    follow_url,
                    { writer_id: writer_id },
                    function(data) {

                        if(data.success){
                            var html = '<div class="user-ranking-following-button" style="display: inline-block; min-width: 90px;" data-author-id="'+writer_id+'">'+FOLLOWING+'</div>';
                            $('.follow-button-container[data-author-id="'+writer_id+'"]').html(html);
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

        $("body").delegate('.user-ranking-following-button', 'click', function(){

            if(USER_LOGGED){
                var writer_id = $(this).data('authorId');

                $.post(
                    unfollow_url,
                    { writer_id: writer_id },
                    function(data) {

                        if(data.success){
                            var html = '<div class="user-ranking-follow-button" style="display: inline-block; min-width: 90px;" data-author-id="'+writer_id+'">'+FOLLOW_AUTHOR+'</div>';
                            $('.follow-button-container[data-author-id="'+writer_id+'"]').html(html);
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

        
        $(window).resize(function(){
            resize(no_text_box_id);
        });

        $("body").delegate('.h-scroll', 'mousewheel', function(event, delta){
            processScroll(delta);
            event.preventDefault();
        });

        $('body').keyup(function(event){

            var target = $(event.target);

            var makeScroll = false;

            var delta = 0;
            if((event.which==37)/* || (event.which==40)*/){
                delta = 1;
                makeScroll = true;
            }
            else if(/*(event.which==38) || */(event.which==39)){
                delta = -1;
                makeScroll = true;
            }

            if(makeScroll){
                if(!$(target).is('input')){
                    processScroll(delta);
                }
            }
        });

        $('#text-of-author-button').click(function(){

            var authorId = $(this).data('authorId');
			
			if(change_author_texts_shown){
				$('#detail_author_texts').slideDown('fast');
			}else{
				$('#detail_author_texts').slideUp('fast');
			}
			
			change_author_texts_shown = !change_author_texts_shown;

           /* $.post(
                get_texts_of_author_url,
                { 	author_id: authorId,
					published:1				
				},
                function(data) {

                    texts = data.texts;
                    resize();

                    $('.text-button').removeClass('selected');
                    $('#text-of-author-button').addClass('selected');

                }
                , "json"
            );*/

        });

		$('#text-of-author-pub-button').click(function(){

            var authorId = $(this).data('authorId');
			$('#draft_texts_bottom').removeClass('white');
			$('#published_texts_bottom').removeClass('greycolor');
			
			$('#draft_texts_bottom').addClass('greycolor');
			$('#published_texts_bottom').addClass('white');
			

            $.post(
                '<?php echo JURI::base();?>index.php?option=com_contact&task=get_texts_of_author&authorsection=1&profile=1&author_id=' + authorId + '&published=1&type=mine',
                { 	author_id: authorId,
					published:1				
				},
                function(data) {

                    texts = data.texts;

                    offset = data.texts.length;
                    can_process_scroll_forward = true;
                    count_all_texts = data.countAllTexts;
                    more_texts_bar_showed = false;
                    can_scroll_foward = true;
                    let_scroll_foward = true;
                    old_num_rows = 0;
                    text_type = 'published';

                    no_text_box_id = "no-texts-box";
                    resize(no_text_box_id);

                    $('.text-button').removeClass('selected');
                    $('#text-of-author-pub-button').addClass('selected');
                    $('#text-of-author-button').addClass('selected');

                }
                , "json"
            );

        });
		
		$('#favourites-texts-button').click(function(){
            var authorId = $(this).data('authorId');

            $.post(
                get_texts_favourites_of_author_url,
                { author_id: authorId },
                function(data) {

                    texts = data.texts;

                    offset = data.texts.length;
                    can_process_scroll_forward = true;
                    count_all_texts = data.countAllTexts;
                    more_texts_bar_showed = false;
                    can_scroll_foward = true;
                    let_scroll_foward = true;
                    old_num_rows = 0;
                    text_type = 'favourites';

                    no_text_box_id = "no-favorite-texts-box";
                    resize(no_text_box_id);

                    $('.text-button').removeClass('selected');
                    $('#favourites-texts-button').addClass('selected');

                    deselect_author_texts_links();
                }
                , "json"
            );
			$('#detail_author_texts').slideUp('fast');
			change_author_texts_shown = !change_author_texts_shown;
			
        });

        $('#applauded-texts-button').click(function(){

            var authorId = $(this).data('authorId');
			
            $.post(
                get_texts_applauded_by_author_url,
                { author_id: authorId },
                function(data) {

                    texts = data.texts;

                    offset = data.texts.length;
                    can_process_scroll_forward = true;
                    count_all_texts = data.countAllTexts;
                    more_texts_bar_showed = false;
                    can_scroll_foward = true;
                    let_scroll_foward = true;
                    old_num_rows = 0;
                    text_type = 'applauded';

                    no_text_box_id = "no-texts-applauded-box";
                    resize(no_text_box_id);

                    $('.text-button').removeClass('selected');
                    $('#applauded-texts-button').addClass('selected');

                    deselect_author_texts_links();
                }
                , "json"
            );
			$('#detail_author_texts').slideUp('fast');
			change_author_texts_shown = !change_author_texts_shown;

        });
		
		
		$('#text-of-author-draft-button').click(function(){

            var authorId = $(this).data('authorId');
			$('#draft_texts_bottom').removeClass('greycolor');
			$('#published_texts_bottom').removeClass('white');
			
			$('#draft_texts_bottom').addClass('white');
			$('#published_texts_bottom').addClass('greycolor');

            $.post(
				'<?php echo JURI::base();?>index.php?option=com_contact&task=get_texts_of_author&authorsection=1&profile=1&author_id=' + authorId + '&published=0&type=draft',
                { 	
					author_id: authorId,
					published: 0
				},
                function(data) {

                    texts = data.texts;

                    offset = data.texts.length;
                    can_process_scroll_forward = true;
                    count_all_texts = data.countAllTexts;
                    more_texts_bar_showed = false;
                    can_scroll_foward = true;
                    let_scroll_foward = true;
                    old_num_rows = 0;
                    text_type = 'draft';

                    no_text_box_id = "no-drafts-box";
                    resize(no_text_box_id);

                    $('.text-button').removeClass('selected');
                    $('#text-of-author-draft-button').addClass('selected');
                    $('#text-of-author-button').addClass('selected');

                }
                , "json"
            );

        });
    });
	function editprofile(){
		window.location='<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>';
	}
	
	function editprofile2(){
		window.location='<?php echo JRoute::_('index.php?option=com_users&task=editinterest&user_id='.(int) $this->data->id);?>';
	}
	
	
	 $("body").delegate('.text-saved-icon', 'click', function(){
			var text_saved_icon=$(this);

            if(USER_LOGGED){
                var text_id = $(this).data('textId');
                var text = $(this).parents('.text');
                var text_index = $(text).data('index');
                $.post(
                    unarchive_url,
                    { text_id: text_id },
                    function(data) {
                        if(data.success){

                            $('.text[data-index="'+text_index+'"]').fadeOut('slow', function(){
                                texts.splice(text_index, 1);
                                count_all_texts--;
                                offset--;
                                no_text_box_id = "no-texts-saved-box";
                                resize(no_text_box_id);
                            });

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
		
		
</script>


<?php if (JFactory::getUser()->id == $this->data->id) : ?>

<div id="public-profile-containers">

	<div id="public-profile-left-container">

		<div id="public-profile-left-border">
			<div id="public-profile-left-content">

				<div id="public-profile">
					<div id="public-profile-img">
						<?php echo Ideary::getUserImage($this->user->id,"200",$this->user->name,'style="width:100px;height:100px;"');?>
					</div>
					
					<div id="public-profile-data">
						<div id="public-profile-username"><?php echo $this->user->name ?></div>
					</div>
					<div style="clear: both;"></div>
					<div id="public-profile-legend"><?php echo $frasepersonal;?></div>
				</div>
	<?php 
		$date = new DateTime();
		$d2 = new DateTime($fechanac);
		$diff = $d2->diff($date);
		$years= $diff->y;
	?>
				
				<div id="public-profile-info">
					<?php if ($ciudad!="" || $provincia!="" || $pais!=""):?>
						<div class="public-profile-info-item public-profile-location">
						<?php echo $ciudad;?><?php if (($provincia!="" || $pais!="") && $ciudad!="") { echo ",";}?>
						<?php echo $provincia;?><?php if ($pais!="" && trim($provincia)!="") { echo ",";}?>
						<?php echo $pais;?></div>

					<?php endif;?>
					<?php if ($fechanac!=""):?>
						<div class="public-profile-info-item public-profile-age"><?php echo str_replace('{X}', $years, JText::_('YEARS_OLD')) ?></div>
					<?php endif;?>
					<?php if (($ocupacion!="") && ($educacion!="")):?>
						<div class="public-profile-info-item public-profile-profession"><?php echo $ocupacion; ?></div>
                    <?php elseif($ocupacion!=""): ?>
                        <div class="public-profile-info-item public-profile-profession"><?php echo $ocupacion; ?></div>
                    <?php elseif($educacion!=""):?>
                        <div class="public-profile-info-item public-profile-education"><?php echo $educacion; ?></div>
					<?php endif;?>
					<?php if ($website!=""):?>
						<div class="public-profile-info-item public-profile-website" style="margin-bottom: 0px !important;"><a href="<?php echo $website;?>" target="_blank"><?php echo $website;?></a></div>
					<?php endif;?>
					<div class="user-ranking-social-icons" style="margin-top: 9px !important;">

						<?php if($this->user->provider == 'facebook'): ?>
							<div class="fb-icon"></div>
						<?php elseif($this->user->provider == 'twitter'): ?>
							<div class="tw-icon"></div>
							<!--<div class="skype-icon"></div>-->
						<?php endif ?>

						<div style="clear: both;"></div>
					</div>
				</div>

				
				<div id="public-profile-follows-container">

         
					<div class="user-ranking-info">

						<div class="followers" style="background-position: left 4px; margin-top: 8px;">
                            <span <?php echo ($this->user->followers > 0)? 'id="followers"' : '' ?> class="user-ranking-info-text"><?php echo JText::_('FOLLOWERS') ?></span>
                            <span class="user-ranking-info-count"><?php echo $this->user->followers ?></span>
                        </div>

                        <div class="followeds" style="background-position: left 3px; margin-top: 8px;">
                            <span <?php echo ($this->user->following > 0)? 'id="followeds"' : '' ?> class="user-ranking-info-text"><?php echo JText::_('FOLLOWING') ?></span>
                            <span class="user-ranking-info-count"><?php echo $this->user->following ?></span>
                        </div>

                        <div class="applausses" style="background-position: left 3px; margin-top: 8px;">
                            <span class="user-ranking-info-text">Aplausos</span>
                            <span class="user-ranking-info-count"><?php echo $this->userData->applausses_received ?></span>
                        </div>

					</div>
				</div>
				
				<div id="text-buttons-container">

					<div class="text-button selected" id="text-of-author-button" data-author-id="<?php echo $this->user->id ?>">
                        <!-- <div class="text-button-bg"></div> -->
						<div class="text-button-text"><?php echo JTEXT::_('MYTEXTSPROFILE') ?></div>
						<div class="text-button-arrow"></div>
					</div>
					<?php 
						$style="style='display:block;'";
						$bold2="style='color: #fff;'";
						$bold1="style='color: #7a877b;'";
						if(isset($_GET["draft"]) && $_GET["draft"]=="1"){
							$style="style='display:block;'";
							$bold1="style='color: #fff;'";
							$bold2="style='color: #7a877b;'";
						}
						if(isset($_GET["mytexts"]) && $_GET["mytexts"]=="1"){
							$style="style='display:block;'";
							$bold2="style='color: #fff;'";
							$bold1="style='color: #7a877b;'";
						}

					?>
					<div id="detail_author_texts" <?php echo $style;?> >
						<div class="text-button extrastyle" id="text-of-author-pub-button" style="background:none;" data-author-id="<?php echo $this->user->id ?>">
							<span class="user-ranking-info" id="published_texts_bottom" <?php echo $bold2;?>><?php echo JTEXT::_('PUBLISHED') ?> (<?php echo $this->cant_pub?>)</span>
						</div>
						<div class="text-button extrastyle" id="text-of-author-draft-button"  style="background:none;" data-author-id="<?php echo $this->user->id ?>">
							<span class="user-ranking-info" id="draft_texts_bottom" <?php echo $bold1;?>><?php echo JTEXT::_('DRAFTS') ?> (<span id="cant_draft"><?php echo $this->cant_draft?></span>)</span>
						</div>
					</div>
					<div class="text-button" id="favourites-texts-button" data-author-id="<?php echo $this->user->id ?>" style="margin-top: 10px;">
                        <!-- <div class="text-button-bg"></div> -->
						<div class="text-button-text"><?php echo JTEXT::_('FAVORITES') ?></div>
						<div class="text-button-arrow"></div>
					</div>
					
					
					<div class="text-button" id="applauded-texts-button" data-author-id="<?php echo $this->user->id ?>" style="margin-top: 10px;">
                        <!-- <div class="text-button-bg"></div> -->
						<div class="text-button-text"><?php echo JTEXT::_('TEXTS_APPLAUDED') ?></div>
						<div class="text-button-arrow"></div>
					</div>
				</div>

			</div>
		</div>
	</div>
	
	<div id="public-profile-right-container">

		<div id="h-scroll-container"></div>

	<?php if(!empty($this->texts)): ?>

        <?php
            if($_GET['draft']==1){
                $textContentType = 'draft';
            }
            else{
                $textContentType = 'mine';
            }
        ?>
	<script type="text/javascript">
		<?php foreach($this->texts as $index => $text): ?>
			texts.push('<?php echo addslashes(ideary::removeNewLinesFromString(ideary::generateTextContent($text, $this->user->id, $index,true, false, false, $textContentType))) ?>');
		<?php endforeach ?>
	</script>

	<?php endif ?>

	</div>

    <div style="clear: both;"></div>
</div>
<?php endif; ?>

<div id="no-texts-applauded-box" class="no-texts-box">

    <div id="no-texts-applauded-icon" class="no-texts-box-icon"></div>

    <div class="no-texts-box-text"><?php echo JTEXT::_('YOU_HAVE_NOT_APPLAUDED_ANY_TEXT') ?></div>
</div>

<div id="no-favorite-texts-box" class="no-texts-box">

    <div id="no-favorite-texts-icon" class="no-texts-box-icon"></div>

    <div class="no-texts-box-text"><?php echo JTEXT::_('YOU_HAVE_NOT_SAVED_ANY_FAVORITES') ?></div>
</div>

<div id="no-texts-box" class="no-texts-box">

    <div id="no-texts-icon" class="no-texts-box-icon"></div>

    <div class="no-texts-box-text"><?php echo JTEXT::_('DO_NOT_COMPOSE_FIRST_TEXT') ?></div>
    <div style="text-align: center;">
        <a href="<?php echo JRoute::_("index.php?option=com_content&view=form&layout=edit&new=1") ?>">
            <div class="simple-button"><?php echo JText::_('WRITING_MY_FIRST_TEXT') ?></div>
        </a>
    </div>
</div>

<div id="no-texts-saved-box" class="no-texts-box">

    <div id="no-texts-saved-icon" class="no-texts-box-icon"></div>

    <div class="no-texts-box-text"><?php echo JTEXT::_('STILL_NOT_SAVED_ANY_TEXT') ?></div>
</div>

<div id="no-drafts-box" class="no-texts-box">

    <div id="no-texts-icon" class="no-texts-box-icon"></div>

    <div class="no-texts-box-text"><?php echo JTEXT::_('YOU_HAVE_NO_DRAFT') ?></div>
    <div style="text-align: center;">
        <a href="<?php echo JRoute::_("index.php?option=com_content&view=form&layout=edit&new=1") ?>">
            <div class="simple-button"><?php echo JTEXT::_('WRITE_A_TEXT') ?></div>
        </a>
    </div>
</div>

<div id="delete-draft-popup">

    <div id="delete-draft-popup-close" class="delete-draft-popup-close" title="<?php echo JTEXT::_('CLOSE') ?>"></div>

    <div id="delete-draft-popup-title">
        <div id="delete-draft-popup-title-icon"></div>
        <div id="delete-draft-popup-title-text"><?php echo JTEXT::_('DO_YOU_WANT_DELETE_DRAFT') ?></div>
        <div style="clear: both;"></div>
    </div>

    <div id="delete-draft-popup-legend"><?php echo JTEXT::_('DELETE_DRAFT_POPUP_LEGEND') ?></div>

    <div id="delete-draft-popup-buttons">
        <div style="margin-top: 20px; clear: both;">
            <div id="delete-draft-popup-cancel" class="delete-draft-popup-button light-green-button delete-draft-popup-close" style="margin-left: 47px;"><?php echo JTEXT::_('JCANCEL') ?></div>
            <div id="delete-draft-popup-confirm" class="delete-draft-popup-button dark-green-button" style="margin-left: 10px;" data-text-id="0" data-text-index="0"><?php echo JTEXT::_('CONFIRM') ?></div>
            <div style="clear: both;"></div>
        </div>
    </div>
</div>