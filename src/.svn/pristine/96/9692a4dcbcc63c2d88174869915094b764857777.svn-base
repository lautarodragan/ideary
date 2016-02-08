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
$params	= $this->item->params;
$images = json_decode($this->item->images);
$urls   = json_decode($this->item->urls);
$user	= JFactory::getUser();

ideary::logUserExperience(UserExperience::Ranking, $user->id, 1);

?>

<script type="text/javascript">

    var userId = '<?php echo $this->user->get('id') ?>';
    var offset = <?php echo $this->offset ?>;
    var period = '<?php echo $this->period ?>';
    var can_process_scroll_forward = true;
    var count_all_texts = <?php echo $this->countTextsRanking ?>;
    var more_texts_bar_showed = false;
    var can_scroll_foward = true;
    var let_scroll_foward = true;

    function resize(){
        var topbar_height = $('#topbar').outerHeight(true);
        var window_height = $(window).height();
        var remaining_height = window_height - topbar_height;

        var contentarea_width = $(window).width() + 17;

        $('#contentarea').css('width', contentarea_width+'px');
        $('#h-scroll-ranking').css('width', contentarea_width+'px');
        $('#wrapper2').css('padding-bottom', 0+'px');
        $('#wrapper2').css('height', remaining_height+'px');
    }

    function recalculateTextRankingContentHeight(){

        $('.text-ranking[data-just-added="true"]').each(function(){

            var text_ranking_height = $(this).height();
            var text_ranking_title_height = $(this).children('.text-ranking-title').outerHeight(true);
            var text_ranking_img_height = $(this).children('.text-ranking-img').outerHeight(true);
            text_ranking_img_height = (text_ranking_img_height != null)? text_ranking_img_height : 0;
            var author_applauses_height = $(this).children('.author-applauses').outerHeight(true);
            var author_applauses_margin_top_height = 25;

            var text_ranking_content_height = text_ranking_height - (text_ranking_title_height+text_ranking_img_height+author_applauses_height+author_applauses_margin_top_height);

            $(this).children('.text-ranking-content').css('height', text_ranking_content_height+'px');

            /*var text_url = $(this).children('.text-ranking-title').children('a').attr('href');

            $(this).children('.text-ranking-content').dotdotdot({
                after: '<a href="'+text_url+'" style="background: none;"><div class="read-more" title="'+READ_MORE+'"></div></a>',
                watch: true,
                wrap: 'letter'
            });*/

            $(this).find('.r-t-title').dotdotdot({
                watch: true,
                wrap: 'letter'
            });

            $(this).children('.text-ranking-content').dotdotdot({
                watch: true,
                wrap: 'letter',
                ellipsis: ''
            });

            var text_ranking_author = $(this).find('.text-ranking-author');
            var author_applauses = $(text_ranking_author).parents('.author-applauses');
            var author_applauses_width = $(author_applauses).width();

            var applauses_width = $(author_applauses).find('.text-ranking-applauses').width();
            var separacion = 20;
            var text_ranking_author_width = author_applauses_width - (applauses_width+separacion);

            $(text_ranking_author).css('width', text_ranking_author_width+'px');


        });

        setTimeout(function(){
            $('.text-ranking[data-just-added="true"] .r-t-title').css('height', '+=1');
            setTimeout(function(){
                $('.text-ranking[data-just-added="true"] .r-t-title').css('height', '-=1');
                $('.text-ranking').attr('data-just-added', 'false');
            }, 100);
        }, 1000);

    }

    function resizeMoreTextsBar(){

        $('#more-texts-ranking-bar').css('height', $('#h-scroll-ranking').height());
        var more_texts_bar_height = $('#more-texts-ranking-bar').height();
        var more_texts_bar_center_height = $('#more-texts-ranking-bar-center').height();
        var more_texts_bar_center_margin = Math.floor((more_texts_bar_height - more_texts_bar_center_height)/2);
        $('#more-texts-ranking-bar-center').css('margin-top', more_texts_bar_center_margin+'px');

    }

    function appendMoreTexts(texts){

        if(texts.length > 0){

            var ul_ranking = $('#ul-ranking');

            for (i = 0; i < texts.length; i++){

                var text = $(texts[i]);

                $(ul_ranking).css('width', '+=320px');
                $(ul_ranking).append($(text));

            }

            can_scroll_foward = true;
            let_scroll_foward = true;
        }
    }

    function getMoreTexts(){

        can_process_scroll_forward = false;
        can_scroll_foward = false;

        $.post(
            get_more_texts_ranking_url,
            {
                period: period,
                userId: userId,
                offset: offset
            },
            function(data) {

                if(data.texts_array_html.length > 0){

                    $('#more-texts-ranking-bar').hide();
                    more_texts_bar_showed = false;

                    appendMoreTexts(data.texts_array_html);
                    recalculateTextRankingContentHeight();
                    offset += data.texts_array_html.length;

                }

            }
            , "json"
        ).done(function(){
                can_process_scroll_forward = true;
                $('#more-texts-ranking-bar').removeClass('disabled');
                //resizeMoreTextsBar();
            }).fail(function(){
                can_process_scroll_forward = true;
                $('#more-texts-ranking-bar').removeClass('disabled');
                //resizeMoreTextsBar();
            });

    }


    function processScrollForward(){

        if(let_scroll_foward){

            var max_ul_last_text = $('#h-scroll-ranking .text-ranking:last');

            var max_ul_right_position = $(max_ul_last_text).offset().left + $(max_ul_last_text).outerWidth();
            var screen_width = $(window).width();
            var remaining_width = max_ul_right_position - screen_width;

            scrollLeft = $('#h-scroll-ranking').scrollLeft();
            more_texts_bar_width = 150;

            if(remaining_width < 700){
                scrollLeft += remaining_width + more_texts_bar_width;
                let_scroll_foward = false;
            }
            else{
                scrollLeft += 700;
            }

            $('#h-scroll-ranking').stop().animate({scrollLeft: scrollLeft}, 500, function(){

                var last_text_right_position = $(max_ul_last_text).offset().left + $(max_ul_last_text).outerWidth();
                var screen_width = $(window).width();
                if(((last_text_right_position - screen_width) < 0) && can_process_scroll_forward && (count_all_texts > offset)){

                    $('#more-texts-ranking-bar').fadeIn('slow');
                    more_texts_bar_showed = true;

                }

            });

        }
    }

    function processScroll(delta){

        if(!can_scroll_foward){
            return false;
        }

        var val = $('#h-scroll-ranking').scrollLeft() - (delta * 700);

        if(delta == 1){
            if(more_texts_bar_showed){
                $('#more-texts-ranking-bar').hide();
                more_texts_bar_showed = false;
            }
            let_scroll_foward = true;
            $('#h-scroll-ranking').stop().animate({scrollLeft:val}, 500);
        }
        else{
            if(more_texts_bar_showed){
                $('#more-texts-ranking-bar').addClass('disabled');
                //resizeMoreTextsBar();
                getMoreTexts();
            }
            else{
                processScrollForward();
            }
        }
    }

    $(document).ready(function(){

        resize();

        $(window).resize(resize);

		var baseurl = "<?php echo JURI::base();?>";
        recalculateTextRankingContentHeight();

        var more_texts_bar = '<div id="more-texts-ranking-bar">' +
            '<div id="more-texts-ranking-bar-center"></div>' +
            '</div>';

        $('#wrapper2').append(more_texts_bar);

        resizeMoreTextsBar();

        $("body").delegate('#more-texts-ranking-bar', 'click', function(){

            if($(this).hasClass('disabled')){
                return false;
            }

            $(this).addClass('disabled');

            getMoreTexts();

        });

        $("body").delegate('.more-applauded-texts-top-buttons', 'click', function(){

                if($(this).hasClass('disabled')){
                    return false;
                }

                $('.more-applauded-texts-top-buttons').addClass('disabled');
                $('#more-applauded-texts-ajax-loading').show();
                var periodo = $(this).data('period');
                $.post(
                    get_texts_ranking_url,
                    { period: periodo },
                    function(data) {
                        $('#ul-ranking').css('width', ((data.text_count*320)+150)+"px");
                        $('#ul-ranking').html(data.text_html);

                        $('#h-scroll-ranking').scrollLeft(0);
                        $('#more-texts-ranking-bar').hide();
                        more_texts_bar_showed = false;
                        let_scroll_foward = true;

                        $('.more-applauded-texts-top-buttons').removeClass('selected');
                        $('.more-applauded-texts-top-buttons[data-period="'+periodo+'"]').addClass('selected');
                        offset = data.text_count;
                        period = periodo;
                        count_all_texts = data.countAllTexts;
                        recalculateTextRankingContentHeight();

                    }
                    , "json"
                ).done(function() {
                        $('#more-applauded-texts-ajax-loading').hide();
                        $('.more-applauded-texts-top-buttons').removeClass('disabled');
                });
        });

        $("body").delegate('.text-ranking-saved-icon', 'click', function(){

            if(USER_LOGGED){

                var text_id = $(this).data('textId');

                $.post(
                    unarchive_url,
                    { text_id: text_id },
                    function(data) {

                        if(data.success){
                            var html = '<div class="text-ranking-save-icon" title="'+SAVE+'" data-text-id="'+text_id+'"></div>';
                            $('.save-container[data-text-id="'+text_id+'"]').html(html);
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

        });

        $("body").delegate('.text-ranking-save-icon', 'click', function(){

            if(USER_LOGGED){

                var text_id = $(this).data('textId');

                $.post(
                    add_to_saved_url,
                    { text_id: text_id },
                    function(data) {

                        if(data.success){
                            var html = '<div class="text-ranking-saved-icon" title="'+UNARCHIVE+'" data-text-id="'+text_id+'"></div>';
                            $('.save-container[data-text-id="'+text_id+'"]').html(html);
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

        });

        $("body").delegate('#h-scroll-ranking', 'mousewheel', function(event, delta){

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

    });

</script>

<div id="more-applauded-texts-top">

    <div id="more-applauded-texts-top-title"><?php echo JText::_('MOST_ACCLAIMED_TEXTS') ?></div>

    <div id="more-applauded-texts-top-buttons-container">

        <div id="more-applauded-texts-ajax-loading"></div>
        <div class="more-applauded-texts-top-buttons write-text-form-button <?php echo ($this->period == "TODAY")? 'selected' : '' ?>" data-period="TODAY"><?php echo JText::_('TODAY') ?></div>
        <div class="more-applauded-texts-top-buttons write-text-form-button <?php echo ($this->period == "LAST-WEEK")? 'selected' : '' ?>" data-period="LAST-WEEK"><?php echo JText::_('LAST-WEEK') ?></div>
        <div class="more-applauded-texts-top-buttons write-text-form-button <?php echo ($this->period == "LAST-MONTH")? 'selected' : '' ?>" data-period="LAST-MONTH"><?php echo JText::_('LAST-MONTH') ?></div>

    </div>

    <div style="clear: both;"></div>

</div>

<?php if(count($this->texts) != 0): ?>

<div id="h-scroll-ranking">
    <ul id="ul-ranking" style="width: <?php echo ((count($this->texts)*320)+150)?>px">
        <?php $i=1 ?>

        <?php foreach($this->texts as $text): ?>

            <?php echo ideary::generateTextRankingHTML($text, $i); ?>
            <?php $i++ ?>
        <?php endforeach ?>
    </ul>
</div>

<?php else: ?>
    <div id="h-scroll-ranking">
        <ul id="ul-ranking">
        </ul>
    </div>
<?php endif ?>

