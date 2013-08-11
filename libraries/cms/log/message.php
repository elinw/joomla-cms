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
 * Log message class.
 *
 * @package     Joomla.Libraries
 * @subpackage  Log
 * @since       3.2
 */
class JLogMessage
{
	/**
	 * @var    integer  All log priorities.
	 * @since  3.2
	 */
	const ALL = 30719;

	/**
	 * @var    integer  The system is unusable.
	 * @since  3.2
	 */
	const EMERGENCY = 1;

	/**
	 * @var    integer  Action must be taken immediately.
	 * @since  3.2
	 */
	const ALERT = 2;

	/**
	 * @var    integer  Critical conditions.
	 * @since  3.2
	 */
	const CRITICAL = 4;

	/**
	 * @var    integer  Error conditions.
	 * @since  3.2
	 */
	const ERROR = 8;

	/**
	 * @var    integer  Warning conditions.
	 * @since  3.2
	 */
	const WARNING = 16;

	/**
	 * @var    integer  Normal, but significant condition.
	 * @since  3.2
	 */
	const NOTICE = 32;

	/**
	 * @var    integer  Informational message.
	 * @since  3.2
	 */
	const INFO = 64;

	/**
	 * @var    integer  Debugging message.
	 * @since  3.2
	 */
	const DEBUG = 128;

	/**
	 * @var    string  Application responsible for log entry.
	 * @since  3.2
	 */
	public $category;

	/**
	 * @var    Date  The date the message was logged.
	 * @since  3.2
	 */
	public $date;

	/**
	 * @var    string  Message to be logged.
	 * @since  3.2
	 */
	public $message;

	/**
	 * @var    string  The priority of the message to be logged.
	 * @since  3.2
	 * @see    $priorities
	 */
	public $priority = self::INFO;

	/**
	 * @var    array  List of available log priority levels [Based on the Syslog default levels].
	 * @since  3.2
	 */
	protected $priorities = array(
		self::EMERGENCY,
		self::ALERT,
		self::CRITICAL,
		self::ERROR,
		self::WARNING,
		self::NOTICE,
		self::INFO,
		self::DEBUG
	);

	/**
	 * Constructor
	 *
	 * @param   string  $message   The message to log.
	 * @param   string  $priority  Message priority based on {$this->priorities}.
	 * @param   string  $category  Type of entry
	 * @param   string  $date      Date of entry (defaults to now if not specified or blank)
	 *
	 * @since   3.2
	 */
	public function __construct($message, $priority = self::INFO, $category = '', $date = null)
	{
		$this->message = (string) $message;

		// Sanitize the priority.
		if (!in_array($priority, $this->priorities, true))
		{
			$priority = self::INFO;
		}
		$this->priority = $priority;

		// Sanitize category if it exists.
		if (!empty($category))
		{
			$this->category = (string) strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $category));
		}

		// Get the date as a Date object.
		$this->date = new Date($date ? $date : 'now');
	}
}
