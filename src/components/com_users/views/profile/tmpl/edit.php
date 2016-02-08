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

    $app = JFactory::getApplication();
    $showInterests = $app->getUserState('users.editinterest'); //SI ESTO ES 1

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
<div style="float:left;width:30%;">

<jdoc:include type="modules" name="leftprivateprofile" />
</div>
<style>
 label.iPhoneCheckLabelOn, label.iPhoneCheckLabelOff{
        font-family: georgia !important;
        font-size: 12px !important;
        color: #898989 !important;
        text-shadow: none;
    }
</style>

<style type="text/css">
    #contentarea{
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
</style>

<script type="text/javascript">

    var edit_profile_img;
    var background_image;

    function dotdotdotImageName(){

        $('#background-filename').each(function() {
            var path = $(this).html().split( '.' );
            if ( path.length > 1 ) {
                var name = path.pop();
                $(this).html( path.pop() + '<span class="extension">.' + name + '</span>' );
                $(this).dotdotdot({
                    after: 'span.extension',
                    wrap: 'letter'
                });
            }
        });

    }

    function handleFileSelect(evt) {
        var files = evt.target.files; // FileList object

        // Loop through the FileList and render image files as thumbnails.
        for (var i = 0, f; f = files[i]; i++) {

            // Only process image files.
            /*if (!f.type.match('image.*')) {
                continue;
            }*/

            if(!(
                    f.type == 'image/gif'
                    || f.type == 'image/jpg'
                    || f.type == 'image/jpeg'
                    || f.type == 'image/png'
                )
                )
            {
                $('#profile-image-error .register-form-error-center').text('La imagén debe ser gif, png, jpeg o jpg');
                $('#profile-image-error').fadeIn('slow');
                return false;
            }

            var reader = new FileReader();

            // Closure to capture the file information.
            reader.onload = (function(theFile) {
                return function(e) {

                    var remove_picture = $('<div id="edit-profile-img-delete" data-has-img="false"/>');
                    $(remove_picture).attr("title", DELETE_IMAGE);

                    var picture_name = $('<div id="edit-profile-img-name"/>');
                    $(picture_name).text(escape(theFile.name));

                    var img = $('<img/>');
                    $(img).attr('src', e.target.result);

                    var image_info = $('<div id="edit-profile-img-data"/>');
                    $(picture_name).appendTo($(image_info));
                    $(remove_picture).appendTo($(image_info));

                    var text_picture = $('<div id="edit-profile-img"/>');
                    $(image_info).appendTo($(text_picture));
                    $(img).appendTo($(text_picture));

                    $('#edit-profile-img-container').html($(text_picture));
                };
            })(f);

            // Read in the image file as a data URL.
            reader.readAsDataURL(f);
        }
    }

    function handleFileSelect2(evt) {
        var files = evt.target.files; // FileList object

        // Loop through the FileList and render image files as thumbnails.
        for (var i = 0, f; f = files[i]; i++) {

            // Only process image files.
            if (!f.type.match('image.*')) {
                continue;
            }

            var reader = new FileReader();

            // Closure to capture the file information.
            reader.onload = (function(theFile) {
                return function(e) {

                    var remove_picture = $('<div id="background-delete" data-has-img="false"/>');
                    $(remove_picture).attr("title", DELETE_IMAGE);

                    var picture_name = $('<div id="background-filename"/>');
                    $(picture_name).text(escape(theFile.name));

                    var img = $('<img/>');
                    $(img).attr('src', e.target.result);

                    var image_info = $('<div id="background-info"/>');
                    $(picture_name).appendTo($(image_info));
                    $(remove_picture).appendTo($(image_info));

                    var text_picture = $('<div id="background-with-image"/>');
                    $(image_info).appendTo($(text_picture));
                    $(img).appendTo($(text_picture));

                    $('#background-container').html($(text_picture));

                    //dotdotdotImageName();
                };
            })(f);

            // Read in the image file as a data URL.
            reader.readAsDataURL(f);
        }
    }

    function edit_profile_no_img(){

        var profile_image_error = '<div id="profile-image-error" class="register-form-error" style="display: none; bottom: 45px; right: -247px;">'
            +'<div class="register-form-error-left"></div>'
            +'<div class="register-form-error-center" style="max-width: 220px;"></div>'
            +'<div class="register-form-error-right"></div>'
            +'</div>';

        var edit_profile_no_img_icon = $('<div id="edit-profile-no-img-icon"/>');

        var edit_profile_no_img_text = $('<div id="edit-profile-no-img-text"/>');
        $(edit_profile_no_img_text).text(SELECT_IMAGE);


        var edit_profile_no_img = $('<div id="edit-profile-no-img"/>');
        $(edit_profile_no_img_icon).appendTo($(edit_profile_no_img));
        $(edit_profile_no_img_text).appendTo($(edit_profile_no_img));

        $('#edit-profile-img-container').html($(edit_profile_no_img));
        $(profile_image_error).appendTo($('#edit-profile-img-container'));

        if ($.browser.msie) {
            $('#jform_profilepicture_file').replaceWith($('#jform_profilepicture_file').clone());
        }
        else {
            $('#jform_profilepicture_file').val('');
        }

        edit_profile_img = document.getElementById('edit-profile-no-img');
        if(edit_profile_img != null){
            edit_profile_img.addEventListener('dragenter', noopHandler, false);
            edit_profile_img.addEventListener('dragexit', noopHandler, false);
            edit_profile_img.addEventListener('dragover', noopHandler, false);
            edit_profile_img.addEventListener('drop', drop1, false);
        }
    }

    function edit_background_no_img(){
        var edit_profile_no_img_icon = $('<div id="background-no-image-icon"/>');

        var edit_profile_no_img_text = $('<div id="background-no-image-text"/>');
        $(edit_profile_no_img_text).text(SELECT_IMAGE);


        var edit_profile_no_img = $('<div id="background-no-image"/>');
        $(edit_profile_no_img_icon).appendTo($(edit_profile_no_img));
        $(edit_profile_no_img_text).appendTo($(edit_profile_no_img));

        $('#background-container').html($(edit_profile_no_img));

        if ($.browser.msie) {
            $('#background-img').replaceWith($('#background-img').clone());
        }
        else {
            $('#background-img').val('');
        }

        background_image = document.getElementById('background-no-image');
        if(background_image != null){
            background_image.addEventListener('dragenter', noopHandler, false);
            background_image.addEventListener('dragexit', noopHandler, false);
            background_image.addEventListener('dragover', noopHandler, false);
            background_image.addEventListener('drop', drop2, false);
        }
    }

    function noopHandler(evt) {
        evt.stopPropagation();
        evt.preventDefault();
    }

    function drop1(evt) {
        evt.stopPropagation();
        evt.preventDefault();

        var f = evt.dataTransfer.files[0];

        var reader = new FileReader();

        // Closure to capture the file information.
        reader.onload = (function(theFile) {
            return function(e) {

                var remove_picture = $('<div id="edit-profile-img-delete" data-has-img="false"/>');
                $(remove_picture).attr("title", DELETE_IMAGE);

                var picture_name = $('<div id="edit-profile-img-name"/>');
                $(picture_name).text(escape(theFile.name));

                var img = $('<img/>');
                $(img).attr('src', e.target.result);

                var image_info = $('<div id="edit-profile-img-data"/>');
                $(picture_name).appendTo($(image_info));
                $(remove_picture).appendTo($(image_info));

                var text_picture = $('<div id="edit-profile-img"/>');
                $(image_info).appendTo($(text_picture));
                $(img).appendTo($(text_picture));

                $('#edit-profile-img-container').html($(text_picture));
            };
        })(f);

        // Read in the image file as a data URL.
        reader.readAsDataURL(f);

    }

    function drop2(evt) {
        evt.stopPropagation();
        evt.preventDefault();

        var f = evt.dataTransfer.files[0];

        var reader = new FileReader();

        // Closure to capture the file information.
        reader.onload = (function(theFile) {
            return function(e) {

                var remove_picture = $('<div id="background-delete" data-has-img="false"/>');
                $(remove_picture).attr("title", DELETE_IMAGE);

                var picture_name = $('<div id="background-filename"/>');
                $(picture_name).text(escape(theFile.name));

                var img = $('<img/>');
                $(img).attr('src', e.target.result);

                var image_info = $('<div id="background-info"/>');
                $(picture_name).appendTo($(image_info));
                $(remove_picture).appendTo($(image_info));

                var text_picture = $('<div id="background-with-image"/>');
                $(image_info).appendTo($(text_picture));
                $(img).appendTo($(text_picture));

                $('#background-container').html($(text_picture));

                //dotdotdotImageName();

            };
        })(f);

        // Read in the image file as a data URL.
        reader.readAsDataURL(f);

    }

    $(document).ready(function(){

        setScreenHeight();

        $('#jform_name').addClass('edit-profile-input-text');

        var edit_profile_container_height = $('#edit-profile-container').outerHeight();
        $('#private-profile-right-container').css('height', edit_profile_container_height+'px');

        $('.category-checkbox').iphoneStyle({ checkedLabel: JYES, uncheckedLabel: CHECK_NO,
            onChange: function(elem, value) {
                var catId = $(elem).val();
                if(value){
                    $('#juser_interests_'+catId).val(catId);
                }
                else{
                    $('#juser_interests_'+catId).val("");
                }
            }
        });

        <?php $profileImageErrors = $app->getUserState('profile-image-errors'); ?>
        <?php if(!empty($profileImageErrors)): ?>

            $('#profile-image-error .register-form-error-center').text('<?php echo $profileImageErrors[0]; ?>');
            $('#profile-image-error').fadeIn('slow');

            <?php $app->setUserState('profile-image-errors', array()); ?>

        <?php endif ?>

        $("body").delegate("#edit-profile-no-img", "click", function() {
            $('#jform_profilepicture_file').click();
        });

        $("body").delegate("#background-no-image", "click", function() {
            $('#background-img').click();
        });


        document.getElementById('jform_profilepicture_file').addEventListener('change', handleFileSelect, false);

        document.getElementById('background-img').addEventListener('change', handleFileSelect2, false);

        edit_profile_img = document.getElementById('edit-profile-no-img');
        if(edit_profile_img != null){
            edit_profile_img.addEventListener('dragenter', noopHandler, false);
            edit_profile_img.addEventListener('dragexit', noopHandler, false);
            edit_profile_img.addEventListener('dragover', noopHandler, false);
            edit_profile_img.addEventListener('drop', drop1, false);
        }

        background_image = document.getElementById('background-no-image');
        if(background_image != null){
            background_image.addEventListener('dragenter', noopHandler, false);
            background_image.addEventListener('dragexit', noopHandler, false);
            background_image.addEventListener('dragover', noopHandler, false);
            background_image.addEventListener('drop', drop2, false);
        }

        $("body").delegate("#edit-profile-img", "hover", function() {
            $('#edit-profile-img-data').fadeIn('slow');
        });

        $("#edit-profile-img").live("mouseleave", function() {
            $('#edit-profile-img-data').fadeOut('slow');
        });

        $("body").delegate("#background-with-image", "hover", function() {
            $('#background-info').fadeIn('slow');
        });

        $("#background-with-image").live("mouseleave", function() {
            $('#background-info').fadeOut('slow');
        });


        $("body").delegate("#edit-profile-img-delete", "click", function() {
            var hasImg = $(this).data('hasImg');
            if(hasImg){

                $.post(
                    delete_user_image_url,
                    {},
                    function(data){

                        if(data.success){
                            edit_profile_no_img();
                        }

                    }, "json");
            }
            else{
                edit_profile_no_img();
            }
        });


        $("body").delegate("#background-delete", "click", function() {
            var hasImg = $(this).data('hasImg');
            if(hasImg){

                $.post(
                    delete_user_bg_image_url,
                    {},
                    function(data){

                        if(data.success){
                            edit_background_no_img();
                        }

                    }, "json");
            }
            else{
                edit_background_no_img();
            }
        });

        $('.edit-profile-save').click(function(){
            var jform_profile_website = $('#jform_profile_website');
            var url = $(jform_profile_website).val();
            //$('#jform_profile_website').removeClass('write-invalid');
            $('.register-form-error').hide();


            if(isURLValid(url) || (url == '')){

                $('#edit-profile-save-progress-1').width($('#edit-profile-save-1').outerWidth());
                $('#edit-profile-save-1').hide();
                $('#edit-profile-save-progress-1').show();

                $('#edit-profile-save-progress-2').width($('#edit-profile-save-2').outerWidth());
                $('#edit-profile-save-2').hide();
                $('#edit-profile-save-progress-2').show();

                var form_id = $(this).data('formId');
                $('#'+form_id).submit();
            }
            else{
                //$('#jform_profile_website').addClass('write-invalid');
                $(jform_profile_website).focus();

                var edit_profile_input_box = $(jform_profile_website).parents('.edit-profile-input-box');
                var register_form_error_center = $(edit_profile_input_box).find('.register-form-error-center');
                $(register_form_error_center).text('Ingresá la URL en un formato correcto - empezando con http://');
                var register_form_error = $(edit_profile_input_box).find('.register-form-error');
                $(register_form_error).fadeIn('slow');
            }


        });

    });
	
