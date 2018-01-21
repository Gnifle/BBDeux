<?php

namespace App\Traits;

use App\Models\Availability;
use App\Models\Stat;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Dynamic Properties:
 * @property-read Availability $current_availability
 *
 * Relationships:
 * @property-read Collection|Availability[] $availability
 *
 * Scopes:
 * @method Builder|static withStats() Join the `stats` table on the query
 * @method Builder|static available(Carbon $when = null) Return only available items
 */
trait HasStats
{
    public function stats()
    {
        return $this->morphMany(Stat::class, 'statable');
    }

    /**
     * Join the `stats` table
     *
     * @param Builder|static $query
     * @return Builder|static
     */
    public function scopeWithStats(Builder $query)
    {
        return $query->join('stats', 'stats.statable_id', '=', "{$this->getTable()}.{$this->getKeyName()}");
    }

    /**
     * Retrieve the current active stats
     *
     * @return Collection|Stat[]
     */
    public function getCurrentStatsAttribute()
    {
        return $this->statsAt(Carbon::now());
    }

    /**
     * Retrieve item stats at a given time.
     *
     * @param Carbon|null $when Defaults to Carbon::now()
     * @param string|null $stat Case-insensitive stat name (`title`). Defaults to all stats.
     *
     * @return Collection|Stat[]
     */
    public function statsAt(Carbon $when = null, string $stat = null)
    {
        $when = $when ?: Carbon::now();

        return Stat::where('statable_id', $this->id)
            ->where('statable_type', get_class($this))
            ->when($stat, function (Builder $query) use ($stat) {
                return $query->where('title', $stat);
            })
            ->withAvailabilities()
            ->where(function (Builder $query) use ($when) {
                return $query
                    ->whereNotNull('availabilities.to')
                    ->where('availabilities.from', '<=', $when)
                    ->where('availabilities.to', '>=', $when);
            })->orWhere(function (Builder $query) use ($when) {
                return $query
                    ->where('availabilities.from', '<=', $when)
                    ->whereNull('availabilities.to');
            })->get();
    }
}
