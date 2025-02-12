<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Type;

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use function array_map;
use function array_merge;
use function count;
use function implode;

class UnionTypeMethodReflection implements MethodReflection
{

	/**
	 * @param MethodReflection[] $methods
	 */
	public function __construct(private string $methodName, private array $methods)
	{
	}

	public function getDeclaringClass(): ClassReflection
	{
		return $this->methods[0]->getDeclaringClass();
	}

	public function isStatic(): bool
	{
		foreach ($this->methods as $method) {
			if (!$method->isStatic()) {
				return false;
			}
		}

		return true;
	}

	public function isPrivate(): bool
	{
		foreach ($this->methods as $method) {
			if ($method->isPrivate()) {
				return true;
			}
		}

		return false;
	}

	public function isPublic(): bool
	{
		foreach ($this->methods as $method) {
			if (!$method->isPublic()) {
				return false;
			}
		}

		return true;
	}

	public function getName(): string
	{
		return $this->methodName;
	}

	public function getPrototype(): ClassMemberReflection
	{
		return $this;
	}

	public function getVariants(): array
	{
		$variants = array_merge(...array_map(static fn (MethodReflection $method) => $method->getVariants(), $this->methods));

		return [ParametersAcceptorSelector::combineAcceptors($variants)];
	}

	public function isDeprecated(): TrinaryLogic
	{
		return TrinaryLogic::extremeIdentity(...array_map(static fn (MethodReflection $method): TrinaryLogic => $method->isDeprecated(), $this->methods));
	}

	public function getDeprecatedDescription(): ?string
	{
		$descriptions = [];
		foreach ($this->methods as $method) {
			if (!$method->isDeprecated()->yes()) {
				continue;
			}
			$description = $method->getDeprecatedDescription();
			if ($description === null) {
				continue;
			}

			$descriptions[] = $description;
		}

		if (count($descriptions) === 0) {
			return null;
		}

		return implode(' ', $descriptions);
	}

	public function isFinal(): TrinaryLogic
	{
		return TrinaryLogic::extremeIdentity(...array_map(static fn (MethodReflection $method): TrinaryLogic => $method->isFinal(), $this->methods));
	}

	public function isInternal(): TrinaryLogic
	{
		return TrinaryLogic::extremeIdentity(...array_map(static fn (MethodReflection $method): TrinaryLogic => $method->isInternal(), $this->methods));
	}

	public function getThrowType(): ?Type
	{
		$types = [];

		foreach ($this->methods as $method) {
			$type = $method->getThrowType();
			if ($type === null) {
				continue;
			}

			$types[] = $type;
		}

		if (count($types) === 0) {
			return null;
		}

		return TypeCombinator::union(...$types);
	}

	public function hasSideEffects(): TrinaryLogic
	{
		return TrinaryLogic::extremeIdentity(...array_map(static fn (MethodReflection $method): TrinaryLogic => $method->hasSideEffects(), $this->methods));
	}

	public function getDocComment(): ?string
	{
		return null;
	}

}
