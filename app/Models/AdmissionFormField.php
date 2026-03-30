<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionFormField extends Model
{
    protected $table = 'admission_form_fields';

    protected $fillable = [
        'institute_id', 'field_key', 'field_label',
        'field_type', 'options', 'is_required',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
    ];

    // All available fields with defaults
    public static function allDefinedFields(): array
    {
        return [
            ['key' => 'name',                'label' => 'Full Name',              'type' => 'text'],
            ['key' => 'photo',               'label' => 'Photo',                  'type' => 'file'],
            ['key' => 'father_name',         'label' => "Father's Name",          'type' => 'text'],
            ['key' => 'mother_name',         'label' => "Mother's Name",          'type' => 'text'],
            ['key' => 'guardian_name',       'label' => 'Guardian Name',          'type' => 'text'],
            ['key' => 'guardian_relation',   'label' => 'Guardian Relation',      'type' => 'select',
             'options' => 'Father,Mother,Brother,Sister,Uncle,Other'],
            ['key' => 'guardian_mobile',     'label' => 'Guardian Mobile',        'type' => 'number'],
            ['key' => 'guardian_occupation', 'label' => 'Guardian Occupation',    'type' => 'select',
             'options' => 'Government Job,Private Job,Business,Farmer,Other'],
            ['key' => 'dob',                 'label' => 'Date of Birth',          'type' => 'date'],
            ['key' => 'gender',              'label' => 'Gender',                 'type' => 'select',
             'options' => 'Male,Female,Other'],
            ['key' => 'category',            'label' => 'Category',               'type' => 'select',
             'options' => 'General,OBC,SC,ST,EWS'],
            ['key' => 'religion',            'label' => 'Religion',               'type' => 'text'],
            ['key' => 'nationality',         'label' => 'Nationality',            'type' => 'text'],
            ['key' => 'whatsapp_no',         'label' => 'WhatsApp Number',        'type' => 'number'],
            ['key' => 'alternate_mobile',    'label' => 'Alternate Mobile',       'type' => 'number'],
            ['key' => 'aadhar_no',           'label' => 'Aadhar Number',          'type' => 'number'],
            ['key' => 'pan_no',              'label' => 'PAN Number',             'type' => 'text'],
            ['key' => 'blood_group',         'label' => 'Blood Group',            'type' => 'select',
             'options' => 'A+,A-,B+,B-,O+,O-,AB+,AB-'],
            ['key' => 'employment_status',   'label' => 'Employment Status',      'type' => 'select',
             'options' => 'Employed,Unemployed'],
            ['key' => 'computer_literacy',   'label' => 'Computer Literacy',      'type' => 'select',
             'options' => 'Yes,No'],
            ['key' => 'qualification',       'label' => 'Highest Qualification',  'type' => 'select',
             'options' => 'Below 10th,10th,12th,Diploma,Graduation,Post Graduation,Other'],
            ['key' => 'address',             'label' => 'Present Address',        'type' => 'textarea'],
            ['key' => 'permanent_address',   'label' => 'Permanent Address',      'type' => 'textarea'],
            ['key' => 'state',               'label' => 'State',                  'type' => 'text'],
            ['key' => 'district',            'label' => 'District',               'type' => 'text'],
            ['key' => 'pin_code',            'label' => 'PIN Code',               'type' => 'number'],
        ];
    }

    public function institute()
    {
        return $this->belongsTo(\App\Models\Owner\Institute::class);
    }
}