<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class VMConfig extends Model
{
    use HasFactory;

    protected $table = 'vm_configs';

    protected $fillable = [
        'name',
        'box',
        'memory',
        'cpus',
        'storage',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
