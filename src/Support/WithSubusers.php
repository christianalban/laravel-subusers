<?php

namespace Alban\LaravelSubusers\Support;

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

    public function removeSubuser($user)
    {
        $this->subusers()->detach($user);
    }
}
