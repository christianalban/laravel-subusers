<?php

namespace Alban\LaravelSubusers\Support;

use Alban\LaravelSubusers\Events\DowngradedAsSubuser;
use Alban\LaravelSubusers\Events\UpgradedAsOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

trait WithSubusers
{
    public function subusers()
    {
        return $this->belongsToMany(config('owner.user_model'), 'subusers', 'main_user_id', 'sub_user_id');
    }

    protected function isMainUser(): Attribute
    {
        return Attribute::make(function () {
            return DB::table('subusers')->where('main_user_id', $this->id)->exists();
        });
    }

    protected function isSubUser(): Attribute
    {
        return Attribute::make(function () {
            return DB::table('subusers')->where('main_user_id', $this->id)->notExists();
        });
    }

    protected function mainUser(): Attribute
    {
        $userModel = config('owner.user_model');

        return Attribute::make(function () use ($userModel) {
            return $userModel::join('subusers', 'subusers.main_user_id', '=', $userModel->getTable() . '.id')
                ->where('sub_user_id', $this->id)->first();
        });
    }

    public function upgradeAsMain()
    {
        $mainUser = $this->main_user;

        $mainUser->detachSubuser($this);

        $this->attachSubuser($mainUser);

        DB::table('subusers')->where('main_user_id', $mainUser->id)->update([
            'main_user_id' => $this->id,
        ]);

        UpgradedAsOwner::dispatch($this);
        DowngradedAsSubuser::dispatch($mainUser);
    }

    public static function createAsMain(array $data)
    {
        $user = self::create($data);

        $user->attachSubuser($user);

        return $user;
    }

    public function attachSubuser($user)
    {
        $this->subusers()->attach($user);
    }

    public function deleteSubuser($user)
    {
        $this->subusers()->detach($user);

        $user->delete();
    }

    public function detachSubuser($user)
    {
        $this->subusers()->detach($user);
    }

    public function scopeWhereAdmin(Builder $query): Builder
    {
        return $query->whereDoesntHave('subusers', function (Builder $query) {
            $query->where('main_user_id', '!=', DB::raw('users.id'));
        });
    }
}
