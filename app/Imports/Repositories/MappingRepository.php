<?php
/**
 * MappingRepository.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\Repositories;

use App\Imports\Enums\DataSource;
use App\Models\LegacyMapping;
use Illuminate\Database\Eloquent\Model;

class MappingRepository
{
    public function hasMapping(DataSource $source, int $sourceId, Model $dataType) : bool
    {
        $id = $this->getSingleMappingQuery($source, $sourceId, $dataType)
            ->value('id');

        return ! empty($id);
    }

    protected function getSingleMappingQuery(DataSource $source, int $sourceId, Model $dataType)
    {
        return LegacyMapping::query()
            ->where('source', $source->value)
            ->where('source_id', $sourceId)
            ->where('mappable_type', $dataType->getMorphClass());
    }
}