</script>

<?php
    $edit_profile_tab_class = 'class="selected"';
    $outstanding_interest_tab_class = '';

    $edit_profile_style = 'style="z-index: 2;"';
    $outstanding_interest_style = 'style="z-index: 1;"';

    $edit_profile_container_style = 'style="visibility: visible;"';
    $outstanding_interest_container_style = 'style="visibility: hidden;"';


    if($showInterests == 1){

        $edit_profile_tab_class = '';
        $outstanding_interest_tab_class = 'class="selected"';

        $edit_profile_style = 'style="z-index: 1;"';
        $outstanding_interest_style = 'style="z-index: 2;"';

        $edit_profile_container_style = 'style="visibility: hidden;"';
        $outstanding_interest_container_style = 'style="visibility: visible;"';

    }

    ?>

<div id="public-profile-left-container">

		<div id="public-profile-left-border">
			<div id="public-profile-left-content">

				<div id="public-profile">
					<div id="public-profile-img">
						<a href="<?php echo JRoute::_('index.php?option==com_users&view=profile');?>" alt="<?php echo $this->user->name ?>">
							<?php echo Ideary::getUserImage($this->user->id,"200",$this->user->name,'style="width:100px;height:100px;"');?>
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

						<div class="applausses" style="background-position: left 2px; margin-top: 8px;"><span class="user-ranking-info-text"><?php echo JText::_('APPLAUSE_GREETED') ?></span> <span class="user-ranking-info-count"><?php echo $this->userData->applausses_received ?></span></div>
					</div>

				</div>

			</div>
		</div>
	</div>

