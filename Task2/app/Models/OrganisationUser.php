<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrganisationUser extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'userId',
        'orgId',
    ];

    public function organisations() : BelongsToMany 
    {
        return $this->belongsToMany(Organisation::class, 'userId', 'id');
    }
}
