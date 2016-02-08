<?php
 /**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

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

    $GLOBALS["userName"] = $this->user->name;
?>
<?php foreach ($this->userextra as $campo):?>
				
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
    var writer_id = <?php echo $this->user->id ?>;
    var no_text_box_id = "no-texts-author-box";

    function resize(){

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
        }
        else{
            var no_text_box = $('#'+no_text_box_id).clone();
            $('#h-scroll-container').html($(no_text_box));
            $('#h-scroll-container #'+no_text_box_id).show();
        }
    }


    function processScroll(delta){

        var val = $('.h-scroll').scrollLeft() - (delta * 700);

        if(delta > 0)
            $('.h-scroll').stop().animate({scrollLeft: val}, 500);
        else
            processScrollForward();
        
    }

    function processScrollForward(){

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

    }



    $(document).ready(function(){

        no_text_box_id = 'no-texts-author-box';
        resize(no_text_box_id);

        $("body").delegate('.user-ranking-follow-button', 'click',function(){

            if(USER_LOGGED){
                var writer_id = $(this).data('authorId');

                $('#follow-button-progress').width($(this).outerWidth());
                $('#follow-button-progress').removeClass('unfollow');
                $('#follow-button-progress').addClass('follow');
                $(this).hide();
                $('#follow-button-progress').show();

                $.post(
                    follow_url,
                    { writer_id: writer_id },
                    function(data) {

                        if(data.success){
                            var html = '<div id="follow-button-progress" class="dot-progress-button"></div>';
                            html += '<div class="user-ranking-following-button" style="display: inline-block; min-width: 90px;" data-author-id="'+writer_id+'">'+FOLLOWING+'</div>';
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

                $('#follow-button-progress').width($(this).outerWidth());
                $('#follow-button-progress').removeClass('follow');
                $('#follow-button-progress').addClass('unfollow');
                $(this).hide();
                $('#follow-button-progress').show();

                $.post(
                    unfollow_url,
                    { writer_id: writer_id },
                    function(data) {

                        if(data.success){
                            var html = '<div id="follow-button-progress" class="dot-progress-button"></div>';
                            html += '<div class="user-ranking-follow-button" style="display: inline-block; min-width: 90px;" data-author-id="'+writer_id+'">'+FOLLOW_AUTHOR+'</div>';
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



        $("body").delegate('#invited-to-write-button', 'click', function(){

            if(USER_LOGGED){

                $.post(
                    invited_to_write_url,
                    { writer_id: writer_id },
                    function(data) {

                        if(data.success){

                            $('#invited-to-write-button-container').html('<div id="thanks-button" class="simple-button">Gracias</div>');

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

        $(window).resize(resize);

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

		var baseurl = "<?php echo JURI::base(); ?>";
        $('#text-of-author-button').click(function(){

            var authorId = $(this).data('authorId');

            $.post(
                baseurl + "index.php?option=com_contact&task=get_texts_of_author&author_id="  + authorId + "&published=1&type=default&profile=0&logged_user_id=" + <?php echo $this->lgid ?>,
                { author_id: authorId	},
                function(data) {

                    texts = data.texts;
                    no_text_box_id = "no-texts-author-box";
                    resize();

                    $('.text-button').removeClass('selected');
                    $('#text-of-author-button').addClass('selected');

                }
                , "json"
            );

        });

        $('#applauded-texts-button').click(function(){

            var authorId = $(this).data('authorId');

            $.post(
                baseurl + "index.php?option=com_contact&task=get_texts_applauded_by_author&author_id="  + authorId + "&logged_user_id=" + <?php echo $this->lgid ?>,
                { author_id: authorId },
                function(data) {

                    texts = data.texts;
                    no_text_box_id = "no-applauded-texts-author-box";
                    resize();

                    $('.text-button').removeClass('selected');
                    $('#applauded-texts-button').addClass('selected');

                }
                , "json"
            );

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

        $("body").delegate('#followers-public', 'click', function(){

            var followers_public = $(this);

            if($(followers_public).hasClass('disabled')){
                return false;
            }

            $(followers_public).addClass('disabled');

            var user_id = $(this).data('userId');
            var user_name = $(this).data('userName');

            $.post(
                get_users_who_follow_me_url,
                {
                    user_id: user_id,
                    offset: 0,
                    limit: 16
                },
                function(data) {

                    if(data.countUsers > 0){

                        if(data.countUsers > 1){
                            var user = 'usuarios';
                            var are = 'están';
                        }
                        else{
                            var user = 'usuario';
                            var are = 'está';
                        }

                        var user_box_title = 'A '+user_name+' lo '+are+' siguiendo <span class="bold">'+data.countUsers+' '+user+'.</span>';
                        $('#user-box-title').html(user_box_title);
                        $('#user-box-title').attr('class', 'followed-users-title');
                        $('#user-list').html(data.usersHtml);

                        generate_user_box_pagination('followers', user_id, data.countUsers);

                        $('#user-box').lightbox_me({
                            centered: true,
                            closeSelector: '#user-box-close-icon',
                             onLoad: function(){
                                 $('.user-box-name').dotdotdot({
                                    wrap: 'letter'
                                 });

                                 $(followers_public).removeClass('disabled');
                             }
                        });

                    }

                }
                , "json"
            );

        });


        $("body").delegate('#following-public', 'click', function(){

            var following_public = $(this);

            if($(following_public).hasClass('disabled')){
                return false;
            }

            $(following_public).addClass('disabled');

            var user_id = $(this).data('userId');
            var user_name = $(this).data('userName');

            $.post(
                get_users_who_i_am_following_url,
                {
                    user_id: user_id,
                    offset: 0,
                    limit: 16
                },
                function(data) {

                    if(data.countUsers > 0){

                        if(data.countUsers > 1){
                            var user = 'usuarios';
                        }
                        else{
                            var user = 'usuario';
                        }

                        var user_box_title = user_name+' está siguiendo a <span class="bold">'+data.countUsers+' '+user+'.</span>';
                        $('#user-box-title').html(user_box_title);
                        $('#user-box-title').attr('class', 'following-users-title');
                        $('#user-list').html(data.usersHtml);

                        generate_user_box_pagination('followeds', user_id, data.countUsers);

                        $('#user-box').lightbox_me({
                            centered: true,
                            closeSelector: '#user-box-close-icon',
                             onLoad: function(){
                                 $('.user-box-name').dotdotdot({
                                    wrap: 'letter'
                                 });

                                 $(following_public).removeClass('disabled');
                             }
                        });

                    }

                }
                , "json"
            );

        });
		

    });
		
</script>

<div id="public-profile-containers">

<div id="public-profile-left-container">

    <div id="public-profile-left-border">
        <div id="public-profile-left-content">

            <div id="public-profile">
                <div id="public-profile-img">
					<?php echo Ideary::getUserImage($this->user->id,"200",$this->user->name,'style="width:100px;height:100px;"'); ?>
                </div>

                <div id="public-profile-data">
                    <div id="public-profile-username"><?php echo $this->user->name ?></div>
                    <div id="public-profile-ranking-message">
                        <!--<div id="public-profile-ranking"><?php //echo $this->user->ranking ?></div>-->
                        <!--<div id="public-profile-message" class="send-message-clickable" title="<?php //echo JText::_('SEND_MESSAGE') ?>"></div>-->
                        <div style="clear: both;"></div>
                    </div>
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

			<?php if ($ciudad!="" || $provincia!="" || $pais!="" || $fechanac!="" || $ocupacion!="" || $website!="" || $this->user->provider!="" ):?>
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
				<?php if ($ocupacion!=""):?>
					<div class="public-profile-info-item public-profile-profession"><?php echo $ocupacion;?></div>
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
			<?php endif ?>
			
            <div id="public-profile-follows-container">

                <div class="follow-button-container" style="margin-top: 0;" data-author-id="<?php echo $this->user->id ?>">
                    <div id="follow-button-progress" class="dot-progress-button"></div>
                    <?php if(is_null($this->user->followed)): ?>
                        <div class="user-ranking-follow-button" style="display: inline-block; min-width: 90px;" data-author-id="<?php echo $this->user->id ?>"><?php echo JText::_('FOLLOW_AUTHOR') ?></div>
                    <?php else: ?>
                        <div class="user-ranking-following-button" style="display: inline-block; min-width: 90px;" data-author-id="<?php echo $this->user->id ?>"><?php echo JText::_('FOLLOWING') ?></div>
                    <?php endif ?>
                </div>
				<!--<div id="public-profile-message" style="margin-top:-25px;" title="<?php //echo JText::_('SEND_MESSAGE') ?>"></div>-->
                <div class="user-ranking-info">
                    <div class="followers" style="background-position: left 4px; margin-top: 8px;"><span <?php echo ($this->user->followers)? 'id="followers-public"' : '' ?> class="user-ranking-info-text" data-user-id="<?php echo $this->user->id ?>" data-user-name="<?php echo $this->user->name ?>"><?php echo JText::_('FOLLOWERS') ?></span> <span class="user-ranking-info-count"><?php echo $this->user->followers ?></span></div>
                    <div class="followeds" style="background-position: left 3px; margin-top: 8px;"><span <?php echo ($this->user->following)? 'id="following-public"' : '' ?> class="user-ranking-info-text" data-user-id="<?php echo $this->user->id ?>" data-user-name="<?php echo $this->user->name ?>"><?php echo JText::_('FOLLOWING') ?></span> <span class="user-ranking-info-count"><?php echo $this->user->following ?></span></div>
                    <div class="applausses" style="background-position: left 2px; margin-top: 8px;"><span class="user-ranking-info-text">Aplausos</span> <span class="user-ranking-info-count"><?php echo $this->user->applausses_received ?></span></div>
                </div>

            </div>

            <div id="text-buttons-container" style="display:none;">

                <div class="text-button selected" id="text-of-author-button" data-author-id="<?php echo $this->user->id ?>">
                    <div class="text-button-bg"></div>
                    <div class="text-button-text"><?php echo JTEXT::_('TEXTS_OF_AUTHOR') ?></div>
                    <div class="text-button-arrow"></div>
                </div>

                <?php if($this->yes_clapped_texts):?>
				<div class="text-button" id="applauded-texts-button" data-author-id="<?php echo $this->user->id ?>" style="margin-top: 10px;">
                    <div class="text-button-bg"></div>
                    <div class="text-button-text"><?php echo JTEXT::_('TEXTS_APPLAUDED') ?></div>
                    <div class="text-button-arrow"></div>
                </div>
				<?php endif;?>

            </div>

        </div>
    </div>
</div>

<div id="public-profile-right-container">

    <div id="h-scroll-container"></div>

<?php if(!empty($this->texts)): ?>

<script type="text/javascript">
    <?php foreach($this->texts as $index => $text): ?>
        texts.push('<?php echo addslashes(ideary::removeNewLinesFromString(ideary::generateTextContent($text, $this->loggedUser->get('id'), $index))) ?>');
    <?php endforeach ?>
</script>

<?php endif ?>

</div>

    <div style="clear: both;"></div>
</div>

<div style="clear: both;"></div>

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

<script type="text/javascript">
    resize();
</script>

<div id="no-texts-author-box" class="no-texts-box">

    <div id="no-texts-icon" class="no-texts-box-icon"></div>

    <div class="no-texts-box-text" style="width: 314px; margin-left: 47px;">Por el momento, <?php echo $this->user->name ?> no posee textos redactados.</div>
    <div id="invited-to-write-button-container" style="text-align: center;">

        <?php if(Ideary::writerInvitedToWrite(JFactory::getUser()->get('id'), $this->user->id)): ?>
            <div id="thanks-button" class="simple-button">Gracias</div>
        <?php else: ?>
            <div id="invited-to-write-button" class="simple-button">Invitar a escribir</div>
        <?php endif; ?>

    </div>
</div>

<div id="no-applauded-texts-author-box" class="no-texts-box">

    <div id="no-texts-applauded-icon" class="no-texts-box-icon"></div>

    <div class="no-texts-box-text" style="width: 314px; margin-left: 47px;">Por el momento, <?php echo $this->user->name ?> no aplaudio ningún texto.</div>
</div>