<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
//load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load( 'plg_user_profile', JPATH_ADMINISTRATOR );


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

    label.iPhoneCheckLabelOn, label.iPhoneCheckLabelOff {
        font-family: georgia !important;
        font-size: 12px !important;
        color: #898989 !important;
        text-shadow: none;
    }

</style>
<script type="text/javascript">

    var change_email_container_hidden = true;
    var change_password_container_hidden = true;

    $(document).ready(function(){

        setScreenHeight();

        $('.notification-checkbox').iphoneStyle({ checkedLabel: JYES, uncheckedLabel: CHECK_NO,
            onChange: function(elem, value) {
                var id = $(elem).attr('id');
                if(value){
                    $('#'+id+'-hidden').val('1');
                }
                else{
                    $('#'+id+'-hidden').val('0');
                }
            }
        });

        var language_option_selected = $('#jform_params_language option:selected');
        //alert($(language_option_selected).val());
        //alert($(language_option_selected).text());

        $('#edit-profile-tab').click(function(){
            $('#outstanding-interest-tab').removeClass('selected');
            $(this).addClass('selected');

            $('#outstanding-interest').css('z-index', '1');
            $('#edit-profile').css('z-index', '2');

            $('#outstanding-interest-container').css('visibility', 'hidden');
            $('#edit-profile-container').css('visibility', 'visible');
        });

        $('#outstanding-interest-tab').click(function(){
            $('#edit-profile-tab').removeClass('selected');
            $(this).addClass('selected');

            $('#edit-profile').css('z-index', '1');
            $('#outstanding-interest').css('z-index', '2');

            $('#edit-profile-container').css('visibility', 'hidden');
            $('#outstanding-interest-container').css('visibility', 'visible');
        });

        $('#change-email').click(function(){

            if(change_email_container_hidden){
                $('#change-email-container').slideDown('slow');
            }
            else{
                $('#change-email-container').slideUp('slow');
            }

            change_email_container_hidden = !change_email_container_hidden;
        });

        $('#change-password-button').click(function(){

            if(change_password_container_hidden){
                $('#change-password-container').slideDown('slow');
            }
            else{
                $('#change-password-container').slideUp('slow');
            }

            change_password_container_hidden = !change_password_container_hidden;
        });

        $('.edit-profile-save').click(function(){

            $('#edit-profile-save-progress-1').width($('#edit-profile-save-1').outerWidth());
            $('#edit-profile-save-1').hide();
            $('#edit-profile-save-progress-1').show();

            $('#edit-profile-save-progress-2').width($('#edit-profile-save-2').outerWidth());
            $('#edit-profile-save-2').hide();
            $('#edit-profile-save-progress-2').show();

            var form_id = $(this).data('formId');

            var new_email_value = $('#new_email').val();

            if(new_email_value!=""){
                $('#jform_email1').val(new_email_value);
            }

            $('#'+form_id).submit();
        });

        $('#select-language .select-item').click(function(){
			var selectedVal = $(this).data('value');
			$('#jform_params_language').val(selectedVal);
			//$("#jform_params_language").find(selectedVal).attr("selected", "selected");

        });

    });

	function editprofile(){
		window.location='<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>';
	}

    function editprofile2(){
        window.location='<?php echo JRoute::_('index.php?option=com_users&task=editinterest&user_id='.(int) $this->data->id);?>';
    }

</script>

