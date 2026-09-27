<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait ReordersRecords
{
    /**
     * Move a record one step up or down among its siblings (by the "sort" column).
     *
     * @param  Builder<Model>  $siblings
     */
    protected function moveRecord(Model $record, string $direction, Builder $siblings): void
    {
        DB::transaction(function () use ($record, $direction, $siblings): void {
            $ordered = $siblings->orderBy('sort')->orderBy('id')->get()->values();

            foreach ($ordered as $index => $item) {
                if ($item->sort !== $index + 1) {
                    $item->forceFill(['sort' => $index + 1])->saveQuietly();
                }
            }

            $position = $ordered->search(fn (Model $item): bool => $item->is($record));
            $target = $direction === 'up' ? $position - 1 : $position + 1;

            if ($position === false || ! isset($ordered[$target])) {
                return;
            }

            $neighbour = $ordered[$target];
            $current = $ordered[$position];
            [$currentSort, $neighbourSort] = [$neighbour->sort, $current->sort];

            $current->forceFill(['sort' => $currentSort])->save();
            $neighbour->forceFill(['sort' => $neighbourSort])->save();
        });
    }
}
