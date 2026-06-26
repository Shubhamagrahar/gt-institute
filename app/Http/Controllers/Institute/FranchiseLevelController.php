<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\CourseDetail;
use App\Models\CourseType;
use App\Models\FranchiseCourseCharge;
use App\Models\FranchiseLevel;
use App\Models\LevelCourseCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FranchiseLevelController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index()
    {
        $levels = FranchiseLevel::where('institute_id', $this->instituteId())
            ->withCount('courseCharges')
            ->latest()
            ->get();

        return view('institute.franchise-levels.index', compact('levels'));
    }

    public function create()
    {
        return view('institute.franchise-levels.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'level_fee'          => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',
            'status'             => 'required|in:active,inactive',
        ]);

        $data['institute_id'] = $this->instituteId();
        $level = FranchiseLevel::create($data);

        return redirect()
            ->route('institute.franchise-levels.charges', $level)
            ->with('success', 'Level created! Now set the course charges for this level.');
    }

    public function edit(FranchiseLevel $franchiseLevel)
    {
        $this->authorizeLevel($franchiseLevel);

        return view('institute.franchise-levels.edit', ['level' => $franchiseLevel]);
    }

    public function update(Request $request, FranchiseLevel $franchiseLevel)
    {
        $this->authorizeLevel($franchiseLevel);

        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'level_fee'          => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',
            'status'             => 'required|in:active,inactive',
        ]);

        $franchiseLevel->update($data);

        return redirect()
            ->route('institute.franchise-levels.index')
            ->with('success', 'Franchise level updated successfully.');
    }

    // ─── Step 2: Set Duration Charges per Course Type ────────────────────────

    public function chargesStep(FranchiseLevel $franchiseLevel)
    {
        $this->authorizeLevel($franchiseLevel);
        $instituteId = $this->instituteId();

        // All active course types for this institute
        $courseTypes = CourseType::where('institute_id', $instituteId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        // For each course type: unique durations with course count
        // Also build "all types" unique durations
        $dursByType = [];
        foreach ($courseTypes as $ct) {
            $dursByType[$ct->id] = CourseDetail::where('institute_id', $instituteId)
                ->where('status', 'active')
                ->where('course_type_id', $ct->id)
                ->whereNotNull('duration')
                ->where('duration', '>', 0)
                ->selectRaw('duration, count(*) as course_count')
                ->groupBy('duration')
                ->orderBy('duration')
                ->get();
        }

        // "All types" = unique durations across ALL courses
        $dursByType[0] = CourseDetail::where('institute_id', $instituteId)
            ->where('status', 'active')
            ->whereNotNull('duration')
            ->where('duration', '>', 0)
            ->selectRaw('duration, count(*) as course_count')
            ->groupBy('duration')
            ->orderBy('duration')
            ->get();

        // Existing charges keyed by course_id for pre-filling (if editing)
        $existingByDuration = LevelCourseCharge::where('franchise_level_id', $franchiseLevel->id)
            ->selectRaw('duration, MIN(student_admission_charge) as adm, MIN(student_certificate_charge) as cert')
            ->groupBy('duration')
            ->pluck('adm', 'duration')
            ->toArray();
        $existingCertByDuration = LevelCourseCharge::where('franchise_level_id', $franchiseLevel->id)
            ->selectRaw('duration, MIN(student_certificate_charge) as cert')
            ->groupBy('duration')
            ->pluck('cert', 'duration')
            ->toArray();

        return view('institute.franchise-levels.charges', compact(
            'franchiseLevel', 'courseTypes', 'dursByType',
            'existingByDuration', 'existingCertByDuration'
        ));
    }

    public function storeCharges(Request $request, FranchiseLevel $franchiseLevel)
    {
        $this->authorizeLevel($franchiseLevel);
        $instituteId = $this->instituteId();

        // Posted: rows[i][course_type_id], rows[i][duration], rows[i][adm], rows[i][cert]
        $rows = $request->input('rows', []);

        DB::transaction(function () use ($rows, $franchiseLevel, $instituteId) {
            foreach ($rows as $row) {
                $typeId   = (int) ($row['course_type_id'] ?? 0);
                $duration = (int) ($row['duration'] ?? 0);
                $adm      = (float) ($row['adm'] ?? 0);
                $cert     = (float) ($row['cert'] ?? 0);

                if ($duration <= 0 || ($adm <= 0 && $cert <= 0)) {
                    continue;
                }

                // Find matching courses
                $query = CourseDetail::where('institute_id', $instituteId)
                    ->where('status', 'active')
                    ->where('duration', $duration);

                if ($typeId > 0) {
                    $query->where('course_type_id', $typeId);
                }

                $courses = $query->get(['id', 'name', 'duration']);

                foreach ($courses as $course) {
                    LevelCourseCharge::updateOrCreate(
                        [
                            'franchise_level_id' => $franchiseLevel->id,
                            'course_id'          => $course->id,
                        ],
                        [
                            'institute_id'              => $instituteId,
                            'course_name'               => $course->name,
                            'duration'                  => $course->duration,
                            'student_admission_charge'  => $adm,
                            'student_certificate_charge'=> $cert,
                            'status'                    => 'active',
                        ]
                    );
                }
            }
        });

        return redirect()
            ->route('institute.franchise-levels.charges.edit', $franchiseLevel)
            ->with('success', 'Charges saved! You can now review and fine-tune individual courses below.');
    }

    // ─── View / Edit All Charges ─────────────────────────────────────────────

    public function editCharges(FranchiseLevel $franchiseLevel)
    {
        $this->authorizeLevel($franchiseLevel);
        $instituteId = $this->instituteId();

        $courseTypes = CourseType::where('institute_id', $instituteId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        // All charges with course type info
        $charges = LevelCourseCharge::where('franchise_level_id', $franchiseLevel->id)
            ->join('course_details', 'course_details.id', '=', 'level_course_charges.course_id')
            ->leftJoin('course_types', 'course_types.id', '=', 'course_details.course_type_id')
            ->select(
                'level_course_charges.id',
                'level_course_charges.course_id',
                'level_course_charges.course_name',
                'level_course_charges.duration',
                'level_course_charges.student_admission_charge',
                'level_course_charges.student_certificate_charge',
                'course_details.course_type_id',
                'course_details.course_short_name',
                'course_types.name as type_name'
            )
            ->orderBy('course_types.name')
            ->orderBy('level_course_charges.duration')
            ->orderBy('level_course_charges.course_name')
            ->get();

        return view('institute.franchise-levels.charges-edit', compact(
            'franchiseLevel', 'charges', 'courseTypes'
        ));
    }

    public function updateCharge(Request $request, FranchiseLevel $franchiseLevel, LevelCourseCharge $charge)
    {
        $this->authorizeLevel($franchiseLevel);
        abort_if($charge->franchise_level_id !== $franchiseLevel->id, 403);

        $data = $request->validate([
            'student_admission_charge'   => 'required|numeric|min:0',
            'student_certificate_charge' => 'required|numeric|min:0',
        ]);

        $charge->update($data);

        // Cascade to all franchises on this level — updateOrCreate so missing rows are also created
        $iid        = $this->instituteId();
        $course     = CourseDetail::find($charge->course_id);
        $franchises = \App\Models\Franchise::where('franchise_level_id', $franchiseLevel->id)
            ->where('institute_id', $iid)
            ->pluck('id');

        foreach ($franchises as $franchiseId) {
            FranchiseCourseCharge::updateOrCreate(
                ['franchise_id' => $franchiseId, 'course_id' => $charge->course_id],
                [
                    'institute_id'       => $iid,
                    'course_type_id'     => $course?->course_type_id,
                    'course_name'        => $charge->course_name,
                    'duration'           => $charge->duration,
                    'admission_charge'   => $data['student_admission_charge'],
                    'certificate_charge' => $data['student_certificate_charge'],
                    'enabled'            => true,
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    // ─── Auth Helper ──────────────────────────────────────────────────────────

    private function authorizeLevel(FranchiseLevel $franchiseLevel): void
    {
        abort_if($franchiseLevel->institute_id !== $this->instituteId(), 403);
    }
}
