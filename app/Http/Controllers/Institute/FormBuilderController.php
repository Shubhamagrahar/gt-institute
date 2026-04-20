<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\AdmissionFormField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormBuilderController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index()
    {
        $instituteId   = $this->instituteId();
        $allFields     = AdmissionFormField::allDefinedFields();
        $savedFields   = AdmissionFormField::where('institute_id', $instituteId)
                            ->orderBy('sort_order')
                            ->get()
                            ->keyBy('field_key');

        return view('institute.form-builder.index', compact('allFields', 'savedFields'));
    }

    public function save(Request $request)
    {
        $instituteId = $this->instituteId();
        $fields      = AdmissionFormField::allDefinedFields();
        $active      = $request->input('active', []);
        $required    = $request->input('required', []);

        foreach ($fields as $i => $field) {
            AdmissionFormField::updateOrCreate(
                ['institute_id' => $instituteId, 'field_key' => $field['key']],
                [
                    'field_label' => $field['label'],
                    'field_type'  => $field['type'],
                    'options'     => $field['options'] ?? null,
                    'is_active'   => in_array($field['key'], $active),
                    'is_required' => in_array($field['key'], $required),
                    'sort_order'  => $i,
                ]
            );
        }

        return back()->with('success', 'Admission form updated successfully.');
    }
}