<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoveProgram extends Model
{
    protected $fillable = [
        'program_id',
        'old_package_id',
        'new_package_id'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function old_package()
    {
        return $this->belongsTo(Package::class, 'old_package_id');
    }

    public function new_package()
    {
        return $this->belongsTo(Package::class, 'new_package_id');
    }
}
