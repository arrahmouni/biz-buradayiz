<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Admin\Traits\UserTrait;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\Address;
use Modules\Base\Trait\ModelHelper;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Models\PackageSubscription;
use Modules\Platform\Models\Service;
use Modules\Zms\Models\City;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia, Auditable
{
    use UserTrait, HasApiTokens, HasFactory, Notifiable, SoftDeletes, ModelHelper, AuditableTrait, InteractsWithMedia;

    const VIEW_PATH = 'users';

    public const MEDIA_COLLECTION = 'user_image';

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
        'central_phone',
        'email_verified_at',
        'remember_token',
        'provider',
        'provider_id',
        'service_id',
        'city_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'full_name',
        'phone_number_without_country_code',
        'status_format',
        'created_at_format',
        'image_url',
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
            'type'              => UserType::class,
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

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION);
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

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed()->withDisabled();
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function packageSubscriptions()
    {
        return $this->hasMany(PackageSubscription::class);
    }

    public function currentPackageSubscription()
    {
        return $this->hasOne(PackageSubscription::class)
            ->where('status', PackageSubscriptionStatus::Active->value)
            ->latestOfMany('id');
    }

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return $query->whereAny(
            ['id', 'first_name', 'last_name', 'phone_number', 'central_phone', 'email'],
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

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getFirstMedia(self::MEDIA_COLLECTION)?->getUrl() ?? asset('images/default/avatars/user.png'),
        );
    }
    // End Accessors
}
