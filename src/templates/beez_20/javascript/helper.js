var login_container_showed = false;
//var notifications_container_showed = false;
var footer_container_showed = false;
var combo_hidden = true;
var IROUTE = window.location.protocol + "//" + window.location.hostname;
var timeOutOne;
var slider_selected;
var notifications_container_showed_once = false;


$.preloadImages = function()
{
    for(var i = 0; i < arguments.length; i++)
    {
        $("<img />").attr("src", arguments[i]);
    }
}

function AJAXCrearObjeto(){
	var obj;   
	if(window.XMLHttpRequest) { // no es IE
		obj = new XMLHttpRequest();
		
	} else { // Es IE o no tiene el objeto
		try {
			obj = new ActiveXObject('Microsoft.XMLHTTP');
		}  catch (e) {
			alert('El navegador utilizado no est? soportado');
		}
	}
	
	return obj;
}

function setTextAuthorDivWidth(){

//     $('.text-author').each(function(){

//         var text_bottom_content = $(this).parents('.text-bottom-content');
//         var text_bottom_content_width = $(text_bottom_content).width();

//         var applauses_width = $(text_bottom_content).find('.applauses').width();
//         var comments_width = $(text_bottom_content).find('.comments-count-container').outerWidth(true);
//         var separacion = 20;
//         var text_author_width = text_bottom_content_width - (applauses_width+comments_width+separacion);

//         $(this).css('width', text_author_width+'px');

//     });
}

function resizePublicProfileLeftContainer(){
    var browser_height = $(window).height();
    var topbar_height = $("#topbar").height();

    var public_profile_left_container_height = browser_height - (topbar_height + 13);
    $('#public-profile-left-container').css('height', public_profile_left_container_height+'px');
}

function setMainHeight(){
    var browser_height = $(window).height();
    var topbar_height = 35;
    $('#main').css('min-height', (browser_height-(topbar_height+20))+'px');
}

function setScreenHeight(){

    var edit_profile = $('#edit-profile-tab').outerHeight(true) + $('#edit-profile-container').outerHeight(true) + 20;
    var outstanding_interest = $('#outstanding-interest-tab').outerHeight(true) + $('#outstanding-interest-container').outerHeight(true) + 20;
    var body_height = $(window).height() - $('#topbar').outerHeight(true);

    var height = Math.max(edit_profile, outstanding_interest, body_height) ;

    $('#contentarea').css('height', height+'px');
    $('#public-profile-left-container').css('height', (height-13)+'px');
}

function isURLValid(url) {
    var regex=/^(ht|f)tps?:\/\/\w+([\.\-\w]+)?\.([a-z]{2,4}|travel)(:\d{2,5})?(\/.*)?$/i;
    return regex.test(url);
}

function checkUserLogged(){

    var result = true;

    if(!USER_LOGGED){

       /* $('#login-required-popup').lightbox_me({
            centered: true,
            closeSelector: '#close-login-required-popup'
        });*/
		
			hideAllTopBarSliders();
            login_container_showed = true;
			document.getElementById("message_not_logged_in_action").style.display="block";
			$('#register_form').lightbox_me({
					centered: true,
					closeSelector: '#close-register_form-required-popup',
                    onClose: function(){
                        $('.register-form-error').hide();
                    }
				});		

        result = false;
    }

    return result;
}

function processTinyMCEKeyUpEvent(){

    var txt = tinyMCE.activeEditor.getContent();
	
	
	
    //strip the html
    txt = txt.replace(/(<([^>]+)>)/ig,"");//Remueve los tags
    //txt = txt.replace(/ /g,\"\");//Remueve los espacios
    txt = txt.replace(/\n/gi,"");//Remueve los saltos de linea
    txt = $("<div/>").html(txt).text();

    $('#chars-count').text(txt.length);

    if((txt.length < 1000) || (txt.length > 3000)){
        $('#chars-count').attr('class', 'insufficient-chars-count');
        $('#publish-button').attr('disabled', true);
    }
    else{
        $('#chars-count').attr('class', 'enough-chars-count');
        $('#publish-button').attr('disabled', false);
    }


}


function processClapEvent(text_id, writer_id, clap_container){

    var result = checkUserLogged();

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
                    $(clap_container).text(APPLAUDED);
                    $(clap_container).attr('class', 'applauded');
                }
            }
            , "json"
        );

    }

    return result;

}

function generate_user_box_pagination(type, itemId, cantItems){

    if(cantItems > 16){


        var clase = '';
        var user_box_pagination_html = '';
        var cantPages = Math.ceil(cantItems/16);

        switch(type)
        {
            case 'applauses':
                clase = 'user-applauses-page';
                break;

            case 'followeds':
                clase = 'user-followeds-page';
                break;

            case 'followers':
                clase = 'user-followers-page';
                break;

        }

        for(var i=0; i < cantPages; i++){
            user_box_pagination_html += '<span class="user-box-pagination-page '+clase+'" data-item-id="'+itemId+'" data-page-number="'+i+'">'+(i+1)+'</span>';

            if(i < (cantPages-1)){
                user_box_pagination_html += '<span class="user-box-pagination-pipe">|</span>';
            }
        }

        $('#user-box-pagination').html(user_box_pagination_html);
        $('#user-box-pagination .user-box-pagination-page:first').addClass('selected');
        $('#user-box-pagination').show();
    }
    else{
        $('#user-box-pagination').hide();
    }
}

