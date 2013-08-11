<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Abstract class for log writers.
 *
 * @package     Joomla.Libraries
 * @subpackage  Log
 * @since       3.2
 */
abstract class JLogWriter
{
	/**
	 * @var    array  Translation array for Message priorities to text strings.
	 * @since  3.2
	 */
	protected $priorities = array(
		Message::EMERGENCY => 'EMERGENCY',
		Message::ALERT => 'ALERT',
		Message::CRITICAL => 'CRITICAL',
		Message::ERROR => 'ERROR',
		Message::WARNING => 'WARNING',
		Message::NOTICE => 'NOTICE',
		Message::INFO => 'INFO',
		Message::DEBUG => 'DEBUG'
	);

	/**
	 * Write a Message.
	 *
	 * @param   Message  $message  The Message object to write.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @throws  RuntimeException
	 */
	abstract public function write(Message $message);
}
