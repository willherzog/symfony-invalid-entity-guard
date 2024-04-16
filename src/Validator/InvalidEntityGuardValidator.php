<?php

namespace WHSymfony\WHInvalidEntityGuardBundle\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\MetadataInterface;
use Symfony\Component\Validator\Validator\{ValidatorInterface,ContextualValidatorInterface};

/**
 * If ValidatorInterface::validate() is called with a Doctrine entity as its subject and it returns a non-empty list of violations,
 * detach the subject entity from the associated EntityManager so that the invalid changes made to it will not be persisted.
 *
 * If the FQCN of the entity matches one of those configured to be excluded, however, the entity will not be detached.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class InvalidEntityGuardValidator implements ValidatorInterface
{
	public function __construct(
		private readonly ValidatorInterface $validator,
		private readonly ManagerRegistry $managerRegistry,
		private readonly array $excludedEntities
	) {}

	/**
	 * @inheritDoc
	 */
	public function getMetadataFor(mixed $value): MetadataInterface
	{
		return $this->validator->getMetadataFor($value);
	}

	/**
	 * @inheritDoc
	 */
	public function hasMetadataFor(mixed $value): bool
	{
		return $this->validator->hasMetadataFor($value);
	}

	/**
	 * @inheritDoc
	 */
	public function validate(mixed $value, null|Constraint|array $constraints = null, null|string|GroupSequence|array $groups = null): ConstraintViolationListInterface
	{
		$violations = $this->validator->validate($value, $constraints, $groups);

		if( $violations->count() > 0 && is_object($value) && !in_array(get_class($value), $this->excludedEntities, true) ) {
			$entityManager = $this->managerRegistry->getManagerForClass(get_class($value));

			if( ($entityManager instanceof EntityManagerInterface) && $entityManager->contains($value) ) {
				$entityManager->detach($value);
			}
		}

		return $violations;
	}

	/**
	 * @inheritDoc
	 */
	public function validateProperty(object $object, string $propertyName, string|GroupSequence|array|null $groups = null): ConstraintViolationListInterface
	{
		return $this->validator->validateProperty($object, $propertyName, $groups);
	}

	/**
	 * @inheritDoc
	 */
	public function validatePropertyValue(object|string $objectOrClass, string $propertyName, mixed $value, string|GroupSequence|array|null $groups = null): ConstraintViolationListInterface
	{
		return $this->validator->validatePropertyValue($objectOrClass, $propertyName, $value, $groups);
	}

	/**
	 * @inheritDoc
	 */
	public function startContext(): ContextualValidatorInterface
	{
		return $this->validator->startContext();
	}

	/**
	 * @inheritDoc
	 */
	public function inContext(ExecutionContextInterface $context): ContextualValidatorInterface
	{
		return $this->validator->inContext($context);
	}
}
