<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class)->withPivot('role');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function tasksDone()
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }

    public function nextTasks()
    {
        return Task::whereIn('group_id',$this->groups()->where('role',Role::STUDENT)->pluck('groups.id'))
            ->where('dueDate','>=',date('Y-m-d'))
            ->orderBy('dueDate');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
