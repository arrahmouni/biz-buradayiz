<?php

namespace Modules\Auth\Models;

use Laravel\Sanctum\HasApiTokens;
use Modules\Auth\Models\Address;
use Modules\Admin\Traits\UserTrait;
use Modules\Base\Trait\ModelHelper;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class User extends Authenticatable implements Auditable
{
    use UserTrait, HasApiTokens, HasFactory, Notifiable, SoftDeletes, ModelHelper, AuditableTrait;

    const VIEW_PATH = 'users';

    const SERVICE_PROVIDER = 'service_provider';
    const CUSTOMER = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'type',
        'lang',
        'status',
        'first_name',
        'last_name',
        'phone_number',
        'email_verified_at',
        'remember_token',
        'provider',
        'provider_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'full_name',
        'phone_number_without_country_code',
        'status_format',
        'created_at_format'
    ];

    /**
     * Attributes to exclude from the Audit.
     *
     * @var array
     */
    protected $auditExclude = [
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Auth\database\factories\UserFactory::new();
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }

    // Start Relationships

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return $query->whereAny(
            ['id', 'first_name', 'last_name', 'phone_number', 'email'],
            'LIKE',
            '%' . $search . '%'
        );
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query
            ->when(!empty($search['status']), fn($q) => $q->where('status', $search['status']));
    }

    // End Scopes

    // Start Get Data From Model

    public function formAjaxArray($selected = true)
    {
        return [
            'id'            => $this->id,
            'text'          => $this->email,
            'selected'      => $selected
        ];
    }


    // End Get Data From Model

    // Start Accessors
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['first_name'] . ' ' . $attributes['last_name'],
        );
    }

    protected function phoneNumberWithoutCountryCode(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => substr($attributes['phone_number'], 3),
        );
    }
    // End Accessors
}
