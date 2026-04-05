<?php

namespace Modules\Verimor\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageSubscription;
use Modules\Platform\Models\PackageSubscriptionSnapshot;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Enums\VerimorCallEventType;
use Modules\Verimor\Models\VerimorCallEvent;
use Modules\Verimor\Support\VerimorPhoneNormalizer;

/**
 * Dummy Verimor CRM webhook-style rows for local / staging admin UI.
 *
 * Payload shape follows Verimor "Olay Bildirme" (HTML form fields); see:
 * https://github.com/verimor/Bulutsantralim-API/blob/master/report_event.md
 *
 * Run: php artisan db:seed --class=Modules\\Verimor\\Database\\Seeders\\VerimorCallEventSeeder
 */
class VerimorCallEventSeeder extends Seeder
{
    /** Fixed UUIDs so re-running the seeder replaces the same demo rows. */
    private const DEMO_CALL_UUIDS = [
        '00000000-0000-4000-8000-000000000001',
        '00000000-0000-4000-8000-000000000002',
        '00000000-0000-4000-8000-000000000003',
        '00000000-0000-4000-8000-000000000004',
        '00000000-0000-4000-8000-000000000005',
        '00000000-0000-4000-8000-000000000006',
    ];

    public function run(): void
    {
        VerimorCallEvent::query()->whereIn('call_uuid', self::DEMO_CALL_UUIDS)->delete();

        $demoNorm = '905320000001';
        $unknownDestination = '908001112233';

        $provider = User::query()
            ->where('type', UserType::ServiceProvider)
            ->whereNotNull('central_phone')
            ->get(['id', 'central_phone'])
            ->first(fn (User $u) => VerimorPhoneNormalizer::canonicalize($u->central_phone) === $demoNorm);

        if ($provider === null) {
            $provider = User::factory()->create([
                'type' => UserType::ServiceProvider,
                'central_phone' => '05320000001',
            ]);
        }

        $package = Package::query()->first() ?? Package::factory()->create(['connections_count' => 50]);
        $subscription = PackageSubscription::query()->where('user_id', $provider->id)->first();
        if ($subscription === null) {
            $subscription = PackageSubscription::query()->create([
                'user_id' => $provider->id,
                'status' => PackageSubscriptionStatus::Active,
                'payment_status' => PackageSubscriptionPaymentStatus::Paid,
                'payment_method' => PackageSubscriptionPaymentMethod::Other,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addYear(),
                'paid_at' => now()->subMonth(),
                'remaining_connections' => 100,
            ]);
        }
        $subscription->loadMissing('snapshot');
        if ($subscription->snapshot === null) {
            $subscription->snapshot()->create(PackageSubscriptionSnapshot::attributesFromPackage($package));
        }

        $providerNoSub = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'central_phone' => '05321112233',
        ]);
        $noSubNorm = VerimorPhoneNormalizer::canonicalize($providerNoSub->central_phone);

        // 1) hangup + inbound + answered: quota consumed (matches job outcome)
        $payload1 = $this->crmFormPayload([
            'event_type' => VerimorCallEventType::Hangup->value,
            'call_uuid' => self::DEMO_CALL_UUIDS[0],
            'destination_number' => '05320000001',
            'answered' => 'true',
            'duration' => '318',
            'sip_hangup_disposition' => 'callee',
            'hangup_cause' => 'NORMAL_CLEARING',
        ]);
        VerimorCallEvent::query()->create([
            'call_uuid' => self::DEMO_CALL_UUIDS[0],
            'event_type' => VerimorCallEventType::Hangup,
            'direction' => VerimorCallDirection::Inbound,
            'destination_number_normalized' => VerimorPhoneNormalizer::canonicalize($payload1['destination_number']),
            'user_id' => $provider->id,
            'package_subscription_id' => $subscription->id,
            'answered' => true,
            'consumed_quota' => true,
            'raw_payload' => $payload1,
        ]);

        // 2) user_hangup, answered, provider matched but no active subscription path → no quota
        $payload2 = $this->crmFormPayload([
            'event_type' => VerimorCallEventType::UserHangup->value,
            'call_uuid' => self::DEMO_CALL_UUIDS[1],
            'destination_number' => (string) $providerNoSub->central_phone,
            'answered' => 'true',
            'duration' => '45',
            'sip_hangup_disposition' => 'caller',
            'hangup_cause' => 'NORMAL_CLEARING',
        ]);
        VerimorCallEvent::query()->create([
            'call_uuid' => self::DEMO_CALL_UUIDS[1],
            'event_type' => VerimorCallEventType::UserHangup,
            'direction' => VerimorCallDirection::Inbound,
            'destination_number_normalized' => $noSubNorm !== '' ? $noSubNorm : null,
            'user_id' => $providerNoSub->id,
            'package_subscription_id' => null,
            'answered' => true,
            'consumed_quota' => false,
            'raw_payload' => $payload2,
        ]);

        // 3) Unanswered inbound hangup (missed)
        $payload3 = $this->crmFormPayload([
            'event_type' => VerimorCallEventType::Hangup->value,
            'call_uuid' => self::DEMO_CALL_UUIDS[2],
            'destination_number' => '905320000001',
            'answered' => 'false',
            'answer_stamp' => '',
            'duration' => '0',
            'sip_hangup_disposition' => 'caller',
            'hangup_cause' => 'NO_ANSWER',
        ]);
        VerimorCallEvent::query()->create([
            'call_uuid' => self::DEMO_CALL_UUIDS[2],
            'event_type' => VerimorCallEventType::Hangup,
            'direction' => VerimorCallDirection::Inbound,
            'destination_number_normalized' => VerimorPhoneNormalizer::canonicalize($payload3['destination_number']),
            'user_id' => $provider->id,
            'package_subscription_id' => null,
            'answered' => false,
            'consumed_quota' => false,
            'raw_payload' => $payload3,
        ]);

        // 4) Unknown DID — no provider match
        $payload4 = $this->crmFormPayload([
            'event_type' => VerimorCallEventType::Hangup->value,
            'call_uuid' => self::DEMO_CALL_UUIDS[3],
            'destination_number' => $unknownDestination,
            'answered' => 'true',
            'duration' => '120',
        ]);
        VerimorCallEvent::query()->create([
            'call_uuid' => self::DEMO_CALL_UUIDS[3],
            'event_type' => VerimorCallEventType::Hangup,
            'direction' => VerimorCallDirection::Inbound,
            'destination_number_normalized' => VerimorPhoneNormalizer::canonicalize($payload4['destination_number']),
            'user_id' => null,
            'package_subscription_id' => null,
            'answered' => true,
            'consumed_quota' => false,
            'raw_payload' => $payload4,
        ]);

        // 5) Rich CDR-style row (all documented keys present, string values like form POST)
        $payload5 = $this->crmFormPayload([
            'event_type' => VerimorCallEventType::Hangup->value,
            'call_uuid' => self::DEMO_CALL_UUIDS[4],
            'domain_id' => '10042',
            'caller_id_number' => '02161234567',
            'outbound_caller_id_number' => '',
            'destination_number' => '905320000001',
            'dialed_user' => '200',
            'connected_user' => '200',
            'start_stamp' => '2026-04-05 09:58:00',
            'answer_stamp' => '2026-04-05 09:58:14',
            'end_stamp' => '2026-04-05 10:02:01',
            'duration' => '227',
            'recording_present' => 'true',
            'answered' => 'true',
            'queue' => '10',
            'queue_wait_duration' => '8',
            'sip_hangup_disposition' => 'callee',
            'hangup_cause' => 'NORMAL_CLEARING',
            'failure_status' => '',
            'failure_phrase' => '',
        ]);
        VerimorCallEvent::query()->create([
            'call_uuid' => self::DEMO_CALL_UUIDS[4],
            'event_type' => VerimorCallEventType::Hangup,
            'direction' => VerimorCallDirection::Inbound,
            'destination_number_normalized' => VerimorPhoneNormalizer::canonicalize($payload5['destination_number']),
            'user_id' => $provider->id,
            'package_subscription_id' => $subscription->id,
            'answered' => true,
            'consumed_quota' => true,
            'raw_payload' => $payload5,
        ]);

        // 6) Second answered call same provider (no duplicate UUID in real life; demo only)
        $payload6 = $this->crmFormPayload([
            'event_type' => VerimorCallEventType::UserHangup->value,
            'call_uuid' => self::DEMO_CALL_UUIDS[5],
            'destination_number' => '+90 532 000 00 01',
            'answered' => 'true',
            'duration' => '92',
            'recording_present' => 'false',
        ]);
        VerimorCallEvent::query()->create([
            'call_uuid' => self::DEMO_CALL_UUIDS[5],
            'event_type' => VerimorCallEventType::UserHangup,
            'direction' => VerimorCallDirection::Inbound,
            'destination_number_normalized' => VerimorPhoneNormalizer::canonicalize($payload6['destination_number']),
            'user_id' => $provider->id,
            'package_subscription_id' => $subscription->id,
            'answered' => true,
            'consumed_quota' => true,
            'raw_payload' => $payload6,
        ]);
    }

    /**
     * @param  array<string, string|int|float|bool|null>  $overrides
     * @return array<string, string>
     */
    private function crmFormPayload(array $overrides = []): array
    {
        $callUuid = (string) ($overrides['call_uuid'] ?? (string) Str::uuid());

        $base = [
            'event_type' => VerimorCallEventType::Hangup->value,
            'domain_id' => '101',
            'direction' => VerimorCallDirection::Inbound->value,
            'caller_id_number' => '05326667788',
            'outbound_caller_id_number' => '',
            'destination_number' => '1001',
            'dialed_user' => '1001',
            'connected_user' => '1001',
            'call_uuid' => $callUuid,
            'start_stamp' => '2026-04-05 10:00:00',
            'answer_stamp' => '2026-04-05 10:00:15',
            'end_stamp' => '2026-04-05 10:05:33',
            'duration' => '318',
            'recording_present' => 'true',
            'answered' => 'true',
            'queue' => '',
            'queue_wait_duration' => '',
            'sip_hangup_disposition' => 'callee',
            'hangup_cause' => 'NORMAL_CLEARING',
            'failure_status' => '',
            'failure_phrase' => '',
        ];

        $merged = array_merge($base, $overrides);

        $stringified = [];
        foreach ($merged as $key => $value) {
            if ($value === null) {
                $stringified[$key] = '';

                continue;
            }
            if (is_bool($value)) {
                $stringified[$key] = $value ? 'true' : 'false';

                continue;
            }
            $stringified[$key] = (string) $value;
        }

        return $stringified;
    }
}