<div id="public-profile-left-container">

		<div id="public-profile-left-border">
			<div id="public-profile-left-content">

				<div id="public-profile">
					<div id="public-profile-img">
						<a href="<?php echo JRoute::_('index.php?option==com_users&view=profile');?>" alt="<?php echo $this->user->name ?>">
							<?php echo Ideary::getUserImage($this->user->id,"50",$this->user->name,'style="width:100px;height:100px;"');?>
						</a>
					</div>
					
					<div id="public-profile-data">
						<div id="public-profile-username">
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=profile');?>" alt="<?php echo $this->user->name ?>">
								<?php echo $this->user->name ?>
							</a>
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

						<div class="applausses" style="background-position: left 2px; margin-top: 8px;"><span class="user-ranking-info-text"><?php echo JText::_('APPLAUSE_GREETED') ?></span> <span class="user-ranking-info-count"><?php echo $this->user->applausses_received ?></span></div>
					</div>

				</div>
				
				<?php $interests = ideary::getInterestsByUserId($this->user->id);				?>

				<?php if(count($interests) > 0): ?>
				<div id="public-profile-interests-container">

                    <div id="public-profile-interests-title-container">
                        <div id="public-profile-interests-title"><?php echo JTEXT::_('FEATURED_INTERESTS') ?></div>
                        <div id="edit-interests" class="public-profile-edit2" title="<?php echo JTEXT::_('EDIT_INTEREST') ?>" onclick="editprofile2();"></div>
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

    <form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">




        <div style="display:none;"> <!-- Esto dejarlo asi-->
            <input type="hidden" name="jform[id]" id="jform_id" value="949">
            <label id="jform_name-lbl" for="jform_name" class="hasTip required" title="" aria-invalid="false">Nome Completo:<span class="star">&nbsp;*</span></label>													</dt>
            <dd><input type="text" name="jform[name]" id="jform_name" value="<?php echo $this->userDatafinal->name?>" class="required" size="30" aria-required="true" required="required" aria-invalid="false"></dd>
        </div>


        <div id="edit-profile">
        <div id="edit-profile-tab" class="selected" style="width: 155px;">Configuración</div>
        <div id="edit-profile-container">

            <div class="profile-configuration-section">

                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('EMAIL') ?></div>
                    <input type="email" name="jform[email1]" style="background: #dcdcdc; color: #aeaeae;" class="validate-email required edit-profile-input-text" id="jform_email1" value="<?php echo $this->userDatafinal->email?>" size="30" aria-required="true" required="required">
                    <div id="change-email"></div>
                </div>

                <div id="change-email-container">
                    <div class="edit-profile-input-text-container">
                        <div class="edit-profile-input-text-label" style="padding: 3px 0;"><?php echo JTEXT::_('ENTER_NEW_EMAIL') ?></div>
                        <input type="email" name="new_email" class="validate-email required edit-profile-input-text" id="new_email" value="<?php echo $this->user->email?>" size="30" aria-required="true" required="required">
                    </div>
                </div>

                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('JGLOBAL_PASSWORD') ?></div>
                    <div id="change-password-button"><?php echo JTEXT::_('CHANGE_PASSWORD') ?></div>
                </div>

                <!--<div class="edit-profile-input-text-container" style="height: auto; margin-left: 160px;">
                    <div class="edit-profile-input-text-label">Contraseña Anterior</div>
                    <div style="width: 206px; float: left;">
                        <input type="text" style="width: 206px; float: none;" class="edit-profile-input-text" name="jform[profile][aboutme]" id="jform_profile_aboutme" value="<?php echo $frasepersonal;?>" size="50" maxlength="80">
                        <div id="phrase-legend">¿Olvidaste tu contraseña?</div>
                    </div>
                    <div style="clear: both;"></div>
                </div>-->

                <div id="change-password-container">
                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label" style="padding: 3px 0;"><?php echo JTEXT::_('NEW_PASSWORD') ?></div>
                    <input type="password" name="jform[password1]" id="jform_password1" value="" autocomplete="off" class="validate-password edit-profile-input-text" size="30">
                </div>

                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label" style="padding: 3px 0;"><?php echo JTEXT::_('CONFIRM_PASSWORD') ?></div>
                    <input type="password" name="jform[password2]" id="jform_password2" value="" autocomplete="off" class="validate-password edit-profile-input-text" size="30">
                </div>
                </div>

            </div>

            <!-- BEGIN - Comento dropdown idiomas-->

            <!-- <div class="profile-configuration-section" style="margin-top: 20px;">

            </div>


            <div class="profile-configuration-section" style="margin-top: 20px;">
				<div class="edit-profile-input-text-container">
					<div class="edit-profile-input-text-label"><?php echo JTEXT::_('SITE_LANGUAGE') ?></div>

					<?php
					
					
					$lang_val = $this->currentlang;
					
					
					
					if ($lang_val==""){
						$lang_val=="es-ES";
					}
					if ($lang_val=="en-GB"){
						$lang = "English";
					}elseif ($lang_val=="es-ES"){
						$lang = "Español";
					}elseif ($lang_val=="pt-BR"){
						$lang = "Português";
					}
					
					if ($lang_val==null){
						$lang = "Seleccion&aacute; Opci&oacute;n";
					}
					
					
					?>
					<div id="select-language" class="select-current" style="width: 218px;">
						<div class="select-text"><?php //echo $lang ?></div>
						<div class="select-arrow arrow-down"></div>
						<div class="select-items" style="width: 210px;">
							<div class="select-item" data-value="en-GB">English</div>
							<div class="select-item" data-value="es-ES">Español</div>
							<div class="select-item" data-value="pt-BR">Português</div>
						</div>
						<input type="hidden" name="jform[params][language]2" id="jform_params_language2" value="<?php //echo $lang_val ?>">
						<input type="hidden" name="jform[params][language]" id="jform_params_language" value="<?php //echo $lang_val ?>">
					</div>
				</div>

                <?php /* 
				<div style="display: none;">
                    <?php foreach ($this->form->getFieldsets() as $group => $fieldset):// Iterate through the form fieldsets and display each one.?>
                        <?php $fields = $this->form->getFieldset($group);?>
                        <?php foreach ($fields as $field):// Iterate through the fields in the set and display them.?>
                            <?php if ($field->id =="jform_params_language"  ):?>
                                <?php echo $field->input; ?>
                            <?php endif;?>
                        <?php endforeach;?>
                    <?php endforeach;?>
                </div>
				*/?>
            </div>-->

            <!-- END - Comento dropdown idiomas-->

            <div id="edit-profile-buttons">

                <div class="edit-profile-save-container">
                    <div id="edit-profile-save-progress-1" class="edit-profile-save-progress dot-progress-button"></div>
                    <div id="edit-profile-save-1" class="edit-profile-button edit-profile-save" data-form-id="member-profile"><?php echo JText::_('SAVE_CHANGES') ?></div>
                </div>

                <div class="edit-profile-button edit-profile-cancel"><?php echo JText::_('JCANCEL') ?></div>
                <div style="clear: both;"></div>

            </div>

        </div>
    </div>

    <div id="outstanding-interest">
        <div id="outstanding-interest-tab" style="left: 190px;"><?php echo JText::_('PRIVACY_AND_NOTIFICATIONS') ?></div>
        <div id="outstanding-interest-container">

            <div class="profile-configuration-section">
            <div class="edit-profile-input-text-container">
                <div class="edit-profile-input-text-label"><?php echo JText::_('VISIBILITY_OF_MY_TEXTS_APPLAUDED') ?></div>

                <?php
                $texts_applauded = "Solo yo";
                $texts_applauded_val = "0";

                switch ($this->UserNotifSettings->clap_texts_visib) {
                    case 0:
                        $texts_applauded = JText::_('JUST_ME');
                        $texts_applauded_val = "0";
                        break;
                    case 1:
                        $texts_applauded = JText::_('ONLY_MY_FOLLOWERS');
                        $texts_applauded_val = "1";
                        break;
                    case 2:
                        $texts_applauded = JText::_('EVERYONE');
                        $texts_applauded_val = "2";
                        break;
                }
                ?>
                <div id="select-language" class="select-current" style="width: 218px;">
                    <div class="select-text"><?php echo $texts_applauded ?></div>
                    <div class="select-arrow arrow-down"></div>
                    <div class="select-items" style="width: 210px;">
                        <div class="select-item" data-value="0"><?php echo JText::_('JUST_ME') ?></div>
                        <div class="select-item" data-value="1"><?php echo JText::_('ONLY_MY_FOLLOWERS') ?></div>
                        <div class="select-item" data-value="2"><?php echo JText::_('EVERYONE') ?></div>
                    </div>
                    <input type="hidden" name="privacidad_notificaciones[visibilidad_texto]" id="privacidad_notificaciones_visibilidad_texto" value="<?php echo $texts_applauded_val ?>">
                </div>
            </div>
            </div>

            <div class="profile-configuration-section" style="padding: 20px 0;">
                <div id="notifications-email-title"><?php echo JText::_('RECEIVE_EMAIL_NOTIFICATIONS_FOR_THE_FOLLOWING') ?>:</div>

                <div class="notification-line">

                    <div class="notification-text"><?php echo JText::_('NEW_APPLAUSE_TO_YOUR_TEXTS') ?></div>
                    <input type="checkbox" <?php if ($this->UserNotifSettings->clap==1) {echo "checked";}?> id="notification-checkbox-1" name="notification-checkbox-1" class="notification-checkbox"/>
                    <input type="hidden" id="notification-checkbox-1-hidden" name="privacidad_notificaciones[aplauso_texto]" value="<?php echo ($this->UserNotifSettings->clap==1)? "1" : "0" ?>">

                </div>

                <div class="notification-line">

                    <div class="notification-text"><?php echo JText::_('NEW_READERS_START_TO_FOLLOW') ?></div>
                    <input type="checkbox" <?php if ($this->UserNotifSettings->follow==1) {echo "checked";}?> id="notification-checkbox-2" name="notification-checkbox-2" class="notification-checkbox"/>
                    <input type="hidden" id="notification-checkbox-2-hidden" name="privacidad_notificaciones[nuevo_seguidor]" value="<?php echo ($this->UserNotifSettings->follow==1)? "1" : "0" ?>">

                </div>

                <div class="notification-line" style="display:none;">

                    <div class="notification-text"><?php echo JText::_('POSITION_CHANGE_IN_THE_RANKING_OF_AUTHORS') ?></div>
                    <input type="checkbox" <?php if ($this->UserNotifSettings->ranking==1) {echo "checked";}?> id="notification-checkbox-3" name="notification-checkbox-3" class="notification-checkbox"/>
                    <input type="hidden" id="notification-checkbox-3-hidden" name="privacidad_notificaciones[cambio_ranking]" value="<?php echo ($this->UserNotifSettings->ranking==1)? "1" : "0" ?>">

                </div>

                <div class="notification-line">

                    <div class="notification-text"><?php echo JText::_('JOIN_TEXTS_WEEKLY_TOP') ?></div>
                    <input type="checkbox" <?php if ($this->UserNotifSettings->text_top==1) {echo "checked";}?> id="notification-checkbox-4" name="notification-checkbox-4" class="notification-checkbox"/>
                    <input type="hidden" id="notification-checkbox-4-hidden" name="privacidad_notificaciones[texto_top]" value="<?php echo ($this->UserNotifSettings->text_top==1)? "1" : "0" ?>">

                </div>

                <div class="notification-line">

                    <div class="notification-text"><?php echo JText::_('QUOTE_FROM_AUTHOR_TO_YOUR_TEXTS') ?></div>
                    <input type="checkbox" <?php if ($this->UserNotifSettings->comment==1) {echo "checked";}?> id="notification-checkbox-5" name="notification-checkbox-5" class="notification-checkbox"/>
                    <input type="hidden" id="notification-checkbox-5-hidden" name="privacidad_notificaciones[comentarios]" value="<?php echo ($this->UserNotifSettings->comment==1)? "1" : "0" ?>">

                </div>
            </div>

            <input type="hidden" name="fromwhere" id="fromwhere" value="editconfiguration"/>

            <div class="profile-configuration-section" style="padding: 20px 0;">



                <div id="notifications-frequency">
                    <div id="notifications-frequency-text"><?php echo JText::_('HOW_OFTEN_RECEIVE_EMAILS') ?></div>

                    <?php
                    $not_frecuency = JText::_('WEEKLY');
                    $not_frecuency_val = "0";

                    switch ($this->UserNotifSettings->frequency) {
                        case 0:
                            $not_frecuency = JText::_('WEEKLY');
                            $not_frecuency_val = "0";
                            break;
                        case 1:
                            $not_frecuency = JText::_('DAILY');
                            $not_frecuency_val = "1";
                            break;
                    }

                    ?>
                    <div id="notifications-frequency-select" class="select-current" style="width: 138px;">
                        <div class="select-text"><?php echo $not_frecuency ?></div>
                        <div class="select-arrow arrow-down"></div>
                        <div class="select-items" style="width: 130px;">
                            <div class="select-item" data-value="0"><?php echo JText::_('WEEKLY') ?></div>
                            <div class="select-item" data-value="1"><?php echo JText::_('DAILY') ?></div>
                        </div>
                        <input type="hidden" name="privacidad_notificaciones[frecuencia_email]" id="privacidad_notificaciones_frecuencia_email" value="<?php echo $not_frecuency_val ?>">
                    </div>

                </div>

            </div>

            <div id="edit-profile-buttons">

                <div class="edit-profile-save-container">
                    <div id="edit-profile-save-progress-2" class="edit-profile-save-progress dot-progress-button"></div>
                    <div id="edit-profile-save-2" class="edit-profile-button edit-profile-save" data-form-id="member-profile"><?php echo JText::_('SAVE_CHANGES') ?></div>
                </div>

                <div class="edit-profile-button edit-profile-cancel"><?php echo JText::_('JCANCEL') ?></div>
                <div style="clear: both;"></div>

            </div>

        </div>
    </div>


            <button style="display: none;" type="submit" class="validate"></button>
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="profile.save" />
            <?php echo JHtml::_('form.token'); ?>

    </form>

</div>

<div style="clear: both;"></div>