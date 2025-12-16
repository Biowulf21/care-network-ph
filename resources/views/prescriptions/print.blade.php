<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Prescription - {{ $record->patient->full_name ?? '' }}</title>
    <style>
        body { font-family: Inter, system-ui, -apple-system, 'Helvetica Neue', Arial; color: #111827; margin: 24px; background:white }
        .header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px }
        .clinic { font-size:14px; color:#374151 }
        .doctor { font-weight:700; font-size:18px }
        .patient, .meta { margin-top:6px; font-size:14px }
        .rx { margin-top:12px; border-top:1px solid #e5e7eb; padding-top:12px }
        .rx-table { width:100%; border-collapse:collapse; margin-top:8px; border:1px solid #e6e7eb }
        .rx-table thead th { text-align:left; padding:10px 8px; background:#f8fafc; font-weight:700; color:#111827; border-bottom:1px solid #e6e7eb }
        .rx-table tbody td { padding:10px 8px; border-bottom:1px solid #f3f4f6; vertical-align:top }
        .rx-table tbody tr:last-child td { border-bottom:0 }
        .signature { margin-top:28px; display:flex; justify-content:space-between; gap:12px }
        .sig-box { width:48%; text-align:left }
        .sig-line { margin-top:40px; border-top:1px solid #111827; width:70% }
        .notes { font-size:13px; color:#374151; border-top:1px dashed #e5e7eb; padding-top:8px; min-height:48px }
        footer { margin-top:24px; font-size:11px; color:#6b7280; text-align:center }
        /* Footer area used for printing signature at bottom-left */
        .print-footer { position: static; margin-top:28px }
        @media print {
            .print-footer { position: fixed; left: 24px; bottom: 18px; width: 40%; }
            .no-print { display:none }
        }
        /* Make on-screen print button full width */
        .no-print .print-button { display:block; width:100%; padding:12px 16px; text-align:center }
        @media print { .no-print { display:none } }
    </style>
 </head>
<body>
    <div class="header" role="banner">
        <div style="max-width:65%">
            <div style="font-size:18px;font-weight:700">{{ $record->clinic->name ?? config('app.name') }}</div>
            <div style="margin-top:6px;color:#374151;font-size:13px">{{ $record->clinic->address ?? '' }}</div>
            <div style="color:#374151;font-size:13px">{{ $record->clinic->phone ?? '' }} {{ $record->clinic->email ? '• ' . $record->clinic->email : '' }}</div>
        </div>
        <div style="text-align:right;max-width:35%">
            <div style="font-size:20px;font-weight:800">Dr. {{ $record->doctor?->name ?? $record->user?->name ?? '' }}</div>
            <div style="font-size:13px;color:#374151">{{ $record->doctor?->specialty ?? '' }}</div>
            <div style="font-size:13px;color:#374151">{{ $record->doctor?->phone ?? '' }} {{ $record->doctor?->email ? '• ' . $record->doctor->email : '' }}</div>
        </div>
    </div>

    <div style="margin-top:8px;display:flex;justify-content:space-between;align-items:flex-start;gap:12px">
        <div style="flex:1;">
            <div style="font-size:13px"><strong>Patient:</strong> {{ $record->patient->full_name ?? 'Unknown' }}</div>
            <div style="font-size:13px;margin-top:4px">PHIC: {{ $record->patient->philhealth_number ?? '—' }} • DOB: {{ optional($record->patient->date_of_birth)->format('M d, Y') ?? 'N/A' }}</div>
        </div>
        <div style="width:220px;text-align:right">
            <div style="font-size:13px"><strong>Date:</strong> {{ optional($record->consultation_date)->format('M d, Y') }}</div>
            <div style="font-size:13px;margin-top:4px"><strong>Prescription:</strong> #{{ $record->id }}</div>
        </div>
    </div>

    <div class="rx" aria-labelledby="rx-heading">
        <h3 id="rx-heading" style="margin:0 0 8px 0;letter-spacing:0.4px">Prescription</h3>

        @php
            // Build items list: prefer relational prescriptions->items, otherwise use stored prescriptions array
            $items = [];
            if (method_exists($record, 'prescriptions') && $record->prescriptions()->exists()) {
                foreach ($record->prescriptions()->with('items')->get() as $pres) {
                    foreach ($pres->items as $it) {
                        $items[] = [ 'name' => $it->name, 'dosage' => $it->dosage, 'quantity' => $it->quantity, 'instructions' => $it->instructions, 'frequency' => $it->frequency ?? null ];
                    }
                }
            } elseif (is_array($record->prescriptions)) {
                $items = $record->prescriptions;
            }
        @endphp

        @if(count($items) > 0)
            <table class="rx-table" role="table" aria-describedby="rx-heading">
                <thead>
                    <tr>
                        <th style="width:52%">Medicine</th>
                        <th style="width:16%">Dosage</th>
                        <th style="width:8%">Qty</th>
                        <th style="width:24%">Instructions / Frequency</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $it)
                        <tr>
                            <td style="font-weight:600">{{ $it['name'] ?? '' }}</td>
                            <td>{{ $it['dosage'] ?? ($it['dosage_unit'] ?? '') }}</td>
                            <td style="text-align:center">{{ $it['quantity'] ?? '' }}</td>
                            <td>
                                @if(!empty($it['frequency']))
                                    <div style="font-size:13px;color:#374151">{{ $it['frequency'] }}</div>
                                @endif
                                <div style="font-size:12px;color:#6b7280;margin-top:4px">{{ $it['instructions'] ?? '' }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No prescription items recorded.</p>
        @endif

        <div>
            <div style="font-size:12px">Notes</div>
            <div class="notes">{{ $record->notes ?? '' }}</div>
        </div>
    </div>
    <div class="print-footer" aria-hidden="false">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div style="font-size:12px">Signature of Prescriber</div>
        </div>
    </div>

    <div style="margin-top:20px" class="no-print">
        <button onclick="window.print();" class="print-button" style="background:#111827;color:#fff;border-radius:6px;border:0">Print</button>
    </div>
</body>
</html>
