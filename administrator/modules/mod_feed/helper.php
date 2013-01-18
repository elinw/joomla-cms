<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_feed
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_feed
 *
 * @package     Joomla.Administrator
 * @subpackage  mod_feed
 * @since       1.5
 */
class modFeedHelper
{
	/**
	 * Get a feed.
	 *
	 * @param   JRegistry  $params  Parameters.
	 *
	 * @return JFeedReader|null|string
	 */
	static function getFeed($params)
	{
		// Module params
		$rssurl	= $params->get('rssurl');

		$msg = '';
		$rssDoc = null;

		try
		{
			jimport('joomla.feed.factory');
			$feed = new JFeedFactory;
			$rssDoc = $feed->getFeed($rssurl);
		}
		catch (InvalidArgumentException $e)
		{
			$msg = JText::_('MOD_NEWSFEEDS_ERRORS_FEED_NOT_RETRIEVED');
		}
		catch (RunTimeException $e)
		{
			$msg = JText::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
		}

		if (empty($rssDoc))
		{
			$msg = JText::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
		}

		if ($msg)
		{
			return $msg;
		}

		if ($rssDoc)
		{
			return $rssDoc;
		}
	}
}
