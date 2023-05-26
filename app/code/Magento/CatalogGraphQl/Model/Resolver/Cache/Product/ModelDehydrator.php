<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQl\Model\Resolver\Cache\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\EntityManager\TypeResolver;
use Magento\GraphQlResolverCache\Model\Resolver\Result\DehydratorInterface;

/**
 * Product resolver data dehydrator to create snapshot data necessary to restore model.
 */
class ModelDehydrator implements DehydratorInterface
{
    /**
     * @var TypeResolver
     */
    private TypeResolver $typeResolver;

    /**
     * @var HydratorPool
     */
    private HydratorPool $hydratorPool;

    /**
     * @param HydratorPool $hydratorPool
     * @param TypeResolver $typeResolver
     */
    public function __construct(
        HydratorPool $hydratorPool,
        TypeResolver $typeResolver
    ) {
        $this->typeResolver = $typeResolver;
        $this->hydratorPool = $hydratorPool;
    }

    /**
     * @inheritdoc
     */
    public function dehydrate(array &$resolvedValue): void
    {
        $mediaGalleryEntityKeys = array_keys($resolvedValue);
        foreach ($mediaGalleryEntityKeys as $mediaGalleryEntityKey) {
            if (array_key_exists('model', $resolvedValue[$mediaGalleryEntityKey])
                && $resolvedValue[$mediaGalleryEntityKey]['model'] instanceof Product) {
                /** @var Product $model */
                $model = $resolvedValue[$mediaGalleryEntityKey]['model'];
                $entityType = $this->typeResolver->resolve($model);
                $resolvedValue[$mediaGalleryEntityKey]['model_data'] = $this->hydratorPool->getHydrator($entityType)
                    ->extract($model);
                $resolvedValue[$mediaGalleryEntityKey]['model_entity_type'] = $entityType;
                $resolvedValue[$mediaGalleryEntityKey]['model_id'] = $model->getId();
            }
        }
    }
}
