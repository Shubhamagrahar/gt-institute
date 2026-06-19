<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\CourseDetail;
use App\Models\Enquiry;
use App\Models\EnquiryFollowup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnquiryController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index(Request $request)
    {
        $iid     = $this->instituteId();
        $tab     = $request->get('tab', 'open');
        $search  = trim($request->get('q', ''));
        $outcome = $request->get('outcome', '');

        $query = Enquiry::with(['course', 'followups'])
            ->where('institute_id', $iid);

        match ($tab) {
            'due'       => $query->where('status', 'OPEN')
                                 ->whereDate('next_followup_date', '<=', today()),
            'converted' => $query->where('status', 'CONVERTED'),
            'lost'      => $query->where('status', 'LOST'),
            default     => $query->where('status', 'OPEN'),
        };

        // Filter open enquiries by last followup outcome
        $validOutcomes = ['INTERESTED', 'NOT_INTERESTED', 'CALLBACK', 'NO_RESPONSE'];
        if ($outcome && in_array($outcome, $validOutcomes) && $tab === 'open') {
            $query->whereHas('followups', function ($q) use ($outcome) {
                $q->where('outcome', $outcome)
                  ->whereRaw('id = (SELECT MAX(f2.id) FROM enquiry_followups f2 WHERE f2.enquiry_id = enquiries.id)');
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enquiries = $query->latest()->paginate(25)->withQueryString();

        $counts = [
            'open'      => Enquiry::where('institute_id', $iid)->where('status', 'OPEN')->count(),
            'due'       => Enquiry::where('institute_id', $iid)->where('status', 'OPEN')
                                  ->whereDate('next_followup_date', '<=', today())->count(),
            'converted' => Enquiry::where('institute_id', $iid)->where('status', 'CONVERTED')->count(),
            'lost'      => Enquiry::where('institute_id', $iid)->where('status', 'LOST')->count(),
        ];

        // Outcome counts (last followup per open enquiry)
        $outcomeCounts = collect(\DB::select("
            SELECT ef.outcome, COUNT(*) as cnt
            FROM enquiry_followups ef
            WHERE ef.id = (SELECT MAX(f2.id) FROM enquiry_followups f2 WHERE f2.enquiry_id = ef.enquiry_id)
              AND ef.enquiry_id IN (SELECT id FROM enquiries WHERE institute_id = ? AND status = 'OPEN')
            GROUP BY ef.outcome
        ", [$iid]))->pluck('cnt', 'outcome');

        $total     = $counts['open'] + $counts['converted'] + $counts['lost'];
        $convRate  = $total > 0 ? round(($counts['converted'] / $total) * 100) : 0;

        return view('institute.enquiries.index', compact(
            'enquiries', 'tab', 'search', 'counts', 'outcome', 'outcomeCounts', 'convRate', 'total'
        ));
    }

    public function create()
    {
        $iid     = $this->instituteId();
        $courses = CourseDetail::with('courseType')
            ->where('institute_id', $iid)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $courseTypes = $courses->pluck('courseType')->filter()->unique('id')->sortBy('name')->values();

        $courseCatalog = $courses->map(fn ($c) => [
            'id'             => $c->id,
            'name'           => $c->name,
            'course_type_id' => $c->course_type_id,
            'duration'       => (int) ($c->duration ?: 1),
        ])->values();

        return view('institute.enquiries.create', compact('courses', 'courseTypes', 'courseCatalog'));
    }

    public function store(Request $request)
    {
        $iid = $this->instituteId();

        $data = $request->validate([
            'name'               => 'required|string|max:150',
            'mobile'             => 'required|string|size:10',
            'email'              => 'nullable|email|max:150',
            'course_id'          => 'nullable|exists:course_details,id',
            'source'             => 'required|in:WALK_IN,PHONE,ONLINE,REFERENCE',
            'notes'              => 'nullable|string|max:1000',
            'next_followup_date' => 'nullable|date|after_or_equal:today',
        ]);

        // Warn about duplicate mobile but allow creation
        Enquiry::create(array_merge($data, ['institute_id' => $iid]));

        return redirect()->route('institute.enquiries.index')
            ->with('success', 'Enquiry saved. Remember to follow up!');
    }

    public function show(Enquiry $enquiry)
    {
        $this->authorize($enquiry);

        $enquiry->load(['course', 'followups.staff.profile', 'courseBook']);

        return view('institute.enquiries.show', compact('enquiry'));
    }

    public function storeFollowup(Request $request, Enquiry $enquiry)
    {
        $this->authorize($enquiry);

        $data = $request->validate([
            'notes'              => 'required|string|max:2000',
            'outcome'            => 'required|in:INTERESTED,NOT_INTERESTED,CALLBACK,NO_RESPONSE',
            'next_followup_date' => 'nullable|date',
        ]);

        $data['enquiry_id'] = $enquiry->id;
        $data['created_by'] = Auth::guard('institute')->id();

        EnquiryFollowup::create($data);

        // Update enquiry's next follow-up date
        $enquiry->update(['next_followup_date' => $data['next_followup_date'] ?? null]);

        return redirect()->route('institute.enquiries.show', $enquiry)
            ->with('success', 'Follow-up logged successfully.');
    }

    public function markLost(Request $request, Enquiry $enquiry)
    {
        $this->authorize($enquiry);

        $request->validate([
            'lost_reason' => 'nullable|string|max:255',
        ]);

        $enquiry->update([
            'status'      => 'LOST',
            'lost_reason' => $request->lost_reason,
        ]);

        return redirect()->route('institute.enquiries.index')
            ->with('success', 'Enquiry marked as lost.');
    }

    public function convert(Enquiry $enquiry)
    {
        $this->authorize($enquiry);

        // Go directly to full booking with enquiry_id — form will auto pre-fill all available data
        return redirect()->route('institute.enrollment.new', [
            'enquiry_id' => $enquiry->id,
        ]);
    }

    public function checkDuplicate(Request $request)
    {
        $iid    = $this->instituteId();
        $mobile = $request->get('mobile');

        $existing = Enquiry::with('course')
            ->where('institute_id', $iid)
            ->where('mobile', $mobile)
            ->where('status', 'OPEN')
            ->latest()
            ->first();

        if ($existing) {
            return response()->json([
                'found'  => true,
                'id'     => $existing->id,
                'name'   => $existing->name,
                'course' => $existing->course?->name ?? '—',
                'date'   => $existing->created_at->format('d M Y'),
            ]);
        }

        return response()->json(['found' => false]);
    }

    private function authorize(Enquiry $enquiry): void
    {
        abort_if($enquiry->institute_id !== $this->instituteId(), 403);
    }
}