function processAddFavoritesEvent(text_id, fav_container){

    if(USER_LOGGED){

        $.post(
            add_to_favorites_url,
            { text_id: text_id },
            function(data) {

                if(data.success){

                    $(fav_container).attr('id', 'no-favorite-icon');
                    $(fav_container).attr('title', UNDO_ADD_TO_FAVORITES);

                }

            }
            , "json"
        );

    }
    else{
        $('#login-required-popup').lightbox_me({
            centered: true,
            closeSelector: '#close-login-required-popup'
        });
    }


}

function processAddFavoritesEvent2(text_id){

    var result = checkUserLogged();

    if(result){

        $.post(
            add_to_favorites_url,
            { text_id: text_id },
            function(data) {

                result = data.success;

            }
            , "json"
        );

    }

    return result;
}

function processUndoAddFavoritesEvent(text_id, fav_container){

    if(USER_LOGGED){

        $.post(
            undo_add_to_favorites_url,
            { text_id: text_id },
            function(data) {

                if(data.success){

                    $(fav_container).attr('id', 'favorite-icon');
                    $(fav_container).attr('title', ADD_TO_FAVORITES);

                }

            }
            , "json"
        );

    }
    else{
        $('#login-required-popup').lightbox_me({
            centered: true,
            closeSelector: '#close-login-required-popup'
        });
    }


}

function processUndoAddFavoritesEvent2(text_id){

    var result = checkUserLogged();

    if(result){

        $.post(
            undo_add_to_favorites_url,
            { text_id: text_id },
            function(data) {

                result = data.success;

            }
            , "json"
        );

    }

    return result;
}

function processSaveEvent(text_id, save_box_id, unarchive_button_id){

    if(USER_LOGGED){

        $.post(
            add_to_saved_url,
            { text_id: text_id },
            function(data) {

                if(data.success){
                    html = "<button id='"+unarchive_button_id+"'>"+UNARCHIVE+"</button>";
                    $('#'+save_box_id).html(html);
                }

            }
            , "json"
        );

    }
    else{
        $('#login-required-popup').lightbox_me({
            centered: true,
            closeSelector: '#close-login-required-popup'
        });
    }


}

function processSaveEvent2(text_id){

    var result = checkUserLogged();

    if(result){

        $.post(
            add_to_saved_url,
            { text_id: text_id },
            function(data) {

                result = data.success

            }
            , "json"
        );

    }

    return result;
}

function processUnarchiveEvent(text_id, save_box_id, save_button_id){

    if(USER_LOGGED){

        $.post(
            unarchive_url,
            { text_id: text_id },
            function(data) {

                if(data.success){
                    html = "<button id='"+save_button_id+"'>"+SAVE+"</button>";
                    $('#'+save_box_id).html(html);
                }

            }
            , "json"
        );

    }
    else{
        $('#login-required-popup').lightbox_me({
            centered: true,
            closeSelector: '#close-login-required-popup'
        });
    }


}

function processUnarchiveEvent2(text_id){

    var result = checkUserLogged();

    if(result){

        $.post(
            unarchive_url,
            { text_id: text_id },
            function(data) {

                result = data.success;

            }
            , "json"
        );

    }

    return result;

}

function processDenounceEvent(text_id, denounce_box_id){

    if(USER_LOGGED){

        $.post(
            denounce_url,
            { text_id: text_id },
            function(data) {

                if(data.success){
                    html = '<span id="denounced">'+YOU_ALREADY_REPORTED_THIS_TEXT+'</span>';
                    $('#'+denounce_box_id).html(html);
                }

            }
            , "json"
        );

    }
    else{
        $('#login-required-popup').lightbox_me({
            centered: true,
            closeSelector: '#close-login-required-popup'
        });
    }


}


function processFollowEvent(writer_id, follow_box_id, unfollow_button_id){

    if(USER_LOGGED){

        $.post(
            follow_url,
            { writer_id: writer_id },
            function(data) {

                if(data.success){
                    html = "<button id='"+unfollow_button_id+"'>"+UNFOLLOW+"</button>";
                    $('#'+follow_box_id).html(html);
                }

            }
            , "json"
        );

    }
    else{
        $('#login-required-popup').lightbox_me({
            centered: true,
            closeSelector: '#close-login-required-popup'
        });
    }


}

function processUnfollowEvent(writer_id, follow_box_id, follow_button_id){

    if(USER_LOGGED){

        $.post(
            unfollow_url,
            { writer_id: writer_id },
            function(data) {

                if(data.success){
                    html = "<button id='"+follow_button_id+"'>"+FOLLOW+"</button>";
                    $('#'+follow_box_id).html(html);
                }

            }
            , "json"
        );

    }
    else{
        $('#login-required-popup').lightbox_me({
            centered: true,
            closeSelector: '#close-login-required-popup'
        });
    }


}


