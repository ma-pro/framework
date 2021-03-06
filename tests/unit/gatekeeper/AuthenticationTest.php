<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\unit\gatekeeper;

use mako\gatekeeper\adapters\AdapterInterface;
use mako\gatekeeper\Authentication;
use mako\tests\TestCase;
use Mockery;

/**
 * @group unit
 */
class AuthenticationTest extends TestCase
{
	/**
	 *
	 */
	public function testConstructorWithFactory(): void
	{
		$factory = function()
		{
			$adapter = Mockery::mock(AdapterInterface::class);

			$adapter->shouldReceive('hello')->once();

			return $adapter;
		};

		$authentication = new Authentication('foobar', $factory);

		$authentication->hello();
	}

	/**
	 *
	 */
	public function testConstructorWithInstance(): void
	{
		$adapter = Mockery::mock(AdapterInterface::class);

		$adapter->shouldReceive('getName')->once()->andReturn('foobar');

		$adapter->shouldReceive('hello')->once();

		$authentication = new Authentication($adapter);

		$authentication->hello();
	}

	/**
	 *
	 */
	public function testExtendWithFactory(): void
	{
		$adapter = Mockery::mock(AdapterInterface::class);

		$adapter->shouldReceive('getName')->once()->andReturn('foobar');

		$authentication = new Authentication($adapter);

		$authentication->extend('barfoo', function()
		{
			$adapter = Mockery::mock(AdapterInterface::class);

			$adapter->shouldReceive('getName')->once()->andReturn('barfoo');

			return $adapter;
		});

		$this->assertSame('barfoo', $authentication->adapter('barfoo')->getName());
	}

	/**
	 *
	 */
	public function testExtendWithInstance(): void
	{
		$adapter = Mockery::mock(AdapterInterface::class);

		$adapter->shouldReceive('getName')->once()->andReturn('foobar');

		$authentication = new Authentication($adapter);

		$adapter = Mockery::mock(AdapterInterface::class);

		$adapter->shouldReceive('getName')->twice()->andReturn('barfoo');

		$authentication->extend($adapter);

		$this->assertSame('barfoo', $authentication->adapter('barfoo')->getName());
	}

	/**
	 *
	 */
	public function testExtendWithInstanceAndNewDefault(): void
	{
		$adapter = Mockery::mock(AdapterInterface::class);

		$adapter->shouldReceive('getName')->once()->andReturn('foobar');

		$authentication = new Authentication($adapter);

		$adapter = Mockery::mock(AdapterInterface::class);

		$adapter->shouldReceive('getName')->twice()->andReturn('barfoo');

		$authentication->extend($adapter)->useAsDefaultAdapter('barfoo');

		$this->assertSame('barfoo', $authentication->getName());
	}
}
