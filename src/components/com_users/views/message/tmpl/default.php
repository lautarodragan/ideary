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
$user		= JFactory::getUser();


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
        margin: 20px 0 0 20px !important;
    }
</style>

<script type="text/javascript">

    function setScreenHeight(){

        var inbox = $('#inbox-tab').outerHeight(true) + $('#inbox-container').outerHeight(true) + 20;
        var unknown_users = $('#unknown-users-tab').outerHeight(true) + $('#unknown-users-container').outerHeight(true) + 20;
        var messages_sent = $('#messages-sent-tab').outerHeight(true) + $('#messages-sent-container').outerHeight(true) + 20;
        var body_height = $(window).height() - $('#topbar').outerHeight(true);

        var height = Math.max(inbox, unknown_users, messages_sent, body_height) ;

        $('#contentarea').css('height', height+'px');
        $('#public-profile-left-container').css('height', (height-13)+'px');
    }

    function setMessageToUserFormContent(user_id, user_name, msg){

        $.post(
            get_message_to_user_form_content_url,
            {
                user_id: user_id,
                username: user_name,
                msg: msg
            },
            function(data){

                var send_message_form = $('.msg-container.selected .send-message-form');
                $(send_message_form).html(data);
                $('.send-message-input').focus();

            }, "html");

    }


    $(document).ready(function(){

        setScreenHeight();

        $(".public-profile-website").dotdotdot({
            wrap: 'letter',
            watch: true
        });

        $('#inbox-tab').click(function(){
            $('#unknown-users-tab').removeClass('selected');
            $('#messages-sent-tab').removeClass('selected');
            $(this).addClass('selected');

            $('#unknown-users-container').removeClass('selected');
            $('#messages-sent-container').removeClass('selected');
            $('#inbox-container').addClass('selected');

            $('#messages-sent').css('z-index', '1');
            $('#unknown-users').css('z-index', '2');
            $('#inbox').css('z-index', '3');

            $('#messages-sent-container').css('visibility', 'hidden');
            $('#unknown-users-container').css('visibility', 'hidden');
            $('#inbox-container').css('visibility', 'visible');
        });

        $('#unknown-users-tab').click(function(){
            $('#inbox-tab').removeClass('selected');
            $('#messages-sent-tab').removeClass('selected');
            $(this).addClass('selected');

            $('#inbox-container').removeClass('selected');
            $('#messages-sent-container').removeClass('selected');
            $('#unknown-users-container').addClass('selected');

            $('#messages-sent').css('z-index', '1');
            $('#inbox').css('z-index', '2');
            $('#unknown-users').css('z-index', '3');

            $('#messages-sent-container').css('visibility', 'hidden');
            $('#inbox-container').css('visibility', 'hidden');
            $('#unknown-users-container').css('visibility', 'visible');
        });

        $('#messages-sent-tab').click(function(){
            $('#inbox-tab').removeClass('selected');
            $('#unknown-users-tab').removeClass('selected');
            $(this).addClass('selected');

            $('#inbox-container').removeClass('selected');
            $('#unknown-users-container').removeClass('selected');
            $('#messages-sent-container').addClass('selected');

            $('#inbox').css('z-index', '1');
            $('#unknown-users').css('z-index', '2');
            $('#messages-sent').css('z-index', '3');

            $('#inbox-container').css('visibility', 'hidden');
            $('#unknown-users-container').css('visibility', 'hidden');
            $('#messages-sent-container').css('visibility', 'visible');
        });


        $("body").delegate(".message-delete", "click", function() {
            var message_id = $(this).data('messageId');

            $.post(
                delete_message_url,
                {message_id: message_id},
                function(data){

                    if(data.success){
                        $('.message-item[data-message-id="'+message_id+'"]').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }

                }, "json");
        });

        $("body").delegate(".message-reply", "click", function() {
            var user_id = $(this).data('userId');
            var username = $(this).data('username');

            setMessageToUserFormContent(user_id, username, '');
        });


        $("body").delegate(".send-message-button", "click", function() {

            if($(this).hasClass('disabled')){
                return false;
            }

            var send_message_button = $(this);

            var user_id = $(this).data('userId');
            var send_message = $(this).parents('.send-message');
            var message = $(send_message).find('.send-message-input').val();

            var send_message_button = $(this);

            //validaciones
            if((message != "") && (user_id != 0)){

                $(send_message_button).addClass('disabled');

                $.post(
                    save_message_url,
                 {
                    user_id: user_id,
                    message: message
                 },
                 function(data){

                     if(data.success){
                        $('#sent-messages-list').html(data.sentMessagesListHtml);
                        var send_message_form = $(send_message_button).parents('.send-message-form');
                        $(send_message_form).load(get_message_to_user_form_with_input_content_url);
                        setScreenHeight();

                        $('#message-sent-popup-container').lightbox_me({
                            centered: true,
                            closeSelector: '.message-popup-cancel'
                        });
                     }


                 }, "json").done(function(){
                    $(send_message_button).removeClass('disabled');
                 }).fail(function(){
                    $(send_message_button).removeClass('disabled');
                 });

            }

        });

        $("body").delegate(".send-message-to-input", "keyup", function() {
            var search = $(this).val();

            var send_message_container = $(this).parents('.send-message-container');
            var user_list = $(send_message_container).find('.user-list');

            if(search != ""){

                $.post(
                    get_users_for_msg_combo_url,
                    {
                        search: search
                    },
                    function(data){

                        $(user_list).html(data);
                        $(user_list).slideDown('slow');

                    }, "html");

            }
            else{
                $(user_list).slideUp('slow');
            }


        });

        $("body").delegate(".user-item", "click", function() {

            var user_id = $(this).data('userId');
            var user_name = $(this).data('userName');
            var send_message_left = $(this).parents('.send-message-left');
            var send_message_input = $(send_message_left).find('.send-message-input');
            var msg = $(send_message_input).val();

            $(this).parents('.user-list').slideUp('slow', function(){
                setMessageToUserFormContent(user_id, user_name, msg);
            });

        });

        $("body").delegate(".send-message-to-user-delete", "click", function() {

            var send_message_form = $(this).parents('.send-message-form');
            $(send_message_form).load(get_message_to_user_form_with_input_content_url);

        });



        $('body').click(function(event){
            if($(event.target).is(':not(.user-list, .user-item)')){
                $('.user-list').slideUp('slow');
            }
        });

    });
	
	function editprofile(){
		window.location='<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->user->id);?>';
	}

