<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

// Create shortcut to parameters.
$params = $this->state->get('params');

// This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params->show_publishing_options);
if (!$editoroptions)
	$params->show_urls_images_frontend = '0';

$doc = JFactory::getDocument();

$app = JFactory::getApplication();
?>

<style type="text/css">

    #contentarea{
        padding: 0 !important;
    }

    #breadcrumbs{
        margin: 0 !important;
    }

    #wrapper2{
        width: 100% !important;
        padding: 0 !important;
    }

    #main{
        padding: 0 !important;
    }
	label.iPhoneCheckLabelOn, label.iPhoneCheckLabelOff{
        font-family: georgia !important;
        font-size: 12px !important;
        color: #898989 !important;
        text-shadow: none;
    }
</style>

<script type="text/javascript">

    var show_on_before_unload_msg = true;
    var editor_font_scale = <?php echo ($this->item->editor_font_scale)? $this->item->editor_font_scale : 4 ?>;
    var editor_font_size = '<?php echo ($this->item->editor_font_size)? $this->item->editor_font_size : '14px' ?>';
    var formChange = false;
    var changeImg = false;

    window.onbeforeunload = function(e){

        checkFormChange();

        if(show_on_before_unload_msg && formChange){
            return '';
        }

    };

    function checkFormChange(){

        formChange = false;

        if($('#text-editor').wysiwyg('document').find('body').css('font-size') != editor_font_size){
            formChange = true;
        }

        if($('#jform_title').val() != $('#jform_title_default').val()){
            formChange = true;
        }

        if($("#text-editor-default").html() != $("#text-editor").wysiwyg("getContent")){
            formChange = true;
        }

        if(changeImg){
            formChange = true;
        }

    }

    function remove_red_edges(){
        $('#jform_title').removeClass('write-invalid');
        $('#jform_title').removeClass('invalid');
        $('#text-editor-wysiwyg-iframe').removeClass('write-invalid');
        //$('.defaultSkin table.mceLayout').removeClass('write-invalid');
        //$('.defaultSkin table.mceLayout tr.mceFirst td').removeClass('write-invalid');
        //$('.defaultSkin table.mceLayout tr.mceLast td').removeClass('write-invalid');
    }

	Joomla.submitbutton = function(task, publish,target) {

        show_on_before_unload_msg = false;

		$('#jform_state').val(publish ? 1 : 0);

        $("#editor-font-scale").val(editor_font_scale);
        $("#editor-font-size").val($('#text-editor').wysiwyg('document').find('body').css('font-size'));

		if($('#jform_alias').val()!=""){
			var titulo = $('#jform_title').val();
			var s1= titulo.replace(/[^0-9a-zA-Z]/g, ' ');
			$('#jform_alias').val(s1);
		}
		
        $('#accionpost').val(target);
	
        var success = true;
		
        var jform_title = $('#jform_title');

        remove_red_edges();

        if($(jform_title).val() == ''){
            $(jform_title).addClass('write-invalid');
            success = false;
        }

        var txt = $("#text-editor").val();
        txt = strip_html_tags(txt);
		
		if(publish){

            if((txt.length < 500) || (txt.length > 3000)){
                $('#text-editor-wysiwyg-iframe').addClass('write-invalid');
                success = false;
            }

		}
        else{

            if(txt.length == 0){
                $('#text-editor-wysiwyg-iframe').addClass('write-invalid');
                success = false;
            }

        }
	
		if (/*(task == 'article.cancel' || document.formvalidator.isValid(document.id('adminForm'))) &&*/ success) {
			<?php //echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task);

            //$('#publish-button').attr("disabled", true);
            $('#preview-popup-publish-text-button').attr("disabled", true);
            //$('#save_button').attr("disabled", true);

            $('#save-button-progress').width($('#save_button').outerWidth());
            $('#save_button').hide();
            $('#save-button-progress').show();

            $('#publish-button-progress').width($('#publish-button').outerWidth());
            $('#publish-button').hide();
            $('#publish-button-progress').show();

		}else{
			hideAllTopBarSliders();
			if (publish==false){
				$('#forgot-password').lightbox_me({
						centered: true,
						closeSelector: '#close-forgot-pass-required-popup',
                        onClose: remove_red_edges
				});
			}else{
				$('#reset-password').lightbox_me({
						centered: true,
						closeSelector: '#close-reset-pass-required-popup',
                        onClose: remove_red_edges
				});
			}

            show_on_before_unload_msg = true;
		}
	}

    var delete_image_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=delete_image_url" ?>';
	
    var delete_icon_url = '<?php echo JURI::base() . "templates/beez_20/images/remove.png"; ?>';

    var REMOVE = '<?php echo JText::_('REMOVE') ?>';
    var ARE_YOU_SURE = '<?php echo JText::_('ARE_YOU_SURE') ?>';
    var ENTER_TITLE = '<?php echo JText::_('ENTER_TITLE') ?>';
    var BY = '<?php echo JText::_('BY') ?>';
	
    var USERNAME = '<?php echo addslashes(JFactory::getUser()->get('username')) ?>';
    var NAME = '<?php echo addslashes(JFactory::getUser()->get('name')) ?>';
	
    var SELECT_A_CATEGORY = '<?php echo JText::_('SELECT_A_CATEGORY') ?>';
    var SELECT_A_CATEGORY_COLOR = '000000';

    var edit = <?php echo ($this->item->id)? "true" : "false" ?>;
    var text_id = <?php echo ($this->item->id)? $this->item->id : "0" ?>;
    var interval1; 
    //var can_save = true;

    function setCharsCountValue(){

       // if(tinyMCE.activeEditor != null){
            //var txt = tinyMCE.activeEditor.getContent();
			var txt = $('#jform_articletext').val();
			//alert(txt);
            //strip the html
            
			/*txt = txt.replace(/(<([^>]+)>)/ig,"");//Remueve los tags
            txt = txt.replace(/\n/gi,"");//Remueve los saltos de linea
            txt = $("<div/>").html(txt).text();
			*/
            $('#chars-count').text(txt.length);
            if((txt.length < 500) || (txt.length > 3000)){
                $('#chars-count').attr('class', 'insufficient-chars-count');
                $('#publish-button').attr('disabled', true);
                $('#preview-popup-publish-text-button').attr('disabled', true);
            }
            else{
                $('#chars-count').attr('class', 'enough-chars-count');
                $('#publish-button').attr('disabled', false);
                $('#preview-popup-publish-text-button').attr('disabled', false);
            }

           // clearInterval(interval1);
      //  }

    }

    function strip_html_tags(html){
        html = html.replace(/(<([^>]+)>)/ig,"");//Remueve los tags
        html = html.replace(/\n/gi,"");//Remueve los saltos de linea
        html = $("<div/>").html(html).text();
        return html;
    }

    function setCharsCountValue2(){

        if($("#text-editor").wysiwyg("getContent") == null){
            return false;
        }
        else{
            clearInterval(interval1);
        }

        if(!$.browser.msie){
            $('#text-editor').wysiwyg('document').find('body').css('padding', '18px 13px');
        }
        $('#text-editor').wysiwyg('document').find('body').css('font-size', editor_font_size);

        var txt = $("#text-editor").wysiwyg("getContent");

        //strip the html
        txt = strip_html_tags(txt);


        $('#chars-count').text(txt.length);
        if((txt.length < 500) || (txt.length > 3000)){
            $('#chars-count').attr('class', 'insufficient-chars-count');
            $('#publish-button').attr('disabled', true);
            $('#preview-popup-publish-text-button').attr('disabled', true);
        }
        else{
            $('#chars-count').attr('class', 'enough-chars-count');
            $('#publish-button').attr('disabled', false);
            $('#preview-popup-publish-text-button').attr('disabled', false);
        }

    }

    function noopHandler(evt) {
        evt.stopPropagation();
        evt.preventDefault();
    }

    $(document).ready(function(){
		
		setTimeout(function(){
			$("#jform_title").show().focus();
		}, 500);
		
        $('#share-fb').iphoneStyle({ checkedLabel: JYES, uncheckedLabel: CHECK_NO,
            onChange: function(elem, value) {
                $('#share-hidden').val(value.toString());
            }
        });

        $('#comments').iphoneStyle({ checkedLabel: JYES, uncheckedLabel: CHECK_NO,
            onChange: function(elem, value) {
                $('#comments-hidden').val(value.toString());
            }
        });

        $('#jform_articletext').keyup(setCharsCountValue);

        interval1 = setInterval(setCharsCountValue2, 100);

        $("#text-editor").wysiwyg({
            rmUnusedControls: true,
            css: '<?php echo JURI::base()."templates/beez_20/css/wysiwyg.css"?>',
            brIE: false,
            plugins: {
                rmFormat: { rmMsWordMarkup: true }
            }
        });

        /*$.wysiwyg.rmFormat.enabled = true;
        $.wysiwyg.rmFormat.rmMsWordMarkup = true;
        $.wysiwyg.removeFormat($("#text-editor"));*/

        /*function paste(){

            //$("#text-editor").wysiwyg("selectAll");
            //$("#text-editor").wysiwyg("removeFormat");

            var newContent = cleanHTML($("#text-editor").wysiwyg("getContent"));
            $("#text-editor").wysiwyg("setContent", newContent);
        }*/

        // removes MS Office generated guff
        function cleanHTML(input) {
            // 1. remove line breaks / Mso classes
            var stringStripper = /(\n|\r| class=(")?Mso[a-zA-Z]+(")?)/g;
            var output = input.replace(stringStripper, ' ');
            // 2. strip Word generated HTML comments
            var commentSripper = new RegExp('<!--(.*?)-->','g');
            var output = output.replace(commentSripper, '');
            var tagStripper = new RegExp('<(/)*(ul|li|a|img|b|i|u|meta|link|span|\\?xml:|st1:|o:|font)(.*?)>','gi');
            // 3. remove tags leave content if any
            output = output.replace(tagStripper, '');
            // 4. Remove everything in between and including tags '<style(.)style(.)>'
            var badTags = ['style', 'script','applet','embed','noframes','noscript'];

            for (var i=0; i< badTags.length; i++) {
                tagStripper = new RegExp('<'+badTags[i]+'.*?'+badTags[i]+'(.*?)>', 'gi');
                output = output.replace(tagStripper, '');
            }
            // 5. remove attributes ' style="..."'
            var badAttributes = ['style', 'start'];
            for (var i=0; i< badAttributes.length; i++) {
                var attributeStripper = new RegExp(' ' + badAttributes[i] + '="(.*?)"','gi');
                output = output.replace(attributeStripper, '');
            }
            return output;
        }

        /*$('#text-editor-wysiwyg-iframe').contents().find("body").bind("paste", function (e) {
            setTimeout(paste, 250);
        });*/


        $("#undo").click(function () {
            $("#text-editor").wysiwyg("triggerControl", "undo");
            setCharsCountValue2();
        });

        $("#redo").click(function () {
            $("#text-editor").wysiwyg("triggerControl", "redo");
            setCharsCountValue2();
        });

        $("#bold").click(function () {
            $("#text-editor").wysiwyg("triggerControl", "bold");
        });

        $("#italic").click(function () {
            $("#text-editor").wysiwyg("triggerControl", "italic");
        });

        $("#left").click(function () {
            $("#text-editor").wysiwyg("triggerControl", "justifyLeft");
        });

        $("#center").click(function () {
            $("#text-editor").wysiwyg("triggerControl", "justifyCenter");
        });

        $("#right-b").click(function () {
            $("#text-editor").wysiwyg("triggerControl", "justifyRight");
        });

        $("#justify").click(function () {
            $("#text-editor").wysiwyg("triggerControl", "justifyFull");
        });

        $(document.getElementById('text-editor-wysiwyg-iframe').contentWindow.document).keyup(setCharsCountValue2);
		
//		$('#help-share-facebook-bubble').fadeOut('slow');
		
		$("body").delegate("#help-share-facebook-icon", "hover", function() {
            $('#help-share-facebook-bubble').fadeIn('fast');
        });

        $("#help-share-facebook-icon").live("mouseleave", function() {
            $('#help-share-facebook-bubble').fadeOut('fast');
        });

        $('.cancel-button').click(function(){

            var jform_state = $('#jform_state').val();
            var location = "index.php?option=com_users&view=profile&user_id="+USER_ID;

            switch(jform_state)
            {
                //Texto nuevo
                case "":
                    window.history.back();
                    break;
                //Texto borrador
                case "0":
                    location += "&draft=1";
                    window.location.href = location;
                    break;
                //Texto publicado
                case "1":
                    location += "&mytexts=1";
                    window.location.href = location;
                    break;
            }

        });	

    });

