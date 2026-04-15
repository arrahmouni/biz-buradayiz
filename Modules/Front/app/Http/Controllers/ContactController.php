<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Crm\Http\Requests\Api\ContactusRequest;
use Modules\Crm\Http\Services\ContactusService;

class ContactController extends BaseController
{
    public function show(): View
    {
        return view('front::contents.contact', $this->data);
    }

    public function store(ContactusRequest $request, ContactusService $contactusService): RedirectResponse
    {
        $result = $contactusService->createModel($request->validated());

        if (is_array($result) && isset($result['success']) && ! $result['success']) {
            return redirect()
                ->route('front.contact.show')
                ->withInput()
                ->withErrors(['form' => $result['message'] ?? __('front::home.contact_error')]);
        }

        return redirect()
            ->route('front.contact.show')
            ->with('success', __('front::home.contact_success'));
    }
}
