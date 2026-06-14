<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    private function franchise(): Franchise
    {
        $user = Auth::guard('institute')->user();
        return Franchise::with(['institute', 'level'])
            ->where('id', $user->franchise_id)
            ->where('institute_id', $user->institute_id)
            ->firstOrFail();
    }

    public function index()
    {
        $franchise = $this->franchise();
        return view('franchise.certificate.index', compact('franchise'));
    }

    public function view()
    {
        $franchise = $this->franchise();
        $franchise->load(['institute', 'level', 'head.profile']);
        // Reuse the institute certificate view (full-page certificate)
        return view('institute.franchises.certificate', compact('franchise'));
    }
}