<div id="private-profile-right-container">

    <div id="edit-profile" <?php echo $edit_profile_style ?>>
        <div id="edit-profile-tab" <?php echo $edit_profile_tab_class ?>><?php echo JText::_('EDIT_PROFILE') ?></div>
        <div id="edit-profile-container" <?php echo $edit_profile_container_style ?>>

            <form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">


                <!-- Este div dejarlo asi vacio entero-->
                <div style="display:none;">
                    <dl>
                        <dt>
                            <label id="jform_password1-lbl" for="jform_password1" class="hasTip" title="">Contraseña:</label>															<span class="optional"></span>
                        </dt>
                        <dd><input type="password" name="jform[password1]" id="jform_password1" value="" autocomplete="off" class="validate-password" size="30"></dd>
                        <dt>
                            <label id="jform_password2-lbl" for="jform_password2" class="hasTip" title="">Confirmar Contraseña:</label>															<span class="optional"></span>
                        </dt>
                        <dd><input type="password" name="jform[password2]" id="jform_password2" value="" autocomplete="off" class="validate-password" size="30"></dd>
                        <dt>
                            <label id="jform_email1-lbl" for="jform_email1" class="hasTip required" title="">Correo electronico:<span class="star">&nbsp;*</span></label>													</dt>
                        <dd><input type="email" name="jform[email1]" class="validate-email required" id="jform_email1" value="<?php echo $this->user->email?>" size="30" aria-required="true" required="required"></dd>
                        <dt>
                            <label id="jform_email2-lbl" for="jform_email2" class="hasTip required" title="">Confirmar correo electronico:<span class="star">&nbsp;*</span></label>													</dt>
                        <dd><input type="email" name="jform[email2]" class="validate-email required" id="jform_email2" value="<?php echo $this->user->email?>" size="30" aria-required="true" required="required"></dd>
                    </dl>
                </div>
                <!-- FIN Este div dejarlo asi vacio entero-->

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

                            /*if($sexo=="M"){
                                $selectedM="selected='selected'";
                            }elseif($sexo=="F"){
                                $selectedF="selected='selected'";
                            }*/
                            break;
                    }?>

                <?php endforeach;?>

                <input type="hidden" name="jform[id]" id="jform_id" value="<?php echo $this->user->id?>"/>
                <input type="hidden" name="fromwhere" id="fromwhere" value="editprofile"/>



            <div id="edit-profile-section-one">

                <?php //foreach ($this->form->getFieldsets() as $group => $fieldset):// Iterate through the form fieldsets and display each one.?>
                    <?php //if ($group=="core"):?>
                        <?php //$fields = $this->form->getFieldset($group);?>
                        <?php //foreach ($fields as $field):// Iterate through the fields in the set and display them.?>
                            <?php //if ($field->id =="jform_name"):?>
                                <!-- <div class="edit-profile-input-text-container">
                                    <div class="edit-profile-input-text-label"><?php //echo JTEXT::_('FULL_NAME') ?></div>
                                    <?php //echo $field->input; ?>
                                </div> -->
                            <?php //endif;?>
                        <?php //endforeach;?>
                    <?php //endif;?>
                <?php //endforeach;?>

                <?php $jform = $app->getUserState('jform'); ?>

                <?php $fullName = ($jform['name'])?  $jform['name'] : $this->user->name ?>
                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('FULL_NAME') ?></div>
                    <input type="text" name="jform[name]" id="jform_name" value="<?php echo $fullName ?>" class="required edit-profile-input-text invalid" size="30" aria-required="true" required="required" aria-invalid="true">
                </div>

                <div class="edit-profile-input-text-container" style="height: auto;">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('PROFILE_PHOTO') ?></div>

                    <div id="edit-profile-img-container">

                        <?php
                            $imageName = ideary::getUserImageName($this->user->id);
                        ?>
                        <?php if($imageName && ideary::existAllImagesOfUser($this->user->id)): ?>

                            <div id="edit-profile-img">
                                <div id="edit-profile-img-data">
                                    <div id="edit-profile-img-name">Cambiar Imagen</div>
                                    <div id="edit-profile-img-delete" data-has-img="true" title="<?php echo JText::_('DELETE_IMAGE') ?>"></div>
                                </div>
                                <img src="<?php echo Ideary::getUserImagePath($this->user->id, 200) ?>">
                            </div>

                        <?php else: ?>
                            <div id="edit-profile-no-img">
                                <div id="edit-profile-no-img-icon"></div>
                                <div id="edit-profile-no-img-text"><?php echo JText::_('SELECT_IMAGE') ?></div>
                            </div>

                            <div id="profile-image-error" class="register-form-error" style="display: none; bottom: 45px; right: -247px;">
                                <div class="register-form-error-left"></div>
                                <div class="register-form-error-center" style="max-width: 220px;"></div>
                                <div class="register-form-error-right"></div>
                            </div>
                        <?php endif ?>

                    </div>

                    <div style="clear: both;"></div>
                </div>

                <input style="display: none;" type="file" name="jform[profilepicture][file]" id="jform_profilepicture_file" value="" size="0" accept="image/png, image/gif, image/jpeg, image/jpg">

                <?php $birthdate = ($jform['profile']['dob'])?  $jform['profile']['dob'] : $fechanac ?>
                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('DATE_OF_BIRTH') ?></div>
                    <input type="date" class="edit-profile-input-text" name="jform[profile][dob]" id="jform_profile_dob" value="<?php echo $birthdate; ?>" size="30">
                </div>

                <?php

                    if($pais == '' || $pais == 'N'){
                        $paisSelect = 'Seleccionar un país';
                    }
                    else{
                        $paisSelect = $pais;
                    }

                    $country = ($jform['profile']['country'])?  $jform['profile']['country'] : $pais;

                    if($country == '' || $country == 'N'){
                        $countrySelect = 'Seleccionar un país';
                    }
                    else{
                        $countrySelect = $country;
                    }
                ?>

                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('COUNTRY') ?></div>
                    <div id="select-country" class="select-current" style="width: 218px;">

                        <div class="select-text"><?php echo $countrySelect ?></div>
                        <div class="select-arrow arrow-down"></div>
                        <div class="select-items" style="width: 210px; z-index: 2;">
                            <div class="select-item" data-value="Argentina">Argentina</div>
                            <div class="select-item" data-value="Brasil">Brasil</div>
                            <div class="select-item" data-value="Chile">Chile</div>
                            <div class="select-item" data-value="Colombia">Colombia</div>
                            <div class="select-item" data-value="México">México</div>
                            <div class="select-item" data-value="Uruguay">Uruguay</div>
                        </div>
                        <input type="hidden" name="jform[profile][country]" id="jform_profile_country" value="<?php echo $country ?>">
                    </div>
                </div>

                <?php $province = ($jform['profile']['region'])?  $jform['profile']['region'] : $provincia ?>
                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('PROVINCE') ?></div>
                    <input type="text" class="edit-profile-input-text" name="jform[profile][region]" id="jform_profile_region" value="<?php echo $province;?>" size="30">
                </div>

                <?php $city = ($jform['profile']['city'])?  $jform['profile']['city'] : $ciudad ?>
                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('CITY') ?></div>
                    <input type="text" class="edit-profile-input-text" name="jform[profile][city]" id="jform_profile_city" value="<?php echo $city;?>" size="30">
                </div>

                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('SEX') ?></div>

                    <?php
                        $sex = "Selecciona tu sexo";
                        $sex_val = "N";

                        $sexo = ($jform['profile']['phone'])?  $jform['profile']['phone'] : $sexo;

                        if($sexo == "M"){
                            $sex = JTEXT::_('MALE');
                            $sex_val = "M";
                        }
                        elseif($sexo == "F"){
                            $sex = JTEXT::_('FEMALE');
                            $sex_val = "F";
                        }
                    ?>
                    <div id="select-gender" class="select-current" style="width: 218px;">
                        <div class="select-text"><?php echo $sex ?></div>
                        <div class="select-arrow arrow-down"></div>
                        <div class="select-items" style="width: 210px;">
                            <div class="select-item" data-value="M"><?php echo JTEXT::_('MALE') ?></div>
                            <div class="select-item" data-value="F"><?php echo JTEXT::_('FEMALE') ?></div>
                        </div>
                        <input type="hidden" name="jform[profile][phone]" id="jform_profile_phone" value="<?php echo $sex_val ?>">
                    </div>
                </div>

                <?php $education = ($jform['profile']['address1'])?  $jform['profile']['address1'] : $educacion ?>
                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('EDUCATION') ?></div>
                    <input type="text" class="edit-profile-input-text" name="jform[profile][address1]" id="jform_profile_address1" value="<?php echo $education;?>" size="30">
                </div>

                <?php $ocupation = ($jform['profile']['address2'])?  $jform['profile']['address2'] : $ocupacion ?>
                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('OCCUPATION') ?></div>
                    <input type="text" class="edit-profile-input-text" name="jform[profile][address2]" id="jform_profile_address2" value="<?php echo $ocupation;?>" size="30">
                </div>

                <?php $sitioWeb = ($jform['profile']['website'])?  $jform['profile']['website'] : $website ?>
                <div class="edit-profile-input-text-container">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('PERSONAL_WEBSITE') ?></div>

                    <div class="edit-profile-input-box">
                        <input type="url" class="edit-profile-input-text" name="jform[profile][website]" id="jform_profile_website" value="<?php echo $sitioWeb;?>" size="30">
                        <div class="register-form-error" style="bottom: -5px; right: -214px;">
                            <div class="register-form-error-left"></div>
                            <div class="register-form-error-center" style="max-width: 190px;"></div>
                            <div class="register-form-error-right"></div>
                        </div>
                        <div style="clear: both;"></div>
                    </div>

                    <div style="clear: both;"></div>
                </div>

            </div>

            <div id="edit-profile-section-two">

                <?php $personalPhrase = ($jform['profile']['aboutme'])?  $jform['profile']['aboutme'] : $frasepersonal ?>
                <div class="edit-profile-input-text-container" style="height: auto;">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('PERSONAL_STATEMENT') ?></div>
                    <div style="width: 380px; float: left;">
                        <input type="text" style="width: 366px; float: none;" class="edit-profile-input-text" name="jform[profile][aboutme]" id="jform_profile_aboutme" value="<?php echo $personalPhrase;?>" size="50" maxlength="80">
                        <div id="phrase-legend"><?php echo JTEXT::_('PERSONAL_STATEMENT_LIMIT') ?>.</div>
                    </div>
                    <div style="clear: both;"></div>
                </div>

                <?php $app->setUserState('jform', null); ?>

                <div class="edit-profile-input-text-container" style="height: auto;">
                    <div class="edit-profile-input-text-label"><?php echo JTEXT::_('WALLPAPER') ?></div>
                    <div id="background-container">

                        <?php
                            $filename = ideary::getUserBackgroundFilename($this->user->id);
                            $userBg = "templates/beez_20/images/user_backgrounds/".$this->user->id."/".$filename;
                        ?>

                        <?php if($filename && file_exists($userBg)): ?>
                            <div id="background-with-image">
                                <div id="background-info">
                                    <div id="background-filename">Cambiar Imagen</div>
                                    <div id="background-delete" data-has-img="true" title="<?php echo JText::_('DELETE_IMAGE') ?>"></div>
                                </div>
                                <img src="<?php echo JURI::base().$userBg ?>">
                            </div>

                            <script type="text/javascript">
                                //dotdotdotImageName();
                            </script>

                        <?php else: ?>

                            <div id="background-no-image">
                                <div id="background-no-image-icon"></div>
                                <div id="background-no-image-text"><?php echo JText::_('SELECT_IMAGE') ?></div>
                            </div>

                        <?php endif ?>

                    </div>
                    <input type="file" style="display: none;" name="background-img" id="background-img">
                    <div style="clear: both;"></div>
                </div>

            </div>

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

    <button type="submit" style="display: none;"></button>
    <input type="hidden" name="option" value="com_users" />
    <input type="hidden" name="task" value="profile.save" />
    <?php echo JHtml::_('form.token'); ?>

    </form>
</div>

<div style="clear: both;"></div>

<?php
    $app->setUserState('users.editinterest',null);
?>