<?php

namespace MinMaxArrays;

use function PHPStan\Analyser\assertType;

function dummy(): void
{
	assertType('1', min([1]));
	assertType('false', min([]));
	assertType('1', max([1]));
	assertType('false', max([]));
}

/**
 * @param int[] $ints
 */
function dummy2(array $ints): void
{
	if (count($ints) === 0) {
		assertType('false', min($ints));
		assertType('false', max($ints));
	} else {
		assertType('int', min($ints));
		assertType('int', max($ints));
	}
	if (count($ints) === 1) {
		assertType('int', min($ints));
		assertType('int', max($ints));
	} else {
		assertType('int|false', min($ints));
		assertType('int|false', max($ints));
	}
	if (count($ints) !== 0) {
		assertType('int', min($ints));
		assertType('int', max($ints));
	} else {
		assertType('false', min($ints));
		assertType('false', max($ints));
	}
	if (count($ints) !== 1) {
		assertType('int|false', min($ints));
		assertType('int|false', max($ints));
	} else {
		assertType('int', min($ints));
		assertType('int', max($ints));
	}
	if (count($ints) > 0) {
		assertType('int', min($ints));
		assertType('int', max($ints));
	} else {
		assertType('false', min($ints));
		assertType('false', max($ints));
	}
	if (count($ints) >= 1) {
		assertType('int', min($ints));
		assertType('int', max($ints));
	} else {
		assertType('false', min($ints));
		assertType('false', max($ints));
	}
	if (count($ints) >= 2) {
		assertType('int', min($ints));
		assertType('int', max($ints));
	} else {
		assertType('int|false', min($ints));
		assertType('int|false', max($ints));
	}
	if (count($ints) <= 0) {
		assertType('false', min($ints));
		assertType('false', max($ints));
	} else {
		assertType('int', min($ints));
		assertType('int', max($ints));
	}
	if (count($ints) < 1) {
		assertType('false', min($ints));
		assertType('false', max($ints));
	} else {
		assertType('int', min($ints));
		assertType('int', max($ints));
	}
	if (count($ints) < 2) {
		assertType('int|false', min($ints));
		assertType('int|false', max($ints));
	} else {
		assertType('int', min($ints));
		assertType('int', max($ints));
	}
}

/**
 * @param int[] $ints
 */
function dummy3(array $ints): void
{
	assertType('int|false', min($ints));
	assertType('int|false', max($ints));
}


function dummy4(\DateTimeInterface $dateA, ?\DateTimeInterface $dateB): void
{
	assertType('array(0 => DateTimeInterface, ?1 => DateTimeInterface)', array_filter([$dateA, $dateB]));
	assertType('DateTimeInterface', min(array_filter([$dateA, $dateB])));
	assertType('DateTimeInterface', max(array_filter([$dateA, $dateB])));
	assertType('array(?0 => DateTimeInterface)', array_filter([$dateB]));
	assertType('DateTimeInterface|false', min(array_filter([$dateB])));
	assertType('DateTimeInterface|false', max(array_filter([$dateB])));
}

function dummy5(int $i, int $j): void
{
	assertType('array(?0 => int<min, -1>|int<1, max>, ?1 => int<min, -1>|int<1, max>)', array_filter([$i, $j]));
	assertType('array(1 => true)', array_filter([false, true]));
}

function dummy6(string $s, string $t): void {
	assertType('array(?0 => string, ?1 => string)', array_filter([$s, $t]));
}
