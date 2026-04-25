<?php

namespace Modules\Auth\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Modules\Admin\Enums\AdminStatus;
use Modules\Admin\Traits\UserTrait;
use Modules\Auth\database\factories\UserFactory;
use Modules\Auth\Enums\UserType;
use Modules\Base\Trait\ModelHelper;
use Modules\Config\Constatnt;
use Modules\Platform\Models\PackageSubscription;
use Modules\Platform\Models\Review;
use Modules\Platform\Models\Service;
use Modules\Zms\Models\City;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable implements Auditable, CanResetPasswordContract, HasMedia
{
    use AuditableTrait, CanResetPassword, HasApiTokens, HasFactory, HasSlug, InteractsWithMedia, ModelHelper, Notifiable, SoftDeletes, UserTrait;

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
        'review_rating_average',
        'approved_reviews_count',
        'profile_slug',
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
        'welcome_free_package_granted_at',
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
            'password' => 'hashed',
            'type' => UserType::class,
            'review_rating_average' => 'decimal:2',
            'approved_reviews_count' => 'integer',
            'welcome_free_package_granted_at' => 'datetime',
            'approved_at' => 'datetime',
            'ranking_score' => 'decimal:4',
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->type !== UserType::ServiceProvider) {
                $user->profile_slug = null;
            }
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        $options = SlugOptions::create()
            ->generateSlugsFrom(function (User $user): string {
                $full = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
                if ($full === '') {
                    return 'provider';
                }

                $slug = Str::slug($full);
                if ($slug === '') {
                    return 'provider';
                }
                if (ctype_digit($slug)) {
                    return 'provider '.$full;
                }

                return $full;
            })
            ->saveSlugsTo('profile_slug')
            ->preventOverwrite()
            ->startSlugSuffixFrom(2);

        if ($this->type !== UserType::ServiceProvider) {
            $options->skipGenerate = true;
        }

        return $options;
    }

    public function generateSlug(): void
    {
        if ($this->type !== UserType::ServiceProvider) {
            return;
        }

        $this->slugOptions = $this->getSlugOptions();

        $this->addSlug();
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

    public function activePackageSubscription()
    {
        return $this->hasOne(PackageSubscription::class)->activeSubscription();
    }

    public function currentPackageSubscription()
    {
        return $this->hasOne(PackageSubscription::class)->activeSubscription()->latest('id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        $like = '%'.$search.'%';

        return $query->where(function ($q) use ($like) {
            $q->whereAny(
                ['id', 'first_name', 'last_name', 'phone_number', 'central_phone', 'email'],
                'LIKE',
                $like
            )->orWhereRaw(
                "TRIM(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) LIKE ?",
                [$like]
            );
        });
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query
            ->when(! empty($search['status']), fn ($q) => $q->where('status', $search['status']))
            ->when(
                ! empty($search['service_id']) && (int) $search['service_id'] > 0,
                fn ($q) => $q->where('service_id', (int) $search['service_id'])
            )
            ->when(
                ! empty($search['city_id']) && (int) $search['city_id'] > 0,
                fn ($q) => $q->where('city_id', (int) $search['city_id'])
            )
            ->when(
                ! empty($search['approval']) && $search['approval'] === 'pending',
                fn ($q) => $q->where('status', AdminStatus::PENDING)->whereNull('approved_at')
            );
    }

    // End Scopes

    // Start Get Data From Model

    public function formAjaxArray($selected = true)
    {
        return [
            'id' => $this->id,
            'text' => $this->email.' ('.$this->full_name.')',
            'selected' => $selected,
        ];
    }

    // End Get Data From Model

    // Start Accessors
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['first_name'].' '.$attributes['last_name'],
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
            get: fn () => $this->getFirstMedia(self::MEDIA_COLLECTION)?->getUrl() ?? asset('images/default/avatars/blank.png'),
        );
    }

    protected function providerCardServiceName(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->service === null) {
                    return __('front::home.provider_card_service_fallback');
                }

                return $this->service->smartTrans('name') ?? __('front::home.provider_card_service_fallback');
            },
        );
    }

    protected function providerCardServiceDescription(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->service?->smartTrans('description'),
        );
    }

    protected function providerCardLocationLine(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if ($this->city === null) {
                    return null;
                }

                $parts = [];
                $parts[] = $this->city->smartTrans('name') ?? $this->city->native_name;
                if ($this->city->relationLoaded('state') && $this->city->state !== null) {
                    $parts[] = $this->city->state->smartTrans('name') ?? $this->city->state->native_name;
                }

                return count($parts) ? implode(', ', $parts) : null;
            },
        );
    }
    // End Accessors

    /**
     * SEO path segment for public provider profile URLs (slug only, no user id).
     */
    public function frontProfileSlug(): string
    {
        if (filled($this->profile_slug)) {
            return $this->profile_slug;
        }

        if ($this->type !== UserType::ServiceProvider) {
            return '';
        }

        return Str::slug(trim(($this->first_name ?? '').' '.($this->last_name ?? ''))) ?: 'provider';
    }

    /**
     * Whether this provider is within the configured "new provider" visibility window (same rules as featured search).
     */
    public function isNewProvider(): bool
    {
        if ($this->type !== UserType::ServiceProvider) {
            return false;
        }

        $hours = (int) getSetting(Constatnt::NEW_PROVIDER_HOURS, 24);
        if ($hours <= 0) {
            return false;
        }

        if ($this->approved_at === null) {
            return false;
        }

        return $this->approved_at->greaterThanOrEqualTo(now()->subHours($hours));
    }

    protected function getArrayableAppends(): array
    {
        $appends = parent::getArrayableAppends();

        if ($this->type === UserType::ServiceProvider) {
            $appends = array_merge($appends, [
                'provider_card_service_name',
                'provider_card_service_description',
                'provider_card_location_line',
            ]);
        }

        return $appends;
    }
}
