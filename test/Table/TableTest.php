<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Table;

use Windwalker\Table\Table;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Table\Table
 *
 * @since {DEPLOY_VERSION}
 */
class TableTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 */
	public static function setUpBeforeClass()
	{
		$db = \JFactory::getDbo();
		$sqls = file_get_contents(__DIR__ . '/sql/install.sql');

		foreach ($db->splitSql($sqls) as $sql)
		{
			$sql = trim($sql);

			if (!empty($sql))
			{
				$db->setQuery($sql)->execute();
			}
		}
	}

	/**
	 * This method is called after the last test of this test class is run.
	 */
	public static function tearDownAfterClass()
	{
		$sql = file_get_contents(__DIR__ . '/sql/uninstall.sql');

		\JFactory::getDbo()->setQuery($sql)->execute();
	}

	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Table\Table::__construct
	 */
	public function test__construct()
	{
		$tableName = '#__test_table';
		$table = new Table($tableName);

		$this->assertEquals($tableName, TestHelper::getValue($table, '_tbl'));
		$this->assertEquals(array('id'), TestHelper::getValue($table, '_tbl_keys'));
		$this->assertSame(\JFactory::getDbo(), $table->getDbo());

		$tableName = '#__test_table2';
		$db = $this->getMockBuilder('JDatabaseDriver')
			->disableOriginalConstructor()->getMock();

		// Just return something to make getFields() no crash.
		$db->expects($this->once())
			->method('getTableColumns')
			->willReturn(array('#__test_table2' => true));

		$table = new Table($tableName, 'pk', $db);

		$this->assertEquals($tableName, TestHelper::getValue($table, '_tbl'));
		$this->assertEquals(array('pk'), TestHelper::getValue($table, '_tbl_keys'));
		$this->assertSame($db, $table->getDbo());
	}

	/**
	 * Method to test store().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Table\Table::store
	 */
	public function testStore()
	{
		$tableName = '#__test_table2';
		$table = new Table($tableName);

		$table->bar = 'foo';
		$table->params = array('foo' => 'bar');
		$table->store();

		$this->assertEquals('{"foo":"bar"}', $table->params);
	}
}