</script>

<div id="public-profile-left-container">

		<div id="public-profile-left-border">
			<div id="public-profile-left-content">

				<div id="public-profile">
					<div id="public-profile-img">
						<a href="<?php echo JRoute::_('index.php?option==com_users&view=profile');?>" alt="<?php echo $this->user->name ?>">
							<?php echo Ideary::getUserImage($this->user->id,"50",$this->user->name,'style="width:50px;height:50px;"');?>
						</a>
					</div>
					
					<div id="public-profile-data">
						<div id="public-profile-username" style="overflow:hidden;">
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=profile');?>" alt="<?php echo $this->user->name ?>">
								<?php echo $this->user->name ?>
							</a>
						</div>
						<div id="public-profile-ranking-message">
							<!--<div id="public-profile-ranking"><?php //echo $this->user->ranking ?></div>-->
							<div class="public-profile-edit2" title="Editar Perfil" onclick="editprofile();">
							</div>
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
				<div id="public-profile-info">
					<?php if ($ciudad!="" || $provincia!="" || $pais!=""):?>
						<div class="public-profile-info-item public-profile-location">
						<?php echo $ciudad;?><?php if ($provincia!="" || $pais!="") { echo ",";}?>
						<?php echo $provincia;?><?php if ($pais!="") { echo ",";}?>
						<?php echo $pais;?>
                        </div>
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

						<div class="applausses" style="background-position: left 2px; margin-top: 8px;"><span class="user-ranking-info-text"><?php echo JText::_('APPLAUSE_GREETED') ?></span> <span class="user-ranking-info-count"><?php echo $this->user->applausses_received ?></span></div>
					</div>

				</div>
				
				<?php $interests = ideary::getInterestsByUserId($this->user->id);				?>

				<?php if(count($interests) > 0): ?>
				<div id="public-profile-interests-container">

                    <div id="public-profile-interests-title-container">
                        <div id="public-profile-interests-title"><?php echo JTEXT::_('FEATURED_INTERESTS') ?></div>
                        <div class="public-profile-edit2" title="Editar Perfil" onclick="editprofile();"></div>
                        <div style="clear: both;"></div>
                    </div>

					<div id="public-profile-interests">

						<?php foreach($interests as $interest): ?>
						<div class="public-profile-interest">
							<div class="public-profile-interest-color" style="background: #<?php echo $interest->color_code ?>;"></div>
							<div class="public-profile-interest-text"><?php echo $interest->title ?></div>
						</div>
						<?php endforeach ?>

					</div>
				</div>

				<?php endif ?>
			</div>
		</div>
	</div>

