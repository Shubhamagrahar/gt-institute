<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::where('group', 'security')->get()->keyBy('key');
        return view('owner.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'otp_login_enabled' => 'required|in:0,1',
        ]);

        SystemSetting::set('otp_login_enabled', $request->input('otp_login_enabled'));

        $status = $request->input('otp_login_enabled') === '1' ? 'enabled' : 'disabled';

        return back()->with('success', "OTP login has been {$status} successfully.");
    }
}
