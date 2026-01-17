<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = auth()->user()->getSettings();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_meeting_frequency_days' => 'required|integer|min:1|max:365',
        ]);

        $settings = auth()->user()->getSettings();
        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Settings saved successfully.');
    }
}
