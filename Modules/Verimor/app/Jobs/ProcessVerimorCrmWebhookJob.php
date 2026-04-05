<?php

namespace Modules\Verimor\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Models\PackageSubscription;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Enums\VerimorCallEventType;
use Modules\Verimor\Models\VerimorCallEvent;
use Modules\Verimor\Support\VerimorPhoneNormalizer;

class ProcessVerimorCrmWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public array $payload
    ) {}

    public function handle(): void
    {
        $eventTypeRaw = strtolower(trim((string) ($this->payload['event_type'] ?? '')));
        $eventType = VerimorCallEventType::tryFrom($eventTypeRaw);
        if ($eventType === null) {
            return;
        }

        $directionRaw = strtolower(trim((string) ($this->payload['direction'] ?? '')));
        $direction = VerimorCallDirection::tryFrom($directionRaw);
        if ($direction !== VerimorCallDirection::Inbound) {
            return;
        }

        $callUuid = trim((string) ($this->payload['call_uuid'] ?? ''));
        if ($callUuid === '') {
            Log::channel('single')->warning('Verimor CRM webhook: missing call_uuid', ['payload' => $this->payload]);

            return;
        }

        $answered = $this->parseBool($this->payload['answered'] ?? false);
        $destinationRaw = (string) ($this->payload['destination_number'] ?? '');
        $normalizedDestination = VerimorPhoneNormalizer::canonicalize($destinationRaw);

        DB::transaction(function () use ($callUuid, $eventType, $direction, $answered, $normalizedDestination) {
            $existing = VerimorCallEvent::query()->where('call_uuid', $callUuid)->lockForUpdate()->first();
            if ($existing !== null) {
                return;
            }

            $user = $this->resolveServiceProviderByCentralPhone($normalizedDestination);

            try {
                $event = VerimorCallEvent::query()->create([
                    'call_uuid' => $callUuid,
                    'event_type' => $eventType,
                    'direction' => $direction,
                    'destination_number_normalized' => $normalizedDestination !== '' ? $normalizedDestination : null,
                    'user_id' => $user?->id,
                    'package_subscription_id' => null,
                    'answered' => $answered,
                    'consumed_quota' => false,
                    'raw_payload' => $this->payload,
                ]);
            } catch (QueryException $e) {
                if (($e->errorInfo[0] ?? '') === '23000') {
                    return;
                }

                throw $e;
            }

            if (! $answered || $user === null) {
                if ($user === null && $normalizedDestination !== '') {
                    Log::channel('single')->info('Verimor CRM webhook: no service provider for destination', [
                        'call_uuid' => $callUuid,
                        'destination_number_normalized' => $normalizedDestination,
                    ]);
                }

                return;
            }

            $subscription = PackageSubscription::query()
                ->activeSubscription()
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($subscription === null) {
                Log::channel('single')->info('Verimor CRM webhook: no active subscription for user', [
                    'call_uuid' => $callUuid,
                    'user_id' => $user->id,
                ]);

                return;
            }

            // this scenario is not possible, but we'll leave it here for future reference
            if ($subscription->remaining_connections <= 0) {
                return;
            }

            $subscription->decrement('remaining_connections');
            $event->forceFill([
                'package_subscription_id' => $subscription->id,
                'consumed_quota' => true,
            ])->save();
        });
    }

    private function resolveServiceProviderByCentralPhone(string $normalizedDestination): ?User
    {
        if ($normalizedDestination === '') {
            return null;
        }

        return User::query()
            ->where('type', UserType::ServiceProvider->value)
            ->whereNotNull('central_phone')
            ->get(['id', 'central_phone'])
            ->first(function (User $user) use ($normalizedDestination) {
                return VerimorPhoneNormalizer::canonicalize($user->central_phone) === $normalizedDestination;
            });
    }

    private function parseBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $v = strtolower(trim((string) $value));

        return in_array($v, ['1', 'true', 'yes', 'on'], true);
    }
}
