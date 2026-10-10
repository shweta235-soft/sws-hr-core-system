<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'team',              // Added corporate taxonomies to fillable matrix
        'manager_id',
        'salary_structure',
        'joining_date',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | 👥 ELOQUENT RELATIONSHIPS INTERFACE (Strict Dependency Order Map)
    |--------------------------------------------------------------------------
    */

    // 👤 Self-Referencing Manager Taxonomy Line
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // 👥 Subordinate Tracking Line (For Managers/HR)
    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    // ⏱️ Employee ➔ hasMany Attendance Logs
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // 📝 Employee ➔ hasMany Timed Project Tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // 📄 Employee ➔ hasMany Leave Application Documents
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    // 💰 Employee ➔ hasMany Payroll Statements
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}