function processSendMessageEvent(user_to, subject, message, send_button_box, loading_div_id, popup_div_id, subject_field_id, message_field_id){

    if(USER_LOGGED){

        var errors = new Array();

        if(subject == ""){
            errors.push(FIELD_IS_REQUIRED.replace("{field}", SUBJECT));
        }

        if(message == ""){
            errors.push(FIELD_IS_REQUIRED.replace("{field}", MESSAGE));
        }

        if(errors.length == 0){

            var html_aux = $('#'+send_button_box).html();
            $('#'+send_button_box).html($('#'+loading_div_id).html());

            $.post(
                send_message_url,
                {
                    user_to: user_to,
                    subject: subject,
                    message: message
                },
                function(data) {

                    if(data.success){

                        $('#'+loading_div_id).hide();
                        $('#'+send_button_box).html(html_aux);
                        $('#'+popup_div_id).trigger('close');
                        $('#'+subject_field_id).val('');
                        $('#'+message_field_id).val('');

                    }
                    else{

                        $('#'+send_button_box).html(html_aux);
                    }

                }
                , "json"
            );

        }
        else{

            var errorMsg = errors.join('\n');
            alert(errorMsg);

        }

    }
    else{
        $('#login-required-popup').lightbox_me({
            centered: true,
            closeSelector: '#close-login-required-popup'
        });
    }

}

function processSendMessageEvent2(user_to, message){

    if(USER_LOGGED){

        //var errors = new Array();
        message = $.trim(message);

        /*if(message == ""){
            errors.push(FIELD_IS_REQUIRED.replace("{field}", MESSAGE));
        }*/

        if(message != ""){

            $('#message-popup-send-button').addClass('disabled');

            $.post(
                send_message2_url,
                {
                    user_to: user_to,
                    message: message
                },
                function(data) {

                    if(data.success){

                        $('#message-send-popup-container').trigger('close');
                        $('#message-sent-popup-container').lightbox_me({
                            centered: true,
                            closeSelector: '.message-popup-cancel'
                        });

                    }

                }
                , "json"
            ).done(function(){
                $('#message-popup-send-button').removeClass('disabled');
            }).fail(function(){
                $('#message-popup-send-button').removeClass('disabled');
            });

        }
        else{
            $('#message-popup-msg').addClass('write-invalid');
            $('#message-popup-msg').val('');
            $('#message-popup-msg').focus();
            //var errorMsg = errors.join('\n');
            //alert(errorMsg);

        }

    }
    else{
        $('#login-required-popup').lightbox_me({
            centered: true,
            closeSelector: '#close-login-required-popup'
        });
    }

}

function hideAllTopBarSliders(){

    $('#footer-container').slideUp('fast');
    footer_container_showed = false;

    if(slider_selected != "login"){
        $('#login-container').slideUp('fast');
        login_container_showed = false;
    }

    //$('#notifications-container').slideUp('fast');
    $('#notifications-container').hide();
    $('#notifications-containera').removeClass('notifications-showed');
    //notifications_container_showed = false;

}

function hideAllSelects(){
    $('.select-arrow').removeClass('arrow-up');
    $('.select-arrow').addClass('arrow-down');
    $('.select-items').slideUp(300);
    $('.select-current').removeClass('open');
    $('.select-current').addClass('closed');
}

function calculateFlashMsgLeft(){

    var flash_msg_width = $('#flash-msg').outerWidth();
    var screen_width = $(window).width();
    var flash_msg_left = Math.ceil((screen_width-flash_msg_width)/2);
    $('#flash-msg').css('left', flash_msg_left+'px');

}