</script>

<div id="write-text-form"  style="overflow:inherit !important;" class="edit item-page<?php echo $this->pageclass_sfx; ?>">

<?
	$userId = JFactory::getUser()->get('id');
	$idearyCount = ideary::getCountTextsByUserId($userId);
	$hasFirst = ideary::getHasFirstByUserId($userId);
	
	if (!$hasFirst && $idearyCount < 1) {
?>

<div id="first-ideari" >Hola! Veo que estás redactando tu primer Ideari. Quería contarte que Ideari es una comunidad, y nos gustaría conocerte. Por eso, nos encantaría que en esta ocasión nos cuentes un poco sobre vos.</div>

<? } ?>

<div id="close-icon" class="cancel-button" title="<?php echo JText::_('JCANCEL') ?>"></div>
<form enctype= "multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

            <input type="hidden" name="jform[id]" value="<?php echo ($this->item->id)? $this->item->id : -1 ?>">

            <?php $jform = $app->getUserState('jformulario'); ?>

			<div class="formelm" id="text-title-form">
			    <?php 
                    $title = ($jform['title'])?  $jform['title'] : $this->item->title;

                    $alias = ($jform['title'])?  $jform['title'] : $this->item->title;
					$alias = str_replace('?','',$alias);
					$alias = str_replace('¿','',$alias);
					$alias = str_replace('!','',$alias);
					$alias = str_replace('¡','',$alias);
					$alias = str_replace(' ','-',$alias);
                ?>
                <input type="text" name="jform[title]" id="jform_title" maxlength="100" autocomplete="off" value="<?php echo $title ?>" placeholder="¿Tenés algo para decir?" class="inputbox required" size="30" required="required" />
                <input type="hidden" id="jform_title_default" name="jform_title_default" value="<?php echo $this->item->title ?>">
                <input type="hidden" name="jform[alias]" id="jform_alias" value="<?php echo $alias ?>"/>
			</div>
			

            <div id="editor-options">
                <div id="undo" unselectable="on"></div>
                <div id="redo" unselectable="on"></div>
                <div id="bold" unselectable="on"></div>
                <div id="italic" unselectable="on"></div>
                <div id="left" unselectable="on"></div>
                <div id="center" unselectable="on"></div>
                <div id="right-b" unselectable="on"></div>
                <div id="justify" unselectable="on"></div>
                <div style="clear: both;"></div>
            </div>

            <?php
                $this->item->introtext = ($this->item->introtext)? $this->item->introtext : '<br><br><span></span>';
                $introtext = ($jform['articletext'])?  $jform['articletext'] : $this->item->introtext;
            ?>
            <textarea id="text-editor" name="jform[articletext]"><?php echo $introtext ?></textarea>
            <div id="text-editor-default" style="display: none;"><?php echo $this->item->introtext ?></div>

		<?php if (is_null($this->item->id)):?>
			<div class="formelm">
			<?php echo $this->form->getLabel('alias','style=display:none;'); ?>
			<?php echo $this->form->getInput('alias','style=display:none;'); ?>
			</div>
		<?php endif; ?>


    <div id="configuration-container">

        <div id="configuration-content">

            <?php $app->setUserState('jformulario', null); ?>
			
			<input type="hidden" value="1" id="jform_catid" name="jform[catid]" class="inputbox required" aria-required="true" required="required" aria-invalid="false">
            <input type="hidden" value="1" id="jform_catid_default">

            <input type="hidden" value="" id="editor-font-scale" name="jform[editor-font-scale]">
            <input type="hidden" value="" id="editor-font-size" name="jform[editor-font-size]">

			<input type="hidden" id="jform_state" name="jform[state]" value="<?php echo $this->item->state?>"/>
			<input type="hidden" id="accionpost" name="accionpost" value="same"/>
			
            <div id="iphone-switches-container">

                <!--<div id="allow_comments" class="iphone-switch-content">
                    <div class="iphone-switch-text"><?php //echo JText::_('ALLOW_COMMENTS') ?></div>
                    <div class="iphone-switch">
                        <input type="checkbox" name="comments" id="comments" <?php //echo ($this->item->allow_comments)? 'checked="checked"' : '' ?> >
                    </div>
                </div>
                <input type="hidden" id="comments-hidden" name="jform[comments]" value="<?php //echo ($this->item->allow_comments)? 'true' : 'false' ?>">
