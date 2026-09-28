<?php

namespace App\Http\Controllers;

use App\Models\OnboardingLead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicController extends Controller
{
    public function home()
    {
        return Inertia::render('Public/Welcome', [
            'stats' => [['value' => '08', 'label' => 'branches in one view'], ['value' => '24/7', 'label' => 'operational clarity'], ['value' => '01', 'label' => 'calm command centre']],
        ]);
    }

    public function register(string $step = 'business')
    {
        abort_unless(in_array($step, ['business', 'team', 'branch', 'complete'], true), 404);
        return Inertia::render('Public/Register', ['step' => $step]);
    }

    public function storeRegistration(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'business_name' => ['required', 'string', 'max:120'],
            'business_type' => ['nullable', 'string', 'max:80'],
            'branch_count' => ['required', 'integer', 'min:1', 'max:500'],
        ]);
        OnboardingLead::create($data);
        return to_route('register', ['step' => 'complete'])->with('success', 'Your workspace request is in the queue.');
    }
}
