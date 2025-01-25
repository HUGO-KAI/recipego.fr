<?php

namespace App\ValueResolver\Attribute;

use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Validator\Constraints\GroupSequence;
use App\ValueResolver\EntityHydratorValueResolver;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class MapHydratedEntity extends ValueResolver
{
  public function __construct(
    public readonly array $groups = [],
    public readonly string|GroupSequence|array|null $validationGroups = null
  ) {
    parent::__construct(EntityHydratorValueResolver::class);
  }
}