-->
                <input type="hidden" id="comments-hidden" name="jform[comments]" value="1">

                <div id="help-share-facebook" style="display:none;">
                    <div id="share_facebook" class="iphone-switch-content">
                        <div class="iphone-switch-text"><?php echo JText::_('SHARE_ON_FB') ?></div>
                        <div class="iphone-switch">
                            <input type="checkbox" name="share" id="share-fb">
                        </div>


                    </div>
                    <input type="hidden" id="share-hidden" name="jform[share]" value="false">

                    <div id="help-share-facebook-icon">
                        <div id="help-share-facebook-bubble" style="display:none;">
                            <div id="help-share-facebook-bubble-left"></div>
                            <div id="help-share-facebook-bubble-center">
                                Habilitá esta opción para publicar tu texto en facebook.
                            </div>
                            <div id="help-share-facebook-bubble-right"></div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>

                    <div style="clear: both;"></div>
                </div>

            </div>

        </div>
        
    </div>


        <div id="char-counter-save-container">

            <div id="char-counter">

                <div class="char-counter-number">500</div>
                <div class="char-counter-lt">&le;</div>
                <div id="chars-count" class="insufficient-chars-count">0</div>
                <div class="char-counter-lt">&le;</div>
                <div class="char-counter-number">3000</div>

            </div>

            <div id="save-button-container">
                <?php if ($this->item->state==0){?>

                    <div id="save-button-progress" class="dot-progress-button"></div>
					<button type="button" id="save_button" class="write-text-form-button" onclick="Joomla.submitbutton('article.save', false, 'same')">
						Guardar
					</button>
				<?php }else{?>
                    <div id="publish-button-container">
                        <div id="publish-button-progress" class="dot-progress-button"></div>
                        <button type="button" id="publish-button" class="write-text-form-button" onclick="Joomla.submitbutton('article.save', true, 'view')">
                            Actualizar
                        </button>
                    </div>
				<?php }?>
            </div>

        </div>

            <div id="buttons-container">

                <button type="button" id="cancel-button" class="write-text-form-button cancel-button">
                    <?php echo JText::_('JCANCEL') ?>
                </button>

                 <?php if ($this->item->state==0){?>
                     <div id="publish-button-container">
                         <div id="publish-button-progress" class="dot-progress-button"></div>
                         <button type="button" id="publish-button" disabled="disabled" class="write-text-form-button" onclick="Joomla.submitbutton('article.save', true, 'home')">
                            Publicar
                         </button>
                     </div>
				<?php }else{?>

                    <div id="save-button-container-2">
                        <div id="save-button-progress" class="dot-progress-button"></div>
                        <button type="button" id="save_button" style="" class="write-text-form-button" onclick="Joomla.submitbutton('article.save', false, 'draft')">
                            Pasar a Borrador
                        </button>
                    </div>
				<?php }?>

                <button type="button" id="preview-button" class="write-text-form-button">
                    <?php echo JText::_('JPREVIEW') ?>
                </button>

            </div>

        <div style="clear: both;"></div>
    </div>

		</div>
		
	</fieldset>
		<input type="hidden" name="task" value="" />
		<?php if (isset($_GET["new"]) && $_GET["new"]=="1") { ?>
			<input type="hidden" name="jform[new]" value="1" />
		<?php }else{ ?>
			<input type="hidden" name="jform[new]" value="0" />
		<?php } ?>
		
		<?php if($this->params->get('enable_category', 0) == 1) :?>
		<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1);?>"/>
		<?php endif;?>
		
		<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />	
		<?php echo JHtml::_( 'form.token' ); ?>

