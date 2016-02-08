<li class="li-text">

    <div class="text">

        <div class="category-color" style="background: #<?php echo $text->color_code ?>"></div>

        <div class="text-title">

            <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($text->id, $text->catid))?>">
                <?php echo $text->title ?>
            </a>

        </div>


        <div class="text-content">
            <?php echo substr($text->introtext, 0, 150)."..." ?>
        </div>

        <div class="text-bottom">
            <div class="text-bottom-content">
                <div class="text-author">
                    <?php
                    $author =  $text->author;
                    $author = ($text->created_by_alias ? $text->created_by_alias : $author);

                    echo JText::_('BY').' '.JHtml::_('link', JRoute::_('index.php?option=com_contact&view=public&id='.$text->contactid), $author)
                    ?>
                </div>

                <div class="text-actions">

                    <?php $countApplauses = ideary::getCountApplausesByTextId($text->id)?>

                    <div class="applauses" title="<?php echo str_replace('{x}', $countApplauses, JText::_('X-APPLAUSES'))?>">
                        <div class="count_applauses"><?php echo $countApplauses ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>
