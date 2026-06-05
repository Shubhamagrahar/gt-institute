<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admission Form Print</title>
  <style>
    :root{
      --ink:#0f172a;
      --muted:#475569;
      --line:#c7d4ea;
      --soft:#eef4ff;
      --brand:#173f92;
    }
    *{box-sizing:border-box}
    body{margin:0;background:#edf2f9;font-family:Arial,sans-serif;color:var(--ink)}
    .print-shell{max-width:900px;margin:20px auto;padding:0 14px}
    .print-actions{display:flex;justify-content:flex-end;gap:10px;margin-bottom:12px}
    .print-btn{display:inline-flex;align-items:center;justify-content:center;padding:10px 14px;border-radius:10px;border:1px solid #cbd5e1;background:#fff;color:var(--ink);text-decoration:none;font-size:13px;font-weight:700;cursor:pointer}

    .print-form{background:#fff;border:1px solid #d8e2f3;box-shadow:0 18px 44px rgba(15,23,42,.08);padding:24px 24px 18px}
    .print-header{display:flex;justify-content:space-between;gap:20px;border-bottom:2px solid var(--brand);padding-bottom:12px;margin-bottom:16px}
    .print-title{font-size:28px;font-weight:900;color:var(--brand);line-height:1.1}
    .print-subtitle{font-size:13px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#1e40af;margin-top:6px}
    .print-address{font-size:12px;color:var(--muted);margin-top:8px;max-width:480px;line-height:1.55}
    .print-header-side{display:flex;flex-direction:column;gap:6px;font-size:12px;color:var(--muted);text-align:right;min-width:220px}
    .print-top-grid{display:grid;grid-template-columns:minmax(0,1fr) 160px;gap:14px;margin-bottom:14px}
    .print-section-card{border:1px solid var(--line);padding:12px 14px 14px}
    .print-section-heading{font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.08em;color:var(--brand);margin-bottom:10px}
    .print-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px 14px}
    .print-grid-basic{grid-template-columns:repeat(2,minmax(0,1fr))}
    .print-field{min-height:48px}
    .print-field-wide{grid-column:1 / -1}
    .print-field label{display:block;font-size:11px;font-weight:700;margin-bottom:6px;color:#111827}
    .print-required{color:#dc2626;margin-left:3px}
    .print-input-line{height:30px;border:1px solid var(--line);background:#fff}
    .print-input-textarea{height:64px}
    .print-photo-card{border:1px solid var(--line);padding:12px}
    .print-photo-slot{height:100%;min-height:172px;border:1px dashed #9eb6dc;display:flex;align-items:center;justify-content:center;text-align:center;color:var(--muted);font-size:11px;padding:12px}
    .print-photo-inline{height:64px;border:1px dashed #9eb6dc;display:flex;align-items:center;justify-content:center;text-align:center;color:var(--muted);font-size:11px;padding:10px}
    .print-edu-wrap{border:1px solid var(--line)}
    .print-edu-table{width:100%;border-collapse:collapse}
    .print-edu-table th,.print-edu-table td{border:1px solid #d7e2f5;padding:8px 7px;text-align:left;font-size:10px}
    .print-edu-table th{background:var(--soft);color:var(--brand);font-weight:800}
    .print-footer-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-top:22px}
    .print-sign-block{font-size:11px;font-weight:700;color:var(--muted)}
    .print-sign-line{margin-top:28px;border-bottom:1.6px solid #94a3b8}

    @media screen and (max-width: 900px){
      .print-top-grid{grid-template-columns:1fr}
      .print-grid,.print-grid-basic{grid-template-columns:repeat(2,minmax(0,1fr))}
      .print-header{flex-direction:column}
      .print-header-side{text-align:left;min-width:0}
    }
    @media screen and (max-width: 640px){
      .print-grid,.print-grid-basic,.print-footer-grid{grid-template-columns:1fr}
    }
    @media print{
      @page{size:A4;margin:10mm}
      body{background:#fff}
      .print-shell{max-width:none;margin:0;padding:0}
      .print-actions{display:none}
      .print-form{border:none;box-shadow:none;padding:0}
    }
  </style>
</head>
<body>
  @php($fieldsByKey = collect($allFields)->keyBy('key'))
  <div class="print-shell">
    <div class="print-actions">
      <button type="button" class="print-btn" onclick="window.print()">Download / Print</button>
    </div>
    @include('institute.form-builder._print_preview', ['builderMode' => $builderMode, 'fieldsByKey' => $fieldsByKey, 'savedFields' => $savedFields, 'institute' => $institute])
  </div>
</body>
</html>