<div id="private-profile-right-container">

<div id="inbox">
    <div id="inbox-tab" class="selected"><?php echo JTEXT::_('INBOX') ?></div>
    <div id="inbox-container" class="msg-container selected">

        <div class="send-message-form">

            <div class="send-message">
                <div class="send-message-left">
                    <div class="send-message-container">

                        <input type="text" class="send-message-to-input" placeholder="<?php echo JTEXT::_('ADD_RECIPIENTS') ?>...">
                        <div style="clear: both;"></div>

                        <div class="user-list"></div>

                    </div>

                    <textarea class="send-message-input" placeholder="<?php echo JTEXT::_('WRITE_MESSAGE') ?>..."></textarea>

                </div>

                <div class="send-message-right">
                    <div class="edit-profile-button send-message-button" data-user-id="0"><?php echo JTEXT::_('SEND') ?></div>
                </div>

                <div style="clear: both;"></div>
            </div>

        </div>

        <?php if(count($this->inbox_messages) > 0): ?>
        <div class="messages-list">
                <?php foreach($this->inbox_messages as $inbox_message): ?>

                    <div class="message-item" data-message-id="<?php echo $inbox_message->message_id ?>">

                        <div class="message-user-img">
                            <?php echo Ideary::getUserImage($inbox_message->id, 50, null, null, null) ?>
                        </div>

                        <div class="message-container">

                            <div class="message-header">
                                <div class="message-title"><?php echo str_replace('{USER}', '<span class="msg-user-link"><a href="index.php?option=com_contact&view=public&id='.$inbox_message->id.'">'.$inbox_message->name.'</a></span>', JTEXT::_('MESSAGE_RECEIVED_FROM')) ?></div>
                                <div class="message-reply" title="<?php echo JTEXT::_('REPLY_MESSAGE') ?>" data-user-id="<?php echo $inbox_message->id ?>" data-username="<?php echo $inbox_message->name ?>"></div>
                                <div class="message-delete" title="<?php echo JTEXT::_('DELETE_MESSAGE') ?>" data-message-id="<?php echo $inbox_message->message_id ?>"></div>
                                <div class="message-date"><?php echo ideary::textDate($inbox_message->date_time) ?></div>
                                <div style="clear: both;"></div>
                            </div>

                            <div class="message-content"><?php echo $inbox_message->message ?></div>

                            <div class="block-user" title="<?php echo JTEXT::_('BLOCK_USER') ?>"></div>
                        </div>

                        <div style="clear: both;"></div>
                    </div>

                <?php endforeach ?>
        </div>
        <?php else: ?>

        <?php endif ?>



                <div style="clear: both;"></div>
    </div>
</div>




