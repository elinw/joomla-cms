<?php
/**
 * @package	    Joomla.UnitTest
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license	    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JHelperContenthistory.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Helper
 * @since       3.2
 */
class JHelperContenthistoryTest extends TestCaseDatabase
{
	/**
	 * @var    JHelperContenthistory
	 * @since  3.2
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new JHelperContenthistory;
		JFactory::$application = $this->getMockApplication();
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 *
	 * @since   3.2
	 */
	protected function getDataSet()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');

		$dataSet->addTable('jos_users', JPATH_TEST_DATABASE . '/jos_users.csv');
		$dataSet->addTable('jos_content', JPATH_TEST_DATABASE . '/jos_content.csv');
		$dataSet->addTable('jos_ucm_base', JPATH_TEST_DATABASE . '/jos_ucm_history.csv');

		return $dataSet;
	}

	/**
	 * deleteHistory data
	 *
	 * @return  array
	 *
	 * @since   3.2
	 */
	public function deleteHistoryProvider()
	{
		return array(
				array('Exists' => 1, true),
				array('Does not exist' => 500, false),
		);
	}

	/**
	 * Tests the deleteHistory() method
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @dataProvider  deleteHistoryProvider
	 */
	public function testDeleteHistory($pk, $expected)
	{
		$table = JTable::getInstance(Content,JTable);
		$table->load($pk);
		$deleteResult = $this->object->deleteHistory($table);
		$this->assertEquals($deleteResult, $expected);
	}


	/**
	 * getHistory data
	 *
	 * @return  array
	 *
	 * @since   3.2
	 */
	public function getHistoryProvider()
	{
		return array(
				// pk, typeid, result, count of objectlist
				array('One version' => 19, 6, 1, 6),
				array('Multiple versions' => 1,1, 3, 9),
				array('Does not exist' => 500, 1, false, null),
		);
	}


	/**
	 * Tests the getHistory method
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function getHistory($pk, $typeId, $count, $firstRowId)
	{
		$return = $this->object->getHistory($typeId, $pk);
		$this->assertEquals(count($return), $count);

		$table = JTable::getInstance('Contenthistory', 'JTable');
		$firstRowHash = $table->load($firstRowId)->sha1_hash;
		$this->assertEquals($return[0]->sha1_hash, $firstRowHash);
	}

	/**
	 * Tests the store() method
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function store()
	{
		$this->markTestSkipped('Test not implemented.');
	}
}
