<?php
/**
 * MappingRepository.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\Repositories;

use App\Imports\DataObjects\LegacyProduct;
use App\Imports\Enums\DataSource;
use App\Models\Bundle;
use App\Models\LegacyMapping;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class MappingRepository
{
    public function hasMapping(DataSource $source, int $sourceId, Model $dataType) : bool
    {
        $id = $this->getSingleMappingQuery($source, $sourceId, $dataType)
            ->value('id');

        return ! empty($id);
    }

    public function getSingleMappingQuery(DataSource $source, int $sourceId, Model $dataType)
    {
        return LegacyMapping::query()
            ->where('source', $source->value)
            ->where('source_id', $sourceId)
            ->where('mappable_type', $dataType->getMorphClass());
    }

    public function isBundleProduct(int $legacyProductId) : bool
    {
        $cacheKey = sprintf(
            'legacy-%s-product-%d-is-bundle',
            Config::get('imports.currentSource')->value,
            $legacyProductId
        );

        return (bool) Cache::remember(
            key: $cacheKey,
            ttl: 3600, // 1 hour
            callback: function() use($legacyProductId) {
                $mapping = $this->getSingleMappingQuery(
                    source: Config::get('imports.currentSource'),
                    sourceId: $legacyProductId,
                    dataType: new Bundle()
                )
                    ->first();

                return ! empty($mapping);
            }
        );
    }

    /**
     * @throws \Exception
     */
    public function getNewProductIdFromLegacyProductId(int $legacyProductId) : int
    {
        $cacheKey = sprintf(
            'legacy-%s-product-%s',
            Config::get('imports.currentSource')->value,
            $legacyProductId
        );

        $value = Cache::remember(
            key: $cacheKey,
            ttl: 3600, // 1 hour
            callback: function() use($legacyProductId) {
                /** @var LegacyMapping $mapping */
                $mapping = $this->getSingleMappingQuery(
                    source: Config::get('imports.currentSource'),
                    sourceId: $legacyProductId,
                    dataType: new Product()
                )
                    ->first();

                return $mapping?->mappable_id;
            }
        );

        if ($value) {
            return (int) $value;
        } else {
            throw new \Exception('New product ID not found');
        }
    }

    public function getNewPriceIdFromLegacyProductId(int $legacyProductId, ?int $legacyPriceIndex) : int
    {
        $priceIndexKey = is_null($legacyPriceIndex) ? 'default' : $legacyPriceIndex;
        $cacheKey = sprintf(
            'legacy-%s-product-%s-price-%s',
            Config::get('imports.currentSource')->value,
            $legacyProductId,
            $priceIndexKey
        );

        $value = Cache::remember(
            key: $cacheKey,
            ttl: 3600, // 1 hour
            callback: function() use($legacyProductId, $legacyPriceIndex) {
                /** @var LegacyMapping $mapping */
                $mapping = $this->getSingleMappingQuery(
                    source: Config::get('imports.currentSource'),
                    sourceId: $legacyProductId,
                    dataType: new Product()
                )
                    ->first();

                $legacyProduct = LegacyProduct::fromArray($mapping->source_data);
                foreach($legacyProduct->prices as $legacyPrice) {
                    if ($legacyPrice->index === $legacyPriceIndex && ! empty($legacyPrice->newPriceId)) {
                        return $legacyPrice->newPriceId;
                    }
                }

                throw new \Exception('New price ID not found');
            }
        );

        if ($value) {
            return (int) $value;
        } else {
            throw new \Exception('New price ID not found');
        }
    }
}
