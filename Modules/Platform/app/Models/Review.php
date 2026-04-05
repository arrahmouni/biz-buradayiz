<?php

namespace Modules\Platform\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Models\User;
use Modules\Base\Models\BaseModel;
use Modules\Platform\Database\Factories\ReviewFactory;
use Modules\Verimor\Models\VerimorCallEvent;

class Review extends BaseModel
{
    use  HasFactory, SoftDeletes;

    // Start Properties

    const VIEW_PATH = 'reviews';

    protected $fillable = [
        'user_id',
        'verimor_call_event_id',
        'rating',
        'body',
        'reviewer_display_name',
        'reviewer_phone_normalized',
    ];

    public $timestamps = true;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ReviewFactory::new();
    }

    // End Properties

    // Start Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verimorCallEvent(): BelongsTo
    {
        return $this->belongsTo(VerimorCallEvent::class);
    }

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('id', $search)
                ->orWhere('body', 'like', '%'.$search.'%')
                ->orWhere('reviewer_display_name', 'like', '%'.$search.'%')
                ->orWhere('reviewer_phone_normalized', 'like', '%'.$search.'%')
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('email', 'like', '%'.$search.'%')
                        ->orWhere('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('central_phone', 'like', '%'.$search.'%');
                });
        });
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query;
    }
    // End Scopes

    // Start Get Data From Model

    public function formAjaxArray($selected = true)
    {
        return [
            'id' => $this->id,
            'selected' => $selected,
        ];
    }

    // End Get Data From Model

    // Start Mutators & Accessors

    // End Mutators & Accessors
}
