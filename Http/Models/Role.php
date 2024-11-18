<?php

namespace Modules\Acl\Http\Models;

use Cviebrock\EloquentSluggable\Sluggable;

class Role extends \Spatie\Permission\Models\Role
{
    use Sluggable;

    protected $fillable = ['name', 'title', 'guard_name', 's_order'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'name' => [
                'source' => 'title',
                'separator' => '_'
            ]
        ];
    }

    protected static function boot()
    {
        parent::boot();
        // auto-sets values on creation
        static::creating(function ($model) {
            if (!$model->title) {
                $model->title = ucfirst($model->name);
            }
        });

        static::updating(function ($model) {
            $model->title = ucfirst($model->name);
        });
    }

    public function setGuardNameAttribute(): string
    {
        return $this->attributes['guard_name'] = 'web';
    }
}
