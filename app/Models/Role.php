<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    /**
     * Get the users for the role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get client role
     */
    public static function getClientRole()
    {
        return self::where('slug', 'client')->first();
    }

    /**
     * Get admin role
     */
    public static function getAdminRole()
    {
        return self::where('slug', 'admin')->first();
    }
}
