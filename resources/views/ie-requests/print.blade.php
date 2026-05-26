<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order - {{ $ieRequest->request_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 14mm;
        }

        body {
            background: #ffffff;
            color: #000000;
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
        }

        .page {
            max-width: 210mm;
            margin: 0 auto;
            padding: 18px;
        }

        .no-print {
            margin-bottom: 16px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .btn {
            border: 1px solid #111827;
            border-radius: 6px;
            color: #111827;
            cursor: pointer;
            display: inline-block;
            font-weight: 700;
            padding: 8px 14px;
            text-decoration: none;
        }

        .header {
            border-bottom: 2px solid #000;
            margin-bottom: 14px;
            padding-bottom: 10px;
            text-align: center;
        }

        h1 {
            font-size: 20px;
            margin: 0;
            letter-spacing: 0;
        }

        h2 {
            border-bottom: 1px solid #000;
            font-size: 14px;
            margin: 18px 0 8px;
            padding-bottom: 4px;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-top: 10px;
            text-align: left;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            font-weight: 700;
        }

        .info-table th {
            width: 22%;
        }

        .text-box {
            border: 1px solid #000;
            min-height: 48px;
            padding: 8px;
            white-space: pre-line;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 12px;
        }

        .signature-box {
            border: 1px solid #000;
            height: 96px;
            padding: 8px;
            text-align: center;
        }

        .signature-space {
            height: 54px;
        }

        .muted {
            color: #555;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
            }

            .page {
                padding: 0;
            }

            a {
                color: #000;
                text-decoration: none;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="no-print">
            <button type="button" class="btn" onclick="window.print()">Print</button>
            <a href="{{ route('ie-requests.show', $ieRequest->id) }}" class="btn">Kembali</a>
        </div>

        <div class="header">
            <h1>WORK ORDER INDUSTRIAL ENGINEERING</h1>
            <p class="muted">Monitoring Request / Handling / Workshop</p>
            <div class="meta">
                <div><strong>No Request:</strong> {{ $ieRequest->request_number }}</div>
                <div><strong>Tanggal Cetak:</strong> {{ now()->format('d M Y H:i') }}</div>
                <div><strong>Dicetak oleh:</strong> {{ Auth::user()?->name ?? '-' }}</div>
            </div>
        </div>

        <h2>1. INFORMASI REQUEST</h2>
        <table class="info-table">
            <tr><th>No Request</th><td>{{ $ieRequest->request_number }}</td><th>Tanggal Request</th><td>{{ $ieRequest->request_date ?? '-' }}</td></tr>
            <tr><th>Requester</th><td>{{ $ieRequest->requester_name }}</td><th>Department</th><td>{{ $ieRequest->department }}</td></tr>
            <tr><th>Line / Area</th><td>{{ $ieRequest->line_area ?? '-' }}</td><th>Request Type</th><td>{{ $ieRequest->request_type }}</td></tr>
            <tr><th>Qty Request</th><td>{{ $ieRequest->request_qty ?? 1 }}</td><th>Priority</th><td>{{ $ieRequest->priority }}</td></tr>
            <tr><th>Status</th><td>{{ $ieRequest->status }}</td><th>Target Date</th><td>{{ $ieRequest->target_date ?? '-' }}</td></tr>
            <tr><th>Created By / Request Owner</th><td colspan="3">{{ $ieRequest->user?->name ?? '-' }}</td></tr>
        </table>

        <h2>2. DESKRIPSI</h2>
        <p><strong>Description</strong></p>
        <div class="text-box">{{ $ieRequest->description }}</div>
        <p><strong>Notes</strong></p>
        <div class="text-box">{{ $ieRequest->notes ?? '-' }}</div>

        <h2>3. MEMO & DRAWING</h2>
        <table class="info-table">
            <tr><th>Memo Status</th><td>{{ $ieRequest->memo_status ?? '-' }}</td><th>Memo Approved By</th><td>{{ $ieRequest->memo_approved_by ?? '-' }}</td></tr>
            <tr><th>Memo Approved At</th><td>{{ $ieRequest->memo_approved_at ?? '-' }}</td><th>Memo File</th><td>@if ($ieRequest->memo_file)<a href="{{ asset('storage/' . $ieRequest->memo_file) }}">Lihat Memo</a>@else - @endif</td></tr>
            <tr><th>Drawing Status</th><td>{{ $ieRequest->drawing_status ?? '-' }}</td><th>Assigned Drafter</th><td>{{ $ieRequest->assigned_drafter ?? '-' }}</td></tr>
            <tr><th>Drawing Started At</th><td>{{ $ieRequest->drawing_started_at ?? '-' }}</td><th>Drawing Finished At</th><td>{{ $ieRequest->drawing_finished_at ?? '-' }}</td></tr>
            <tr><th>Drawing File</th><td colspan="3">@if ($ieRequest->drawing_file)<a href="{{ asset('storage/' . $ieRequest->drawing_file) }}">Lihat Drawing</a>@else - @endif</td></tr>
        </table>

        <h2>4. MATERIAL / BOM</h2>
        @if ($ieRequest->materials->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Material Name</th>
                        <th>Specification</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Estimated Price</th>
                        <th>Total Price</th>
                        <th>Material Status</th>
                        <th>Arrival Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ieRequest->materials as $material)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $material->material_name }}</td>
                            <td>{{ $material->specification ?? '-' }}</td>
                            <td>{{ $material->qty }}</td>
                            <td>{{ $material->unit ?? '-' }}</td>
                            <td>Rp {{ number_format((float) $material->estimated_price, 2, ',', '.') }}</td>
                            <td>Rp {{ number_format((float) $material->total_price, 2, ',', '.') }}</td>
                            <td>{{ $material->material_status }}</td>
                            <td>{{ $material->arrival_status ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">Total Estimasi Biaya</th>
                        <th colspan="3">Rp {{ number_format((float) $ieRequest->materials->sum('total_price'), 2, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        @else
            <p>Belum ada data material.</p>
        @endif

        <h2>5. BUDGET / PR</h2>
        @if ($ieRequest->purchaseRequest)
            <table class="info-table">
                <tr><th>PR Number</th><td>{{ $ieRequest->purchaseRequest->pr_number }}</td><th>PR Date</th><td>{{ $ieRequest->purchaseRequest->pr_date ?? '-' }}</td></tr>
                <tr><th>Total Budget</th><td>Rp {{ number_format((float) $ieRequest->purchaseRequest->total_budget, 2, ',', '.') }}</td><th>PR Status</th><td>{{ $ieRequest->purchaseRequest->pr_status }}</td></tr>
                <tr><th>Requested By</th><td>{{ $ieRequest->purchaseRequest->requested_by ?? '-' }}</td><th>Approved By</th><td>{{ $ieRequest->purchaseRequest->approved_by ?? '-' }}</td></tr>
                <tr><th>Approved At</th><td colspan="3">{{ $ieRequest->purchaseRequest->approved_at ?? '-' }}</td></tr>
            </table>
        @else
            <p>Belum ada PR.</p>
        @endif

        <h2>6. WORKSHOP SCHEDULE & PROGRESS</h2>
        @if ($ieRequest->workshopSchedule)
            <table class="info-table">
                <tr><th>Schedule Number</th><td>{{ $ieRequest->workshopSchedule->schedule_number }}</td><th>Planned Start</th><td>{{ $ieRequest->workshopSchedule->planned_start_date ?? '-' }}</td></tr>
                <tr><th>Planned Finish</th><td>{{ $ieRequest->workshopSchedule->planned_finish_date ?? '-' }}</td><th>PIC Workshop</th><td>{{ $ieRequest->workshopSchedule->pic_workshop ?? '-' }}</td></tr>
                <tr><th>Schedule Status</th><td>{{ $ieRequest->workshopSchedule->schedule_status }}</td><th>Progress Status</th><td>{{ $ieRequest->workshopSchedule->progress_status }}</td></tr>
                <tr><th>Progress Percentage</th><td>{{ $ieRequest->workshopSchedule->progress_percentage }}%</td><th>Started At</th><td>{{ $ieRequest->workshopSchedule->started_at ?? '-' }}</td></tr>
                <tr><th>Finished At</th><td colspan="3">{{ $ieRequest->workshopSchedule->finished_at ?? '-' }}</td></tr>
            </table>
        @else
            <p>Belum ada workshop schedule.</p>
        @endif

        <h2>7. FINAL CHECK</h2>
        @if ($ieRequest->finalCheck)
            <table class="info-table">
                <tr><th>Check Status</th><td>{{ $ieRequest->finalCheck->check_status }}</td><th>Result Status</th><td>{{ $ieRequest->finalCheck->result_status ?? '-' }}</td></tr>
                <tr><th>Checked By</th><td>{{ $ieRequest->finalCheck->checked_by ?? '-' }}</td><th>Check Date</th><td>{{ $ieRequest->finalCheck->check_date ?? '-' }}</td></tr>
                <tr><th>Problem Note</th><td>{{ $ieRequest->finalCheck->problem_note ?? '-' }}</td><th>Correction Note</th><td>{{ $ieRequest->finalCheck->correction_note ?? '-' }}</td></tr>
                <tr><th>Final Note</th><td colspan="3">{{ $ieRequest->finalCheck->final_note ?? '-' }}</td></tr>
            </table>
        @else
            <p>Belum ada final check.</p>
        @endif

        <h2>8. HANDOVER</h2>
        @if ($ieRequest->handover)
            <table class="info-table">
                <tr><th>Handover Number</th><td>{{ $ieRequest->handover->handover_number }}</td><th>Handover Date</th><td>{{ $ieRequest->handover->handover_date ?? '-' }}</td></tr>
                <tr><th>Handed Over By</th><td>{{ $ieRequest->handover->handed_over_by ?? '-' }}</td><th>Received By</th><td>{{ $ieRequest->handover->received_by ?? '-' }}</td></tr>
                <tr><th>Receiver Department</th><td>{{ $ieRequest->handover->receiver_department ?? '-' }}</td><th>Handover Status</th><td>{{ $ieRequest->handover->handover_status }}</td></tr>
                <tr><th>Handover Note</th><td>{{ $ieRequest->handover->handover_note ?? '-' }}</td><th>Receiver Note</th><td>{{ $ieRequest->handover->receiver_note ?? '-' }}</td></tr>
            </table>
        @else
            <p>Belum ada handover.</p>
        @endif

        <h2>9. TANDA TANGAN</h2>
        <div class="signature-grid">
            <div class="signature-box"><strong>Customer / Requester</strong><div class="signature-space"></div><div>(________________)</div></div>
            <div class="signature-box"><strong>Admin IE</strong><div class="signature-space"></div><div>(________________)</div></div>
            <div class="signature-box"><strong>PIC Workshop</strong><div class="signature-space"></div><div>(________________)</div></div>
            <div class="signature-box"><strong>Manager / Approver</strong><div class="signature-space"></div><div>(________________)</div></div>
        </div>
    </div>
</body>
</html>
