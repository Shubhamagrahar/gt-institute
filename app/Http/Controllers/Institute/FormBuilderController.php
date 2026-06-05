<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\AdmissionFormField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FormBuilderController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index(): View
    {
        return view('institute.form-builder.index');
    }

    public function admission(): View
    {
        return view('institute.form-builder.builder', $this->builderPayload('admission'));
    }

    public function quick(): View
    {
        return view('institute.form-builder.builder', $this->builderPayload('quick'));
    }

    public function printAdmission(): View
    {
        return view('institute.form-builder.print', $this->builderPayload('admission'));
    }

    public function saveAdmission(Request $request): RedirectResponse
    {
        return $this->saveForMode($request, 'admission');
    }

    public function saveQuick(Request $request): RedirectResponse
    {
        return $this->saveForMode($request, 'quick');
    }

    private function saveForMode(Request $request, string $mode): RedirectResponse
    {
        $instituteId = $this->instituteId();
        $fields = AdmissionFormField::allDefinedFields();
        $active = $request->input('active', []);
        $required = $request->input('required', []);

        foreach ($fields as $index => $field) {
            $row = AdmissionFormField::firstOrNew([
                'institute_id' => $instituteId,
                'field_key' => $field['key'],
            ]);

            $row->field_label = $field['label'];
            $row->field_type = $field['type'];
            $row->options = $field['options'] ?? null;
            $row->sort_order = $index;

            if ($mode === 'admission') {
                $row->is_active = in_array($field['key'], $active, true);
                $row->is_required = in_array($field['key'], $required, true);
            } else {
                $row->quick_is_active = in_array($field['key'], $active, true);
                $row->quick_is_required = in_array($field['key'], $required, true);
            }

            $row->save();
        }

        return back()->with('success', ucfirst($mode) . ' form builder updated successfully.');
    }

    private function builderPayload(string $mode): array
    {
        $instituteId = $this->instituteId();
        $allFields = AdmissionFormField::allDefinedFields();
        $savedFields = AdmissionFormField::where('institute_id', $instituteId)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('field_key');
        $instituteUser = Auth::guard('institute')->user()->loadMissing('institute');

        return [
            'allFields' => $allFields,
            'savedFields' => $savedFields,
            'builderMode' => $mode,
            'institute' => $instituteUser->institute,
            'instituteUser' => $instituteUser,
        ];
    }
}
