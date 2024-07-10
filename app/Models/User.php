<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\RefreshesPermissionCache;
use App\Models\Team;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;

class User extends Authenticatable
{
    use RefreshesPermissionCache;
    use HasRoles;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use HasTeams;
    use TwoFactorAuthenticatable;

	// use AuthenticatesWithLdap;

    protected $with =[
        'roles',
        'roles.permissions'
    ];
    protected $without=['pivot'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // public static function boot()
    // {
    //     parent::boot();

    //     self::created(function ($model) {
    //        // temporary: get session team_id for restore at end
    //        $session_team_id = getPermissionsTeamId();
    //        setPermissionsTeamId($model);
    //        User::find('1')->assignRole('Super Admin');
    //        setPermissionsTeamId($session_team_id);
    //     });
    // }

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profile_photo_path',
        'current_team_id'
        ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'plain',
        'remember_token',
        'password',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];




    public function jet_teams()
    {
        return $this->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id');
    }
}
