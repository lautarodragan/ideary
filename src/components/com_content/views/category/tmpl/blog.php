<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

?>

<script type="text/javascript">

    $(document).ready(function(){

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

        $('.h-scroll').bind('mousewheel', function(event, delta) {
            var val = this.scrollLeft - (delta * 700);
            $(this).stop().animate({scrollLeft:val}, 1000);
            event.preventDefault();
        });

        $('body').keyup(function(event){

            var target = $(event.target);

            if($(target).is('input')){
                return false;
            }

            var delta = 0;
            if((event.which==37)/* || (event.which==40)*/){
                delta = 1;
            }
            else if(/*(event.which==38) || */(event.which==39)){
                delta = -1;
            }
            else{
                return false;
            }

            var val = $('.h-scroll').scrollLeft() - (delta * 700);
            $('.h-scroll').stop().animate({scrollLeft:val},1000);

        });

    });
</script>

<div class="blog<?php echo $this->pageclass_sfx;?>">



<?php if(!empty($this->texts)): ?>

        <?php
            $num_of_rows = 2;
            $current_row = 1;
            $rows = array();
            foreach($this->texts as $text){
                $rows[$current_row][] = $text;
                if($current_row == $num_of_rows){
                    $current_row = 1;
                }
                else{
                    $current_row++;
                }
            }
        ?>

    <div class="h-scroll" style="height: <?php echo $num_of_rows*250 ?>px;">
	<?php for($i = 1; $i <= $num_of_rows; $i++): ?>


            <ul class="ul-text-list" style="width: <?php echo count($rows[$i])*467 ?>px;">
                <?php foreach($rows[$i] as $item): ?>

                    <li class="li-text">
                        <?php
                        $this->item = &$item;
                        echo $this->loadTemplate('item');
                        ?>
                    </li>

                <?php endforeach ?>
            </ul>

    <?php endfor ?>
    </div>

<?php else: ?>
        <?php echo JText::_("BE_THE_FIRST_TO_WRITE_IN") ?> <a href="index.php?option=com_content&view=form&layout=edit&category_id=<?php echo "5" ?>"><?php echo $this->category->title; ?></a>
<?php endif ?>

</div>
