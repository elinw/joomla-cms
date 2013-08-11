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
 * Log message writer for printing to output streams.
 *
 * @package     Joomla.Libraries
 * @subpackage  Log
 * @since       3.2
 */
class JLogWriterPrint extends JLogWriter
{
	/**
	 * @var    string  The format string to use in printing log messages.
	 * @since  3.2
	 */
	protected $format = "%s: %s [%s]\n";

	/**
	 * @var    boolean  True to print messages to STDERR, otherwise use STDOUT or the output buffer.
	 * @since  3.2
	 */
	protected $useStdErr = true;

	/**
	 * Constructor.
	 *
	 * @param   string   $format     The format string to use in printing log messages.  Three string values are passed into the format: priority,
	 *                               message and category respectively.
	 * @param   boolean  $useStdErr  True to print messages to STDERR, otherwise use STDOUT or the output buffer.
	 *
	 * @since   3.2
	 */
	public function __construct($format = "%s: %s [%s]\n", $useStdErr = true)
	{
		$this->format = (string) $format;
		$this->useStdErr = (bool) $useStdErr;
	}

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
	public function write(Message $message)
	{
		// Web context doesn't have STDOUT or STDERR defined.
		if (!defined('STDOUT'))
		{
			echo sprintf($this->format, $this->priorities[$message->priority], $message->message, $message->category);
		}

		if ($this->useStdErr && defined('STDERR'))
		{
			fprintf(STDERR, $this->format, $this->priorities[$message->priority], $message->message, $message->category);
		}
		else
		{
			fprintf(STDOUT, $this->format, $this->priorities[$message->priority], $message->message, $message->category);
		}
	}
}
