{{--
resources/views/admin/mes/acidTesting/print.blade.php
Variables: $test (AcidTesting with details + supplier), $company (Company)
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Acid Test — LOT {{ $test->lot_number }}</title>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
            padding: 16px 22px
        }

        @media print {
            body {
                padding: 6px 10px
            }

            .no-print {
                display: none !important
            }

            @page {
                size: A4 portrait;
                margin: 8mm 10mm
            }
        }

        /* ── Outer wrapper ── */
        .wrap {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000
        }

        .wrap td,
        .wrap th {
            border: 1px solid #000;
            padding: 0;
            vertical-align: middle
        }

        /* ── Inner tables ── */
        .inn {
            width: 100%;
            border-collapse: collapse
        }

        .inn td,
        .inn th {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle
        }

        th {
            background: #f0f0f0;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            line-height: 1.35
        }

        td {
            font-size: 10.5px;
            text-align: center
        }

        td.lft {
            text-align: left
        }

        td.lbl {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: left
        }

        /* ── Print / Back buttons ── */
        .no-print {
            position: fixed;
            top: 14px;
            right: 18px;
            display: flex;
            gap: 10px;
            z-index: 99
        }

        .btn-p {
            background: #1a7a3a;
            color: #fff;
            border: none;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            gap: 7px
        }

        .btn-p:hover {
            background: #145f2d
        }

        .btn-p svg {
            width: 14px;
            height: 14px;
            stroke: #fff;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .btn-b {
            background: #fff;
            color: #1a7a3a;
            border: 2px solid #1a7a3a;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: Arial, sans-serif;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px
        }
    </style>
</head>

<body>

    {{-- Print/Back buttons --}}
    <div class="no-print">
        <a href="{{ url()->previous() }}" class="btn-b">← Back</a>
        <button class="btn-p" onclick="window.print()">
            <svg viewBox="0 0 24 24">
                <polyline points="6 9 6 2 18 2 18 9" />
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                <rect x="6" y="14" width="12" height="8" />
            </svg>
            Print / Save PDF
        </button>
    </div>

    @php
        $details = $test->details ?? collect();
        $supplier = $test->supplier;
        $avgPF = (float) ($test->avg_pallet_and_foreign_weight ?? 0);
        $palletCount = $details->count();

        // Totals — weight section
        $totalGross = $details->sum('gross_weight');
        $totalNet = $details->sum('net_weight');
        $totalAvgPF = $avgPF * max($palletCount, 1);

        // Acid section — rows with initial_weight > 0
        $acidRows = $details->filter(fn($d) => ($d->initial_weight ?? 0) > 0)->values();
        $totalInitial = $acidRows->sum('initial_weight');
        $totalDrained = $acidRows->sum('drained_weight');
        $totalWtDiff = $acidRows->sum('weight_difference');

        // Net avg acid % = (total drained / total initial) * 100
        $netAvgAcid = $totalInitial > 0
            ? round(($totalWtDiff / $totalInitial) * 100, 2)
            : 0;

        $docPrefix = ($company->document_prefix ?? 'DBR') . '/PWM-';
        $minRows = 1;   // minimum blank rows to always show
    @endphp

    <table class="wrap">

        {{-- ── ROW 1 : Company Header ─────────────────────────────── --}}
        <tr>
            {{-- Logo --}}
            <td style="width:110px;padding:10px;text-align:center;border-right:1px solid #000">
                @if(!empty($company->logo_path) && file_exists(public_path($company->logo_path)))
                    <img src="{{ asset($company->logo_path) }}" alt="logo"
                        style="max-width:80px;max-height:55px;object-fit:contain">
                @else
                    <span
                        style="font-size:22px;font-weight:900;letter-spacing:2px;font-family:'Arial Black',Arial,sans-serif">
                        {{ strtoupper(substr($company->company_name ?? 'DUBATT', 0, 7)) }}
                    </span>
                @endif
            </td>

            {{-- Company name + address --}}
            <td style="border-right:1px solid #000;padding:8px 14px;text-align:center">
                <div
                    style="font-size:13px;font-weight:700;text-decoration:underline;text-transform:uppercase;margin-bottom:4px">
                    {{ strtoupper($company->legal_name ?? $company->company_name ?? '') }}
                </div>
                <div style="font-size:10px;line-height:1.65">
                    @if($company->plot_number)
                        Plot # {{ $company->plot_number }}{{ $company->zone ? ', ' . $company->zone : '' }}<br>
                    @endif
                    @if($company->city)
                        {{ $company->city }}{{ $company->country ? ' - ' . $company->country : '' }}<br>
                    @endif
                    @if($company->contact_phone)
                        Phone: {{ $company->contact_phone }}<br>
                    @endif
                    @if($company->contact_email)
                        Email: {{ $company->contact_email }}
                    @endif
                </div>
            </td>

            {{-- Document No + LOT No --}}
            <td style="width:155px;padding:8px 10px;vertical-align:middle">
                <table class="inn" style="margin-bottom:6px">
                    <tr>
                        <td class="lbl" style="padding:4px 7px;font-size:9px">Document No.</td>
                        <td style="font-size:10px;padding:4px 8px">{{ $docPrefix }}</td>
                    </tr>
                </table>
                <table class="inn">
                    <tr>
                        <td class="lbl" style="padding:4px 7px;font-size:9px">LOT No.</td>
                        <td style="font-size:20px;font-weight:900;padding:3px 8px;letter-spacing:1px">
                            {{ $test->lot_number }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- ── ROW 2 : Report Title ────────────────────────────────── --}}
        <tr>
            <td colspan="3" style="border-top:2px solid #000;border-bottom:2px solid #000;text-align:center;
        font-size:14px;font-weight:900;text-transform:uppercase;text-decoration:underline;
        padding:9px 0;letter-spacing:.5px">
                INCOMING SCRAP BATTERY PALLET WEIGHT REPORT
            </td>
        </tr>

        {{-- ── ROW 3 : Meta info (Date / Supplier / Vehicle / Invoice / Received) ── --}}
        <tr>
            <td colspan="3" style="padding:0;border-bottom:1px solid #000">
                <table class="inn" style="border:none">
                    <tr>
                        <td class="lbl" style="width:16%;border-top:none;border-left:none">DATE &amp; TIME</td>
                        <td style="width:20%;border-top:none">
                            {{ \Carbon\Carbon::parse($test->test_date)->format('d/m/Y') }}
                        </td>
                        <td class="lbl" style="width:18%;border-top:none">SUPPLIER NAME</td>
                        <td style="border-top:none;border-right:none;font-weight:600;text-transform:uppercase">
                            {{ $supplier->supplier_name ?? '—' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl" style="border-left:none">TYPES OF BATTERIES</td>
                        <td>ULAB</td>
                        <td class="lbl">VEHICLE NO.</td>
                        <td style="border-right:none;font-weight:600;text-transform:uppercase">
                            {{ $test->vehicle_number ?? '—' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl" style="border-left:none;border-bottom:none">INVOICE QTY</td>
                        <td style="border-bottom:none">{{ number_format((float) ($test->invoice_qty ?? 0)) }} PCS</td>
                        <td class="lbl" style="border-bottom:none;line-height:1.4">IN HOUSE<br>WEIGHBRIDGE<br>WEIGHT
                        </td>
                        <td style="border-right:none;border-bottom:none">
                            {{ number_format((float) ($test->received_qty ?? 0)) }} KG
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- ── ROW 4 : Foreign material / Avg Pallet weight ──────── --}}
        <tr>
            <td colspan="3" style="padding:0;border-bottom:2px solid #000">
                <table class="inn" style="border:none">
                    <tr>
                        <td class="lbl" style="width:30%;border-top:none;border-left:none;border-bottom:none">
                            Foreign material Weight
                        </td>
                        <td style="width:20%;border-top:none;border-bottom:none">
                            {{ number_format((float) ($test->foreign_material_weight ?? 0)) }} KG
                        </td>
                        <td class="lbl" style="border-top:none;border-bottom:none">AVERAGE PALLET WEIGHT</td>
                        <td style="border-top:none;border-right:none;border-bottom:none">
                            {{ number_format((float) ($test->avg_pallet_weight ?? 0)) }} KG
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- ── ROW 5 : Pallet Weight Table ────────────────────────── --}}
        <tr>
            <td colspan="3" style="padding:0">
                <table class="inn">
                    <thead>
                        <tr>
                            <th style="width:7%">SR. NO.</th>
                            <th style="width:15%">PALLET<br>NUMBER</th>
                            <th style="width:20%">GROSS WEIGHT (KG)</th>
                            <th style="width:22%">AVERAGE PALLET<br>&amp; FOREIGN WEIGHT</th>
                            <th style="width:18%">NET WEIGHT<br>(KG)</th>
                            <th>REMARKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td style="font-weight:600">{{ $row->pallet_no }}</td>
                                <td>{{ number_format((float) ($row->gross_weight ?? 0), 2) }}</td>
                                <td>{{ number_format($avgPF, 2) }}</td>
                                <td>{{ number_format((float) ($row->net_weight ?? 0), 2) }}</td>
                                <!-- <td>{{ $row->ulab_type ?? ($row->remarks ?? '') }}{{ $row->stock_code ? ' [' . $row->stock_code . ']' : '' }} -->
                                <td>{{ $row->remarks }}</td>
                            </tr>
                        @endforeach

                        @for($b = $details->count(); $b < $minRows; $b++)
                            <tr>
                                <td>{{ $b + 1 }}</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor

                        <tr style="background:#efefef">
                            <td colspan="2"
                                style="font-weight:700;font-size:11px;text-transform:uppercase;text-align:center">TOTAL
                                (KG)</td>
                            <td style="font-weight:700;font-size:11px">{{ number_format($totalGross, 2) }}</td>
                            <td style="font-weight:700;font-size:11px">{{ number_format($totalAvgPF, 2) }}</td>
                            <td style="font-weight:700;font-size:11px">{{ number_format($totalNet, 2) }}</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        {{-- ── ROW 6 : Acid Content Title ──────────────────────────── --}}
        <tr>
            <td colspan="3" style="border-top:2px solid #000;text-align:center;
        font-size:13px;font-weight:900;text-transform:uppercase;text-decoration:underline;
        padding:8px 0;letter-spacing:.5px">
                ACID CONTENT ANALYSIS IN SCRAP BATTERY
            </td>
        </tr>

        {{-- ── ROW 7 : Acid Content Table ─────────────────────────── --}}
        <tr>
            <td colspan="3" style="padding:0">
                <table class="inn">
                    <thead>
                        <tr>
                            <th style="width:7%">SR. NO.</th>
                            <th style="width:12%">PELLET NO.</th>
                            <th style="width:13%">INITIAL<br>WEIGHT<br>(KG.)</th>
                            <th style="width:13%">DRAINED<br>WEIGHT<br>(KG.)</th>
                            <th style="width:13%">WEIGHT<br>DIFFERENCE<br>(KG.)</th>
                            <th style="width:12%">ACID CONTENT<br>(%)</th>
                            <th style="width:14%">STOCK CODE</th>
                            <th>REMARKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($acidRows as $j => $row)
                            <tr>
                                <td>{{ $j + 1 }}</td>
                                <td style="font-weight:600">{{ $row->pallet_no }}</td>
                                <td>{{ number_format((float) ($row->initial_weight ?? 0), 2) }}</td>
                                <td>{{ number_format((float) ($row->drained_weight ?? 0), 2) }}</td>
                                <td>{{ number_format((float) ($row->weight_difference ?? max(0, ($row->initial_weight ?? 0) - ($row->drained_weight ?? 0))), 2) }}
                                </td>
                                <td>{{ number_format((float) ($row->avg_acid_pct ?? 0), 2) }}</td>
                                <!-- <td style="font-weight:700;font-size:10.5px">{{ $row->stock_code ?? '—' }}</td> -->
                                <td style="font-weight:700;font-size:10.5px">{{ $row->ulab_type }}</td>
                                <td>{{ $row->remarks ?? '' }}</td>
                            </tr>
                        @endforeach

                        @for($b = $acidRows->count(); $b < $minRows; $b++)
                            <tr>
                                <td>{{ $b + 1 }}</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor

                        <tr style="background:#efefef">
                            <td colspan="2"
                                style="font-weight:700;font-size:11px;text-transform:uppercase;text-align:center">TOTAL
                                (KG.)</td>
                            <td style="font-weight:700;font-size:11px">
                                <span style="text-decoration:underline">{{ number_format($totalInitial, 2) }}</span>
                            </td>
                            <td style="font-weight:700;font-size:11px">
                                <span style="text-decoration:underline">{{ number_format($totalDrained, 2) }}</span>
                            </td>
                            <td style="font-weight:700;font-size:11px">
                                <span style="text-decoration:underline">{{ number_format($totalWtDiff, 2) }}</span>
                            </td>
                            <td style="font-weight:700;font-size:15px">
                                <span style="text-decoration:underline">{{ number_format($netAvgAcid, 2) }}</span>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        {{-- ── ROW 8 : Signature Row ───────────────────────────────── --}}
        <tr>
            <td colspan="3" style="padding:0;border-top:2px solid #000">
                <table class="inn">
                    <tr>
                        <td class="lbl" style="width:33%;height:72px;vertical-align:top;padding:8px 14px">
                            RECORDED BY:
                        </td>
                        <td class="lbl" style="width:34%;height:72px;vertical-align:top;padding:8px 14px">
                            VERIFIED BY
                        </td>
                        <td class="lbl" style="width:33%;height:72px;vertical-align:top;padding:8px 14px">
                            SUPPLIER
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
</body>

</html>