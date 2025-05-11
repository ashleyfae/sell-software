<?php
/**
 * AbstractImportCommand.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Console\Commands\Import;

use App\Console\Commands\Traits\HasDryRunOption;
use App\Console\Commands\Traits\HasMaxOption;
use App\Imports\DataObjects\AbstractLegacyObject;
use App\Imports\DataObjects\LegacyCustomer;
use App\Imports\Repositories\MappingRepository;
use App\Models\LegacyMapping;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

abstract class AbstractImportCommand extends Command
{
    use HasDryRunOption, HasMaxOption;

    protected string $idProperty = 'id';

    public function __construct(
        protected MappingRepository $mappingRepository
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->line('Starting import...');
        $count = $this->getItemsToImportQuery()->count();
        $this->line("Found {$count} items(s) to import.");

        $this->getItemsToImportQuery()->chunkById(100, function(Collection $items) {
            foreach($items as $item) {
                try {
                    if ($this->atMaxItems()) {
                        break;
                    }

                    $this->maybeImportItem($this->makeItemObject($item));
                    $this->numberImported++;
                    $this->line('');
                } catch(Exception $e) {
                    $this->warn("ERROR: {$e->getMessage()}");
                    $this->warn($e->getTraceAsString());
                }
            }
        }, $this->idProperty);

        $this->afterImports();

        $this->line("Total imported: {$this->numberImported}");
    }

    abstract protected function getItemsToImportQuery() : Builder;

    abstract protected function makeItemObject(object $itemRow) : object;

    protected function maybeImportItem(object $item): void
    {
        $this->line("Processing item ID {$item->id}");
        $this->line('-- Parsed item data: '.json_encode($item->toArray()));

        if (! $this->itemExists($item)) {
            $this->line('-- Item does not exist; importing.');
            $this->importItem($item);
        } else {
            $this->line('-- Item already exists; skipping.');
        }
    }

    abstract protected function itemExists(object $item) : bool;
    abstract protected function importItem(object $item) : void;

    protected function makeLegacyMapping(AbstractLegacyObject $legacyObject) : LegacyMapping
    {
        $legacyMapping = new LegacyMapping();
        $legacyMapping->source_id = $legacyObject->id ?? null;
        $legacyMapping->source_data = $legacyObject->toArray();

        return $legacyMapping;
    }

    protected function afterImports() : void
    {

    }
}
