<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Prescription - {{ $record->patient->full_name ?? '' }}</title>
    <style>
        body { font-family: Inter, system-ui, -apple-system, 'Helvetica Neue', Arial; color: #111827; margin: 24px; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px }
        .clinic { font-size:14px; color:#374151 }
        .doctor { font-weight:700; font-size:18px }
        .patient, .meta { margin-top:6px; font-size:14px }
        .rx { margin-top:18px; border-top:1px solid #e5e7eb; padding-top:12px }
        table { width:100%; border-collapse:collapse; margin-top:8px }
        th, td { text-align:left; padding:8px 6px; border-bottom:1px solid #f3f4f6 }
        th { background:#fafafa; font-weight:600 }
        .signature { margin-top:28px; display:flex; justify-content:space-between }
        .sig-box { width:40%; text-align:left }
        .sig-line { margin-top:40px; border-top:1px solid #111827; width:80% }
        @media print { .no-print { display:none } }
    </style>
 </head>
<body>
    <div class="header">
        <div>
            <div class="clinic">{{ config('app.name') }} — {{ $record->clinic->name ?? '' }}</div>
            <div class="patient"><strong>Patient:</strong> {{ $record->patient->full_name ?? 'Unknown' }} ({{ $record->patient->patient_id ?? '' }})</div>
            <div class="meta">DOB: {{ optional($record->patient->date_of_birth)->format('M d, Y') ?? 'N/A' }} • Date: {{ optional($record->consultation_date)->format('M d, Y') }}</div>
        </div>
        <div style="text-align:right">
            <div class="doctor">Dr. {{ $record->doctor?->name ?? $record->user?->name ?? '' }}</div>
            <div class="clinic">{{ $record->doctor?->specialty ?? '' }}</div>
            <div class="clinic">{{ $record->doctor?->phone ?? '' }} {{ $record->doctor?->email ? '• ' . $record->doctor->email : '' }}</div>
        </div>
    </div>

    <div class="rx">
        <h3 style="margin:0 0 8px 0">Prescription</h3>

        @php
            // Build items list: prefer relational prescriptions->items, otherwise use stored prescriptions array
            $items = [];
            if ($record->prescriptions()->exists()) {
                foreach ($record->prescriptions()->with('items')->get() as $pres) {
                    foreach ($pres->items as $it) {
                        $items[] = [ 'name' => $it->name, 'dosage' => $it->dosage, 'quantity' => $it->quantity, 'instructions' => $it->instructions ];
                    }
                }
            } elseif (is_array($record->prescriptions)) {
                $items = $record->prescriptions;
            }
        @endphp

        @if(count($items) > 0)
            <table>
                <thead>
                    <tr><th>Medicine</th><th>Dosage</th><th>Qty</th><th>Instructions</th></tr>
                </thead>
                <tbody>
                    @foreach($items as $it)
                        <tr>
                            <td>{{ $it['name'] ?? '' }}</td>
                            <td>{{ $it['dosage'] ?? '' }}</td>
                            <td>{{ $it['quantity'] ?? '' }}</td>
                            <td>{{ $it['instructions'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No prescription items recorded.</p>
        @endif

        <div class="signature">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div style="font-size:12px">Signature of Prescriber</div>
            </div>
            <div style="width:40%">
                <div style="font-size:12px">Notes</div>
                <div style="min-height:40px; border-top:1px dashed #e5e7eb; padding-top:8px">{{ $record->notes ?? '' }}</div>
            </div>
        </div>
    </div>

    <div style="margin-top:20px" class="no-print">
        <button onclick="window.print();" style="padding:8px 12px;background:#111827;color:#fff;border-radius:6px;border:0">Print</button>
    </div>
</body>
</html>
