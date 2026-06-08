<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\ChannelPartner;
use App\Models\District;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ChannelPartnerController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index()
    {
        $status = request('status', 'all');
        $search = trim((string) request('search', ''));

        $partners = ChannelPartner::withCount('admissions')
            ->where('institute_id', $this->instituteId())
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('whatsapp_no', 'like', "%{$search}%")
                        ->orWhere('alternate_mobile', 'like', "%{$search}%")
                        ->orWhere('father_name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('state', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('institute.channel-partners.index', compact('partners', 'status', 'search'));
    }

    public function create()
    {
        return view('institute.channel-partners.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['institute_id'] = $this->instituteId();

        ChannelPartner::create($data);

        return redirect()->route('institute.channel-partners.index')
            ->with('success', 'Channel partner added successfully.');
    }

    public function edit(ChannelPartner $channelPartner)
    {
        $this->authorizePartner($channelPartner);

        return view('institute.channel-partners.edit', compact('channelPartner') + $this->formData());
    }

    public function update(Request $request, ChannelPartner $channelPartner)
    {
        $this->authorizePartner($channelPartner);
        $data = $this->validateData($request, $channelPartner->id);

        $channelPartner->update($data);

        return redirect()->route('institute.channel-partners.index')
            ->with('success', 'Channel partner updated successfully.');
    }

    public function toggle(ChannelPartner $channelPartner)
    {
        $this->authorizePartner($channelPartner);

        $newStatus = $channelPartner->status === 'active' ? 'inactive' : 'active';

        $channelPartner->update([
            'status' => $newStatus,
        ]);

        return redirect()
            ->route('institute.channel-partners.index', request()->only(['status', 'search', 'page']))
            ->with('success', "Channel partner marked as {$newStatus}.");
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'mobile' => [
                'required',
                'string',
                'max:15',
                Rule::unique('channel_partners', 'mobile')
                    ->where(fn ($query) => $query->where('institute_id', $this->instituteId()))
                    ->ignore($ignoreId),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('channel_partners', 'email')
                    ->where(fn ($query) => $query->where('institute_id', $this->instituteId()))
                    ->ignore($ignoreId),
            ],
            'whatsapp_no' => 'nullable|string|max:15',
            'alternate_mobile' => 'nullable|string|max:15',
            'father_name' => 'nullable|string|max:150',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'occupation' => 'nullable|string|max:120',
            'aadhar_no' => [
                'nullable',
                'string',
                'max:16',
                Rule::unique('channel_partners', 'aadhar_no')
                    ->where(fn ($query) => $query->where('institute_id', $this->instituteId()))
                    ->ignore($ignoreId),
            ],
            'pan_no' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'state' => ['nullable', 'string', 'max:100', Rule::exists('states', 'name')],
            'district' => array_values(array_filter([
                'nullable',
                'string',
                'max:100',
                Schema::hasTable('districts') ? Rule::exists('districts', 'name') : null,
            ])),
            'city' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);
    }

    private function formData(): array
    {
        $states = State::orderBy('name')->pluck('name');
        $districtsByState = Schema::hasTable('districts')
            ? District::query()
                ->select('districts.name as district_name', 'states.name as state_name')
                ->join('states', 'states.id', '=', 'districts.state_id')
                ->orderBy('states.name')
                ->orderBy('districts.name')
                ->get()
                ->groupBy('state_name')
                ->map(fn ($rows) => $rows->pluck('district_name')->values()->all())
                ->toArray()
            : [];

        return compact('states', 'districtsByState');
    }

    private function authorizePartner(ChannelPartner $channelPartner): void
    {
        abort_if($channelPartner->institute_id !== $this->instituteId(), 403);
    }
}
