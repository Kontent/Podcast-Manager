<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');

/**
 * Routing helper class.
 *
 * @package		Podcast Manager
 * @subpackage	com_podcastmanager
 * @since		1.8
 */
abstract class PodcastManagerHelperRoute
{
	protected static $lookup;

	/**
	 * Method to get the route to the feed edit view
	 *
	 * @param	int		$id		The id of the feed.
	 * @param	string	$return	The return page variable.
	 *
	 * @return	string	$link	The link to the item
	 * @since	1.8
	 */
	public static function getFeedEditRoute($id, $return = null)
	{
		// Create the link.
		$link = 'index.php?option=com_podcastmanager&task=form.edit&layout=edit&feedname='.$id;

		if ($return) {
			$link .= '&return='.$return;
		}

		return $link;
	}

	/**
	 * Method to get the route to the selected podcast
	 *
	 * @param	int		$id		The id of the podcast
	 *
	 * @return	string	$link	The link to the item
	 * @since	1.8
	 */
	public static function getPodcastRoute($id)
	{
		$needles = array(
			'podcast'  => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_podcastmanager&view=podcast&id='. $id;

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		else if ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	/**
	 * Method to get the route to the podcast edit view
	 *
	 * @param	int		$id		The id of the podcast.
	 * @param	string	$return	The return page variable.
	 *
	 * @return	string	$link	The link to the item
	 * @since	1.8
	 */
	public static function getPodcastEditRoute($id, $return = null)
	{
		// Create the link.
		$link = 'index.php?option=com_podcastmanager&task=podcast.edit&layout=edit&p_id='.$id;

		if ($return) {
			$link .= '&return='.$return;
		}

		return $link;
	}

	/**
	 * Method to lookup whether the item is within the menu structure
	 *
	 * @param	array	$needles	The menu items.
	 *
	 * @return	various
	 * @since	1.8
	 */
	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null) {
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_podcastmanager');
			$items		= $menus->getItems('component_id', $component->id);
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view'])) {
					$view = $item->query['view'];

					if (!isset(self::$lookup[$view])) {
						self::$lookup[$view] = array();
					}

					if (isset($item->query['id'])) {
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
				}
			}
		}

		if ($needles) {
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view])) {
					foreach($ids as $id)
					{
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		}
		else {
			$active = $menus->getActive();
			if ($active) {
				return $active->id;
			}
		}

		return null;
	}
}