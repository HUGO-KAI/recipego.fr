<?php

namespace App\ValueResolver;

use App\Entity\Recipe;
use App\ValueResolver\Attribute\MapHydratedEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class EntityHydratorValueResolver implements ValueResolverInterface
{
  public function resolve(Request $request, ArgumentMetadata $argument): iterable
  {
    $attribute = $argument->getAttributes(MapHydratedEntity::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;
    if (!($attribute instanceof MapHydratedEntity)) {
      return [];
    }
    return [
      new Recipe()
    ];
  }
}
