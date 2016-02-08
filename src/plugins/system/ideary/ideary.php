<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Language Code plugin class.
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.language
 */
class Ideary extends JPlugin
{
	public function hola(){
		echo "hola mundo";
	}

    public static function isApplauded($text_id, $user_id){

        $db = JFactory::getDbo();

        $query = 'SELECT * FROM #__applauses WHERE text_id='.(int) $text_id.' AND user_id='.(int) $user_id;

        $db->setQuery($query);

        $applause = $db->loadObject();

        return ($applause)? true : false;

    }

    public static function isReported($text_id, $user_id){

        $db = JFactory::getDbo();

        $query = 'SELECT * FROM #__complaints WHERE text_id='.(int) $text_id.' AND user_id='.(int) $user_id;

        $db->setQuery($query);

        $reported = $db->loadObject();

        return ($reported)? true : false;

    }

    public static function isFollowed($writer_id, $user_id){

        $db = JFactory::getDbo();

        $query = 'SELECT * FROM #__follows WHERE followed_id='.(int) $writer_id.' AND follower_id='.(int) $user_id;

        $db->setQuery($query);

        $followed = $db->loadObject();

        return ($followed)? true : false;

    }

    public static function isSaved($text_id, $user_id){

        $db = JFactory::getDbo();

        $query = 'SELECT * FROM #__favorites WHERE text_id='.(int) $text_id.' AND user_id='.(int) $user_id.' AND fav=false';

        $db->setQuery($query);

        $saved = $db->loadObject();

        return ($saved)? true : false;

    }

    public static function isFavorite($text_id, $user_id){

        $db = JFactory::getDbo();

        $query = 'SELECT * FROM #__favorites WHERE text_id='.(int) $text_id.' AND user_id='.(int) $user_id.' AND fav=true';

        $db->setQuery($query);

        $favorite = $db->loadObject();

        return ($favorite)? true : false;

    }

	public function getTexts($category=null, $period=null, $text_search=null, $user_id=null, $offset=0, $limit = 20){
		$sortByActivity = true;

		if ($text_search || $offset ) {
			return ideary::getTextsWithoutCache($category, $period, $text_search, $user_id, $offset, $limit, $sortByActivity);
		}
		
		$cachedTexts = ShmopCache::get_cache("home");
		
		if (!$cachedTexts) {
			$cachedTexts = ideary::getTextsWithoutCache($category, $period, $text_search, $user_id, $offset, $limit, $sortByActivity);
			ShmopCache::save_cache($cachedTexts, "home", 600);
        }
		
		return $cachedTexts;
	}

    public function clearCache() {
        ShmopCache::clearCache("home");
    }
	
