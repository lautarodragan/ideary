<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Create a shortcut for params.
$params = &$this->item->params;
$images = json_decode($this->item->images);
//$canEdit	= $this->item->params->get('access-edit');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

?>

<div class="text">

    <div class="category-color" style="background: #FF0000"></div>

    <div class="text-title">
        <?php if (/*$params->get('link_titles') && $params->get('access-view')*/true) : ?>
            <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>">
                <?php echo $this->escape($this->item->title); ?></a>
        <?php else : ?>
            <?php echo $this->escape($this->item->title); ?>
        <?php endif; ?>
    </div>

    <div class="text-content">
        <?php echo substr($this->item->introtext, 0, 150)."..."; ?>
    </div>

    <div class="text-bottom">
        <div class="text-bottom-content">
        <div class="text-author">
            <?php $author =  $this->item->author; ?>
            <?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author);?>

            <?php if (!empty($this->item->contactid ) &&  $params->get('link_author') == true):?>
                <?php echo JText::_('BY').' '.JHtml::_('link', JRoute::_('index.php?option=com_contact&view=contact&id='.$this->item->contactid), $author); ?>

            <?php else :?>
                <?php echo JText::_('BY')." ".$author; ?>
            <?php endif; ?>
        </div>

        <div class="text-actions">

            <?php
                if($this->category_model->isApplauded($this->item->id, $this->user->get('id'))){
                    $icon_box_class = "already-applauded";
                    $icon_box_title = JText::_('ALREADY_APPLAUDED');
                }
                else{
                    $icon_box_class = "no-applauded";
                    $icon_box_title = JText::_('JCLAP');
                }
            ?>

            <div class="icon-box <?php echo $icon_box_class ?>" data-id="<?php echo $this->item->id ?>" data-writer-id="<?php echo $this->item->created_by ?>" title="<?php echo $icon_box_title ?>"></div>

            <?php
            if($this->category_model->isSaved($this->item->id, $this->user->get('id'))){
                $icon_box_class = "unarchive";
                $icon_box_title = JText::_('UNARCHIVE');
            }
            else{
                $icon_box_class = "save";
                $icon_box_title = JText::_('SAVE');
            }
            ?>

            <div class="icon-box <?php echo $icon_box_class ?>" data-id="<?php echo $this->item->id ?>" title="<?php echo $icon_box_title ?>"></div>


            <?php
            if($this->category_model->isFavorite($this->item->id, $this->user->get('id'))){
                $icon_box_class = "des-fav";
                $icon_box_title = JText::_('UNDO_ADD_TO_FAVORITES');
            }
            else{
                $icon_box_class = "fav";
                $icon_box_title = JText::_('ADD_TO_FAVORITES');
            }
            ?>

            <div class="icon-box <?php echo $icon_box_class ?>" data-id="<?php echo $this->item->id ?>" title="<?php echo $icon_box_title ?>"></div>

        </div>
        </div>
    </div>
</div>