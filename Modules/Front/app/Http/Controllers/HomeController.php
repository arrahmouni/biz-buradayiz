<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Platform\Models\Service;
use Modules\Zms\Models\City;
use Modules\Zms\Models\State;

class HomeController extends Controller
{
    public function index()
    {
        $filterServices = Service::query()
            ->forSearchFilters()
            ->orderBy('id')
            ->get()
            ->map(fn (Service $service) => [
                'id' => $service->id,
                'name' => $service->smartTrans('name') ?? (string) $service->id,
            ]);

        return view('front::index', [
            'filterServices' => $filterServices,
        ]);
    }

    public function search(Request $request)
    {
        $request->merge([
            'state_id' => $request->filled('state_id') ? $request->integer('state_id') : null,
            'city_id' => $request->filled('city_id') ? $request->integer('city_id') : null,
            'service_id' => $request->filled('service_id') ? $request->integer('service_id') : null,
        ]);

        $validated = $request->validate([
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'service_id' => [
                'nullable',
                'integer',
                Rule::exists('services', 'id')->whereNull('deleted_at'),
            ],
        ]);

        $stateName = null;
        $cityName = null;
        $serviceName = null;

        if (! empty($validated['state_id'])) {
            $state = State::query()->find($validated['state_id']);
            $stateName = $state ? ($state->smartTrans('name') ?? $state->native_name) : null;
        }

        if (! empty($validated['city_id'])) {
            $city = City::query()->find($validated['city_id']);
            $cityName = $city ? ($city->smartTrans('name') ?? $city->native_name) : null;
        }

        if (! empty($validated['service_id'])) {
            $service = Service::query()->find($validated['service_id']);
            $serviceName = $service ? ($service->smartTrans('name') ?? (string) $service->id) : null;
        }

        return view('front::search', [
            'stateId' => $validated['state_id'] ?? null,
            'cityId' => $validated['city_id'] ?? null,
            'serviceId' => $validated['service_id'] ?? null,
            'stateName' => $stateName,
            'cityName' => $cityName,
            'serviceName' => $serviceName,
        ]);
    }
}