    public function getTextsWithoutCache($category=null, $period=null, $text_search=null, $user_id=null, $offset=0, $limit = 20, $sortByActivity = false){
		
        $db = JFactory::getDbo();

        $where = array();
		$flag = false;
        if($text_search){
			$flag = true;
        }

        if($category && ($category != -1)){
            $where[] = "c.id = $category";
        }

        if($period){

            $datetime = new \DateTime();
            switch ($period) {
                case "TODAY":
                    $datetime->modify('today');
                    break;
                case "YESTERDAY":
                    $datetime->modify('yesterday');
                    break;
                case "LAST-WEEK":
                    $datetime->modify('last week');
                    break;
                case "LAST-MONTH":
                    $datetime->modify('last month');
                    break;
                case "LAST-YEAR":
                    $datetime->modify('last year');
                    break;
            }

            $where[] = "t.created > '".$datetime->format("Y-m-d H:i:s")."'";

        }
		
        $query = "SELECT t.*, u.name AS author, c.color_code, i.image_name AS image, f.id AS saved ";
		
		if ($sortByActivity) {
			$query .= ", ifnull(max(cm.date), t.created) lastActivity ";
		}
		
		$query .= "FROM #__content t ".
            "JOIN #__users u ON (t.created_by=u.id) ".
            "JOIN #__categories c ON (c.id=t.catid) ".
            "LEFT JOIN #__content_images i ON (i.text_id=t.id) ".
            "LEFT JOIN #__favorites f ON ((f.text_id=t.id) AND (f.user_id=".$user_id.") AND (f.fav = FALSE))";
		
		if ($sortByActivity) {
			$query .= " LEFT JOIN #__comments cm ON (t.id = cm.text_id)";
		}
		
        $where[] = "t.state = 1";

        if(count($where)){
            $query .= " WHERE ".implode(" AND ", $where);
        }
		
		if ($sortByActivity) {
			$query .= " GROUP BY t.id";
		}
		
        if($flag){
            $query = "SELECT * FROM (".$query." AND t.title LIKE '%".$text_search."%' ORDER BY t.created DESC) a UNION ".
                     "SELECT * FROM (".$query." AND EXISTS(SELECT * FROM text_tags_texts tt JOIN text_tags tags ON (tt.tag_id=tags.id) WHERE tt.text_id=t.id AND tags.name LIKE '%".$text_search."%') ORDER BY t.created DESC) b UNION ".
                     "SELECT * FROM (".$query." AND t.introtext LIKE '%".$text_search."%' ORDER BY t.created DESC) c";
        }

		

        if(!$text_search){
			if (!$sortByActivity) {
				$query .= " ORDER BY pinned DESC, created DESC";
			} else {
				$query .= " ORDER BY pinned DESC, lastActivity DESC";
			}
			
            $query .= " LIMIT ".$offset.", ".$limit;
        }

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getTextsCount($category=null, $period=null, $text_search=null, $user_id=null){

        $db = JFactory::getDbo();

        $where = array();
        $flag = false;
        if($text_search){
            $flag =true;
        }

        if($category && ($category != -1)){
            $where[] = "c.id = $category";
        }

        if($period){

            $datetime = new \DateTime();
            switch ($period) {
                case "TODAY":
                    $datetime->modify('today');
                    break;
                case "YESTERDAY":
                    $datetime->modify('yesterday');
                    break;
                case "LAST-WEEK":
                    $datetime->modify('last week');
                    break;
                case "LAST-MONTH":
                    $datetime->modify('last month');
                    break;
                case "LAST-YEAR":
                    $datetime->modify('last year');
                    break;
            }

            $where[] = "t.created > '".$datetime->format("Y-m-d H:i:s")."'";

        }

        $from = "FROM #__content t ".
            "JOIN #__users u ON (t.created_by=u.id) ".
            "JOIN #__categories c ON (c.id=t.catid) ".
            "LEFT JOIN #__content_images i ON (i.text_id=t.id) ".
            "LEFT JOIN #__favorites f ON ((f.text_id=t.id) AND (f.user_id=".$user_id.") AND (f.fav = FALSE))";

        $where[] = "t.state = 1";

        if (!$flag){
            $where[] = "t.featured = 1";
        }

        if(count($where)){
            $from .= " WHERE ".implode(" AND ", $where);
        }

        if($flag){
            $query = "SELECT COUNT(*) FROM ((SELECT t.id ".$from." AND t.title LIKE '%".$text_search."%') UNION ".
                "(SELECT t.id ".$from." AND t.introtext LIKE '%".$text_search."%') UNION ".
                "(SELECT t.id ".$from." AND EXISTS(SELECT * FROM text_tags_texts tt JOIN text_tags tags ON (tt.tag_id=tags.id) WHERE tt.text_id=t.id AND tags.name LIKE '%".$text_search."%'))) AS a";
        }
        else{
            $query = "SELECT COUNT(*) ".$from;
        }

        $db->setQuery($query);

        return $db->loadResult();
    }


    public function getTextsAccordingToUser($userId){

        $db = JFactory::getDbo();

        $query1 = "SELECT t.*, u.username AS author, c.color_code, NULL AS count_applauses, f.id AS saved ".
            "FROM #__content t ".
            "JOIN #__users u ON (t.created_by=u.id) ".
            "JOIN #__categories c ON (c.id=t.catid) ".
            "JOIN #__follows fl ON (t.created_by=fl.followed_id) ".
            "LEFT JOIN #__favorites f ON ((f.text_id=t.id) AND (f.user_id=".$userId.") AND (f.fav = FALSE)) ".
            "WHERE t.state=1 and fl.follower_id=".$userId;

        $query2 = "SELECT t.*, u.username AS author, c.color_code, COUNT(*) AS count_applauses, f.id AS saved ".
            "FROM #__content t ".
            "JOIN #__users u ON (t.created_by=u.id) ".
            "JOIN #__categories c ON (c.id=t.catid) ".
            "JOIN #__applauses a ON (a.text_id=t.id) ".
            "LEFT JOIN #__favorites f ON ((f.text_id=t.id) AND (f.user_id=".$userId.") AND (f.fav = FALSE)) ".
            "where t.state=1 GROUP BY t.id ".
            "ORDER BY count_applauses DESC ".
            "LIMIT 10";

        $query3 = "SELECT t.*, u.username AS author, c.color_code, NULL AS count_applauses, f.id AS saved ".
            "FROM #__content t ".
            "JOIN #__users u ON (t.created_by=u.id) ".
            "JOIN #__categories c ON (c.id=t.catid) ".
            "LEFT JOIN #__favorites f ON ((f.text_id=t.id) AND (f.user_id=".$userId.") AND (f.fav = FALSE)) ".
            "WHERE t.state=1 and t.catid IN (SELECT i.category_id FROM text_interests AS i WHERE i.user_id=".$userId.")";

        $query = "SELECT DISTINCT x.id, x.color_code, x.catid, x.title, x.introtext, x.created_by_alias, x.author,x.created_by, x.saved ".
            "FROM (($query1) UNION ($query2) UNION ($query3)) AS x WHERE x.state = 1 ORDER BY x.created DESC";
        
		//echo $query;die;
		$db->setQuery($query);

        return $db->loadObjectList();

    }

    public function getWriterSearch($search, $userId){

        $db = JFactory::getDbo();

        $query = "SELECT DISTINCT(u.id), u.name, f.follower_id FROM text_users u ".
            "LEFT JOIN text_follows f ON ((u.id=f.followed_id) AND (f.follower_id=".$userId.")) ".
            "WHERE name LIKE '%".$search."%' ".
            "ORDER BY f.follower_id DESC";

        $db->setQuery($query);

        return $db->loadObjectList();

    }

    public function getTextsByUserId($userId, $loggedUser,$pub="1"){

        $db = JFactory::getDbo();
		if ($loggedUser!=0){
			$query = "SELECT t.*, u.name AS author, c.color_code, i.image_name AS image, f.id AS saved FROM #__content t ".
				"JOIN #__users u ON (t.created_by=u.id) ".
				"LEFT JOIN #__categories c ON (c.id=t.catid) ".
				"LEFT JOIN #__content_images i ON (i.text_id=t.id) ".
				"LEFT JOIN #__favorites f ON ((f.text_id=t.id) AND (f.user_id=".$loggedUser.") AND (f.fav = FALSE)) ".
				"WHERE (t.state = ".$pub.") AND (t.created_by = ".$userId.") ".
				"ORDER BY t.created DESC";
		}else{
			$query = "SELECT t.*, u.name AS author, c.color_code, i.image_name AS image, null as saved FROM #__content t ".
				"JOIN #__users u ON (t.created_by=u.id) ".
				"LEFT JOIN #__categories c ON (c.id=t.catid) ".
				"LEFT JOIN #__content_images i ON (i.text_id=t.id) ".
				"WHERE (t.state = ".$pub.") AND (t.created_by = ".$userId.") ".
				"ORDER BY t.created DESC";
		}

        $db->setQuery($query);

        return $db->loadObjectList();

    }

    public function getCountTextsByUserId($userId, $pub="1"){

        $db = JFactory::getDbo();

        $query = "SELECT count(*) FROM #__content t ".
                "WHERE t.state = ".$pub." AND t.created_by = ".$userId;

        $db->setQuery($query);

        return $db->loadResult();

    }

    public function getHasFirstByUserId($userId){

        $db = JFactory::getDbo();

        $query = "SELECT count(*) FROM #__content t ".
                "WHERE first = 1 AND t.created_by = ".$userId;

        $db->setQuery($query);

        return $db->loadResult() > 0;

    }

    public function getTextsOfUser($userId, $pub=1, $offset=0, $limit=10){

        $db = JFactory::getDbo();

        $query = "SELECT t.*, u.name AS author, c.color_code, i.image_name AS image, f.id AS saved FROM #__content t ".
                "JOIN #__users u ON (t.created_by=u.id) ".
                "LEFT JOIN #__categories c ON (c.id=t.catid) ".
                "LEFT JOIN #__content_images i ON (i.text_id=t.id) ".
                "LEFT JOIN #__favorites f ON ((f.text_id=t.id) AND (f.user_id=".$userId.") AND (f.fav = FALSE)) ".
                "WHERE (t.state = ".$pub.") AND (t.created_by = ".$userId.") ".
                "ORDER BY t.created DESC ".
                "LIMIT ".$offset.", ".$limit;

        $db->setQuery($query);

        return $db->loadObjectList();

    }

    public function getNotificationTypeByType($type){

        $db = JFactory::getDbo();

        $query = 'SELECT * FROM text_notification_types WHERE type="'.$type.'"';

        $db->setQuery($query);

        return $db->loadObject();

    }


    public function writerInvitedToWrite($userId, $writerId){

        $db = JFactory::getDbo();

        $notificationType = Ideary::getNotificationTypeByType('invited_to_write');

        $query = 'SELECT COUNT(*) count_rows FROM text_notifications WHERE user_id='.$userId.' AND notified_id='.$writerId.' AND notification_type_id='.$notificationType->id;

        $db->setQuery($query);

        $invitedToWrite = ($db->loadObject()->count_rows)? true : false;

        return $invitedToWrite;
    }
	
	
	public function getFavArchTexts($fav=1, $offset=0, $limit=10){
		$userid = JFactory::getUser()->get('id');
        $db = JFactory::getDbo();

        $query = "SELECT t.*, u.name AS author, c.color_code, i.image_name AS image, f.id AS saved FROM #__content t ".
            "JOIN #__users u ON (t.created_by=u.id) ".
            "JOIN #__categories c ON (c.id=t.catid) ".
            "LEFT JOIN #__content_images i ON (i.text_id=t.id) ".
            "JOIN #__favorites f ON ((f.text_id=t.id) AND (f.user_id=".$userid.") AND (f.fav = ".$fav.")) ".
            "WHERE (t.state = 1) GROUP BY t.id ".
            "ORDER BY t.created DESC ".
            "LIMIT ".$offset.", ".$limit;

        $db->setQuery($query);

        return $db->loadObjectList();

    }

    public function getCountFavArchTexts($fav=1){
        $userid = JFactory::getUser()->get('id');
        $db = JFactory::getDbo();

        $query = "SELECT COUNT(DISTINCT t.id) ".
            "FROM #__content t ".
            "JOIN #__favorites f ON ((f.text_id=t.id) AND (f.user_id=".$userid.") AND (f.fav = ".$fav.")) ".
            "WHERE t.state = 1";

        $db->setQuery($query);

        return $db->loadResult();

    }

    public function getTextsApplaudedByUser($userId, $offset=0, $limit=10){

        $db = JFactory::getDbo();

	    $query = "SELECT t.*, u.name AS author, c.color_code, i.image_name AS image, f.id AS saved FROM #__content t ".
				"JOIN #__users u ON (t.created_by=u.id) ".
				"JOIN #__categories c ON (c.id=t.catid) ".
				"JOIN #__applauses a ON (a.text_id=t.id) ".
				"LEFT JOIN #__content_images i ON (i.text_id=t.id) ".
				"LEFT JOIN #__favorites f ON ((f.text_id=t.id) AND (f.user_id=".$userId.") AND (f.fav = FALSE)) ".
				"WHERE (t.state = 1) AND (a.user_id = ".$userId.") ".
				"GROUP BY t.id ORDER BY t.created DESC ".
                "LIMIT ".$offset.", ".$limit;

        $db->setQuery($query);

        return $db->loadObjectList();

    }

    public function getCountTextsApplaudedByUser($userId){

        $db = JFactory::getDbo();

        $query = "SELECT COUNT(DISTINCT t.id) FROM #__content t ".
            "JOIN #__applauses a ON (a.text_id=t.id) ".
            "WHERE (t.state = 1) AND (a.user_id = ".$userId.")";

        $db->setQuery($query);

        return $db->loadResult();

    }

    public function getCategoriesForCombo(){

        $db = JFactory::getDbo();

        $lang_tag = JFactory::getLanguage()->getTag();
        $lang_code = substr($lang_tag, 0, 2);

        $query = "SELECT id, title_".$lang_code." title, color_code FROM text_categories WHERE lang_label IS NOT NULL AND lang_label!='OTHERS' ORDER BY title ASC";
        $db->setQuery($query);
        $catsWhithoutOthers = $db->loadObjectList();

        $query = "SELECT id, title_".$lang_code." title, color_code FROM text_categories WHERE lang_label IS NOT NULL AND lang_label='OTHERS' LIMIT 1";
        $db->setQuery($query);
        $catOthers = $db->loadObject();

        $catsWhithoutOthers[] = $catOthers;

        return $catsWhithoutOthers;
    }

    public function getCategoryById($categoryId){

        $db = JFactory::getDbo();

        $lang_tag = JFactory::getLanguage()->getTag();
        $lang_code = substr($lang_tag, 0, 2);

        $query = "SELECT id, title_".$lang_code." title, color_code FROM text_categories WHERE lang_label IS NOT NULL AND id=".(int)$categoryId;

        $db->setQuery($query);

        return $db->loadObject();
    }

    public static function getWriterById($writerId){

        $db = JFactory::getDbo();

        $query = "SELECT id, name, username FROM text_users WHERE id=".(int)$writerId;

        $db->setQuery($query);

        return $db->loadObject();
    }

    public function getTagsByTextId($textId){

        $db = JFactory::getDbo();

        $query = "SELECT t.* FROM text_tags_texts tt JOIN text_tags t ON (t.id=tt.tag_id) WHERE t.name !='' AND tt.text_id=".(int)$textId;

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function removeDuplicatedTags($tags){
        $uniqueTags = array();

        foreach($tags as $tag){
            $uniqueTags[] = strtolower($tag);
        }

        return array_unique($uniqueTags);
    }

    public static function getCommentsByTextId($textId){

        $db = JFactory::getDbo();
        $userId = JFactory::getUser()->get('id');

        $query = "SELECT DISTINCT(c.id), c.comment, u.name AS author_name, c.date, c.votes_up, c.votes_down, cu.comment_id,u.id as user_id, cu.votes_up as voted_up, c.date, cast(c.date as unsigned) ctime FROM text_comments c ".
            "JOIN text_users u ON (c.commenter_id=u.id) ".
            "LEFT JOIN text_comments_users cu ON ((c.id=cu.comment_id) AND (cu.user_id=".$userId.")) ".
            "WHERE c.text_id=".(int)$textId." ".
            "ORDER BY c.date DESC";

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function textDate($mysqlDate){

        $timeZone = 'America/Buenos_Aires';  // -3 hours

        $date_text = "";

        $date = date_create($mysqlDate);
        $fechaActual = date_create();

        if($date > $fechaActual){
            $date = new DateTime($mysqlDate, new DateTimeZone('GMT'));
            $date->setTimeZone(new DateTimeZone($timeZone));
        }

        $year = $date->format('Y');
        $month = $date->format('n');
        $day = $date->format('j');
        $hour = $date->format('G');
        $minute = $date->format('i');
        $second = $date->format('s');

        $fechaActualYear = $fechaActual->format('Y');
        $fechaActualMonth = $fechaActual->format('n');
        $fechaActualDay = $fechaActual->format('j');
        $fechaActualHour = $fechaActual->format('G');
        $fechaActualMinute = $fechaActual->format('i');
        $fechaActualSecond = $fechaActual->format('s');

        $difference_in_seconds = $fechaActual->format('U') - $date->format('U');
        $difference_in_minutes = floor($difference_in_seconds/60);
        $difference_in_hours = floor($difference_in_minutes/60);
        $difference_in_days = floor($difference_in_hours/24);

        if($difference_in_days >= 30){

            switch ($month) {

                case '1':
                    $month_text = JText::_('JANUARY');
                    break;
                case '2':
                    $month_text = JText::_('FEBRUARY');
                    break;
                case '3':
                    $month_text = JText::_('MARCH');
                    break;
                case '4':
                    $month_text = JText::_('APRIL');
                    break;
                case '5':
                    $month_text = JText::_('MAY');
                    break;
                case '6':
                    $month_text = JText::_('JUNE');
                    break;
                case '7':
                    $month_text = JText::_('JULY');
                    break;
                case '8':
                    $month_text = JText::_('AUGUST');
                    break;
                case '9':
                    $month_text = JText::_('SEPTEMBER');
                    break;
                case '10':
                    $month_text = JText::_('OCTOBER');
                    break;
                case '11':
                    $month_text = JText::_('NOVEMBER');
                    break;
                case '12':
                    $month_text = JText::_('DECEMBER');
                    break;

            }

            $date_text = JText::_('TEXT_DATE');
            $date_text = str_replace('{YEAR}', $year, $date_text);
            $date_text = str_replace('{MONTH}', $month_text, $date_text);
            $date_text = str_replace('{DAY}', $day, $date_text);

        }
        else{


            if($difference_in_days > 0){

                $date_text = ($difference_in_days > 1)? JTEXT::_('X_DAYS_AGO'): JTEXT::_('X_DAY_AGO');
                $date_text = str_replace('{X}', $difference_in_days, $date_text);

            }
            elseif($difference_in_hours > 0){

                $date_text = ($difference_in_hours > 1)? JTEXT::_('X_HOURS_AGO'): JTEXT::_('X_HOUR_AGO');
                $date_text = str_replace('{X}', $difference_in_hours, $date_text);

            }
            elseif($difference_in_minutes > 0){

                $date_text = ($difference_in_minutes > 1)? JTEXT::_('X_MINUTES_AGO'): JTEXT::_('X_MINUTE_AGO');
                $date_text = str_replace('{X}', $difference_in_minutes, $date_text);

            }
            elseif($difference_in_seconds > 0){

                $date_text = ($difference_in_seconds > 1)? JTEXT::_('X_SECONDS_AGO'): JTEXT::_('X_SECOND_AGO');
                $date_text = str_replace('{X}', $difference_in_seconds, $date_text);

            }
        }

        return $date_text;

    }

    public function generateTextContent($item, $userId, $index, $inProfile=false, $authorsection=false, $inHome=false, $type='default', $follower=false){
				
        $draftClass = '';
        if($type=='draft'){
            $draftClass = ' draft';
        }
		
		$authorId = $item->created_by;

        $html = '<div class="text'.$draftClass.'" data-index="'.$index.'">';
            if(!$inProfile)
				if($item->first)
					$html .= '<div class="category-color" style="background: #E02424"></div>'; // #00B60E = green, #E65151 = red
				//else if($follower === true)
				//	$html .= '<div class="category-color" style="background: #00B60E"></div>'; //#c1c2c2 = gray

            if ($inProfile==false){
				$html .= '<div class="save-container" data-text-id="'.$item->id.'">';
				if($item->pinned) {
					$html .= '<div class="text-pinned-icon" title="'.JText::_('PINNED').'" ></div>';
				}
				$html .= '</div>';
			}

                $html .= '<div class="text-data" >';

                    $html .= '<div class="text-title-container">';

                    if ($inHome) {
                        $user_picture = ideary::getUserImagePath($authorId, "200");

                        if($userId!=$item->created_by){
                            $authorLink .= JRoute::_('index.php?option=com_contact&view=public&id='.$item->created_by);
                        }else{
                            $authorLink .= JRoute::_('index.php?option=com_users&view=profile&user_id='.$item->created_by);
                        }

                        $html .= '<a class="text-img-avatar" href="'.$authorLink.'">';
                        $html .= "<img src='$user_picture'>";
                        $html .= '</a>';
                    }

                    if($type=='draft'){
                        $html .= '<a href="'.JRoute::_("index.php?option=com_content&Itemid=101&a_id=". $item->id ."&task=article.edit").'">';
                    }
                    else{
                        $html .= '<a style="display: inline-block; margin-left: 10px; background: transparent; " href="'.JRoute::_("index.php?option=com_content&view=article&id=".$item->id).'">';
                    }
                            $html .= '<div class="text-title">';
                                $html .= $item->title;
                            $html .= '</div>';
                        $html .= '</a>';
                    $html .= '</div>';

                    $html .= '<div class="text-actions">';
							
                    $html .= '<div class="text-content">';
                        //$html .= strip_tags(html_entity_decode($item->introtext));
                        $html .= strip_tags($item->introtext, "<br>");
                    $html .= '</div>';

                    $html .= '<div class="text-bottom">';
                        $html .= '<div class="text-bottom-content">';

                    if($type=='draft'){

                        $html .= '<div class="draft-bottom-left">';
                        $html .= '<a href="'.JRoute::_("index.php?option=com_content&Itemid=101&a_id=". $item->id ."&task=article.edit").'">';
                        $html .= '<div class="draft-edit" title="'.JTEXT::_('EDIT_DRAFT').'"></div>';
                        $html .= '</a>';
                        $html .= '<div class="draft-delete" data-text-id="'.$item->id.'" title="'.JTEXT::_('DELETE_DRAFT').'"></div>';
                        $html .= '</div>';

                        $html .= '<div class="draft-bottom-right">';
                        $html .= JTEXT::_('DRAFT');
                        $html .= '</div>';

                    }
                    else{

                        if ($type=='mine'){
                            $html .= '<a href="'.JRoute::_("index.php?option=com_content&Itemid=101&a_id=". $item->id ."&task=article.edit").'">';
                            $html .= '<div class="edit-text" title="'.JTEXT::_('EDIT_TEXT').'"></div>';
                            $html .= '</a>';
                        }
                        else{

						 /*  $taglist = ideary::getTextTags($item->id,3);
						   if ($taglist!=""){
								$html .= '<div class="text-author" style="font-weight:normal;">';
								$html .= "Tags: " . $taglist;
								$html .= '</div>';
						    }
							*/
							if($authorsection==false){
								$html .= '<div class="text-author">';
								$author =  $item->author;
								$author = ($item->created_by_alias ? $item->created_by_alias : $author);
								
								$html .= JText::_('BY') . "&nbsp;";
								//if($follower)
								//	$html .= "<strong>";
								if($userId!=$item->created_by){
									$html .= JHtml::_('link', JRoute::_('index.php?option=com_contact&view=public&id='.$item->created_by), $author);
								}else{
									$html .= JHtml::_('link', JRoute::_('index.php?option=com_users&view=profile&user_id='.$item->created_by), $author);
								}
								//if($follower)
								//	$html .= "</strong>";

								if ($item->created != "0000-00-00 00:00:00") {
									$timeZone = 'America/Buenos_Aires';
									$curDate = date_create();
									$textDate = date_create($item->created);

									if($textDate > $curDate){
										$textDate = new DateTime($item->created, new DateTimeZone('GMT'));
										$textDate->setTimeZone(new DateTimeZone($timeZone));
									}

									$dateDiff = $textDate->diff($curDate);
									
                                    if ($dateDiff->days > 14) {
                                        $dateDiff = "";
									} else if ($dateDiff->days > 1) {
										$dateDiff = str_replace('{X}', $dateDiff->days, $dateDiff->days > 1 ? JTEXT::_('X_DAYS_AGO') : JTEXT::_('X_DAY_AGO'));
									} else if ($dateDiff->days > 0) {
										$dateDiff = JTEXT::_('YESTERDAY');
									} else if ($dateDiff->h > 0) {
										$dateDiff = str_replace('{X}', $dateDiff->h, $dateDiff->h > 1 ? JTEXT::_('X_HOURS_AGO') : JTEXT::_('X_HOUR_AGO'));
									} else if ($dateDiff->i > 0) {
										$dateDiff = str_replace('{X}', $dateDiff->i, $dateDiff->i > 1 ? JTEXT::_('X_MINUTES_AGO') : JTEXT::_('X_MINUTE_AGO'));
									} else {
										$dateDiff = str_replace('{X}', $dateDiff->s, $dateDiff->s > 1 ? JTEXT::_('X_SECONDS_AGO') : JTEXT::_('X_SECOND_AGO'));
									} 
									
									$dateDiff = strtolower($dateDiff);
									
									$html .= "&nbsp;<div class=\"age\" >$dateDiff</div>";
								}

                                $html .= "</div>";
							}
                        }


                        //if($inHome){
                            $countComments = Ideary::getCommentsCountByTextId($item->id);

                            if($countComments > 0){
                                $comment_label = ($countComments > 1)? 'X-COMMENTS' : 'X-COMMENT';
                                // $html .= '<a href="'.JRoute::_("index.php?option=com_content&view=article&id=".$item->id."#comments-section").'" style="text-decoration: none;">';
                                $html .= '<div class="comments-count-container" title="'.str_replace('{x}', $countComments, JText::_($comment_label)).'">';
                                $html .= '<div class="comments-count-bg"></div>';
                                $html .= '<div class="comments-count-num">'.$countComments.'</div>';
                                $html .= '</div>';
                                // $html .= '</a>';
                            }
                        //}

                        $countApplauses = ideary::getCountApplausesByTextId($item->id);

                        if($countApplauses > 0){
                            $clap_label = ($countApplauses > 1)? 'X-APPLAUSES' : 'X-APPLAUSE';
                            $html .= '<div class="applauses applauses-clickable" data-text-id="'.$item->id.'" data-text-title="'.$item->title.'" title="'.str_replace('{x}', $countApplauses, JText::_($clap_label)).'">';
                            $html .= '<div class="icon_applauses"></div>';
                            $html .= '<div class="count_applauses">'.$countApplauses.'</div>';
                            $html .= '</div>';
                        }

                    }
                        $html .= '</div>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';

        return $html;
    }


    public function generateUserListContent($users){

        $usersHtml = '';
        $i = 1;

        foreach($users as $user){
            $parity = ($i % 2)? "odd" : "even";
            $usersHtml .= '<a href="'.JRoute::_("index.php?option=com_contact&view=public&id=".$user->id).'" style="text-decoration: none;">';
            $usersHtml .= '<div class="user-box-item '.$parity.'" title="'.$user->name.'">';
            $usersHtml .= '<div class="user-box-item-img">';
            $usersHtml .= Ideary::getUserImage($user->id, "50", $user->name);
            $usersHtml .= '</div>';
            $usersHtml .= '<div class="user-box-name">';
            //$usersHtml .= ideary::truncate_chars($user->name, 12);
            $usersHtml .= $user->name;
            $usersHtml .= '</div>';
            $usersHtml .= '<div style="clear: both;"></div>';
            $usersHtml .= '</div>';
            $usersHtml .= '</a>';
            $i++;
        }
        $usersHtml .= '<div style="clear: both;"></div>';

        return $usersHtml;
    }

	public function generateMyStatsBox(){
		$user = JFactory::getUser();
		
		$user_picture = ideary::getUserImage($user->id, "200", $user->get('name'));
        
        $publishedCount = ideary::getCountTextsByUserId($user->id);
        $userImpact = ideary::getUserImpact($user->id);
        $applauses = ideary::getUserApplausesCount($user->id);

        $readWord = $publishedCount > 1 ? 'fueron leídos' : 'fue leído';
        $publishedWord = $publishedCount > 1 ? 'idearis, los cuales' : 'ideari, el cual';

        $html = '<div id="my-ideary-box">';

            $html .= '<div id="my-ideary-title">' . $user_picture . '<span>Impacto</span></div>';

            $html .= '<div id="my-ideary-container">';
                $html .= '<ul>';
                    $html .= '<li class="my-ideary-idearis" ><span class="key">Publicaste </span> <span class="value">' . $publishedCount . '</span><span> ' . $publishedWord . ' </span></li>';
                    $html .= '<li class="my-ideary-impact" ><span class="key">' . $readWord . '</span> <span class="value">' . $userImpact . '</span> veces </li>';
                    if ($applauses > 1)
                        $html .= '<li class="my-ideary-applauses" ><span class="key">y aplaudidos</span> <span class="value">' . $applauses . '</span> veces.</li>';
				$html .= '</ul>';
            $html .= '</div>';


        $html .= '</div>';

        return $html;
    }
	
	public function getUserImpact($userId){
        $db = JFactory::getDbo();

        $query = "SELECT sum(hits) from #__content where created_by = $userId";

        $db->setQuery($query);

		$result = $db->loadResult();
		
		if($result == null)
			$result = 0;
			
        return $result;

    }
	
	public function getUserApplausesCount($userId){

        $db = JFactory::getDbo();

        $query = "
			select count(id) from #__applauses
			where text_id in (
				select id from #__content where created_by = $userId
			)
			";
		
        $db->setQuery($query);

        return $db->loadResult();

    }
	
	public function getUserCharCount($userId){
        $db = JFactory::getDbo();

        $query = "select sum(CHAR_LENGTH(introtext)) from #__content where created_by = $userId and state = 1";

        $db->setQuery($query);

		
        return $db->loadResult();

    }
	
    public function generateMoreApplaudedTextsBox(){

        $html = '<div id="more-applauded-texts-box">';

            $html .= '<div id="more-applauded-texts-title">'.JText::_('TEN_MOST_ACCLAIMED_TEXTS').'</div>';

            $html .= '<div id="more-applauded-texts-container">';
                foreach(ideary::getTextsRanking(0, 'LAST-WEEK', 0, 5) as $index => $text){
                    $html .= '<div class="more-applauded-text">';
                    $html .= '<div class="more-applauded-text-index">'.($index+1).'</div>';
                    $html .= '<div class="more-applauded-text-title">';
                        $html .= '<a href="'.JRoute::_(ContentHelperRoute::getArticleRoute($text->id, $text->catid)).'">';
                        //$html .= ideary::truncate_chars($text->title, 30, '...');
                        $html .= $text->title;
                        $html .= '</a>';
                    $html .= '</div>';
                    $html .= '<div style="clear: both;"></div>';
                    $html .= '</div>';
                }
            $html .= '</div>';

            $html .= '<a href="'.JRoute::_("index.php?option=com_content&view=ranking",true).'" id="view-remaining-texts-link">';
                $html .= '<div id="view-remaining-texts">'.JText::_('VIEW_REMAINING_TEXTS').'</div>';
            $html .= '</a>';

        $html .= '</div>';

        return $html;
    }

    public function generateAuthorRanking(){
        $userId = JFactory::getUser()->get('id');
        $is_among = ($userId == 0)? true : false;
        $html = '';

        $html .= '<div id="author-ranking-box">';

            $html .= '<div id="author-ranking-title">'.JText::_('RANKING_OF_AUTHORS').'</div>';

            $html .= '<div id="author-ranking-authors">';
            $i = 1;
            foreach(ideary::getFourAuthorsOrderByRanking() as $author){

                if($author->id == $userId){
                    $is_among = true;
                }

                if(($i == 4) && (!$is_among)){
                    break;
                }

                $html .= '<div class="author-ranking">';
                    $html .= '<div class="ranking">'.$i.'</div>';
                    $html .= '<a href="'.JRoute::_('index.php?option=com_contact&view=public&id='.$author->id).'">';
                        $html .= '<div class="author">'.$author->name.'</div>';
                    $html .= '</a>';
                    $html .= '<div style="clear: both;"></div>';
                $html .= '</div>';
                $i++;
            }

            if(!$is_among){
                $html .= '<div class="author-ranking">';
                $html .= '<div class="ranking">...</div>';
                $html .= '<div class="author"></div>';
                $html .= '<div style="clear: both;"></div>';
                $html .= '</div>';

                $html .= '<div class="author-ranking">';
                $html .= '<div class="ranking">'.JFactory::getUser()->get('id').'</div>';
                $html .= '<div class="author">'.JFactory::getUser()->get('username').'</div>';
                $html .= '<div style="clear: both;"></div>';
                $html .= '</div>';
            }

            $html .= '</div>';

            $html .= '<a href="'.JRoute::_("index.php?option=com_users&view=ranking").'" style="text-decoration: none;">';
                $html .= '<div id="view-complete-ranking">'.JText::_('VIEW_FULL_RANKING').'</div>';
            $html .= '</a>';
        $html .= '</div>';

        return $html;
    }

    public function getFourAuthorsOrderByRanking(){

        $db = JFactory::getDbo();

        $query = "SELECT id, name, username ".
            "FROM text_users ".
            "ORDER BY ranking asc ".
            "LIMIT 4";

        $db->setQuery($query);

        return $db->loadObjectList();

    }

    public function getCommentsCountByTextId($textId){

        $db = JFactory::getDbo();

        $query = "SELECT COUNT(*) comments_count FROM text_comments WHERE text_id=".$textId;

        $db->setQuery($query);

        return $db->loadObject()->comments_count;

    }

    public function getAllAuthorsOrderByRanking(){

        $db = JFactory::getDbo();
        $userId = JFactory::getUser()->get('id');

        $query = "SELECT u.id, u.name, u.username, u.ranking, s.provider, COUNT(DISTINCT fd.id) followers, COUNT(DISTINCT fr.id) following, COUNT(DISTINCT a.id) applausses_received, f.id followed ".
            "FROM text_users u ".
            "LEFT JOIN text_follows fd ON (u.id=fd.followed_id) ".
            "LEFT JOIN text_follows fr ON (u.id=fr.follower_id) ".
            "LEFT JOIN text_follows f ON ((u.id=f.followed_id) AND (f.follower_id = ".$userId.")) ".
            "LEFT JOIN text_content c ON (u.id=c.created_by) ".
            "LEFT JOIN text_applauses a ON (c.id=a.text_id) ".
            "LEFT JOIN text_slogin_users s ON (u.id=s.user_id) ".
            "GROUP BY u.id ".
            "ORDER BY u.ranking asc";

        $db->setQuery($query);

        return $db->loadObjectList();

    }

    public function getFiveMoreApplaudedTexts(){

        $db = JFactory::getDbo();

        $query = "SELECT c.id, c.title, c.catid, (SELECT COUNT(DISTINCT u.id) FROM text_applauses a JOIN text_users u ON (a.user_id=u.id) WHERE text_id=c.id) AS applauses, ".
            "(SELECT MAX(ap.created_at) FROM text_applauses ap WHERE ap.text_id=c.id) last_clap_date ".
            "FROM text_content c ".
            "JOIN text_applauses a ON (c.id=a.text_id) ".
            "JOIN text_users au ON (a.user_id=au.id) ".
            "WHERE c.state=1 ".
            "GROUP BY c.id ".
            "ORDER BY applauses DESC, last_clap_date DESC ".
            "LIMIT 5";

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function getCountApplaussesByUserId($id){
        $db = JFactory::getDbo();

        $query = 'SELECT COUNT(*) FROM text_content c '.
            'JOIN text_applauses a ON (a.text_id=c.id) '.
            'WHERE c.created_by='.$id;

        $db->setQuery($query);
        return $db->loadResult();
    }

    public function getUserById($id, $loggedUserId){

        $db = JFactory::getDbo();

        // $query = "SELECT u.*, su.provider, COUNT(DISTINCT fd.id) followers, COUNT(DISTINCT fr.id) following, f.id followed ".
        //     "FROM text_users u ".
        //     "LEFT JOIN text_follows fd ON (u.id=fd.followed_id) ".
        //     "LEFT JOIN text_follows fr ON (u.id=fr.follower_id) ".
        //     "LEFT JOIN text_follows f ON ((u.id=f.followed_id) AND (f.follower_id = ".$loggedUserId.")) ".
        //     "LEFT JOIN text_slogin_users su ON (u.id=su.user_id) ".
        //     "WHERE u.id=".$id . " group by u.id" ;

        $query = "
                SELECT u.*, su.provider, f.id followed 
                FROM text_users u 
                LEFT JOIN text_follows f ON ((u.id=f.followed_id) AND (f.follower_id = $loggedUserId)) 
                LEFT JOIN text_slogin_users su ON (u.id=su.user_id) 
                WHERE u.id= $id group by u.id
            ";
			

        $db->setQuery($query);
        $user_info = $db->loadObject();
        $user_info->following = Ideary::getCountUsersWhoIAmFollowing($id);
        $user_info->followers = Ideary::getCountUsersWhoFollowMe($id);
        $user_info->applausses_received = Ideary::getCountApplaussesByUserId($id);

        return $user_info;
    }
	
	public function getUserInfoById($id){

        $db = JFactory::getDbo();

        // $query = "SELECT u.*, su.provider, COUNT(DISTINCT fd.id) followers, COUNT(DISTINCT fr.id) following, f.id followed ".
        //     "FROM text_users u ".
        //     "LEFT JOIN text_follows fd ON (u.id=fd.followed_id) ".
        //     "LEFT JOIN text_follows fr ON (u.id=fr.follower_id) ".
        //     "LEFT JOIN text_follows f ON (u.id=f.followed_id) ".
        //     "LEFT JOIN text_slogin_users su ON (u.id=su.user_id) ".
        //     "WHERE u.id=".$id . " group by u.id" ;

        $query = "
                SELECT u.*, su.provider, f.id followed 
                FROM text_users u 
                LEFT JOIN text_follows f ON (u.id=f.followed_id) 
                LEFT JOIN text_slogin_users su ON (u.id=su.user_id) 
                WHERE u.id= $id group by u.id
            ";

        $db->setQuery($query);
        $user_info = $db->loadObject();
        $user_info->following = Ideary::getCountUsersWhoIAmFollowing($id);
        $user_info->followers = Ideary::getCountUsersWhoFollowMe($id);

        return $user_info;
    }

    public function getInterestsByUserId($userId,$profile=false){

        $db = JFactory::getDbo();

        $lang_tag = JFactory::getLanguage()->getTag();
        $lang_code = substr($lang_tag, 0, 2);

        $query = "SELECT c.id, c.color_code, c.title_".$lang_code." AS title ".
            "FROM text_categories c ".
            "JOIN text_interests i ON (c.id=i.category_id) ".
            "WHERE i.user_id=".$userId . " order by rand()";
		if ($profile==false)
		$query.=" limit 3";

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function getTextsRanking($userId, $period="LAST-WEEK", $offset=0, $limit=20){

        $db = JFactory::getDbo();

        $lang_tag = JFactory::getLanguage()->getTag();
        $lang_code = substr($lang_tag, 0, 2);
		$having = "";
        $where = "WHERE t.state=1 ";

        if($period){

            $datetime = new DateTime();
            switch ($period) {
                case "TODAY":
                    $datetime->modify('today');
                    //$having = "HAVING applauses > 2 " ;
                    break;
                case "LAST-WEEK":
                    $datetime->modify('last week');
                    //$having = "HAVING applauses > 3 " ;
                    break;
                case "LAST-MONTH":
                    $datetime->modify('last month');
                    //$having = "HAVING applauses >= 4 " ;
                    break;
            }

            $where .= "AND t.created >= '".$datetime->format("Y-m-d")."' ";

        }
		
		
        $query = "SELECT t.id, t.title, t.introtext, t.created_by, t.catid, c.title_".$lang_code." cat_title, c.color_code, u.username, u.name, (SELECT COUNT(DISTINCT u.id) FROM text_applauses a JOIN text_users u ON (a.user_id=u.id) WHERE text_id=t.id) AS applauses, f.id saved, i.image_name, ".
            "(SELECT MAX(ap.created_at) FROM text_applauses ap WHERE ap.text_id=t.id) last_clap_date ".
            "FROM text_content t ".
            "LEFT JOIN text_categories c ON (t.catid=c.id) ".
            "LEFT JOIN text_users u ON (t.created_by=u.id) ".
            "JOIN text_applauses a ON (a.text_id=t.id) ".
            "JOIN text_users au ON (a.user_id=au.id) ".
            "LEFT JOIN text_favorites f ON ((t.id=f.text_id) AND (f.user_id=".$userId.") AND (f.fav=FALSE)) ".
            "LEFT JOIN text_content_images i ON (t.id=i.text_id) ".
            $where.
            "GROUP BY t.id ".
            $having .
            " ORDER BY applauses DESC, last_clap_date DESC ".
            "LIMIT ".$offset.", ".$limit;

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function getCountTextsRanking($period="LAST-WEEK"){

        $db = JFactory::getDbo();
        $having = "";
        $where = "WHERE t.state=1 ";

        if($period){

            $datetime = new DateTime();
            switch ($period) {
                case "TODAY":
                    $datetime->modify('today');
                    //$having = "HAVING applauses > 2";
                    break;
                case "LAST-WEEK":
                    $datetime->modify('last week');
                    //$having = "HAVING applauses > 3";
                    break;
                case "LAST-MONTH":
                    $datetime->modify('last month');
                    //$having = "HAVING applauses >= 4";
                    break;
            }

            $where .= "AND t.created >= '".$datetime->format("Y-m-d")."' ";

        }

        $query = "SELECT COUNT(*) FROM (SELECT DISTINCT(t.id) count_texts_ranking, ".
            "(SELECT COUNT(DISTINCT u.id) FROM text_applauses a JOIN text_users u ON (a.user_id=u.id) WHERE text_id=t.id) AS applauses ".
            "FROM text_content t ".
            "JOIN text_applauses a ON (a.text_id=t.id) ".
            "JOIN text_users au ON (a.user_id=au.id) ".
            $where.
            $having.") st";

        $db->setQuery($query);

        return $db->loadResult();
    }

    public function getNoSawNotificationsCountByUser($userId){

        $db = JFactory::getDbo();

        $query = "SELECT SUM(t.no_saw_notifications) FROM
                    (

                    SELECT SUM(t2.no_saw_comments_by_text) no_saw_notifications
                    FROM (SELECT COUNT(DISTINCT n.user_id) no_saw_comments_by_text
                          FROM text_notifications n
                          JOIN text_notification_types nt ON (n.notification_type_id=nt.id)
                          WHERE n.notified_id=".$userId." AND n.saw=FALSE AND nt.type='comment' AND n.user_id!=".$userId."
                          GROUP BY n.text_id) t2

                    UNION ALL

                    SELECT COUNT(DISTINCT n.user_id) no_saw_notifications FROM text_notifications n
                    JOIN text_notification_types nt ON (n.notification_type_id=nt.id)
                    WHERE notified_id=".$userId." AND n.saw=FALSE AND nt.type='follow'

                    UNION ALL

                    SELECT COUNT(*) no_saw_notifications FROM text_notifications n
                    JOIN text_notification_types nt ON (nt.id=n.notification_type_id)
                    WHERE n.notified_id=".$userId." AND n.saw=FALSE AND nt.type NOT IN ('comment', 'follow')

                    ) t";

        $db->setQuery($query);

        return $db->loadResult();
    }
	
	public function getNoSawNotificationsByUser($userId, $minId = 0){

        $db = JFactory::getDbo();

        $query = "
			select n.id NotificationId, n.created_at NotificationDate, n.notification_type_id NotificationType, n.saw NotificationSeen, n.user_id UserId, u.name UserName, n.text_id ArticleId, c.title ArticleTitle, n.comment_id CommentId from text_notifications n
				left outer join text_users u on (n.user_id = u.id)
				left outer join text_content c on (n.text_id = c.id)
			where n.notified_id= $userId 
				and n.saw = false 
				and n.id > $minId
			order by n.id desc;
			";

        $db->setQuery($query);
		
        return $db->loadObjectList();
    }
	
	public function getLastSeenNotificationsByUser($userId, $limit = 10){

        $db = JFactory::getDbo();

        $query = "
			select n.id NotificationId, n.created_at NotificationDate, n.notification_type_id NotificationType, n.saw NotificationSeen, n.user_id UserId, u.name UserName, n.text_id ArticleId, c.title ArticleTitle, n.comment_id CommentId from text_notifications n
				left outer join text_users u on (n.user_id = u.id)
				left outer join text_content c on (n.text_id = c.id)
			where n.notified_id= $userId 
				and n.saw = true 
			order by n.id desc
			limit $limit;
			";

        $db->setQuery($query);
		
        return $db->loadObjectList();
    }
	
	public function getNotificationMaxIdByUser($userId){

        $db = JFactory::getDbo();

        $query = "
			select n.id from text_notifications n
			where n.notified_id= $userId 
                and saw = 1
			order by n.id desc
			limit 1;
			";

        $db->setQuery($query);
		
        $result = $db->loadResult();

        return $result ? $result : 0;
    }

    public function checkEmailExistence($email){

        $db = JFactory::getDbo();

        $query = "SELECT COUNT(*) FROM text_users WHERE email='".$email."'";

        $db->setQuery($query);

        return ($db->loadResult() > 0)? true : false;

    }

    public function checkIfFollowing($writer_id, $user_id){

        $db = JFactory::getDbo();

        $query = "SELECT COUNT(*) FROM text_follows WHERE followed_id=".$writer_id." AND follower_id=".$user_id;

        $db->setQuery($query);

        return ($db->loadResult() > 0)? true : false;

    }

    public function generateTextRankingHTML($text, $index){

        $html = '';

        $html .= '<li class="li-ranking">';

            $html .= '<div class="text-ranking" data-just-added="true">';

                $html .= '<div class="text-ranking-category-color" style="background: #'.$text->color_code.'" title="'.$text->cat_title.'"></div>';

                $html .= '<div class="text-ranking-index">';
                    $html .= '<div class="text-ranking-index-left"></div>';
                    $html .= '<div class="text-ranking-index-center">'.$index.'</div>';
                    $html .= '<div class="text-ranking-index-right"></div>';
                $html .= '</div>';

                $html .= '<div class="save-container" data-text-id="'.$text->id.'">';
                if(is_null($text->saved)){
                    $html .= '<div class="text-ranking-save-icon" title="'.JText::_('SAVE').'" data-text-id="'.$text->id.'"></div>';
                }
                else{
                    $html .= '<div class="text-ranking-saved-icon" title="'.JText::_('UNARCHIVE').'" data-text-id="'.$text->id.'"></div>';
                }
                $html .= '</div>';

                if(!is_null($text->image_name)){

                    $html .= '<div class="text-ranking-title with-image">';
                        $html .= '<a href="'.JRoute::_(ContentHelperRoute::getArticleRoute($text->id, $text->catid)).'">';
                            //$html .= ideary::truncate_chars($text->title, 21);
                            $html .= '<div class="r-t-title">';
                            $html .= $text->title;
                            $html .= '</div>';
                        $html .= '</a>';
                    $html .= '</div>';


                    $html .= '<div class="text-ranking-img">';
                        $html .= '<a href="'.JRoute::_(ContentHelperRoute::getArticleRoute($text->id, $text->catid)).'">';
                            $html .= '<img src="'.JURI::base()."templates/beez_20/images/texts/".$text->id."/ranking/".$text->image_name.'">';
                        $html .= '</a>';
                    $html .= '</div>';

                    $html .= '<div class="text-ranking-content with-image">'.$text->introtext.'</div>';
                }
                else{

                    $html .= '<div class="text-ranking-title">';
                        $html .= '<a href="'.JRoute::_(ContentHelperRoute::getArticleRoute($text->id, $text->catid)).'">';
                            //$html .= ideary::truncate_chars($text->title, 21);
                            $html .= '<div class="r-t-title">';
                            $html .= $text->title;
                            $html .= '</div>';
                        $html .= '</a>';
                    $html .= '</div>';

                    $html .= '<div class="text-ranking-content">'.$text->introtext.'</div>';
                }

                $html .= '<div class="author-applauses">';

                    $html .= '<div class="text-ranking-author">'.JText::_('BY').' <a style="color: #acacac;" href="'.JRoute::_('index.php?option=com_contact&view=public&id='.$text->created_by).'">'.$text->name .'</a></div>';
                    $countApplauses = Ideary::getCountApplausesByTextId($text->id);
                    if($text->applauses > 0){
                        $html .= '<div class="text-ranking-applauses applauses-clickable" data-text-id="'.$text->id.'" data-text-title="'.$text->title.'">';
                            $html .= '<div class="text-ranking-applauses-icon"></div>';
                            $html .= '<div class="text-ranking-applauses-num">'.$text->applauses.'</div>';
                        $html .= '</div>';
                    }
                $html .= '</div>';

            $html .= '</div>';

        $html .= '</li>';

        return $html;
    }

    public function generateTextsRankingHTML($texts){
        $html = '';
        $i = 1;
        foreach($texts as $text){
            $html .= '<li class="li-ranking">';
                $html .= '<div class="text-ranking">';

                $html .= '<div class="text-ranking-category-color" style="background: #'.$text->color_code.';" title="'.$text->cat_title.'"></div>';

                $html .= '<div class="text-ranking-index">';
                    $html .= '<div class="text-ranking-index-left"></div>';
                    $html .= '<div class="text-ranking-index-center">'.$i.'</div>';
                    $html .= '<div class="text-ranking-index-right"></div>';
                $html .= '</div>';

            $html .= '<div class="save-container" data-text-id="'.$text->id.'">';
            if(is_null($text->saved)){
                $html .= '<div class="text-ranking-save-icon" title="'.JText::_('SAVE').'" data-text-id="'.$text->id.'"></div>';
            }
            else{
                $html .= '<div class="text-ranking-saved-icon" title="'.JText::_('UNARCHIVE').'" data-text-id="'.$text->id.'"></div>';
            }
            $html .= '</div>';

            if(!is_null($text->image_name)){
                $html .= '<div class="text-ranking-title with-image">';
                    $html .= '<a href="'.JRoute::_(ContentHelperRoute::getArticleRoute($text->id, $text->catid)).'">';
                    $html .= ideary::truncate_chars($text->title, 21);
                    $html .= '</a>';
                $html .= '</div>';

                $html .= '<div class="text-ranking-img">';
                    $html .= '<a href="'.JRoute::_(ContentHelperRoute::getArticleRoute($text->id, $text->catid)).'">';
                        $html .= '<img src="'.JURI::base()."templates/beez_20/images/texts/".$text->id."/ranking/".$text->image_name .'">';
                    $html .= '</a>';
                $html .= '</div>';

                $html .= '<div class="text-ranking-content with-image">'.$text->introtext.'</div>';

            }
            else{

                $html .= '<div class="text-ranking-title">';
                $html .= '<a href="'.JRoute::_(ContentHelperRoute::getArticleRoute($text->id, $text->catid)).'">';
                $html .= ideary::truncate_chars($text->title, 21);
                $html .= '</a>';
                $html .= '</div>';

                $html .= '<div class="text-ranking-content">'.$text->introtext.'</div>';

            }

            $html .= '<div class="author-applauses">';
                $html .= '<div class="text-ranking-author">'.JText::_('BY')." <a style='color: #acacac;' href='".JRoute::_('index.php?option=com_contact&view=public&id='.$text->created_by)."'>".$text->username.'</a></div>';
                if($text->applauses > 0){
                    $html .= '<div class="text-ranking-applauses">'.$text->applauses.'</div>';
                }
                $html .= '</div>';
            $html .= '</div>';
            $html .= '</li>';


            $i++;
        }

        return $html;
    }

    public function getCountApplausesByTextId($textId){

        $db = JFactory::getDbo();

        $query = "SELECT COUNT(DISTINCT u.id) count_applauses FROM text_applauses a JOIN text_users u ON (a.user_id=u.id) WHERE text_id=".(int)$textId;

        $db->setQuery($query);

        return $db->loadResult();
    }

    public static function getApplausesByTextId($textId){

        $db = JFactory::getDbo();

        $query = "
            SELECT a.id, a.created_at, u.name, u.id userId, cast(a.created_at as unsigned) ctime
            FROM text_applauses a 
            JOIN text_users u 
                ON (a.user_id = u.id) 
            where a.text_id = $textId
            order by a.created_at DESC
        ";

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function getUserBackground($userId){

        $db = JFactory::getDbo();

        $query = "SELECT background FROM text_users WHERE id=".(int)$userId;
        $db->setQuery($query);
        $result = $db->loadObject();
        $css = "";

        $userBg = 'templates/beez_20/images/user_backgrounds/'.$userId.'/'.$result->background;

        if($result->background && file_exists($userBg)){
            $css .= 'background-image: url("'.JURI::base().$userBg.'");';
            $css .= 'background-repeat: no-repeat;';
            $css .= 'background-attachment: fixed;';
            $css .= '-webkit-background-size: cover;';
            $css .= '-moz-background-size: cover;';
            $css .= '-o-background-size: cover;';
            $css .= 'background-size: cover;';
            $css .= 'background-position: center center;';
        }
        else{
            $css .= 'background-image: url("'.JURI::base().'templates/beez_20/images/user_backgrounds/default.jpg?v=3");';
			$css .= 'background-repeat: no-repeat;';
            $css .= 'background-attachment: fixed;';
            $css .= '-webkit-background-size: cover;';
            $css .= '-moz-background-size: cover;';
            $css .= '-o-background-size: cover;';
            $css .= 'background-size: cover;';
            $css .= 'background-position: center center;';
        }
        return $css;
    }

    public function getUserBackgroundFilename($userId){

        $db = JFactory::getDbo();

        $query = "SELECT background FROM text_users WHERE id=".(int)$userId;

        $db->setQuery($query);

        $result = $db->loadObject();

        return $result->background;
    }

    public function removeNewLinesFromString($string){
        return preg_replace(array('/\r/', '/\n/'), '', $string);
    }

    public function file_extension($filePath){
        return pathinfo($filePath, PATHINFO_EXTENSION);
    }

    public function generateThumbnail($imgSrc, $imgDst, $thumbnail_width, $thumbnail_height) { //$imgSrc is a FILE - Returns an image resource.
        //getting the image dimensions
        list($width_orig, $height_orig) = getimagesize($imgSrc);

        $imgExt = strtolower(pathinfo($imgSrc, PATHINFO_EXTENSION));
        switch ($imgExt) {
            case 'jpg':
                $myImage = imagecreatefromjpeg($imgSrc);
                break;

            case 'jpeg':
                $myImage = imagecreatefromjpeg($imgSrc);
                break;

            case 'png':
                $myImage = imagecreatefrompng($imgSrc);
                break;

            case 'gif':
                $myImage = imagecreatefromgif($imgSrc);
                break;
            default:
                ;
                break;
        }

        $ratio_orig = $width_orig/$height_orig;

        if ($thumbnail_width/$thumbnail_height > $ratio_orig) {
            $new_height = $thumbnail_width/$ratio_orig;
            $new_width = $thumbnail_width;
        } else {
            $new_width = $thumbnail_height*$ratio_orig;
            $new_height = $thumbnail_height;
        }

        $x_mid = $new_width/2;  //horizontal middle
        $y_mid = $new_height/2; //vertical middle

        $process = imagecreatetruecolor(round($new_width), round($new_height));

        imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
        $thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);

        imagecopyresampled($thumb, $process, 0, 0, ($x_mid-($thumbnail_width/2)), ($y_mid-($thumbnail_height/2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);
        imagedestroy($process);
        imagedestroy($myImage);

        switch ($imgExt) {
            case 'jpg':
                imagejpeg($thumb, $imgDst, 100);
                break;

            case 'jpeg':
                imagejpeg($thumb, $imgDst, 100);
                break;

            case 'png':
                imagepng($thumb, $imgDst, null, null);
                break;

            case 'gif':
                imagegif($thumb, $imgDst);
                break;

            default:
                break;
        }
    }

    function image_resize($src, $dst, $width, $height, $crop=0){

        if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

        $type = strtolower(substr(strrchr($src,"."),1));
        if($type == 'jpeg') $type = 'jpg';
        switch($type){
            case 'bmp': $img = imagecreatefromwbmp($src); break;
            case 'gif': $img = imagecreatefromgif($src); break;
            case 'jpg': $img = imagecreatefromjpeg($src); break;
            case 'png': $img = imagecreatefrompng($src); break;
            default : return "Unsupported picture type!";
        }

        // resize
        if($crop){
            if($w < $width or $h < $height) return "Picture is too small!";
            $ratio = max($width/$w, $height/$h);
            $h = $height / $ratio;
            $x = ($w - $width / $ratio) / 2;
            $w = $width / $ratio;
        }
        else{
            if($w < $width and $h < $height) return "Picture is too small!";
            $ratio = min($width/$w, $height/$h);
            $width = $w * $ratio;
            $height = $h * $ratio;
            $x = 0;
        }

        $new = imagecreatetruecolor($width, $height);

        // preserve transparency
        if($type == "gif" or $type == "png"){
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

        switch($type){
            case 'bmp': imagewbmp($new, $dst); break;
            case 'gif': imagegif($new, $dst); break;
            case 'jpg': imagejpeg($new, $dst); break;
            case 'png': imagepng($new, $dst); break;
        }
        return true;
    }

    public function validateUploadedImage($uploadedImage, $width, $height){

        $imageErrors = array();

        if($uploadedImage['error']==0){


            if($uploadedImage['size'] > 5242880){
                $imageErrors[] = "La imagén debe ser menor a 5MB";
            }

            if(
                !(
                    $uploadedImage['type'] == 'image/gif'
                    || $uploadedImage['type'] == 'image/jpg'
                    || $uploadedImage['type'] == 'image/jpeg'
                    || $uploadedImage['type'] == 'image/png'
                )
            ){
                $imageErrors[] = "La imagén debe ser gif, png, jpeg o jpg";
            }

            list($w, $h) = getimagesize($uploadedImage["tmp_name"]);

            if($w < $width || $h < $height){
                $imageErrors[] = "La imagén debe ser mayor a ".$width."x".$height." px";
            }

        }

        return $imageErrors;

    }

    public function rrmdir($dir) {
        if(file_exists($dir)){
            foreach(glob($dir . '/*') as $file) {
                if(is_dir($file)) {
                    ideary::rrmdir($file);
                }
                else {
                    unlink($file);
                }
            }
            rmdir($dir);
        }
    }

    public function truncateText($text, $lenght, $end='...'){

        $text = trim($text);

        if(strlen($text) > $lenght){
            $text = trim(substr($text, 0, ($lenght-strlen($end))));
            $text .= $end;
        }

        return $text;

    }
	
	public function getTaggedUsersFromComment($comment) {
		preg_match_all("/data-id=\"([0-9]+)\"/i", $comment, $matches);
		
		$tagged_users = $matches[1];
		$tagged_users = array_unique($tagged_users, SORT_NUMERIC);
		
		return $tagged_users;
	}

    public function addComment($commenter_id, $text_id, $comment, $writer_id){

        $db = JFactory::getDbo();
		$tagged_users = ideary::getTaggedUsersFromComment($comment);
		$text_notification_type_comment = 5;
		$text_notification_type_comment_commented_text = 8;
		$escaped_comment = mysql_escape_string($comment);
		
		$query = "
			INSERT INTO #__comments ( text_id, commenter_id, comment, date )
            VALUES ($text_id, $commenter_id, '$escaped_comment', now() )
			";

        $db->setQuery($query);
        $success = $db->query();
	
		$commentId = ideary::getLastInsertedCommentId();
		
		// Notify the author
        if ($commenter_id != $writer_id && !in_array($writer_id, $tagged_users)) {
            $query = "
				INSERT INTO text_notifications (user_id, notified_id, text_id, notification_type_id, created_at, saw, comment_id) 
				VALUES ($commenter_id, $writer_id, $text_id, $text_notification_type_comment, now(), 0, {$db->insertid()})
				";
            $db->setQuery($query);
            $db->query();
        }

		
		// Notify other commenters
        $query = "SELECT DISTINCT commenter_id FROM text_comments WHERE text_id = $text_id";
        $db->setQuery($query);
        $textCommenters = $db->loadObjectList();

        foreach($textCommenters as $textCommenter){

            if($commenter_id == $textCommenter->commenter_id)
				continue;
            if($writer_id == $textCommenter->commenter_id)
				continue;
			if (in_array($textCommenter->commenter_id, $tagged_users))
				continue;
			
			$query = "
					INSERT INTO text_notifications (user_id, notified_id, text_id, notification_type_id, created_at, saw, comment_id)
					VALUES ($commenter_id, {$textCommenter->commenter_id}, $text_id, $text_notification_type_comment_commented_text, NOW(), FALSE, $commentId)
					";
			$db->setQuery($query);
			$db->query();
            
        }
		
		// Notify tagged users
		foreach ($tagged_users as $tagged_user) {	
			if($commenter_id == $tagged_user)
				continue;		
			$query = "
				INSERT INTO text_notifications (user_id, notified_id, text_id, notification_type_id, created_at, saw, comment_id) 
				VALUES ($commenter_id, $tagged_user, $text_id, 10, NOW(), FALSE, $commentId);
				";
			$db->setQuery($query);
			$db->query();
		}
		
		if (!empty($tagged_users)) {
			ideary::logUserExperience(UserExperience::CommentTagging, $commenter_id, 1);
		}
		
        return $success;
    }

    public function saveMessage($user_id, $message){

        $db = JFactory::getDbo();
        $logged_user_id = JFactory::getUser()->get('id');

        $query = 'INSERT INTO text_messages ( user_id_from, user_id_to, date_time, state, message) '.
            'VALUES ('.(int) $logged_user_id.', '.(int) $user_id.', \''.date_create()->format('Y-m-d H:i:s').'\', 0, "'.$message.'" )';

        $db->setQuery($query);

        return $db->query();

    }

    public function getLastInsertedCommentId(){

        $db = JFactory::getDbo();

        return $db->insertid();

    }

    public function deleteUserImg($userId){
        $db = JFactory::getDbo();
        $query = 'DELETE FROM text_user_profiles WHERE profile_key="profilepicture.file" AND user_id='.$userId;
        $db->setQuery($query);
        return $db->query();
    }

    public function voteUp($comment_id, $user_id, $commenter_id, $text_id){

        $db = JFactory::getDbo();

        $query = 'UPDATE #__comments SET votes_up = votes_up + 1 WHERE id='.(int)$comment_id;
        $db->setQuery($query);
        $success1 = $db->query();

        $query = 'INSERT INTO #__comments_users (comment_id, user_id, votes_up) VALUES ('.$comment_id.', '.$user_id.', true)';
        $db->setQuery($query);
        $success2 = $db->query();

        if($user_id != $commenter_id){
            $query = 'INSERT INTO text_notifications (user_id, notified_id, text_id, comment_id, notification_type_id, created_at, saw) '.
                     'VALUES '.
                     '('.$user_id.', '.$commenter_id.', '.$text_id.', '.$comment_id.', (SELECT id FROM text_notification_types WHERE TYPE="like_comment"), NOW(), FALSE)';
            $db->setQuery($query);
            $db->query();
        }

        return ($success1 && $success2);
    }

    public function getVotesUp($comment_id){

        $db = JFactory::getDbo();

        $query = 'SELECT votes_up FROM #__comments WHERE id='.(int)$comment_id;
        $db->setQuery($query);
        $db->query();

        $result = $db->loadObject();
        return $result->votes_up;
    }

    public function voteDown($comment_id, $user_id){

        $db = JFactory::getDbo();

        $query = 'UPDATE #__comments SET votes_down = votes_down + 1 WHERE id='.(int)$comment_id;
        $db->setQuery($query);
        $success1 = $db->query();

        $query = 'INSERT INTO #__comments_users (comment_id, user_id, votes_up) VALUES ('.$comment_id.', '.$user_id.', false)';
        $db->setQuery($query);
        $success2 = $db->query();

        return ($success1 && $success2);
    }

    public function getVotesDown($comment_id){

        $db = JFactory::getDbo();

        $query = 'SELECT votes_down FROM #__comments WHERE id='.(int)$comment_id;
        $db->setQuery($query);
        $db->query();

        $result = $db->loadObject();
        return $result->votes_down;
    }

    public function getFiveRecentTextsByAuthor($author_id,$item_id){

        $db = JFactory::getDbo();

        $query = 'SELECT id, title, catid FROM #__content WHERE created_by='.(int)$author_id.' and state=1 and id<> '. $item_id . ' ORDER BY created DESC LIMIT 5';
        $db->setQuery($query);
        $db->query();

        return $db->loadObjectList();
    }

    public function getUsersWhoApplaudedText($text_id, $offset=0, $limit=16){

        $db = JFactory::getDbo();

        $query = 'SELECT DISTINCT(u.id), u.name '.
            'FROM text_users u '.
            'JOIN text_applauses a ON (u.id=a.user_id) '.
            'WHERE a.text_id='.$text_id.
            ' ORDER BY u.name '.
            'LIMIT '.$offset.','.$limit;

        $db->setQuery($query);
        $db->query();

        return $db->loadObjectList();
    }

    public function getUsersWhoFollowMe($user_id, $offset=0, $limit=16){

        $db = JFactory::getDbo();

        $query = 'SELECT DISTINCT(u.id), u.name '.
            'FROM text_users u '.
            'JOIN text_follows f ON (u.id=f.follower_id) '.
            'WHERE f.followed_id='.$user_id.
            ' ORDER BY u.name '.
            'LIMIT '.$offset.','.$limit;

        $db->setQuery($query);
        $db->query();

        return $db->loadObjectList();
    }

    public function getCountUsersWhoFollowMe($user_id){

        $db = JFactory::getDbo();

        $query = 'SELECT COUNT(DISTINCT(u.id)) '.
            'FROM text_users u '.
            'JOIN text_follows f ON (u.id=f.follower_id) '.
            'WHERE f.followed_id='.$user_id;

        $db->setQuery($query);
        $db->query();

        return $db->loadResult();
    }

    public function getUsersWhoIAmFollowing($user_id, $offset=0, $limit=16){

        $db = JFactory::getDbo();

        $query = 'SELECT DISTINCT(u.id), u.name '.
            'FROM text_users u '.
            'JOIN text_follows f ON (u.id=f.followed_id) '.
            'WHERE f.follower_id='.$user_id.
            ' ORDER BY u.name '.
            'LIMIT '.$offset.','.$limit;

        $db->setQuery($query);
        $db->query();

        return $db->loadObjectList();

    }

    public function getCountUsersWhoIAmFollowing($user_id){

        $db = JFactory::getDbo();

        $query = 'SELECT COUNT(DISTINCT(u.id)) '.
            'FROM text_users u '.
            'JOIN text_follows f ON (u.id=f.followed_id) '.
            'WHERE f.follower_id='.$user_id;

        $db->setQuery($query);
        $db->query();

        return $db->loadResult();

    }
	
	public function getUsersWhoIAmFollowingId($user_id){

        $db = JFactory::getDbo();

        $query = 'SELECT DISTINCT(f.followed_id) '.
            'FROM text_follows f '.
            'WHERE f.follower_id='.$user_id;

        $db->setQuery($query);
        $db->query();

        return $db->loadResultArray();

    }


    function truncate_words($text, $limit, $ellipsis = '...') {
        $words = preg_split("/[\n\r\t ]+/", $text, $limit + 1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);
        if (count($words) > $limit) {
            end($words); //ignore last element since it contains the rest of the string
            $last_word = prev($words);

            $text =  substr($text, 0, $last_word[1] + strlen($last_word[0])) . $ellipsis;
        }
        return $text;
    }

    function truncate_chars($text, $limit, $ellipsis = '...') {
        if( strlen($text) > $limit ) {
            $endpos = strpos(str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $text), ' ', $limit);
            if($endpos){
                $text = trim(substr($text, 0, $endpos)) . $ellipsis;
            }
            else{
                $text = trim(substr($text, 0, $limit)) . $ellipsis;
            }

        }
        return $text;
    }
	
	function getUserImage($user_id, $format="50", $alt, $style="", $noimage=""){
		$db = JFactory::getDbo();
		$sql = "SELECT profile_value FROM `text_user_profiles` where profile_key='profilepicture.file' and user_id =" . $user_id;
		$db->setQuery($sql);
        $result =  $db->loadResult();
		if(!empty($result) && ideary::existAllImagesOfUser($user_id)){

            $path = JURI::root().'templates/beez_20/images/user_profile/'.$user_id.'/'.$format.'/'.$result;

		} else{

            if ($noimage==""){
                $path= JURI::root().'media/com_socialloginandsocialshare/images/noimage.png';
            }else{
                $path=$noimage;
            }

		}

		$html = "<img src='".$path."' " . $style. "/>";
		return $html;
	}

	function getUserImagePath($user_id, $format='50'){
		$db = JFactory::getDbo();
		$sql = "SELECT profile_value FROM `text_user_profiles` where profile_key='profilepicture.file' and user_id =" . $user_id;
		$db->setQuery($sql);
        $result =  $db->loadResult();

        $relPath = "templates/beez_20/images/user_profile/$user_id/$format/$result";
        
		if (empty($result) || !file_exists($_SERVER["DOCUMENT_ROOT"] . "/$relPath" )) {
			$path = JURI::base() . 'media/com_socialloginandsocialshare/images/noimage.png';
		} else{
            $path = JURI::base() . $relPath;
		}
		return $path;
	}

    public function getUserImageName($userId){
        $db = JFactory::getDbo();
        $sql = 'SELECT up.profile_value FROM text_user_profiles up WHERE up.profile_key="profilepicture.file" AND up.user_id='.$userId;
        $db->setQuery($sql);
        $result = $db->loadResult();
        if(is_null($result)){
            return false;
        }
        else{
            return $result;
        }
    }

    public function existAllImagesOfUser($userId){
        $imageName = ideary::getUserImageName($userId);
        $userImg200 = 'templates/beez_20/images/user_profile/'.$userId.'/200/'.$imageName;
        $userImg50 = 'templates/beez_20/images/user_profile/'.$userId.'/50/'.$imageName;

        return (file_exists($userImg200) && file_exists($userImg50));
    }

    public function existAllImagesOfText($textId, $imageName){
        $textImgEdit = "templates/beez_20/images/texts/".$textId."/edit/".$imageName;
        $textImgHome = "templates/beez_20/images/texts/".$textId."/home/".$imageName;
        $textImgRanking = "templates/beez_20/images/texts/".$textId."/ranking/".$imageName;

        return (file_exists($textImgEdit) && file_exists($textImgHome) && file_exists($textImgRanking));
    }

	public function getUserPermission($permission,$user_id,$logged_user_id){
        $db = JFactory::getDbo();
        $sql = 'SELECT '.$permission.' FROM `text_user_config_notif` where user_id='.$user_id;
		//echo $sql;die;
        $db->setQuery($sql);
        $result = $db->loadResult();
		
		$devolver=false;
		if ($result=="2"){
			$devolver = true;
		}elseif($result==="1"){
			$sql2 = 'SELECT count(*) as cont FROM `text_follows` where followed_id= '.$user_id.' and follower_id='.$logged_user_id;
			$db->setQuery($sql2);
			$follows = $db->loadResult();
			if ($follows=="0"){
				$devolver = false;
			}else{
				$devolver = true;
			}
		}elseif($result==false){
			$devolver = false;
		}
		return $devolver;
    }

    public function deleteUserBgImage($userId){
        $db = JFactory::getDbo();
        $sql = 'UPDATE text_users SET background="" WHERE id='.$userId;
        $db->setQuery($sql);
        $result = $db->query();

        if($result){
            $dir = "templates/beez_20/images/user_backgrounds/".$userId."/";
            ideary::rrmdir($dir);
        }

        return $result;
    }


	public function getExtraUserData($userId){

        $db = JFactory::getDbo();
        $query = "SELECT profile_key,profile_value FROM `text_user_profiles` where user_id = " . $userId;
        $db->setQuery($query);
        return $db->loadObjectList();
    }
	public function getUserCantTexts($userId,$state="1"){
		$db = JFactory::getDbo();
		if ($state=="1"){
			$query = "SELECT count(*) as cont FROM #__content t  JOIN text_categories c ON (c.id=t.catid) WHERE (t.state = ".$state.") AND t.created_by = ".$userId;
		}else{
			$query = "SELECT count(*) as cont from text_content where state=".$state." and created_by = " . $userId;
		}
		//echo $query;die;
        $db->setQuery($query);
		$cant = $db->loadResult();
		return $cant;
    }
	public function getUserData($userId){

        $db = JFactory::getDbo();
        $query = "SELECT * FROM `text_users` where id = " . $userId;
        $db->setQuery($query);
        return $db->loadObjectList();
    }
	public function getCategories(){

        $lang_tag = JFactory::getLanguage()->getTag();
        $lang_code = substr($lang_tag, 0, 2);

        $db = JFactory::getDbo();
        $query = 'SELECT id, title_'.$lang_code.' title, color_code FROM text_categories where lang_label is not null';
        $db->setQuery($query);
        return $db->loadObjectList();
    }
	public function getUserInterests($user_id){
        $db = JFactory::getDbo();
        $query = "SELECT category_id as id FROM `text_interests` where user_id =" . $user_id;
        $db->setQuery($query);
        return $db->loadObjectList();
    }
	public function getUserNotifSettings($user_id){
        $db = JFactory::getDbo();
        $query = "SELECT * FROM `text_user_config_notif` where user_id =" . $user_id;
        $db->setQuery($query);
        return $db->loadObjectList();
    }
	
	public function saveInterests($userid,$intereses){
		
	 $db = JFactory::getDbo();

        $query = 'delete from `text_interests` where user_id = '.(int)$userid;
        $db->setQuery($query);
        $success1 = $db->query();
		
		foreach ($intereses as $int){
			$query = 'insert into `text_interests` (user_id,category_id) values(' .(int)$userid. ','.(int)$int. ')';
			$db->setQuery($query);
			$success1 = $db->query();		
		}
		return true;
	}

    public function saveBgImage($userId, $filename){
        $db = JFactory::getDbo();

        $query = 'UPDATE text_users SET background="'.$filename.'" WHERE id='.$userId;
        $db->setQuery($query);
        return $db->query();
    }

    public function saveProfileImage($userId, $filename){
        $db = JFactory::getDbo();

        $query = "SELECT COUNT(*) count_img FROM text_user_profiles WHERE user_id=".$userId." AND profile_key='profilepicture.file'";
        $db->setQuery($query);
        $hasImg = $db->loadObject()->count_img;

        if($hasImg){
            $query = "UPDATE text_user_profiles SET profile_value='".$filename."' WHERE user_id=".$userId." AND profile_key='profilepicture.file'";
            $db->setQuery($query);
        }
        else{
            $query = "INSERT INTO text_user_profiles (user_id, profile_key, profile_value, ordering) VALUES (".$userId.", 'profilepicture.file', '".$filename."', 1)";
            $db->setQuery($query);
        }

        return $db->query();
    }

	public function saveUserNotif($userId,$data_notif){
	
		
	 $db = JFactory::getDbo();

        $query = 'delete from `text_user_config_notif` where user_id = '.(int)$userId;
		
        $db->setQuery($query);
        $success1 = $db->query();

		/*if($data_notif["aplauso_texto"]=="on"){
			$data_notif["aplauso_texto"]=1;
		}else{
			$data_notif["aplauso_texto"]=0;
		}
		
		if($data_notif["nuevo_seguidor"]=="on"){
			$data_notif["nuevo_seguidor"]=1;
		}else{
			$data_notif["nuevo_seguidor"]=0;
		}
		if($data_notif["cambio_ranking"]=="on"){
			$data_notif["cambio_ranking"]=1;
		}else{
			$data_notif["cambio_ranking"]=0;
		}
		if($data_notif["texto_top"]=="on"){
			$data_notif["texto_top"]=1;
		}else{
			$data_notif["texto_top"]=0;
		}
		if($data_notif["comentarios"]=="on"){
			$data_notif["comentarios"]=1;
		}else{
			$data_notif["comentarios"]=0;
		}*/
		$query = 'insert into `text_user_config_notif` (user_id,clap,follow,ranking,text_top,comment,frequency,clap_texts_visib) 
		values(' .(int)$userId. ',' . $data_notif["aplauso_texto"] . ',' . $data_notif["nuevo_seguidor"] . ',' . $data_notif["cambio_ranking"] . ',' . $data_notif["texto_top"] . ',' . $data_notif["comentarios"] . ',' . $data_notif["frecuencia_email"] . ',' . $data_notif["visibilidad_texto"] .')';
		$db->setQuery($query);
		$success1 = $db->query();		
		return $success1;
	}
	public function getUserMessages($user_id){
	    $db = JFactory::getDbo();
        $query = "SELECT * FROM `text_messages` where user_id_to =" . $user_id;
        $db->setQuery($query);
        return $db->loadObjectList();
	}
	public function getUserMessagesSent($user_id){
	    $db = JFactory::getDbo();
        $query = "SELECT * FROM `text_messages` where user_id_from =" . $user_id;
        $db->setQuery($query);
        return $db->loadObjectList();
	}
	public function getUserName($user_id){
	    $db = JFactory::getDbo();
        $query = "SELECT name FROM `text_users` where id =" . $user_id;
        $db->setQuery($query);
        return $db->loadResult();
	}
	public function getUserFollowers($user_id){
	    $db = JFactory::getDbo();
        $query = "SELECT follower_id FROM `text_follows` where followed_id =" . $user_id;
        $db->setQuery($query);
        return $db->loadObjectList();
	}
	public function namefromuser($uid){
		$db = JFactory::getDbo();
        $query = "SELECT name FROM `text_users` where id =" . $uid;
        $db->setQuery($query);
		$name = $db->loadResult();
		return $name;
	}
	public function printOpenGraphContent($content_id){
		$db = JFactory::getDbo();
        $query = "SELECT title FROM `text_content` where id =" . $content_id;
        $db->setQuery($query);
		$title = $db->loadResult();
		
		$query = "SELECT introtext FROM `text_content` where id =" . $content_id;
        $db->setQuery($query);
		$introtext = $db->loadResult();
		
		$query = "SELECT image_name FROM `text_content_images` where text_id =" . $content_id;
        $db->setQuery($query);
		$image = $db->loadResult();
		//var_dump($image);die;
		$html="";
		if ($image!=""){
			$path= "http://".$_SERVER["HTTP_HOST"]. '/templates/beez_20/images/texts/'.$content_id.'/home/'.$image;
			$html.='<meta property="og:image" content="'.$path.'"/>';
		}else{
			$html.='<meta property="og:image" content="'."http://".$_SERVER["HTTP_HOST"]. "/images/logoideary.jpg" .'"/>';
		}
		$html.='<meta property="og:title" content="'.str_replace('"','',$title).'"/>';
		$html.='<meta property="og:description" content="'.str_replace('"', '', substr(strip_tags(html_entity_decode($introtext)),0,250)).'..."/>';
		
        return $html;	
	}
	public function getTextUnread($user_id){
		$db = JFactory::getDbo();
        $query = "SELECT count(*) as unread FROM `text_messages` where state=0 and user_id_to=".$user_id;
        $db->setQuery($query);
        return $db->loadResult();	
	}

	public function getTextTags($itemid,$canttags){
		$db = JFactory::getDbo();
        $query = "SELECT tt.text_id,t.name FROM `text_tags_texts` tt left join text_tags t on tt.tag_id = t.id where tt.text_id=" . $itemid . " limit " . $canttags;
        $db->setQuery($query);
        $result = $db->loadObjectList();
		$tags = Array();
		foreach ( $result as $tag){
			$tags[]=$tag->name;
		}
		$taglist = implode(",",$tags);
		return $taglist;
	}
	


    public function getMessagesOfFollowedUsersByUserId($userId){

        $db = JFactory::getDbo();

        $query = "SELECT m.message_id, m.date_time, m.state, m.message, u.id, u.name, u.username ".
            "FROM text_messages m ".
            "JOIN text_users u ON (u.id=m.user_id_from) ".
            "JOIN text_follows f ON (f.followed_id=u.id) ".
            "WHERE f.follower_id=".$userId." ".
            "ORDER BY m.date_time desc";

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function getMessagesOfNoFollowedUsersByUserId($userId){

        $db = JFactory::getDbo();

        $query = "SELECT m.message_id, m.date_time, m.state, m.message, u.id, u.name, u.username ".
            "FROM text_messages m ".
            "JOIN text_users u ON (u.id=m.user_id_from) ".
            "WHERE m.user_id_to=".$userId." AND u.id NOT IN (SELECT f.followed_id FROM text_follows f WHERE f.follower_id=".$userId.") ".
            "ORDER BY m.date_time desc";

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function getMessagesSentByUserId($userId){

        $db = JFactory::getDbo();

        $query = "SELECT m.message_id, m.date_time, m.state, m.message, u.id, u.name, u.username ".
            "FROM text_messages m ".
            "JOIN text_users u ON (u.id=m.user_id_to) ".
            "WHERE m.user_id_from=".$userId." ".
            "ORDER BY m.date_time desc";

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function setAllMailsAsReaded($userId){
        $db = JFactory::getDbo();
        $query = 'UPDATE text_messages SET state=1 WHERE user_id_to='.$userId;
        $db->setQuery($query);
        return $db->query();
    }

    public function getUsersForMsgCombo($search){
        $db = JFactory::getDbo();

        if($search != ""){
            $query = 'SELECT u.id, u.name FROM text_users u WHERE u.name LIKE "'.$search.'%"';
        }
        else{
            $query = 'SELECT u.id, u.name FROM text_users u';
        }

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function deleteMessage($messageId){
        $db = JFactory::getDbo();
        $query = 'DELETE FROM text_messages WHERE message_id='.$messageId;
        $db->setQuery($query);
        return $db->query();
    }

    public function deleteText($textId){
        $db = JFactory::getDbo();

        $query = 'DELETE FROM text_content WHERE id='.$textId;
        $db->setQuery($query);
        $success = $db->query();

        if($success){
            $query = 'DELETE FROM text_content_images WHERE text_id='.$textId;
            $db->setQuery($query);
            $db->query();

            $query = 'DELETE FROM text_tags_texts WHERE text_id='.$textId;
            $db->setQuery($query);
            $db->query();

            $query = 'DELETE FROM text_favorites WHERE text_id='.$textId;
            $db->setQuery($query);
            $db->query();

            $query = 'DELETE FROM text_applauses WHERE text_id='.$textId;
            $db->setQuery($query);
            $db->query();
        }

        return $success;
    }

    public function getMessageToUserFormContent($userId, $username, $msg){
        $html = '';
        $html .= '<div class="send-message">';
            $html .= '<div class="send-message-left">';
                $html .= '<div class="send-message-container">';

                    $html .= '<div class="send-message-to" style="width: 362px;">';
                        $html .= '<div class="send-message-to-user">';
                            $html .= '<div class="send-message-to-user-image">';
                                $html .= Ideary::getUserImage($userId, 50, null,'style="width: 24px; height: 24px;"' , null);
                            $html .= '</div>';
                            $html .= '<div class="send-message-to-user-name">'.$username.'</div>';
                            $html .= '<div class="send-message-to-user-delete"></div>';
                        $html .= '</div>';
                    $html .= '</div>';

                $html .= '</div>';

                $html .= '<textarea class="send-message-input" placeholder="'.JTEXT::_('WRITE_MESSAGE').'...">'.$msg.'</textarea>';

            $html .= '</div>';

            $html .= '<div class="send-message-right">';
                $html .= '<div class="edit-profile-button send-message-button" data-user-id="'.$userId.'">'.JTEXT::_('SEND').'</div>';
            $html .= '</div>';

            $html .= '<div style="clear: both;"></div>';
        $html .= '</div>';

        return $html;
    }

    public function getMessageToUserFormWithInputContent(){
        $html = '';
        $html .= '<div class="send-message">';
        $html .= '<div class="send-message-left">';
        $html .= '<div class="send-message-container">';

            $html .= '<input type="text" class="send-message-to-input" placeholder="'.JTEXT::_('ADD_RECIPIENTS').'...">';
            $html .= '<div style="clear: both;"></div>';
            $html .= '<div class="user-list"></div>';

        $html .= '</div>';

        $html .= '<textarea class="send-message-input" placeholder="'.JTEXT::_('WRITE_MESSAGE').'..."></textarea>';

        $html .= '</div>';

        $html .= '<div class="send-message-right">';
        $html .= '<div class="edit-profile-button send-message-button" data-user-id="0">'.JTEXT::_('SEND').'</div>';
        $html .= '</div>';

        $html .= '<div style="clear: both;"></div>';
        $html .= '</div>';

        return $html;
    }

    public function getMessageListContent($messageList){

        $html = '';
        if(count($messageList) > 0){
        $html .= '<div class="messages-list">';
        foreach($messageList as $inbox_message){

            $html .= '<div class="message-item" data-message-id="'.$inbox_message->message_id.'">';

                $html .= '<div class="message-user-img">';
                    $html .= Ideary::getUserImage($inbox_message->id, 50, null, null, null);
                $html .= '</div>';

                $html .= '<div class="message-container">';

                    $html .= '<div class="message-header">';
                        $html .= '<div class="message-title">'.str_replace('{USER}', '<span class="msg-user-link"><a href="index.php?option=com_contact&view=public&id='.$inbox_message->id.'">'.$inbox_message->name.'</a></span>', JTEXT::_('MESSAGE_RECEIVED_FROM')).'</div>';
                        $html .= '<div class="message-delete" title="'.JTEXT::_('DELETE_MESSAGE').'" data-message-id="'.$inbox_message->message_id.'"></div>';
                        $html .= '<div class="message-date">'.str_replace('{X}', '1', JTEXT::_('X_SECOND_AGO')).'</div>';
                        $html .= '<div style="clear: both;"></div>';
                    $html .= '</div>';

                    $html .= '<div class="message-content">'.$inbox_message->message.'</div>';

                    $html .= '<div class="block-user" title="'.JTEXT::_('BLOCK_USER').'"></div>';
                $html .= '</div>';

                $html .= '<div style="clear: both;"></div>';
                $html .= '</div>';

        }
        $html .= '</div>';

        }
        else{

        }

        return $html;

    }

    public function generateUsersForMsgCombo($users){

        $html = '';

        if(count($users)){
            foreach($users as $user){

                $html .= '<div class="user-item" data-user-id="'.$user->id.'" data-user-name="'.$user->name.'">';
                    $html .= '<div class="user-item-img">';
                        $html .= Ideary::getUserImage($user->id, 50, null,'style="width: 24px; height: 24px;"' , null);
                    $html .= '</div>';
                    $html .= '<div class="user-item-name">'.$user->name.'</div>';
                $html .= '</div>';
            }
        }
        else{
            $html .= 'No hay usuarios';
        }

        return $html;
    }

	public function getUserAndPass($token){
	
	}
    public function addTag($tag_id, $tag_name, $text_id){
        $db = JFactory::getDbo();

        $lang =& JFactory::getLanguage();
        $lang_code = $lang->getTag();

        $query = 'SELECT t.id '.
            'FROM text_tags t '.
            'JOIN text_languages l ON (t.language_id=l.lang_id) '.
            'WHERE l.lang_code="'.$lang_code.'" AND t.name="'.$tag_name.'"';

        $db->setQuery($query);
        $tag = $db->loadObject();
        $tag_id = $tag->id;

        if(is_null($tag_id)){
            $query = "INSERT INTO #__tags (name, language_id) VALUES ('".$tag_name."', (SELECT lang_id FROM #__languages WHERE lang_code='".$lang_code."'))";
            $db->setQuery($query);
            $db->query();
            $tag_id = $db->insertid();
        }

        $query = 'INSERT INTO #__tags_texts (tag_id, text_id) VALUES ('.(int) $tag_id.', '.(int) $text_id.')';
        $db->setQuery($query);
        $db->query();

        return $tag_id;
    }


    public function sendMessage($user_from, $user_to, $message){

        $db = JFactory::getDbo();

        $query = "INSERT INTO #__messages ( user_id_from, user_id_to, date_time, state, message )" .
            " VALUES ( ".(int) $user_from.", ".(int) $user_to.", '".date_create()->format('Y-m-d H:i:s')."', false, '".$message."' )";

        $db->setQuery($query);

        $db->query();

        return $db->insertid();
    }

    public function saveNotification($notification_type_id, $notified_id, $text_id='null', $user_id='null', $message_id='null'){

        $db = JFactory::getDbo();

        $query = "INSERT INTO #__notifications ( user_id, notified_id, text_id, message_id, notification_type_id, created_at )" .
            " VALUES ( ".(int) $user_id.", ".(int) $notified_id.", ".$text_id.", ".$message_id.",".$notification_type_id.", '".date_create()->format('Y-m-d H:i:s')."' )";

        $db->setQuery($query);

        return $db->query();

    }

    public function getNotification($user_id, $notification_type){

        $db = JFactory::getDbo();

        $query = "SELECT nt.id, ntu.send_email FROM #__notification_types_users AS ntu JOIN #__notification_types AS nt ON (nt.id = ntu.notification_type_id)".
            " WHERE ntu.user_id=".$user_id." AND nt.type='".$notification_type."'";

        $db->setQuery($query);

        $object = $db->loadObject();

        return $object;

    }

    public function getNotification2($user_id, $notification_type){

        $db = JFactory::getDbo();

        $query = "SELECT ".$notification_type." from text_user_config_notif WHERE user_id=".$user_id;

        $db->setQuery($query);

        $object = $db->loadObject();

        return $object;

    }
	
	
	public function getRecommendations($text_id, $user_id){
		
        $db = JFactory::getDbo();
		
        $query = "
				select text_applauses.text_id id, count(text_applauses.id) as THEMAGICK, text_content.title title, text_users.name username, text_content.created, catid
				from text_applauses
				join text_content on text_content.id = text_applauses.text_id
				join text_users on (text_users.id = text_content.created_by)
				where text_applauses.user_id in (
					select user_id from text_applauses
					where text_id = $text_id
					) 
					and text_id <> $text_id
					and text_users.id <> (select created_by from text_content where id = $text_id)
					and text_id not in (select text_id from text_applauses where user_id = $user_id)
				group by text_applauses.text_id
				order by THEMAGICK desc, text_content.created desc
				limit 1
			";

        $db->setQuery($query);

        $object = $db->loadObjectList();
		
		if (count($object) > 0)
			return $object;
		
		// if there are no applauses on which to calculate a recommendation, 
		// use the last published text that was applauded by this author.
		
		$query = "
				select A.text_id id, C.title title, U.name username, C.created, catid
				from text_applauses A
				join text_content C on (C.id = A.text_id)
				join text_users U on (U.id = C.created_by)
				where A.user_id in (select created_by from text_content where id = $text_id)
				order by C.created desc
				limit 1
				
			";

        $db->setQuery($query);

        $object = $db->loadObjectList();
		
		return $object;
		
        

    }
	
	public function registerDoppler($data){
	
		$apiKey = "7F4967762DFEE0851B7AA0A503F21543";
		$subscribersListId = "514273";
		
		$uri = "http://api2.fromdoppler.com";
		$contentType = "application/soap+xml; charset=utf-8";
		$body = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
		<soap12:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap12=\"http://www.w3.org/2003/05/soap-envelope\">
		  <soap12:Body>
			<Subscriber.AddwithName xmlns=\"http://api.fromdoppler.com\">
		<APIKey>".$apiKey."</APIKey>
			  <SubscribersListID>".$subscribersListId."</SubscribersListID>
			  <FirstName>".$data["name"]."</FirstName>
			  <LastName>doppler</LastName>
			  <EMail>".$data["email1"]."</EMail>
			</Subscriber.AddwithName>
		  </soap12:Body>
		</soap12:Envelope>";
		
		$headers = array(             
			"Content-type: application/soap+xml;charset=\"utf-8\"", 
			"Accept: text/xml", 
			"Cache-Control: no-cache", 
			"Pragma: no-cache", 
			"SOAPAction: \"run\"", 
			"Content-length: ".strlen($body),
		); 

		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL, $uri);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch,CURLOPT_POST,0);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		return $result;
	}
	
	
	public function emailBody($username,$site,$siteurlwithtoken,$siteurl){
	
		$html='<div id="mail-container" style="width: 600px; margin: 0 auto; background: #ffffff !important;">

            <div id="mail-logo" style="width: 154px; height: 37px; background: url(\'' . JURI::base() . 'images/mailing/sprite.png\') no-repeat 0px 0px; margin: 26px auto 0 auto;"></div>

            <div id="bg" style="width: 600px; position: relative; margin-top: 25px;">

                <div id="user-name-balloon" style="position: absolute; top: 0px; left: 0px; width: 100%; text-align: center;">

                    <div style="display: inline-block;">
                        <div id="user-name-balloon-left" style="float: left; background: url(\'' . JURI::base() . 'images/mailing/sprite.png\') 0px -58px no-repeat; width: 20px; height: 47px;"></div>
                        <div id="user-name-balloon-middle" style="float: left; background: url(\'' . JURI::base() . 'images/mailing/user-name-balloon-middle.png\') repeat-x; padding: 11px 0 15px 0; font-family: georgia; font-size: 21px; line-height: 21px; color: #ffffff; text-align: center;">Hola '.$username.',</div>
                        <div id="user-name-balloon-right" style="float: left; background: url(\'' . JURI::base() . 'images/mailing/sprite.png\') -21px -58px no-repeat; width: 10px; height: 47px;"></div>
                        <div style="clear: both;"></div>
                    </div>
                </div>

                <div id="bg-top" style="height: 25px; width: 600px; background: url(\'' . JURI::base() . 'images/mailing/bg-top.png\') no-repeat;"></div>

                <div id="bg-middle" style="width: 600px; background: url(\'' . JURI::base() . 'images/mailing/bg-middle.png\') repeat-y;">

                    <!-- Inicio - Esto es distinto en cada mail -->

                    <p style="margin: 0 !important; width: 500px; padding: 46px 50px 40px 50px; font-family: georgia; font-size: 14px; line-height: 28px; color: #898989; text-align: left;">Gracias por registrarte en '.$site.'!.<br/>Tu cuenta ha sido creada y se debe activar antes de poder usarla.</p>

                    <p style="margin: 0 !important; width: 500px; padding: 0 50px; font-family: georgia; font-size: 14px; line-height: 28px; color: #898989; text-align: left;">Para activar tu cuenta, hac&eacute; clic en el siguiente enlace o copi&aacute; y peg&aacute; en tu navegador:<br/><br/>'.$siteurlwithtoken.'<br/><br/>
					Despu&eacute;s de la activaci&oacute;n, inici&aacute; sesi&oacute;n en '.$siteurl.'</p>
                    <div style="text-align: center; padding: 25px 0 30px 0;">
                        <a href="'.$siteurl.'" target="_blank">
                            <div id="red-button" style="min-width: 170px; background: #bc5b5e; margin: 0 auto; behavior: url(border-radius.htc); -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px; font-family: georgia; font-size: 16px; line-height: 16px; color: #ffffff; text-align: center; padding: 14px 10px 15px 10px; display: inline-block;">Bot&oacute;n</div>
                        </a>
                    </div>
                    <!-- Fin - Esto es distinto en cada mail -->
                </div>

                <div id="bg-bottom" style="height: 40px; width: 600px; background: url(\'' . JURI::base() . 'images/mailing/bg-bottom.png\') no-repeat;"></div>
            </div>
            <div style="font-family: georgia; font-size: 11px; line-height: 14px; text-align: center; color: #bfbebe;">Si deseas no seguir recibi&eacute;ndo estos mensajes, pod&eacute;s <a href="#" style="text-decoration: none !important;"><span style="color: #7a877b;">desuscribirte.</span></a></div>
            <div style="font-family: georgia; font-size: 11px; line-height: 14px; text-align: center; color: #bfbebe;">Para m&aacute;s informaci&oacute;n, pod&eacute;s leer nuestros <a href="#" style="text-decoration: none !important;"><span style="color: #7a877b;">T&eacute;rminos y condiciones.</span></a></div>

            <div style="width: 85px; height: 21px; background: url(\'' . JURI::base() . 'images/mailing/sprite.png\') 0px -37px no-repeat; margin: 12px auto 0 auto;"></div>

            <div style="font-family: georgia; font-size: 11px; color: #bfbebe; text-align: center;">� 2013 | <a href="http://www.ideary.com.ar" target="_blank" style="text-decoration: none; color: #bfbebe;">www.ideary.com.ar</a></div>


        </div>';
		return $html;		
	}

	public function emailBodyTable($username, $site, $siteurlwithtoken, $siteurl){

        $hello = 'Hola '.$username;

        $html = '<table style="background:#fff; margin: 0; padding: 0;" width="100%" border="0" cellpadding="0" cellspacing="0" align="center">

        <tbody>

        <tr>
            <td align="center">
                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center">
                    <tbody>
                    <tr>
                        <td align="center" style="padding-top: 25px;">
                            <img src="' . JURI::base() . 'images/mailing/logo.png">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-top: 25px;">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0; width: 239px; height: 47px; background-repeat: no-repeat;" background="'. JURI::base() . 'images/mailing/recorte1.png"></td>

                                    <td style="max-width: 126px !important; width: 126px !important; height: 21px; background-repeat: repeat-x; padding: 11px 0 15px 0; width: 126px; font-family: georgia; font-size: 13px; line-height: 21px; color: #ffffff; text-align: center;" background="' . JURI::base() . 'images/mailing/recorte3.png" title="'.$hello.'">'.ideary::truncateText($hello, 11).',</td>

                                    <td style="padding: 0; width: 235px; height: 47px; background-repeat: no-repeat;" background="' . JURI::base() . 'images/mailing/recorte2.png"></td>
                                </tr>
                            </table>
                        </td>

                    </tr>

                    <tr>
                        <td>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="min-width: 20px; width: 20px; background-repeat: repeat-y;" background="' . JURI::base() . 'images/mailing/recorte4.png"></td>
                                    <td>
                                        <p style="min-width: 500px; max-width: 500px; width: 500px; word-break: break-all; padding: 21px 30px; font-family: georgia; font-size: 14px; line-height: 28px; text-align: left; color: #898989;">Gracias por registrarte en '.$site.'!.<br/>Tu cuenta ha sido creada y se debe activar antes de poder usarla.</p>
                                        <p style="min-width: 500px; max-width: 500px; width: 500px; word-break: break-all; padding: 21px 30px; font-family: georgia; font-size: 14px; line-height: 28px; text-align: left; color: #898989;">Para activar tu cuenta, hac&eacute; clic en el siguiente enlace o copi&aacute; y peg&aacute; en tu navegador:<br/>
                                        <br/><a style="word-break: break-all; text-decoration: none; color: #7a877b;" href="'.$siteurlwithtoken.'" target="_blank">'.$siteurlwithtoken.'</a><br/>
                                        <br/>
											Despu&eacute;s de la activaci&oacute;n, inici&aacute; sesi&oacute;n en <a style="text-decoration: none; color: #7a877b;" href="'.$siteurl.'" target="_blank">'.$siteurl.'</a></p>
                                    </td>
                                    <td style="min-width: 20px; width: 20px; background-repeat: repeat-y;" background="' . JURI::base() . 'images/mailing/recorte5.png"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="min-width: 20px; width: 20px; background-repeat: repeat-y;" background="' . JURI::base() . 'images/mailing/recorte4.png"></td>
                                    <td style="width: 185px;"></td>
                                    <td>
                                         <a href="'.$siteurlwithtoken.'" style="text-decoration: none; cursor: pointer;" target="_blank">
                                            <span style="display: block; behavior: url(border-radius.htc); border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; -khtml-border-radius: 4px; width: 190px; background: #bc5b5e; text-align: center; font-family: georgia; font-size: 16px; line-height: 16px; color: #ffffff; padding: 15px 0 14px 0;">
                                                Activar
                                            </span>
                                        </a>
                                    </td>
                                    <td style="width: 185px;"></td>
                                    <td style="min-width: 20px; width: 20px; background-repeat: repeat-y;" background="' . JURI::base() . 'images/mailing/recorte5.png"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr style="height: 50px;">
                        <td style="background-repeat: no-repeat; min-height: 50px; height: 50px !important; width: 600px; padding-top: 50px;" background="' . JURI::base() . 'images/mailing/recorte8.png"></td>
                    </tr>

                    <tr>
                        <td align="center">

                            <p style="word-break: break-word; margin: 0; font-family: georgia; font-size: 11px; line-height: 14px; color: #bfbebe;">Si deseas no seguir recibiendo estos mensajes, pod&eacute;s <a href="#" style="text-decoration: none;"><span style="color: #7a877b;">desuscribirte.</span></a></p>
                            <p style="word-break: break-word; margin: 0; font-family: georgia; font-size: 11px; line-height: 14px; color: #bfbebe;">Para m&aacute;s informaci&oacute;n, pod&eacute;s leer nuestros <a href="#" style="text-decoration: none;"><span style="color: #7a877b;">T&eacute;rminos y condiciones.</span></a></p>

                            <img src="' . JURI::base() . 'images/mailing/b.png" style="margin-top: 13px;">

                            <p style="word-break: break-word; margin: 3px 0 0 0; font-family: georgia; font-size: 11px; line-height: 14px; color: #bfbebe;">&copy; 2013 | <a style="text-decoration: none; color: #7a877b;" href="http://www.ideary.com.ar" target="_blank">www.ideary.com.ar</a></p>

                        </td>
                    </tr>

                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>';

		return $html;		
	}

	public function emailBodyTableForgotPass($token,$siteurl){
	
		$html='<table style="background:#fff;" width="100%" border="0" cellpadding="0" cellspacing="0" align="center">

        <tbody>

        <tr>
            <td align="center">
                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center">
                    <tbody>
                    <tr>
                        <td align="center" style="padding-top: 25px;">
                            <img src="' . JURI::base() . 'images/mailing/logo.png">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-top: 25px;">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0; width: 239px; height: 47px; background: url(' . JURI::base() . 'images/mailing/recorte1.png) no-repeat;"></td>

                                    <td style="width: 126px; height: 47px; background: url(' . JURI::base() . 'images/mailing/recorte3.png) repeat-x; padding: 11px 0 15px 0; width: 126px; font-family: georgia; font-size: 13px; line-height: 21px; color: #ffffff; text-align: center;">Hola!</td>

                                    <td style="padding: 0; width: 235px; height: 47px; background: url(' . JURI::base() . 'images/mailing/recorte2.png) no-repeat;"></td>
                                </tr>
                            </table>
                        </td>

                    </tr>

                    <tr>
                        <td>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 20px; background: url(' . JURI::base() . 'images/mailing/recorte4.png) repeat-y;"></td>
                                    <td>
                                        <p style="padding: 21px 30px; font-family: georgia; font-size: 14px; line-height: 28px; text-align: left; color: #898989;">Has solicitado reestablecer tu contrase&ntilde;a de Ideary.<br/><br/> Para restablecer su contrase&ntilde;a, tendr&aacute; que enviar el c&oacute;digo de verificaci&oacute;n con el fin de verificar que la solicitud es leg&iacute;tima</p>
                                        <p style="padding: 21px 30px; font-family: georgia; font-size: 14px; line-height: 28px; text-align: left; color: #898989;">El c&oacute;digo de verificaci&oacute;n es:  <b>'.$token.'</b><br/><br/>Hac&eacute; clic <a href="http://www.ideary.co/index.php?forgot=confirm">ac&aacute;</a> para introducir el c&oacute;digo de verificaci&oacute;n y poder restablecer tu contrase&ntilde;a.  <br/><br/>Gracias!
										</p>
                                    </td>
                                    <td style="width: 20px; background: url(' . JURI::base() . 'images/mailing/recorte5.png) repeat-y;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 205px; background: url(' . JURI::base() . 'images/mailing/recorte4.png) repeat-y left;"></td>
                                    <td>
                                         <a href="'.$siteurl.'" style="text-decoration: none; cursor: pointer;">
                                            <span style="display: block; behavior: url(border-radius.htc); border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; -khtml-border-radius: 4px; width: 190px; background: #bc5b5e; text-align: center; font-family: georgia; font-size: 16px; line-height: 16px; color: #ffffff; padding: 15px 0 14px 0;">
                                                Activar
                                            </span>
                                        </a>
                                    </td>
                                    <td style="width: 205px; background: url(' . JURI::base() . 'images/mailing/recorte5.png) repeat-y right;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background: url(' . JURI::base() . 'images/mailing/recorte8.png) no-repeat; height: 50px; width: 600px;"></td>
                    </tr>

                    <tr>
                        <td align="center">
                            <p style="margin: 0; font-family: georgia; font-size: 11px; line-height: 14px; color: #bfbebe;">Si deseas no seguir recibiendo estos mensajes, pod&eacute;s <a href="#" style="text-decoration: none;"><span style="color: #7a877b;">desuscribirte.</span></a></p>
                            <p style="margin: 0; font-family: georgia; font-size: 11px; line-height: 14px; color: #bfbebe;">Para m&aacute;s informaci&oacute;n, pod&eacute;s leer nuestros <a href="#" style="text-decoration: none;"><span style="color: #7a877b;">T&eacute;rminos y condiciones.</span></a></p>

                            <img src="' . JURI::base() . 'images/mailing/b.png" style="margin-top: 13px;">

                            <p style="margin: 3px 0 0 0; font-family: georgia; font-size: 11px; line-height: 14px; color: #bfbebe;">� 2013 | www.ideary.com.ar</p>

                        </td>
                    </tr>

                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>';
		return $html;		
	}

    public function getAccuserUser($accuserId){

        $db = JFactory::getDbo();

        $query = "SELECT id, name FROM text_users WHERE id=".$accuserId;

        $db->setQuery($query);

        return $db->loadObject();
    }

    public function getDenouncedText($textId){

        $db = JFactory::getDbo();

        $query = "SELECT c.id text_id, title, u.id user_id, u.name ".
                 "FROM text_content c ".
                 "JOIN text_users u ON (c.created_by=u.id) ".
                 "WHERE c.id=".$textId;

        $db->setQuery($query);

        return $db->loadObject();
    }

    public function denounceEmailBodyTable($accuser, $denouncedText){

        $hello = 'Hola Admin';

        $html = '<table style="background:#fff; margin: 0; padding: 0;" width="100%" border="0" cellpadding="0" cellspacing="0" align="center">

        <tbody>

        <tr>
            <td align="center">
                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center">
                    <tbody>
                    <tr>
                        <td align="center" style="padding-top: 25px;">
                            <img src="' . JURI::base() . 'images/mailing/logo.png">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-top: 25px;">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0; width: 239px; height: 47px; background-repeat: no-repeat;" background="'. JURI::base() . 'images/mailing/recorte1.png"></td>

                                    <td style="max-width: 126px !important; width: 126px !important; height: 21px; background-repeat: repeat-x; padding: 11px 0 15px 0; width: 126px; font-family: georgia; font-size: 13px; line-height: 21px; color: #ffffff; text-align: center;" background="' . JURI::base() . 'images/mailing/recorte3.png" title="'.$hello.'">'.ideary::truncateText($hello, 11).',</td>

                                    <td style="padding: 0; width: 235px; height: 47px; background-repeat: no-repeat;" background="' . JURI::base() . 'images/mailing/recorte2.png"></td>
                                </tr>
                            </table>
                        </td>

                    </tr>

                    <tr>
                        <td>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="min-width: 20px; width: 20px; background-repeat: repeat-y;" background="' . JURI::base() . 'images/mailing/recorte4.png"></td>
                                    <td>
                                        <p style="min-width: 500px; max-width: 500px; width: 500px; word-break: break-all; padding: 21px 30px; font-family: georgia; font-size: 14px; line-height: 28px; text-align: left; color: #898989;"><a href="'.JRoute::_(JURI::base().'index.php?option=com_contact&view=public&id='.$accuser->id).'" target="_blank" style="font-weight: bold;text-decoration: none;color: #898989;">'.$accuser->name.'</a> ha denunciado el texto <a href="'.JRoute::_(JURI::base().'index.php?option=com_content&view=article&id='.$denouncedText->text_id).'" target="_blank" style="font-weight: bold;text-decoration: none;color: #898989;">"'.$denouncedText->title.'"</a> escrito por <a href="'.JRoute::_(JURI::base().'index.php?option=com_contact&view=public&id='.$denouncedText->user_id).'" target="_blank" style="font-weight: bold;text-decoration: none;color: #898989;">'.$denouncedText->name.'</a></p>

                                    </td>
                                    <td style="min-width: 20px; width: 20px; background-repeat: repeat-y;" background="' . JURI::base() . 'images/mailing/recorte5.png"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="min-width: 20px; width: 20px; background-repeat: repeat-y;" background="' . JURI::base() . 'images/mailing/recorte4.png"></td>
                                    <td style="width: 185px;"></td>
                                    <td>
                                         <a href="'.JRoute::_(JURI::base().'index.php?option=com_content&view=article&id='.$denouncedText->text_id).'" style="text-decoration: none; cursor: pointer;" target="_blank">
                                            <span style="display: block; behavior: url(border-radius.htc); border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; -khtml-border-radius: 4px; width: 190px; background: #bc5b5e; text-align: center; font-family: georgia; font-size: 16px; line-height: 16px; color: #ffffff; padding: 15px 0 14px 0;">
                                                Ver texto
                                            </span>
                                        </a>
                                    </td>
                                    <td style="width: 185px;"></td>
                                    <td style="min-width: 20px; width: 20px; background-repeat: repeat-y;" background="' . JURI::base() . 'images/mailing/recorte5.png"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr style="height: 50px;">
                        <td style="background-repeat: no-repeat; min-height: 50px; height: 50px !important; width: 600px; padding-top: 50px;" background="' . JURI::base() . 'images/mailing/recorte8.png"></td>
                    </tr>

                    <tr>
                        <td align="center">

                            <p style="word-break: break-word; margin: 0; font-family: georgia; font-size: 11px; line-height: 14px; color: #bfbebe;">Si deseas no seguir recibiendo estos mensajes, pod&eacute;s <a href="#" style="text-decoration: none;"><span style="color: #7a877b;">desuscribirte.</span></a></p>
                            <p style="word-break: break-word; margin: 0; font-family: georgia; font-size: 11px; line-height: 14px; color: #bfbebe;">Para m&aacute;s informaci&oacute;n, pod&eacute;s leer nuestros <a href="#" style="text-decoration: none;"><span style="color: #7a877b;">T&eacute;rminos y condiciones.</span></a></p>

                            <img src="' . JURI::base() . 'images/mailing/b.png" style="margin-top: 13px;">

                            <p style="word-break: break-word; margin: 3px 0 0 0; font-family: georgia; font-size: 11px; line-height: 14px; color: #bfbebe;">&copy; 2013 | <a style="text-decoration: none; color: #7a877b;" href="http://www.ideary.com.ar" target="_blank">www.ideary.com.ar</a></p>

                        </td>
                    </tr>

                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>';

        return $html;
    }

    function cleanImageName($name) {
        $path_parts = pathinfo($name);
        $extension = $path_parts['extension'];

        $filename = $path_parts['filename'];
        $filename = str_replace(" ", "-", $filename); // Replaces all spaces with hyphens.
        $filename = str_replace("_", "-", $filename); // Replaces all _ with hyphens.
        $filename = preg_replace('/[^A-Za-z0-9\-]/', '', $filename); // Removes special chars.
        return preg_replace('/-+/', '-', $filename).'.'.$extension; // Replaces multiple hyphens with single one.
    }

    function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
    }

    function createSlug($text) {

        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        $table = array(
            'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
            'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
            'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
            'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
            'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', '/' => '-', ' ' => '-'
        );

        $text = strtr($text, $table);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;

    }

    public function setLoginErrorCodeInSession($username, $password){

        $db = JFactory::getDbo();

        $query = 'SELECT id, password, block FROM text_users WHERE username='.$db->Quote($username).' OR email='.$db->Quote($username);
        $db->setQuery($query);

        $user = $db->loadObject();

        session_start();
        //login exitoso
        $_SESSION["login-error-code"] = 0;

        if ($user) {
            $parts	= explode(':', $user->password);
            $crypt	= $parts[0];
            $salt	= @$parts[1];
            $testcrypt = JUserHelper::getCryptedPassword($password, $salt);

            if ($crypt == $testcrypt) {

                if($user->block == 1){
                    //login: usuario sin activar
                    $_SESSION["login-error-code"] = 3;
                }


            } else {
                //login: password invalida
                $_SESSION["login-error-code"] = 1;
            }
        } else {
            //login: usuario inexistente
            $_SESSION["login-error-code"] = 2;
        }
    }
	
	
	public function logUserExperience($feature, $user, $version) {
		
		$db = JFactory::getDbo();
		
		$query = "insert into #__user_experience (feature, user, date, version) values($feature, $user, now(), $version)";
		$db->setQuery($query);
		$db->query();
		
	}
	
	public function getUsersForCommentTagging(){

        $db = JFactory::getDbo();

        $query = "SELECT u.id, u.name " .
            "FROM text_users u " .
			"WHERE u.lastvisitDate > 0";

        $db->setQuery($query);
        $users = $db->loadObjectList();

        return $users;
		
    }

}


class UserExperience {
	const Stumble = 1;
	const EditProfile = 2;
	const Ranking = 3;
	const CommentTagging = 4;
}

class ShmopCache {

	public function save_cache($data, $name, $timeout) {
		// delete cache
		$id=shmop_open(ShmopCache::get_cache_id($name), "a", 0, 0);
		shmop_delete($id);
		shmop_close($id);
		
		// get id for name of cache
		$id=shmop_open(ShmopCache::get_cache_id($name), "c", 0644, strlen(serialize($data)));
		
		// return int for data size or boolean false for fail
		if ($id) {
			ShmopCache::set_timeout($name, $timeout);
			return shmop_write($id, serialize($data), 0);
		}
		else return false;
	}

	public function get_cache($name) {
		if (!ShmopCache::check_timeout($name)) {
			$id=shmop_open(ShmopCache::get_cache_id($name), "a", 0, 0);

			if ($id) 
                $data=unserialize(shmop_read($id, 0, shmop_size($id)));
			else 
                return false;          // failed to load data

			if ($data) {                // array retrieved
				shmop_close($id);
				return $data;
			}
			else return false;          // failed to load data
		}
		else return false;              // data was expired
	}

	public function get_cache_id($name) {
		// maintain list of caches here
		$id=array(  'home' => 1,
					'test2' => 2
					);

		return $id[$name];
	}

	public function set_timeout($name, $int) {
		$timeout=new DateTime(date('Y-m-d H:i:s'));
		date_add($timeout, date_interval_create_from_date_string("$int seconds"));
		$timeout=date_format($timeout, 'YmdHis');

		$id=shmop_open(100, "a", 0, 0);
		if ($id) $tl=unserialize(shmop_read($id, 0, shmop_size($id)));
		else $tl=array();
		shmop_delete($id);
		shmop_close($id);

		$tl[$name]=$timeout;
		$id=shmop_open(100, "c", 0644, strlen(serialize($tl)));
		shmop_write($id, serialize($tl), 0);
	}

	public function check_timeout($name) {
		$now=new DateTime(date('Y-m-d H:i:s'));
		$now=date_format($now, 'YmdHis');

		$id=shmop_open(100, "a", 0, 0);
		if ($id) $tl=unserialize(shmop_read($id, 0, shmop_size($id)));
		else return true;
		shmop_close($id);

		$timeout=$tl[$name];
		return (intval($now)>intval($timeout));
	}

    public function clearCache($name) {
        ShmopCache::save_cache(null, $name);
    }

}