<div id="unknown-users">
    <div id="unknown-users-tab"><?php echo JTEXT::_('UNKNOWN_USERS') ?></div>
    <div id="unknown-users-container" class="msg-container">


        <div class="send-message-form">

            <div class="send-message">
                <div class="send-message-left">
                    <div class="send-message-container">

                        <input type="text" class="send-message-to-input" placeholder="<?php echo JTEXT::_('ADD_RECIPIENTS') ?>...">
                        <div style="clear: both;"></div>

                        <div class="user-list"></div>

                    </div>

                    <textarea class="send-message-input" placeholder="<?php echo JTEXT::_('WRITE_MESSAGE') ?>..."></textarea>

                </div>

                <div class="send-message-right">
                    <div class="edit-profile-button send-message-button" data-user-id="0"><?php echo JTEXT::_('SEND') ?></div>
                </div>

                <div style="clear: both;"></div>
            </div>

        </div>

        <?php if(count($this->unknown_users_messages) > 0): ?>
            <div class="messages-list">
                <?php foreach($this->unknown_users_messages as $inbox_message): ?>

                    <div class="message-item" data-message-id="<?php echo $inbox_message->message_id ?>">

                        <div class="message-user-img">
                            <?php echo Ideary::getUserImage($inbox_message->id, 50, null, null, null) ?>
                        </div>

                        <div class="message-container">

                            <div class="message-header">
                                <div class="message-title"><?php echo str_replace('{USER}', '<span class="msg-user-link"><a href="index.php?option=com_contact&view=public&id='.$inbox_message->id.'">'.$inbox_message->name.'</a></span>', JTEXT::_('MESSAGE_RECEIVED_FROM')) ?></div>
                                <div class="message-reply" title="<?php echo JTEXT::_('REPLY_MESSAGE') ?>" data-user-id="<?php echo $inbox_message->id ?>" data-username="<?php echo $inbox_message->name ?>"></div>
                                <div class="message-delete" title="<?php echo JTEXT::_('DELETE_MESSAGE') ?>" data-message-id="<?php echo $inbox_message->message_id ?>"></div>
                                <div class="message-date"><?php echo ideary::textDate($inbox_message->date_time) ?></div>
                                <div style="clear: both;"></div>
                            </div>

                            <div class="message-content"><?php echo $inbox_message->message ?></div>

                            <div class="block-user" title="<?php echo JTEXT::_('BLOCK_USER') ?>"></div>
                        </div>

                        <div style="clear: both;"></div>
                    </div>

                <?php endforeach ?>
            </div>
        <?php else: ?>

        <?php endif ?>


        <div style="clear: both;"></div>

    </div>
</div>


<div id="messages-sent">
    <div id="messages-sent-tab"><?php echo JTEXT::_('SENT_MESSAGES') ?></div>
    <div id="messages-sent-container" class="msg-container">


        <div id="sent-messages-list">
        <?php if(count($this->sent_messages) > 0): ?>
            <div class="messages-list">
                <?php foreach($this->sent_messages as $inbox_message): ?>

                    <div class="message-item" data-message-id="<?php echo $inbox_message->message_id ?>">

                        <div class="message-user-img">
                            <?php echo Ideary::getUserImage($inbox_message->id, 50, null, null, null) ?>
                        </div>

                        <div class="message-container">

                            <div class="message-header">
                                <div class="message-title"><?php echo str_replace('{USER}', '<span class="msg-user-link"><a href="index.php?option=com_contact&view=public&id='.$inbox_message->id.'">'.$inbox_message->name.'</a></span>', JTEXT::_('MESSAGE_SENT_TO')) ?></div>
                                <div class="message-delete" title="<?php echo JTEXT::_('DELETE_MESSAGE') ?>" data-message-id="<?php echo $inbox_message->message_id ?>"></div>
                                <div class="message-date"><?php echo ideary::textDate($inbox_message->date_time) ?></div>
                                <div style="clear: both;"></div>
                            </div>

                            <div class="message-content"><?php echo $inbox_message->message ?></div>

                            <div class="block-user" title="<?php echo JTEXT::_('BLOCK_USER') ?>"></div>
                        </div>

                        <div style="clear: both;"></div>
                    </div>

                <?php endforeach ?>
            </div>
        <?php else: ?>

        <?php endif ?>
        </div>


        <div style="clear: both;"></div>

    </div>
</div>


</div>

<div style="clear: both;"></div>