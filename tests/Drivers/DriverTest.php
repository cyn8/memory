<?php namespace Orchestra\Memory\Tests\Drivers;

class DriverTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Get Mock instance 1.
	 * 
	 * @return MemoryDriverStub
	 */
	protected function getMockInstance1()
	{
		$mock = new MemoryDriverStub;
		$mock->put('foo.bar', 'hello world');
		$mock->put('username', 'laravel');

		return $mock;
	}

	/**
	 * Get Mock instance 2.
	 *
	 * @return MemoryDriverStub
	 */
	protected function getMockInstance2()
	{
		$mock = new MemoryDriverStub;
		$mock->put('foo.bar', 'hello world');
		$mock->put('username', 'laravel');
		$mock->put('foobar', function ()
		{
			return 'hello world foobar';
		});
		
		$mock->get('hello.world', function () use ($mock)
		{
			return $mock->put('hello.world', 'HELLO WORLD');
		});

		return $mock;
	}

	/**
	 * Test Orchestra\Memory\Drivers\Driver::initiate()
	 *
	 * @test
	 * @group support
	 */
	public function testInitiateMethod()
	{
		$stub = new MemoryDriverStub;
		$this->assertTrue($stub->initiated);
	}

	/**
	 * Test Orchestra\Memory\Drivers\Driver::shutdown()
	 *
	 * @test
	 * @group support
	 */
	public function testShutdownMethod()
	{
		$stub = new MemoryDriverStub;
		$this->assertFalse($stub->shutdown);
		$stub->shutdown();
		$this->assertTrue($stub->shutdown);
	}

	/**
	 * Test Orchestra\Memory\Drivers\Driver::get() method.
	 *
	 * @test
	 * @group support
	 */
	public function testGetMethod()
	{
		$mock1 = $this->getMockInstance1();
		$mock2 = $this->getMockInstance2();
		
		$this->assertEquals(array('bar' => 'hello world'), $mock1->get('foo'));
		$this->assertEquals('hello world', $mock1->get('foo.bar'));
		$this->assertEquals('laravel', $mock1->get('username'));
		
		$this->assertEquals(array('bar' => 'hello world'), $mock2->get('foo'));
		$this->assertEquals('hello world', $mock2->get('foo.bar'));
		$this->assertEquals('laravel', $mock2->get('username'));
		
		$this->assertEquals('hello world foobar', $mock2->get('foobar'));
		$this->assertEquals('HELLO WORLD', $mock2->get('hello.world'));
	}

	/**
	 * Test Orchestra\Memory\Drivers\Driver::put() method.
	 *
	 * @test
	 * @group support
	 */
	public function testPutMethod()
	{
		$stub = new MemoryDriverStub;

		$refl = new \ReflectionObject($stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$this->assertEquals(array(), $data->getValue($stub));

		$stub->put('foo', 'foobar');

		$this->assertEquals(array('foo' => 'foobar'), $data->getValue($stub));
	}

	/**
	 * Test Orchestra\Memory\Drivers\Driver::forget() method.
	 *
	 * @test
	 * @group support
	 */
	public function testForgetMethod()
	{
		$mock = $this->getMockInstance2();
		$mock->forget('hello.world');

		$this->assertEquals(array(), $mock->get('hello'));
	}
}

class MemoryDriverStub extends \Orchestra\Memory\Drivers\Driver {

	public $initiated = false;
	public $shutdown  = false;

	public function initiate() 
	{
		$this->initiated = true;
	}

	public function shutdown() 
	{
		$this->shutdown = true;
	}
}