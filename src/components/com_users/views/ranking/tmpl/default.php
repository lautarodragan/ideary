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

?>

<script type="text/javascript">

    var max_posicion = <?php echo count($this->authors) ?>;

    $(document).ready(function(){

        $("body").delegate('.user-ranking-follow-button', 'click',function(){

            if(USER_LOGGED){
                var writer_id = $(this).data('authorId');

                $.post(
                    follow_url,
                    { writer_id: writer_id },
                    function(data) {

                        if(data.success){
                            var html = '<div class="user-ranking-following-button" data-author-id="'+writer_id+'">'+FOLLOWING+'</div>';
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
                            var html = '<div class="user-ranking-follow-button" data-author-id="'+writer_id+'">'+FOLLOW_AUTHOR+'</div>';
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

        $("body").delegate('#h-scroll-author-ranking', 'mousewheel', function(event, delta){

            val = this.scrollLeft - (delta * 700);
            $(this).stop().animate({scrollLeft:val}, 1000);
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
                    var val = $('#h-scroll-author-ranking').scrollLeft() - (delta * 700);
                    $('#h-scroll-author-ranking').stop().animate({scrollLeft:val},1000);
                }
            }

        });

        $('#go-to-position-button').click(function(){
            var position = $('#go-to-position-input').val();
            var regex = /^[0-9]+$/;
            if(regex.test(position)){

                if(position <= max_posicion){
                    position = position - 1;
                    $('#h-scroll-author-ranking').stop().animate({scrollLeft:(position*150)},500);
                }
                else{
                    alert('La posición '+position+' no existe');
                    return false;
                }

            }
            else{
                alert('La posición debe ser un numero entero positivo');
                return false;
            }

        });

        $('#top-position').click(function(){
            $('.more-applauded-texts-top-buttons').removeClass('selected');
            $(this).addClass('selected');
            $('#h-scroll-author-ranking').stop().animate({scrollLeft:0},500);
        });

        $('#my-position').click(function(){
            $('.more-applauded-texts-top-buttons').removeClass('selected');
            $(this).addClass('selected');
            var my_id = $(this).data('myId');
            var my_position = $('#user-'+my_id).data('position');
            my_position = my_position - 1;
            $('#h-scroll-author-ranking').stop().animate({scrollLeft:(my_position*150)},500);
        });

    });

</script>

<div id="ranking-of-authors-top">

    <div id="ranking-of-authors-top-title"><?php echo JText::_('RANKING_OF_AUTHORS') ?></div>

    <div id="ranking-of-authors-top-buttons-container">

        <div class="more-applauded-texts-top-buttons write-text-form-button selected" id="top-position"><?php echo JText::_('TOP') ?></div>

        <?php if($this->user->get('id') != 0): ?>
            <div class="more-applauded-texts-top-buttons write-text-form-button" id="my-position" data-my-id="<?php echo $this->user->get('id') ?>"><?php echo JText::_('MY_POSITION') ?></div>
        <?php endif ?>

        <div id="go-to-position"><?php echo JText::_('GO_TO_POSITION') ?></div>
        <input type="text" id="go-to-position-input" placeholder="0">
        <div id="go-to-position-button"></div>

    </div>

    <div style="clear: both;"></div>

</div>

<?php if(count($this->authors) > 0): ?>

<div id="h-scroll-author-ranking">
    <ul id="ul-author-ranking" style="width: <?php echo (count($this->authors)*150)?>px">

        <?php $i=1 ?>
        <?php foreach($this->authors as $author): ?>
		<?php 
		
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
			$userExtraData = Ideary::getExtraUserData($author->id);
	?>
	
	<?php foreach ($userExtraData as $campo):?>
				
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
            <li class="li-author-ranking">

            <div class="ranking-user" id="user-<?php echo $author->id ?>" data-position="<?php echo $i ?>">

                <div class="user-ranking-index">
                    <div class="user-ranking-index-left"></div>
                    <div class="user-ranking-index-center"><?php echo $i ?></div>
                    <div class="user-ranking-index-right"></div>
                </div>

                <div class="user-ranking-img-container">
					<a href="<?php echo JRoute::_('index.php?option=com_contact&view=public&id='.$author->id) ?>" title="<?php echo $author->name?>">
						<?php $user_picture = Ideary::getUserImage($author->id,"200",$author->name,'style="width:104px;height:104px;"',JURI::base() . "templates/beez_20/images/no-user-image.png"); 
							echo $user_picture;
						?>
					</a>
                </div>

                <div class="user-ranking-name">
					<a href="<?php echo JRoute::_('index.php?option=com_contact&view=public&id='.$author->id) ?>" title="<?php echo $author->name?>">
						<?php echo $author->name ?>
					</a>
				</div>

                <div class="user-ranking-data">
					<?php if ($ciudad!="" || $provincia!="" || $pais!=""):?>
						<div class="user-ranking-location">
							<?php echo $ciudad;?><?php if ($provincia!="" || $pais!="") { echo ",";}?>
							<?php echo $provincia;?><?php if ($pais!="") { echo ",";}?>
							<?php echo $pais;?>
						</div>
					<?php endif;?>
                    <?php if ($website!=""):?>
						<div class="user-ranking-website">
							<a href="<?php echo $website;?>" target="_blank"><?php echo $website;?></a>
						</div>
					<?php endif;?>
                    <div class="user-ranking-social-icons">

                        <?php if($author->provider == "facebook"): ?>
                            <div class="fb-icon"></div>
                        <?php elseif($author->provider == "twitter"): ?>
                            <div class="tw-icon"></div>
                            <!--<div class="skype-icon"></div>-->
                        <?php endif ?>

                        <div style="clear: both;"></div>
                    </div>
                </div>

                <div class="user-ranking-info-follow-button">
                    <div class="user-ranking-info">
                        <div class="followers"><span class="user-ranking-info-text"><?php echo JText::_('FOLLOWERS') ?></span> <span class="user-ranking-info-count"><?php echo $author->followers ?></span></div>
                        <div class="followeds"><span class="user-ranking-info-text"><?php echo JText::_('FOLLOWING') ?></span> <span class="user-ranking-info-count"><?php echo $author->following ?></span></div>
                        <div class="applausses"><span class="user-ranking-info-text"><?php echo JText::_('APPLAUSE_GREETED') ?></span> <span class="user-ranking-info-count"><?php echo $author->applausses_received ?></span></div>
                    </div>
                    <div class="follow-button-container" data-author-id="<?php echo $author->id ?>">
                        <?php if(is_null($author->followed)): ?>
                            <div class="user-ranking-follow-button" data-author-id="<?php echo $author->id ?>"><?php echo JText::_('FOLLOW_AUTHOR') ?></div>
                        <?php else: ?>
                            <div class="user-ranking-following-button" data-author-id="<?php echo $author->id ?>"><?php echo JText::_('FOLLOWING') ?></div>
                        <?php endif ?>
                    </div>
                </div>
            </div>

            </li>
            <?php $i++ ?>
        <?php endforeach ?>
    </ul>
</div>

<?php else: ?>
    <div id="h-scroll-author-ranking">
        <ul id="ul-author-ranking">
        </ul>
    </div>
<?php endif ?>