<?php

namespace Modules\Admin\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Admin;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Cms\Enums\contents\BaseContentTypes;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\ContentCategory;
use Modules\Crm\Models\Contactus;
use Modules\Crm\Models\Subscribe;
use Modules\Permission\Models\Role;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Enums\ReviewStatus;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageSubscription;
use Modules\Platform\Models\Review;
use Modules\Platform\Models\Service;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Models\VerimorCallEvent;

class DashboardController extends BaseController
{
    public function dashboard(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'date_range' => 'required|string',
            ]);
        }

        $this->data['fromDate'] = null;
        $this->data['toDate'] = null;

        if ($request->has('date_range')) {
            $dateRange = explode(' - ', $request->date_range);
            $this->data['fromDate'] = Carbon::parse($dateRange[0])->format('Y-m-d');
            $this->data['toDate'] = Carbon::parse($dateRange[1])->format('Y-m-d');
        }

        $this->getUsersData();
        $this->getPlatformData();
        $this->getSubscriptionsAndTelephonyData();
        $this->getReviewsData();
        $this->getContentData();
        // $this->getCrmData();

        if ($request->ajax()) {
            return sendSuccessInternalResponse(data: $this->data);
        }

        return view('admin::dashboard', $this->data);
    }

    private function getUsersData()
    {
        $this->data['statistics']['users'][] = dashboardSetItem(
            key         : 'role',
            label       : trans('admin::dashboard.aside_menu.user_management.roles'),
            modelClass  : Role::class,
            fromDate    : $this->data['fromDate'],
            toDate      : $this->data['toDate']
        );

        $this->data['statistics']['users'][] = dashboardSetItem(
            key         : 'user',
            label       : trans('admin::dashboard.aside_menu.user_management.service_providers'),
            modelClass  : User::class,
            fromDate    : $this->data['fromDate'],
            toDate      : $this->data['toDate'],
            route       : route('auth.users.index', ['userType' => UserType::ServiceProvider->value]),
        );

        $this->data['statistics']['users'][] = dashboardSetItem(
            key         : 'admin',
            label       : trans('admin::dashboard.aside_menu.user_management.admins'),
            modelClass  : Admin::class,
            fromDate    : $this->data['fromDate'],
            toDate      : $this->data['toDate']
        );
    }

    private function getContentData()
    {
        // $this->data['statistics']['contents'][] = dashboardSetItem(
        //     key         : 'content_categories',
        //     label       : trans('admin::dashboard.aside_menu.content_category_management.content_categories'),
        //     modelClass  : ContentCategory::class,
        //     fromDate    : $this->data['fromDate'],
        //     toDate      : $this->data['toDate'],
        // );

        foreach (BaseContentTypes::all() as $type) {
            $this->data['statistics']['contents'][] = dashboardSetItem(
                key         : $type,
                label       : trans('admin::cruds.'.$type.'.title'),
                route       : route('cms.contents.index', ['type' => $type]),
                icon        : Content::getTypeInfo($type)['icon'] ?? null,
                modelClass  : Content::class,
                fromDate    : $this->data['fromDate'],
                toDate      : $this->data['toDate'],
                customQuery : fn ($query) => $query->byType($type),
            );
        }
    }

    private function getPlatformData(): void
    {
        $this->data['statistics']['platform'][] = dashboardSetItem(
            key: 'service',
            label: trans('admin::cruds.services.title'),
            modelClass: Service::class,
            fromDate: $this->data['fromDate'],
            toDate: $this->data['toDate'],
            icon: 'fas fa-concierge-bell',
            route: route('platform.services.index'),
        );

        $this->data['statistics']['platform'][] = dashboardSetItem(
            key: 'package',
            label: trans('admin::cruds.packages.title'),
            modelClass: Package::class,
            fromDate: $this->data['fromDate'],
            toDate: $this->data['toDate'],
            icon: 'fas fa-box-open',
            route: route('platform.packages.index'),
        );
    }

    private function getSubscriptionsAndTelephonyData(): void
    {
        $fromDate = $this->data['fromDate'];
        $toDate = $this->data['toDate'];

        $this->data['statistics']['subscriptions_and_telephony'] = [];

        $this->data['statistics']['subscriptions_and_telephony'][] = dashboardSetItem(
            key: 'package_subscription',
            label: trans('admin::dashboard.page.stats.active_package_subscriptions'),
            modelClass: PackageSubscription::class,
            fromDate: $fromDate,
            toDate: $toDate,
            customQuery: fn ($query) => $query->where('status', PackageSubscriptionStatus::Active),
            icon: 'fas fa-file-invoice-dollar',
            route: route('platform.package_subscriptions.index'),
        );

        $revenueQuery = PackageSubscription::query()
            ->join(
                'package_subscription_snapshots',
                'package_subscription_snapshots.package_subscription_id',
                '=',
                'package_subscriptions.id'
            )
            ->where('package_subscriptions.payment_status', PackageSubscriptionPaymentStatus::Paid);

        if ($fromDate && $toDate) {
            $revenueQuery->whereBetween('package_subscriptions.created_at', [$fromDate, $toDate]);
        }

        $revenueRows = (clone $revenueQuery)
            ->select([
                'package_subscription_snapshots.currency',
                DB::raw('SUM(package_subscription_snapshots.price) as total'),
            ])
            ->groupBy('package_subscription_snapshots.currency')
            ->get();

        $revenueDisplay = $revenueRows->isEmpty()
            ? trans('admin::dashboard.page.stats.revenue_empty')
            : $revenueRows
                ->map(fn ($row) => number_format((float) $row->total, 2, '.', ',').' '.$row->currency)
                ->implode(' · ');

        $this->data['statistics']['subscriptions_and_telephony'][] = [
            'key' => 'package_subscriptions_paid_revenue',
            'label' => trans('admin::dashboard.page.stats.package_subscriptions_paid_revenue'),
            'icon' => 'fas fa-coins',
            'route' => route('platform.package_subscriptions.index'),
            'count' => $revenueDisplay,
        ];

        $inboundCallsQuery = VerimorCallEvent::query()
            ->where('direction', VerimorCallDirection::Inbound)
            ->whereNotNull('user_id');

        if ($fromDate && $toDate) {
            $inboundCallsQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $this->data['statistics']['subscriptions_and_telephony'][] = [
            'key' => 'verimor_inbound_provider_calls',
            'label' => trans('admin::dashboard.page.stats.verimor_inbound_calls_to_providers'),
            'icon' => 'fas fa-phone-volume',
            'route' => route('verimor.verimor_call_events.index'),
            'count' => $inboundCallsQuery->count(),
        ];
    }

    private function getReviewsData(): void
    {
        $fromDate = $this->data['fromDate'];
        $toDate = $this->data['toDate'];

        $this->data['statistics']['reviews'] = [];

        $this->data['statistics']['reviews'][] = dashboardSetItem(
            key: 'review',
            label: trans('admin::dashboard.page.stats.reviews_total'),
            modelClass: Review::class,
            fromDate: $fromDate,
            toDate: $toDate,
            icon: 'fas fa-star',
            route: route('platform.reviews.index'),
        );

        $pendingQuery = Review::query()->where('status', ReviewStatus::Pending);
        if ($fromDate && $toDate) {
            $pendingQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $this->data['statistics']['reviews'][] = [
            'key' => 'reviews_pending_moderation',
            'label' => trans('admin::dashboard.page.stats.reviews_pending_moderation'),
            'icon' => 'fas fa-hourglass-half',
            'route' => route('platform.reviews.index'),
            'count' => $pendingQuery->count(),
        ];

        $avgQuery = Review::query()->approved();
        if ($fromDate && $toDate) {
            $avgQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $avg = $avgQuery->avg('rating');
        $avgDisplay = $avg === null
            ? trans('admin::dashboard.page.stats.reviews_avg_empty')
            : number_format((float) $avg, 2, '.', ',').'/5';

        $this->data['statistics']['reviews'][] = [
            'key' => 'reviews_average_rating',
            'label' => trans('admin::dashboard.page.stats.reviews_average_rating'),
            'icon' => 'fas fa-star-half-alt',
            'route' => route('platform.reviews.index'),
            'count' => $avgDisplay,
        ];
    }

    private function getCrmData()
    {
        $this->data['statistics']['crm'][] = dashboardSetItem(
            key         : 'contactuses',
            label       : trans('admin::dashboard.aside_menu.crm_management.contactuses'),
            modelClass  : Contactus::class,
            fromDate    : $this->data['fromDate'],
            toDate      : $this->data['toDate'],
        );

        $this->data['statistics']['crm'][] = dashboardSetItem(
            key         : 'subscribes',
            label       : trans('admin::dashboard.aside_menu.crm_management.subscribes'),
            modelClass  : Subscribe::class,
            fromDate    : $this->data['fromDate'],
            toDate      : $this->data['toDate'],
        );
    }
}