$(document).ready(function(){

    setMainHeight();

    if($("#public-profile-username").length > 0){
        $("#public-profile-username").dotdotdot({
            wrap: 'letter',
            watch: true
        });
    }

    if($(".public-profile-info-item").length > 0){
        $(".public-profile-info-item").dotdotdot({
            wrap: 'letter',
            watch: true
        });
    }

    // setTextAuthorDivWidth();

    calculateFlashMsgLeft();

    $('body').click(function(){
        hideAllSelects();
        $('#flash-msg').fadeOut('slow');
    });

    $('.select-current').click(function(e){

        e.stopPropagation();

        if($(this).children('.select-arrow').hasClass('arrow-down')){

            hideAllSelects();

            $(this).children('.select-arrow').removeClass('arrow-down');
            $(this).children('.select-arrow').addClass('arrow-up');
            $(this).children('.select-items').slideDown(300);
            $(this).removeClass('closed');
            $(this).addClass('open');
        }
        else{
            $(this).children('.select-arrow').removeClass('arrow-up');
            $(this).children('.select-arrow').addClass('arrow-down');
            $(this).children('.select-items').slideUp(300);
            $(this).removeClass('open');
            $(this).addClass('closed');
        }
    });

    $('.select-item').click(function(e){

        e.stopPropagation();

        var value = $(this).data('value');
        var text = $(this).text();

        var select_current = $(this).parents('.select-current');
        var select_text = $(select_current).children('.select-text');
        $(select_text).text(text);
        $(select_current).children('input[type="hidden"]').val(value);
        $(select_current).children('.select-arrow').removeClass('arrow-up');
        $(select_current).children('.select-arrow').addClass('arrow-down');
        $(select_current).children('.select-items').slideUp(300);

    });

    $('.edit-profile-cancel').click(function(){
        window.location = profile_url;
    });


    $('#write-link').click(function(event){

        if(!USER_LOGGED){

            event.preventDefault();
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

    $('#preview-button').click(function(){

        var category_color = $("#category-select-current").data('categoryColorCode');
        $('#preview-popup-category-color').css('background', '#'+category_color);
        $('#preview-popup-text-category').css('color', '#'+category_color);

        $('#preview-popup-text-category').html($("#category-select-current").data('categoryTitle'));
        $('#preview-popup-text-title').html($('#jform_title').val());
        $('#preview-popup-text-author').html(BY+" "+NAME);
        //$('#preview-popup-text-content').html(tinyMCE.activeEditor.getContent());
        $('#preview-popup-text-content').html($('#text-editor').val());
        var tags_string = "";
        var tags = new Array();
        $('.tag-name').each(function() {
            tags.push($(this).text());
        });
        tags_string += tags.join(', ');
        $('#preview-popup-text-tags').html(tags_string);

        $("#preview-popup").lightbox_me({
                closeSelector: "#back-to-edit-button",
                /*centered: true,*/
                lightboxSpeed: 'slow',
                onLoad: function(){
                    //alert($('#preview-popup').height());
                    //$('#contentarea').height(($(this).height()+100)+'px');
                },
                onClose: function(){
                    //alert('onclose')
                }
            });

    });

    /*$('#login-link-container').click(function(event){

        if(login_container_showed){
            $('#login-container').slideUp('slow');
            login_container_showed = false;
        }
        else{
            hideAllTopBarSliders();
            $('#login-container').slideDown('slow');
            login_container_showed = true;
        }
        event.stopPropagation();
        event.preventDefault();
    });*/

    $('#login-link-container').click(function(){
        if(USER_LOGGED){
            window.location = my_profile_url;
        }
    });

    $('#profile-slider-logout').click(function(event){
        event.preventDefault();
        $('#logout-submit-button').click();
    });

    $('#login-link-wrapper').hover(
        function(){
            clearTimeout(timeOutOne);
            slider_selected = "login";

			if (errorlogin==false){
				hideAllTopBarSliders();
			}
            //$('#login-container').slideDown('slow');
			$("#login-container").show();
        },
        function(){
            //$('#login-container').slideUp('slow');

            if(!($('#modlgn-username').is( ":focus" ) || $('#modlgn-passwd').is( ":focus" ))){

                clearTimeout(timeOutOne);

                timeOutOne = setTimeout(function(){
                        $("#login-container").hide();
                        slider_selected = "";
                    }
                    ,1000
                );

            }

        }
    );

    $("body").delegate("#modlgn-username, #modlgn-passwd", "blur", function( event ) {

        if(!($('#modlgn-username').is(":focus") || $('#modlgn-passwd').is(":focus") || ($("#login-link-wrapper:hover").length > 0))){

            clearTimeout(timeOutOne);

            timeOutOne = setTimeout(function(){
                    $("#login-container").hide();
                    slider_selected = "";
                }
                ,1000
            );

        }

    });

    $('#modlgn-passwd').focus(function(){
        clearTimeout(timeOutOne);
        slider_selected = "login";

        if (errorlogin==false){
            hideAllTopBarSliders();
        }

        $("#login-container").show();
    });

	
	 $('#forgot_password_buttton').click(function(event){
		if(!USER_LOGGED){
		  hideAllTopBarSliders();
            $('#login-container').slideDown('slow');
            login_container_showed = true;
			$('#forgot-password').lightbox_me({
					centered: true,
					closeSelector: '#close-forgot-pass-required-popup'
				});
			}
	});

	$('#forgot_username_buttton').click(function(event){
		if(!USER_LOGGED){
		  hideAllTopBarSliders();
            $('#login-container').slideDown('slow');
            login_container_showed = true;
			$('#forgot-username').lightbox_me({
					centered: true,
					closeSelector: '#close-forgot-username-required-popup'
				});
			}
	});

	$('#registersite_link').click(function(event){
		if(!USER_LOGGED){
			hideAllTopBarSliders();
            login_container_showed = true;
			document.getElementById("message_not_logged_in_action").style.display="none";
			$('#register_form').lightbox_me({
					centered: true,
					closeSelector: '#close-register_form-required-popup',
                    onClose: function(){
                        $('.register-form-error').hide();
                    }
				});
			}
	});
	
	
	
	
    $('body').not('#login-container').click(function(event){
        event.stopPropagation();

        var $target = $(event.target);
        if(!($target.is("#login-container")) && !($target.is("#login-container *"))){

            if(login_container_showed){
                $('#login-container').slideUp('slow');
                login_container_showed = false;
            }

        }


    });


    $('#footer-link').click(function(event){

        if(footer_container_showed){
            $('#footer-container').slideUp('slow');
            footer_container_showed = false;
        }
        else{
            hideAllTopBarSliders();
            $('#footer-container').slideDown('slow');
            footer_container_showed = true;
        }
        event.stopPropagation();
        event.preventDefault();
    });

    $('body').not('#footer-container').click(function(event){
        event.stopPropagation();

        var $target = $(event.target);
        if(!($target.is("#footer-container")) && !($target.is("#footer-container *"))){

            if(footer_container_showed){
                $('#footer-container').slideUp('slow');
                footer_container_showed = false;
            }

        }


    });

    $('#filters').click(function(){
        if(combo_hidden){
            $('#combos').show('slow');
            $('#filter-arrow').addClass('filter-left-arrow');
            $('#filter-arrow').removeClass('filter-right-arrow');
        }
        else{
            $('#combos').hide('slow');
            $('#filter-arrow').addClass('filter-right-arrow');
            $('#filter-arrow').removeClass('filter-left-arrow');
        }
        combo_hidden = !combo_hidden;
    });

    $('.combo').change(function(){

        $("#text-filters-form").submit();

    });

    $('#magnifying-glass').click(function(){

        $("#text-filters-form").submit();

    });

    $('#text-search-input').focus(function(){
        if($(this).val() == SEARCH){
            $(this).val('');
        }
    });

    $('#text-search-input').blur(function(){
        if($(this).val() == ''){
            $(this).val(SEARCH);
        }
    });

    $('.send-message-clickable').click(function(){

        if(USER_LOGGED){

            $('#message-send-popup-container').lightbox_me({
                centered: true,
                closeSelector: '.message-popup-cancel',
                onClose: function(){
                    $('#message-popup-msg').removeClass('write-invalid');
                    $('#message-popup-msg').val('');
                },
                onLoad:	function(){
                    $('#message-popup-msg').focus();
                }
            });

        }else{
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

    // $("body").delegate('.applauses-clickable', 'click', function(){

    //     var applauses_clickable = $(this);

    //     if($(applauses_clickable).hasClass('disabled')){
    //         return false;
    //     }

    //     $(applauses_clickable).addClass('disabled');

    //     var text_id = $(this).data('textId');
    //     var text_title = $(this).data('textTitle');

    //     $.post(
    //         get_users_who_applauded_text_url,
    //         {
    //             text_id: text_id,
    //             offset: 0,
    //             limit: 16
    //         },
    //         function(data) {

    //             if(data.clapCount > 0){

    //                 if(data.clapCount > 1){
    //                     var clap = 'aplausos';
    //                 }
    //                 else{
    //                     var clap = 'aplauso';
    //                 }

    //                 var user_box_title = '<span class="bold italic">"'+text_title+'"</span> ha recibido <span class="bold">'+data.clapCount+' '+clap+'.</span>';
    //                 $('#user-box-title').html(user_box_title);
    //                 $('#user-box-title').attr('class', 'claps-users-title');
    //                 $('#user-list').html(data.usersHtml);

    //                 generate_user_box_pagination('applauses', text_id, data.clapCount);

    //                 $('#user-box').lightbox_me({
    //                     centered: true,
    //                     closeSelector: '#user-box-close-icon',
    //                     onLoad: function(){
    //                         $('.user-box-name').dotdotdot({
    //                             wrap: 'letter'
    //                         });

    //                         $(applauses_clickable).removeClass('disabled');

    //                         var user_list_height = $('#user-list').height();
    //                         $('#user-list').height(user_list_height);
    //                     }
    //                 });

    //             }

    //         }
    //         , "json"
    //     );

    // });

    $("body").delegate('.user-applauses-page', 'click', function(){

        var user_box_pagination_page = $(this);

        if($(user_box_pagination_page).hasClass('selected')){
            return false;
        }

        $('.user-box-pagination-page').removeClass('selected');
        $(user_box_pagination_page).addClass('selected');

        var itemId = $(this).data('itemId');
        var pageNumber = $(this).data('pageNumber');
        var offset = pageNumber * 16;

        $('#user-list').css('opacity', 0.6);

        $.post(
            get_users_who_applauded_text_page_url,
            {
                text_id: itemId,
                offset: offset,
                limit: 16
            },
            function(data) {

                $('#user-list').html(data.usersHtml);
                $('#user-list').css('opacity', 1);

                $('.user-box-name').dotdotdot({
                    wrap: 'letter'
                });

            }
            , "json"
        );

    });

    $("body").delegate('#message-popup-send-button', 'click', function(){

        if($(this).hasClass('disabled')){
            return false;
        }

        var message = $('#message-popup-msg').val();
        processSendMessageEvent2(writer_id, message);

    });

    $("body").delegate('#followers', 'click', function(){

        var followers = $(this);

        if($(followers).hasClass('disabled')){
            return false;
        }

        $(followers).addClass('disabled');

        $.post(
            get_users_who_follow_me_url,
            {
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

                    var user_box_title = 'Te '+are+' siguiendo <span class="bold">'+data.countUsers+' '+user+'.</span>';
                    $('#user-box-title').html(user_box_title);
                    $('#user-box-title').attr('class', 'followed-users-title');
                    $('#user-list').html(data.usersHtml);

                    generate_user_box_pagination('followers', USER_ID, data.countUsers);

                    $('#user-box').lightbox_me({
                        centered: true,
                        closeSelector: '#user-box-close-icon',
                        onLoad: function(){
                            $('.user-box-name').dotdotdot({
                                wrap: 'letter'
                            });

                            $(followers).removeClass('disabled');

                            var user_list_height = $('#user-list').height();
                            $('#user-list').height(user_list_height);

                        }
                    });

                }

            }
            , "json"
        );

    });

    $("body").delegate('.user-followers-page', 'click', function(){

        var user_box_pagination_page = $(this);

        if($(user_box_pagination_page).hasClass('selected')){
            return false;
        }

        $('.user-box-pagination-page').removeClass('selected');
        $(user_box_pagination_page).addClass('selected');

        var pageNumber = $(this).data('pageNumber');
        var user_id = $(this).data('itemId');
        var offset = pageNumber * 16;

        $('#user-list').css('opacity', 0.6);

        $.post(
            get_users_who_follow_me_page_url,
            {
                user_id: user_id,
                offset: offset,
                limit: 16
            },
            function(data) {

                $('#user-list').html(data.usersHtml);
                $('#user-list').css('opacity', 1);

                $('.user-box-name').dotdotdot({
                    wrap: 'letter'
                });

            }
            , "json"
        );

    });
	
	
	$("body").delegate('#reestablish-password-button', 'click', function(e){
		if($(this).hasClass('disabled')){
		   return false;
		}
		$(this).addClass('disabled');		
	});
	

    $("body").delegate('#followeds', 'click', function(){

        var followeds = $(this);

        if($(followeds).hasClass('disabled')){
            return false;
        }

        $(followeds).addClass('disabled');

        $.post(
            get_users_who_i_am_following_url,
            {
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

                    var user_box_title = 'Estás siguiendo a <span class="bold">'+data.countUsers+' '+user+'.</span>';
                    $('#user-box-title').html(user_box_title);
                    $('#user-box-title').attr('class', 'following-users-title');
                    $('#user-list').html(data.usersHtml);

                    generate_user_box_pagination('followeds', USER_ID, data.countUsers);

                    $('#user-box').lightbox_me({
                        centered: true,
                        closeSelector: '#user-box-close-icon',
                        onLoad: function(){
                            $('.user-box-name').dotdotdot({
                                wrap: 'letter'
                            });

                            $(followeds).removeClass('disabled');
                            var user_list_height = $('#user-list').height();
                            $('#user-list').height(user_list_height);

                        }
                    });

                }

            }
            , "json"
        );

    });


    $("body").delegate('.user-followeds-page', 'click', function(){

        var user_box_pagination_page = $(this);

        if($(user_box_pagination_page).hasClass('selected')){
            return false;
        }

        $('.user-box-pagination-page').removeClass('selected');
        $(user_box_pagination_page).addClass('selected');

        var pageNumber = $(this).data('pageNumber');
        var user_id = $(this).data('itemId');
        var offset = pageNumber * 16;

        $('#user-list').css('opacity', 0.6);

        $.post(
            get_users_who_i_am_following_page_url,
            {
                user_id: user_id,
                offset: offset,
                limit: 16
            },
            function(data) {

                $('#user-list').html(data.usersHtml);
                $('#user-list').css('opacity', 1);

                $('.user-box-name').dotdotdot({
                    wrap: 'letter'
                });

            }
            , "json"
        );

    });


});






/*CALLBACK MESSAGES*/
function wrong_registration(texto,langg){		
	oXML = AJAXCrearObjeto();		
	var lang="en";
	if (langg){
		lang=langg;
	}
	oXML.open('GET', IROUTE+'/index.php?lang='+lang+'&option=com_users&controller=Ajax&no_html=1&task=wrong_registration');
	oXML.onreadystatechange = abt;
	oXML.send(' ');	
}
function success_registration(texto,langg){		
	oXML = AJAXCrearObjeto();		
	var lang="en";
	if (langg){
		lang=langg;
	}
    //oXML.open('GET', IROUTE+'/index.php?lang='+lang+'&option=com_users&controller=Ajax&no_html=1&task=success_registration');
	oXML.open('GET', base_url+'/index.php?lang='+lang+'&option=com_users&controller=Ajax&no_html=1&task=success_registration');
	oXML.onreadystatechange = abt;
	oXML.send(' ');	
}
function success_registration_pre(texto,langg){		
	oXML = AJAXCrearObjeto();		
	var lang="en";
	if (langg){
		lang=langg;
	}
	//oXML.open('GET', IROUTE+'/index.php?lang='+lang+'&option=com_users&controller=Ajax&no_html=1&task=success_registration_pre');
	oXML.open('GET', base_url+'/index.php?lang='+lang+'&option=com_users&controller=Ajax&no_html=1&task=success_registration_pre');
	oXML.onreadystatechange = abt;
	oXML.send(' ');	
}

function abt(){
	if (oXML.readyState  == 4) {
		var result = oXML.responseText;
			document.getElementById('ajax_messages').innerHTML = result;
			
			$('#ajax_messages').lightbox_me({
				centered: true,
				closeSelector: '#close-ajax_messages-required-popup'
			});		
		
	}
}

function forgot_confirm(texto,langg){		
	oXML = AJAXCrearObjeto();		
	var lang="en";
	if (langg){
		lang=langg;
	}
	oXML.open('GET', IROUTE+'/index.php?lang='+lang+'&option=com_users&controller=Ajax&no_html=1&task=forgot_confirm');
	oXML.onreadystatechange = abt;
	oXML.send(' ');	
}
function forgot_complete(texto,langg){		
	oXML = AJAXCrearObjeto();		
	var lang="en";
	if (langg){
		lang=langg;
	}
	oXML.open('GET', IROUTE+'/index.php?lang='+lang+'&option=com_users&controller=Ajax&no_html=1&task=forgot_complete');
	oXML.onreadystatechange = abt;
	oXML.send(' ');	
}
function forgot_success(texto,langg){		
	oXML = AJAXCrearObjeto();		
	var lang="en";
	if (langg){
		lang=langg;
	}
	oXML.open('GET', IROUTE+'/index.php?lang='+lang+'&option=com_users&controller=Ajax&no_html=1&task=forgot_success');
	oXML.onreadystatechange = abt;
	oXML.send(' ');	
}




function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function removeTags(text){

    var txt = text.replace(/(<([^>]+)>)/ig,"");//Remueve los tags
    //txt = txt.replace(/ /g,\"\");//Remueve los espacios
    txt = txt.replace(/\n/gi,"");//Remueve los saltos de linea
    txt = $("<div/>").html(txt).text();

    return txt;
}

var Tools = {
    createCookie: function(name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else{
            var expires = "";
        }
        document.cookie = name+"="+value+expires+"; path=/";
    },

    readCookie: function(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    },

    eraseCookie: function(name) {
        Tools.createCookie(name,"",-1);
    }
};

/*REA CALLBACK MESSAGES*/

if (!String.prototype.replaceLast) {
    String.prototype.replaceLast = function(find, replace) {
        var index = this.lastIndexOf(find);

        if (index >= 0) {
            return this.substring(0, index) + replace + this.substring(index + find.length);
        }

        return this.toString();
    };
}

/**

	logica de notificaciones

	-lo que se muestra en el UI son notificationGroups.
	-group by notificationType, articleId
	-cuando tenemos 1 nueva notificacion, se busca entre los nofiticationGroups a ver si ya existe uno al cual agregarlo
	--si existe, [el grupo no es demasiado viejo, el grupo no contiene demasiadas notificaciones], 
			lo agregamos, y subimos ese notification group al principio de la lista
	--sino, lo creamos, y le agregamos nuestra notificacion.
	-notificationGroup.updateMessage();
	

**/

$(function(){
    var hash = window.location.hash.substring(1);
    var hashes = hash.split("&");
    for(var i = 0; i < hashes.length; i++) {
        hashes[i] = hashes[i].split("=");

        if(hashes[i][0] == "comment") {
            window.scrollTo(0, $("a[name='comment-" + hashes[i][1] + "']").position().top);
            
        }
    }
});

var notificationGroups = [];
var notificationMessages = {
	1: {
		message: "<b class='username'>{$user.name}</b> te está siguiendo",
		messagePlural: "<b class='username'>{$user.name}</b> te están siguiendo",
		url: "index.php?option=com_contact&view=public&id={$user.id}"
	},
	3: {
		message: "<b class='username'>{$user.name}</b> te aplaudió en <b>{$article.title}</b>",
		messagePlural: "<b class='username'>{$user.name}</b> te aplaudieron en <b>{$article.title}</b>",
		url: "index.php?option=com_contact&view=public&id={$user.id}"
	},
	5: {
		message: "<b class='username'>{$user.name}</b> comentó <b>{$article.title}</b>",
		messagePlural: "<b class='username'>{$user.name}</b> comentaron <b>{$article.title}</b>",
		url: "index.php?option=com_content&view=article&id={$article.id}#comment={$comment.id}"
	},
	8: {
		message: "<b class='username'>{$user.name}</b> también comentó <b>{$article.title}</b>",
		messagePlural: "<b class='username'>{$user.name}</b> también comentaron <b>{$article.title}</b>",
		url: "index.php?option=com_content&view=article&id={$article.id}#comment={$comment.id}"
	},
	9: {
		message: "<b class='username'>{$user.name}</b> votó tu comentario en <b>{$article.title}</b>",
		messagePlural: "<b class='username'>{$user.name}</b> votaron tu comentario en <b>{$article.title}</b>",
		url: "index.php?option=com_content&view=article&id={$article.id}#comment={$comment.id}"
	},
	10: {
		message: "<b class='username'>{$user.name}</b> te etiquetó en <b>{$article.title}</b>",
		messagePlural: "<b class='username'>{$user.name}</b> te etiquetaron en <b>{$article.title}</b>",
		url: "index.php?option=com_content&view=article&id={$article.id}#comment={$comment.id}"
	},
};

function hookNotifications() {
	for(var i = recentUnseenNotifications.length - 1; i >= 0 ; i--) {
		var notification = recentUnseenNotifications[i];
		// TODO: remove publication notifications from core
		if (notification.NotificationType == 7)
			continue;
		addToNotificationGroups(notification);
	}
	
	for (var i = 0; i < notificationGroups.length; i++) 
		notificationGroups[i].hasNewNotifications = false;
		
	renderNotificationGroups();
	setTimeout(getNotifications, 1500);
}

function getNotifications(){
	
	$.get(base_url + "/index.php?option=com_users&task=get_new_notifications", {"lastNotificationId": lastNotificationId}).done(function(result){
		var notifications = JSON.parse(result);
		
		if (notifications != null && notifications.length > 0) {
		
			lastNotificationId = notifications[0].NotificationId;
            
            for(var i = 0; i < notifications.length; i++) {
                var notification = notifications[i];
                addToNotificationGroups(notification);
            }

			no_read_notifications_count = getUnreadNotificationCount();
            
            $("#notifications-red-box")
                .text(no_read_notifications_count)
                .show()
                .effect("shake", {direction: "down", distance: 4});
			
			renderNotificationGroups();
		
		}
		
		setTimeout(getNotifications, 1500);
	});
};

function getUnreadNotificationCount() {
    var count = 0;
    for(var i = 0; i < notificationGroups.length; i++) {
        if (notificationGroups[i].hasNewNotifications)
            count++;
    }
    return count;
}

function addToNotificationGroups(notification) {
	var notificationGroup = null;
    var found = false;

	for (var i = 0; i < notificationGroups.length; i++) {
		notificationGroup = notificationGroups[i];

        if (notificationGroup.groupType == notification.NotificationType) {
            if (notificationGroup.notifications.length > 0 && 
                notificationGroup.notifications[0].ArticleId != notification.ArticleId)
                continue;

            if (!notificationGroup.hasNewNotifications)
                continue;

			notificationGroup.notifications.push(notification); 
			notificationGroup.message = getNotificationGroupMessage(notificationGroup);

            found = true;
			break;
		}
	}
	
	if (!found) {
		notificationGroup = {
			groupId: notification.NotificationId,
			groupType: notification.NotificationType,
			notifications: [notification],
			message: getNotificationMessage(notification)
		};
	} else {
		notificationGroups = _.without(notificationGroups, notificationGroup);
	}
	
	notificationGroup.hasNewNotifications = true;
	
	notificationGroups.push(notificationGroup);
	
}

function getNotificationMessage(notification) {	
	var notificationMessage = notificationMessages[notification.NotificationType];
		
	var message = notificationMessage.message;
	message = message.replace("{$user.name}", notification.UserName);
	message = message.replace("{$article.title}", notification.ArticleTitle);
	var url = notificationMessage.url;
	url = url.replace("{$user.id}", notification.UserId);
	url = url.replace("{$article.id}", notification.ArticleId);
	url = url.replace("{$comment.id}", notification.CommentId);
	
	return {message: message, url: url};
	
}

function getNotificationGroupMessage(notificationGroup) {
	if (notificationGroup.notifications.length < 1)
		return;
	
	var notificationMessage = notificationMessages[notificationGroup.groupType];
			
	var notificationNames = _.map(notificationGroup.notifications, function(element){
		return {UserName: element.UserName, NotificationId: element.NotificationId};
	});
	var groupedNotificationNames = _.groupBy(notificationNames, "UserName");
	groupedNotificationNames = _.map(groupedNotificationNames, function(element) {
		return _.max(element,  function(el){return parseInt(el.NotificationId)}).UserName;
	});
	
    var notification = notificationGroup.notifications[0];

    for (var i = 0; i < notificationGroup.notifications.length; i++) {
        if (notification.NotificationId < notificationGroup.notifications[i].NotificationId)
            notification = notificationGroup.notifications[i];
    }

	if (groupedNotificationNames.length == 1) {
		var message = notificationMessage.message;
		var names = groupedNotificationNames[0];
	} else {
		var message = notificationMessage.messagePlural;
		groupedNotificationNames = groupedNotificationNames.reverse();
		var names = groupedNotificationNames.join(", ").replaceLast(",", " y");
	}
	
	message = message.replace("{$user.name}", names);
	message = message.replace("{$article.title}", notification.ArticleTitle);
	var url = notificationMessage.url;
	url = url.replace("{$user.id}", notification.UserId);
	url = url.replace("{$article.id}", notification.ArticleId);
	url = url.replace("{$comment.id}", notification.CommentId);
		
	return {message: message, url: url};
	
}

function renderNotificationGroups() {
	$("#notifications-container .mCSB_container").html("");
	for(var i = 0; i < notificationGroups.length; i++) {
		var notificationMessage = notificationGroups[i].message;
		var html = "<a href='" + notificationMessage.url + "'>" + notificationMessage.message + "</a>";
		
		if (notificationGroups[i].hasNewNotifications)
			var backgroundKey = ".3";
		else 
			var backgroundKey = "0";
		
		html = "<hr/><div class='notifications-item menu-slider-item' style='background-color: rgba(0, 0, 0, " + backgroundKey + ");' >" + html + "</div>";
		$("#notifications-container .mCSB_container").prepend(html);
	}
	
}

$(document).ready(function(){

	$('#notifications-icon').click(function(event){	
		
        if($('#notifications-containera').is(".notifications-showed")){
            $('#notifications-container').hide();
            $('#notifications-containera').removeClass('notifications-showed');
            
        } else{
			$.post(
				set_saw_all_notifications, {},
				function(data) {
					if(data.success){
						amount_notifications = 0;
						no_read_notifications_count = 0;
                        for (var i = 0; i < notificationGroups.length; i++) 
                            notificationGroups[i].hasNewNotifications = false;
					}
				}
				, "json"
			);
           
            amount_notifications = 0;
			no_read_notifications_count = 0;
            hideAllTopBarSliders();
            $('#notifications-red-box').html(amount_notifications);
            $('#notifications-red-box').fadeOut('slow');
            $('#notifications-container').show();
            $('#notifications-containera').addClass('notifications-showed');

        }
        event.stopPropagation();
        event.preventDefault();
    });

	$('#notifications-icon').mouseover(function(event){
		$("#login-container").hide();
	});
	
	$('#write-text-2').mouseover(function(event){
		$("#login-container").hide();
	});
	
    $('body').not('#notifications-container').click(function(event){
        event.stopPropagation();

        var $target = $(event.target);
        if(!($target.is("#notifications-container")) && !($target.is("#notifications-container *"))){

            //if(notifications_container_showed){
			if($('#notifications-containera').is(".notifications-showed")){
                $('#notifications-container').hide();
                $('#notifications-containera').removeClass('notifications-showed');
                //notifications_container_showed = false;
            }

        }
    });
	
	$("#notifications-container").mCustomScrollbar({
		scrollButtons:{
			enable:true
		}
	});
	notifications_container_showed_once = true;
});