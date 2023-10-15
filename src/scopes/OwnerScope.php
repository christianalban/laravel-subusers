<?php

namespace Alban\LaravelSubusers\Scopes;

use Alban\LaravelSubusers\Subusers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OwnerScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $current_user = Auth::user();

        $callback = Subusers::$beforeUserCallback;

        if (!$current_user || $callback($current_user)) {
            return;
        }

        $builder->whereHas('owners', function($q_customers) use ($current_user)
        {
            $q_customers->where('owners.id', $current_user->main_user->id);
        });
    }
}