</form>

</div>

<div id="preview-popup">

    <div id="preview-popup-category-color" style="background: #C1C2C2;"></div>

    <div id="preview-popup-buttons-container">
        <div id="back-to-edit-button" class="write-text-form-button"><?php echo JText::_('BACK_TO_EDITING') ?></div>

        <?php if ($this->item->state==0){?>
            <button id="preview-popup-publish-text-button" disabled="disabled" class="write-text-form-button" onclick="Joomla.submitbutton('article.save', true,'mytexts')"><?php echo JText::_('PUBLISH_TEXT') ?></button>
        <?php }else{?>
            <button id="preview-popup-publish-text-button" disabled="disabled" class="write-text-form-button" onclick="Joomla.submitbutton('article.save', true,'same')">Actualizar</button>
        <?php }?>

    </div>

    <div id="preview-popup-text-header">
        <div id="preview-popup-text-category" style="color: #C1C2C2;"></div>
        <div id="preview-popup-text-title"></div>
        <div id="preview-popup-text-author"></div>
    </div>

    <!--<div id="preview-popup-text-adjustments">
        <div id="preview-popup-text-a-plus"></div>
        <div id="preview-popup-text-a-minus"></div>
        <div id="preview-popup-text-contrast"></div>
    </div>-->

    <div id="preview-popup-text-content"></div>
</div>


<div id="forgot-password">

    <div id="close-forgot-pass-required-popup" class="close-button" title="<?php echo JText::_('CLOSE') ?>">
        <img src="<?php echo JURI::base() . "templates/beez_20/images/register-form-close-icon.png"; ?>"/>
    </div>

    <div id="forgot-password-title" class="with-pencil">¡Buen intento!</div>

    <div id="forgot-password-legend">Deb&eacute;s completar titulo y texto para poder guardarlo como borrador.</div>
</div>

<div id="reset-password">

    <div id="close-reset-pass-required-popup" class="close-button" title="<?php echo JText::_('CLOSE') ?>">
        <img src="<?php echo JURI::base() . "templates/beez_20/images/register-form-close-icon.png"; ?>"/>
    </div>

    <div id="reset-password-title" class="with-pencil">¡Buen intento!</div>

    <div id="reset-password-legend">Deb&eacute;s completar titulo y texto para poder publicarlo.</div>
</div>