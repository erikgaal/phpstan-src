<?php

namespace HasOffsetTypeBug;

use function PHPStan\Testing\assertType;

class Foo
{

	/**
	 * @param string[] $errorMessages
	 * @return void
	 */
	public function doFoo(array $errorMessages): void
	{
		$fileErrorsCounts = [];
		assertType('array{}', $fileErrorsCounts);
		foreach ($errorMessages as $errorMessage) {
			assertType('string', $errorMessage);
			if (!isset($fileErrorsCounts[$errorMessage])) {
				assertType('array<string, int<1, max>>', $fileErrorsCounts);
				assertType('int<1, max>', $fileErrorsCounts[$errorMessage]);
				$fileErrorsCounts[$errorMessage] = 1;
				assertType('non-empty-array<string, int<1, max>>', $fileErrorsCounts);
				assertType('1', $fileErrorsCounts[$errorMessage]);
				continue;
			}

			assertType('array<string, int<1, max>>', $fileErrorsCounts);
			assertType('int<1, max>', $fileErrorsCounts[$errorMessage]);

			$fileErrorsCounts[$errorMessage]++;

			assertType('non-empty-array<string, int<1, max>>', $fileErrorsCounts);
			assertType('int<2, max>', $fileErrorsCounts[$errorMessage]);
		}

		assertType('array<string, int<1, max>>', $fileErrorsCounts);
	}

	/**
	 * @param mixed[] $result
	 * @return void
	 */
	public function doBar(array $result): void
	{
		assertType('array', $result);
		assert($result['totals']['file_errors'] === 3);
		assertType("array", $result);
		assertType("mixed", $result['totals']);
		assertType('3', $result['totals']['file_errors']);
		assertType('mixed', $result['totals']['errors']);
		assert($result['totals']['errors'] === 0);
		assertType("array", $result);
		assertType("mixed", $result['totals']);
		assertType('3', $result['totals']['file_errors']);
		assertType('0', $result['totals']['errors']);
	}

	/**
	 * @param array{}|array{min?: bool|float|int|string|null, max?: bool|float|int|string|null} $range
	 * @return void
	 */
	public function testIsset($range): void
	{
		assertType("array{}|array{min?: bool|float|int|string|null, max?: bool|float|int|string|null}", $range);
		if (isset($range['min']) || isset($range['max'])) {
			assertType("array{min?: bool|float|int|string|null, max?: bool|float|int|string|null}&non-empty-array", $range);
		} else {
			assertType("array{}|array{min?: bool|float|int|string|null, max?: bool|float|int|string|null}", $range);
		}

		assertType("array{}|array{min?: bool|float|int|string|null, max?: bool|float|int|string|null}", $range);
	}

}

class TryMixed
{

	public function doFoo($mixed)
	{
		if (isset($mixed[0])) {
			assertType("mixed~null", $mixed[0]);
			assertType("mixed~null", $mixed);
		} else {
			assertType("mixed", $mixed);
		}

		assertType("mixed", $mixed);
	}

	public function doFoo2($mixed)
	{
		if (isset($mixed['foo'])) {
			assertType("mixed~null", $mixed['foo']);
			assertType("mixed~null", $mixed);
		} else {
			assertType("mixed", $mixed);
		}

		assertType("mixed", $mixed);
	}

	public function doBar(\SimpleXMLElement $xml)
	{
		if (isset($xml['foo'])) {
			assertType('SimpleXMLElement', $xml['foo']);
			assertType("SimpleXMLElement&hasOffsetValue('foo', SimpleXMLElement)", $xml);
		}
	}

}
