<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Zms\Models\City;
use Modules\Zms\Models\Country;
use Modules\Zms\Models\State;

class GeographyController extends Controller
{
    public function states(): JsonResponse
    {
        $countryId = $this->turkeyCountryId();
        if ($countryId === null) {
            return response()->json(['data' => []]);
        }

        $states = State::query()
            ->where('country_id', $countryId)
            ->orderBy('native_name')
            ->get()
            ->map(fn (State $state) => [
                'id' => $state->id,
                'name' => $state->smartTrans('name') ?? $state->native_name,
            ])
            ->values();

        return response()->json(['data' => $states]);
    }

    public function cities(Request $request): JsonResponse
    {
        $request->validate([
            'state_id' => ['required', 'integer', 'exists:states,id'],
        ]);

        $cities = City::query()
            ->where('state_id', $request->integer('state_id'))
            ->orderBy('native_name')
            ->get()
            ->map(fn (City $city) => [
                'id' => $city->id,
                'name' => $city->smartTrans('name') ?? $city->native_name,
            ])
            ->values();

        return response()->json(['data' => $cities]);
    }

    private function turkeyCountryId(): ?int
    {
        return Cache::rememberForever('geography.country.iso2.tr.id', function () {
            return Country::query()->where('iso2', 'TR')->value('id');
        });
    }
}
