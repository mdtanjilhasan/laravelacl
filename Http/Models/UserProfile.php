<?php

namespace Modules\Acl\Http\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Acl\Traits\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'user_profile';

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'phone', 'date_of_birth', 'gender', 'state', 'city', 'country', 'zipcode',
        'address', 'street_1', 'street_2', 'image_avatar', 'deleted_by'
    ];

    public function profiles()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected $appends = ['image_path', 'full_name', 'phone_parsed'];

    protected function imagePath() : Attribute
    {
        return Attribute::get(function () {
            return $this->image_avatar ? asset($this->image_avatar) : '';
        });
    }

    protected function fullName() : Attribute
    {
        return Attribute::get(function () {
            return "$this->first_name $this->last_name";
        });
    }

    protected function phoneParsed() : Attribute
    {
        return Attribute::get(function () {
            return phoneUsaFormat($this->phone);
        });
    }
}
