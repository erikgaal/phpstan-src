<?php // lint >= 8.1

namespace EnumTypeAssertions;

use function PHPStan\Testing\assertType;

enum Foo
{

	case ONE;
	case TWO;

	public function doFoo(): void
	{
		if ($this === self::ONE) {
			assertType('$this(EnumTypeAssertions\Foo)&' . self::class . '::ONE', $this);
			return;
		} else {
			assertType('$this(EnumTypeAssertions\Foo)&' . self::class . '::TWO', $this);
		}

		assertType('$this(EnumTypeAssertions\Foo)&' . self::class . '::TWO', $this);
	}

}


class FooClass
{

	public function doFoo(Foo $foo): void
	{
		assertType(Foo::class . '::ONE' , Foo::ONE);
		assertType(Foo::class . '::TWO', Foo::TWO);
		assertType('*ERROR*', Foo::TWO->value);
		assertType('array{EnumTypeAssertions\Foo::ONE, EnumTypeAssertions\Foo::TWO}', Foo::cases());
		assertType("'ONE'|'TWO'", $foo->name);
		assertType("'ONE'", Foo::ONE->name);
		assertType("'TWO'", Foo::TWO->name);
	}

}

enum Bar : string
{

	case ONE = 'one';
	case TWO = 'two';

}

class BarClass
{

	public function doFoo(string $s, Bar $bar): void
	{
		assertType(Bar::class . '::ONE', Bar::ONE);
		assertType(Bar::class . '::TWO', Bar::TWO);
		assertType('\'two\'', Bar::TWO->value);
		assertType('array{EnumTypeAssertions\Bar::ONE, EnumTypeAssertions\Bar::TWO}', Bar::cases());

		assertType(Bar::class, Bar::from($s));
		assertType(Bar::class . '|null', Bar::tryFrom($s));

		assertType("'one'|'two'", $bar->value);
	}

}

enum Baz : int
{

	case ONE = 1;
	case TWO = 2;
	const THREE = 3;
	const FOUR = 4;

}

class BazClass
{

	public function doFoo(int $i, Baz $baz): void
	{
		assertType(Baz::class . '::ONE', Baz::ONE);
		assertType(Baz::class . '::TWO', Baz::TWO);
		assertType('2', Baz::TWO->value);
		assertType('array{EnumTypeAssertions\Baz::ONE, EnumTypeAssertions\Baz::TWO}', Baz::cases());

		assertType(Baz::class, Baz::from($i));
		assertType(Baz::class . '|null', Baz::tryFrom($i));

		assertType('3', Baz::THREE);
		assertType('4', Baz::FOUR);
		assertType('*ERROR*', Baz::NONEXISTENT);

		assertType('1|2', $baz->value);
		assertType('1', Baz::ONE->value);
		assertType('2', Baz::TWO->value);
	}

	/**
	 * @param Baz::ONE $enum
	 * @param Baz::THREE $constant
	 * @return void
	 */
	public function doBar($enum, $constant): void
	{
		assertType(Baz::class . '::ONE', $enum);
		assertType('3', $constant);
	}

	/**
	 * @param Baz::ONE $enum
	 * @param Baz::THREE $constant
	 * @return void
	 */
	public function doBaz(Baz $enum, $constant): void
	{
		assertType(Baz::class . '::ONE', $enum);
		assertType('3', $constant);
	}

	/**
	 * @param Foo::* $enums
	 * @return void
	 */
	public function doLorem($enums): void
	{
		assertType(Foo::class . '::ONE|' . Foo::class . '::TWO', $enums);
	}

}

class Lorem
{

	public function doFoo(Foo $foo): void
	{
		if ($foo === Foo::ONE) {
			assertType(Foo::class . '::ONE', $foo);
			return;
		}

		assertType(Foo::class . '::TWO', $foo);
	}

	public function doBar(Foo $foo): void
	{
		if (Foo::ONE === $foo) {
			assertType(Foo::class . '::ONE', $foo);
			return;
		}

		assertType(Foo::class . '::TWO', $foo);
	}

	public function doBaz(Foo $foo): void
	{
		if ($foo === Foo::ONE) {
			assertType(Foo::class . '::ONE', $foo);
			if ($foo === Foo::TWO) {
				assertType('*NEVER*', $foo);
			} else {
				assertType(Foo::class . '::ONE', $foo);
			}

			assertType(Foo::class . '::ONE', $foo);
		}
	}

	public function doClass(Foo $foo): void
	{
		assertType('class-string<' . Foo::class . '>', $foo::class);
		assertType(Foo::class . '::ONE', Foo::ONE);
		assertType('class-string<' . Foo::class . '>', Foo::ONE::class);
		assertType(Bar::class . '::ONE', Bar::ONE);
		assertType('class-string<' . Bar::class . '>', Bar::ONE::class);
	}

}

class EnumInConst
{

	const TEST = [Foo::ONE];

	public function doFoo()
	{
		assertType('array{EnumTypeAssertions\Foo::ONE}', self::TEST);
	}

}

/** @template T */
interface GenericInterface
{

	/** @return T */
	public function doFoo();

}

/** @implements GenericInterface<int> */
enum EnumImplementsGeneric: int implements GenericInterface
{

	case ONE = 1;

	public function doFoo()
	{
		return 1;
	}

}

class TestEnumImplementsGeneric
{

	public function doFoo(EnumImplementsGeneric $e): void
	{
		assertType('int', $e->doFoo());
		assertType('int', EnumImplementsGeneric::ONE->doFoo());
	}

}

class MixedMethod
{

	public function doFoo(): int
	{
		return 1;
	}

}

/** @mixin MixedMethod */
enum EnumWithMixin
{

}

function (EnumWithMixin $i): void {
	assertType('int', $i->doFoo());
};

/**
 * @phpstan-type TypeAlias array{foo: int, bar: string}
 */
enum EnumWithTypeAliases
{

	/**
	 * @param TypeAlias $p
	 * @return TypeAlias
	 */
	public function doFoo($p)
	{
		assertType('array{foo: int, bar: string}', $p);
	}

	public function doBar()
	{
		assertType('array{foo: int, bar: string}', $this->doFoo());
	}

}
