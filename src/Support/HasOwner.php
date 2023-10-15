<?php

namespace Alban\LaravelSubusers\Support;

use Alban\LaravelSubusers\Models\Owner;
use Alban\LaravelSubusers\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasOwner
{
    protected function owner(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->owners()->first()
        );
    }

    protected function owners()
    {
        return $this->morphToMany(Owner::class, 'ownerables');
    }

    public static function ignoreOwner()
    {
        return static::withoutGlobalScope(OwnerScope::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new OwnerScope);
    }

    public function scopeIgnoreOwner(Builder $query)
    {
        return $query->withoutGlobalScope(OwnerScope::class);
    }

    public function scopeFromOwner(Builder $query, ?Owner $customer)
    {
        return $query->ignoreOwner()->whereHas('owners', function($q_customers) use ($customer)
        {
            $q_customers->where('owners.id', $customer ? $customer->id : 0);
        });
    }

    public function isOwner(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->customers()->first()->id === $this->getCustomerFromDomain()->id
        );
    }

    // public function isOwnerLogged(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn() => $this->customers()->first()->id === Auth::user()->owner->id,
    //     );
    // }

    public function isNotOwner(): Attribute
    {
        return Attribute::make(
            get: fn() => !$this->is_owner
        );
    }

    public function loadIgnoringOwner(array $relations = [])
    {
        $toLoadRelations = $this->getRelationsIgnoringOwner($relations);
        return $this->load($toLoadRelations);
    }

    public function scopeWithIgnoringOwner(Builder $query, array $relations = [])
    {
        $toLoadRelations = $this->getRelationsIgnoringOwner($relations);
        return $query->with($toLoadRelations);
    }

    protected function getRelationsIgnoringOwner(array $relations): array {
        $toLoadRelations = [];
        foreach ($relations as $key => $relation) {
            $key = is_callable($relation) ? $key : $relation;
            $funtion = is_callable($relation) ? $relation : null;
            $toLoadRelations[$key] = function ($query) use ($funtion) {
                $query->ignoreOwner();

                if ($funtion) {
                    $funtion($query);
                }
            };
        }

        return $toLoadRelations;
    }
}
