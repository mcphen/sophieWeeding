<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
use Inertia\Inertia;

class LegalController extends Controller
{
    protected ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function legalNotice()
    {
        return Inertia::render('Front/LegalNotice', [
            'contactSettings' => $this->contactService->getContactSettings(),
        ]);
    }

    public function privacyPolicy()
    {
        return Inertia::render('Front/PrivacyPolicy', [
            'contactSettings' => $this->contactService->getContactSettings(),
        ]);
    }
}
