<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class AdminBrandingController extends Controller
{
    public function index(): View
    {
        $logoPath = SiteSetting::get('logo_path');
        $siteName = SiteSetting::get('site_name', config('app.name'));

        return view('admin.branding.index', compact('logoPath', 'siteName'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        // Update site name
        if ($request->filled('site_name')) {
            SiteSetting::set('site_name', $request->input('site_name'));
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            $oldLogo = SiteSetting::get('logo_path');
            if ($oldLogo) {
                $oldPath = public_path($oldLogo);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            // Ensure branding directory exists
            $brandingDir = public_path('branding');
            if (!File::isDirectory($brandingDir)) {
                File::makeDirectory($brandingDir, 0755, true);
            }

            // Store new logo directly in public/branding/
            $file = $request->file('logo');
            $filename = 'logo-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($brandingDir, $filename);

            SiteSetting::set('logo_path', 'branding/' . $filename);
        }

        return redirect()->route('admin.branding.index')
            ->with('success', 'Branding settings updated successfully.');
    }

    public function removeLogo(): RedirectResponse
    {
        $logoPath = SiteSetting::get('logo_path');

        if ($logoPath) {
            $fullPath = public_path($logoPath);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        SiteSetting::set('logo_path', null);

        return redirect()->route('admin.branding.index')
            ->with('success', 'Logo removed successfully.');
    }
}
