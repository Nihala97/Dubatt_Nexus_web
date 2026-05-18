{{--
resources/views/admin/mes/acidTesting/print.blade.php
Variables: $test (AcidTesting with details + supplier + createdBy + updatedBy), $company (Company)
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

        /* ── Master Clean Layout ── */
        .wrap {
            width: 100%;
            border-collapse: collapse;
            border-top: 0.5px solid #000;
            border-left: 0.5px solid #000;
            margin-bottom: 0px;
        }

        /* Every single cell gets a uniform, crisp single line on bottom and right sides */
        .wrap td,
        .wrap th {
            border-bottom: 0.5px solid #000;
            border-right: 0.5px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
            font-size: 10.5px;
            text-align: center;
        }

        /* ── Sub Table Cleanups ── */
        .inn {
            width: 100%;
            border-collapse: collapse;
        }

        .inn td,
        .inn th {
            border: none;
            padding: 0px;
        }

        th {
            background-color: #f0f0f0;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.35;
        }

        td.lft {
            text-align: left
        }

        td.lbl {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: left;
        }

        /* ── Fixed Remarks Column: Removed ellipsis truncation and allowed clean multi-line wrapping ── */
        td.rmk {
            text-align: left;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            padding: 6px 8px;
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
        <a href="{{ url()->previous() }}" class="btn-b">&#8592; Back</a>
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

        $totalGross = $details->sum('gross_weight');
        $totalNet = $details->sum('net_weight');
        $totalAvgPF = $avgPF * max($palletCount, 1);

        $acidRows = $details->filter(fn($d) => ($d->initial_weight ?? 0) > 0)->values();
        $totalInitial = $acidRows->sum('initial_weight');
        $totalDrained = $acidRows->sum('drained_weight');
        $totalWtDiff = $acidRows->sum('weight_difference');

        $netAvgAcid = $totalInitial > 0
            ? round(($totalWtDiff / $totalInitial) * 100, 2)
            : 0;

        $docPrefix = ($company->document_prefix ?? 'DBR') . '/PWM-';
        $minRows = 1;

        $recordedBy = optional($test->createdBy)->name ?? '&mdash;';
        $verifiedBy = optional($test->updatedBy)->name ?? '&mdash;';
    @endphp

    {{-- Single Main structural table wrapper --}}
    <table class="wrap">

        {{-- ── ROW 1 : Company Header ── --}}
        <tr>
            <td style="width: 16%; padding: 10px;">
                @if(!empty($company->logo_path) && file_exists(public_path($company->logo_path)))
                    <img src="{{ asset($company->logo_path) }}" alt="logo"
                        style="max-width:80px; max-height:55px; object-fit:contain">
                @else
                    <span style="font-size:22px; font-weight:900; letter-spacing:2px; font-family:'Arial Black',sans-serif">
                        {{ strtoupper(substr($company->company_name ?? 'DUBATT', 0, 7)) }}
                    </span>
                @endif
            </td>
            <td style="width: 52%; text-align: center; padding: 8px 14px;">
                <div style="font-size: 13px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px">
                    {{ strtoupper($company->legal_name ?? $company->company_name ?? '') }}
                </div>
                <div style="font-size: 10px; line-height: 1.5">
                    @if($company->plot_number) Plot #
                    {{ $company->plot_number }}{{ $company->zone ? ', ' . $company->zone : '' }} | @endif
                    @if($company->city) {{ $company->city }}{{ $company->country ? ' - ' . $company->country : '' }}
                    @endif <br>
                    @if($company->contact_phone) Phone: {{ $company->contact_phone }} @endif
                    @if($company->contact_email) | Email: {{ $company->contact_email }} @endif
                </div>
            </td>
            <td style="width: 32%; padding: 4px;">
                <div style="display: flex; flex-direction: column; width: 100%; height: 100%;">
                    <div style="display: flex; border-bottom: 0.5px solid #000; padding: 4px;">
                        <span class="lbl" style="width: 50%; font-size: 9px;">DOCUMENT NO.</span>
                        <span style="width: 50%; text-align: center; font-size: 10px;">{{ $docPrefix }}</span>
                    </div>
                    <div style="display: flex; padding: 4px; align-items: center;">
                        <span class="lbl" style="width: 40%; font-size: 9px;">LOT NO.</span>
                        <span
                            style="width: 60%; text-align: center; font-size: 20px; font-weight: 900;">{{ $test->lot_number }}</span>
                    </div>
                </div>
            </td>
        </tr>

        {{-- ── ROW 2 : Report Title ── --}}
        <tr>
            <td colspan="3"
                style="font-size: 13px; font-weight: 900; text-transform: uppercase; padding: 8px 0; background: #fff;">
                INCOMING SCRAP BATTERY PALLET WEIGHT REPORT
            </td>
        </tr>

        {{-- ── ROW 3 : Meta Info Blocks ── --}}
        <tr>
            <td colspan="3" style="padding: 0;">
                <table class="inn" style="width: 100%;">
                    <tr>
                        <td class="lbl"
                            style="width: 16%; padding: 6px 8px; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">
                            DATE &amp; TIME</td>
                        <td
                            style="width: 20%; padding: 6px 8px; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">
                            {{ \Carbon\Carbon::parse($test->test_date)->format('d/m/Y') }}</td>
                        <td class="lbl"
                            style="width: 16%; padding: 6px 8px; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">
                            SUPPLIER NAME</td>
                        <td
                            style="padding: 6px 8px; text-align: center; font-weight: 600; text-transform: uppercase; border-bottom: 0.5px solid #000;">
                            {{ $supplier->supplier_name ?? '&mdash;' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl"
                            style="padding: 6px 8px; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">
                            TYPES OF BATTERIES</td>
                        <td style="padding: 6px 8px; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">
                            ULAB</td>
                        <td class="lbl"
                            style="padding: 6px 8px; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">
                            VEHICLE NO.</td>
                        <td
                            style="padding: 6px 8px; text-align: center; font-weight: 600; text-transform: uppercase; border-bottom: 0.5px solid #000;">
                            {{ $test->vehicle_number ?? 'NA' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl"
                            style="padding: 6px 8px; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">
                            INVOICE QTY</td>
                        <td style="padding: 6px 8px; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">
                            {{ number_format((float) ($test->invoice_qty ?? 0)) }}</td>
                        <td class="lbl"
                            style="padding: 6px 8px; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">
                            IN HOUSE WEIGHBRIDGE WT</td>
                        <td
                            style="padding: 6px 8px; text-align: center; font-weight: 600; border-bottom: 0.5px solid #000;">
                            {{ number_format((float) ($test->received_qty ?? 0)) }} KG</td>
                    </tr>
                    <tr>
                        <td class="lbl" style="padding: 6px 8px; border-right: 0.5px solid #000;">FOREIGN MATERIAL
                            WEIGHT</td>
                        <td style="padding: 6px 8px; border-right: 0.5px solid #000;">
                            {{ number_format((float) ($test->foreign_material_weight ?? 0)) }} KG</td>
                        <td class="lbl" style="padding: 6px 8px; border-right: 0.5px solid #000;">AVERAGE PALLET WEIGHT
                        </td>
                        <td style="padding: 6px 8px; text-align: center; font-weight: 600;">
                            {{ number_format((float) ($test->avg_pallet_weight ?? 0)) }} KG</td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- ── ROW 4 : Pallet Weight Table Content ── --}}
        <tr>
            <td colspan="3" style="padding: 0;">
                <table class="inn" style="width: 100%;">
                    <thead>
                        <tr>
                            <th
                                style="width: 5%; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 6px 4px;">
                                SR.</th>
                            <th
                                style="width: 11%; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 6px 4px;">
                                PALLET NO.</th>
                            <th
                                style="width: 18%; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 6px 4px;">
                                GROSS WEIGHT (KG)</th>
                            <th
                                style="width: 18%; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 6px 4px;">
                                AVG PALLET &amp; FOREIGN WT.</th>
                            <th
                                style="width: 18%; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 6px 4px;">
                                NET WEIGHT (KG)</th>
                            <th style="border-bottom: 0.5px solid #000; padding: 6px 4px; width: 30%;">REMARKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $i => $row)
                            <tr>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 5px;">
                                    {{ $i + 1 }}</td>
                                <td
                                    style="font-weight: 600; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 5px;">
                                    {{ $row->pallet_no }}</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 5px;">
                                    {{ number_format((float) ($row->gross_weight ?? 0), 2) }}</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 5px;">
                                    {{ number_format($avgPF, 2) }}</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 5px;">
                                    {{ number_format((float) ($row->net_weight ?? 0), 2) }}</td>
                                <td class="rmk" style="border-bottom: 0.5px solid #000;">{{ $row->remarks }}</td>
                            </tr>
                        @endforeach

                        @for($b = $details->count(); $b < $minRows; $b++)
                            <tr>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">{{ $b + 1 }}
                                </td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">&nbsp;</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">&nbsp;</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">&nbsp;</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">&nbsp;</td>
                                <td style="border-bottom: 0.5px solid #000;">&nbsp;</td>
                            </tr>
                        @endfor

                        <tr style="background: #efefef;">
                            <td colspan="2"
                                style="font-weight: 700; text-transform: uppercase; border-right: 0.5px solid #000; padding: 6px;">
                                TOTAL (KG)</td>
                            <td style="font-weight: 700; border-right: 0.5px solid #000; padding: 6px;">
                                {{ number_format($totalGross, 2) }}</td>
                            <td style="font-weight: 700; border-right: 0.5px solid #000; padding: 6px;">
                                {{ number_format($totalAvgPF, 2) }}</td>
                            <td style="font-weight: 700; border-right: 0.5px solid #000; padding: 6px;">
                                {{ number_format($totalNet, 2) }}</td>
                            <td style="padding: 6px;">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        {{-- ── ROW 5 : Acid Content Title ── --}}
        <tr>
            <td colspan="3"
                style="font-size: 13px; font-weight: 900; text-transform: uppercase; padding: 8px 0; background: #fff;">
                ACID CONTENT ANALYSIS IN SCRAP BATTERY
            </td>
        </tr>

        {{-- ── ROW 6 : Acid Content Table Data ── --}}
        <tr>
            <td colspan="3" style="padding: 0;">
                <table class="inn" style="width: 100%;">
                    <thead>
                        <tr>
                            <th
                                style="width: 5%; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 6px 4px;">
                                SR.</th>
                            <th
                                style="width: 11%; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 6px 4px;">
                                PALLET NO.</th>
                            <th
                                style="width: 14%; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 6px 4px;">
                                INITIAL WEIGHT (KG)</th>
                            <th
                                style="width: 14%; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 6px 4px;">
                                DRAINED WEIGHT (KG)</th>
                            <th
                                style="width: 14%; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 6px 4px;">
                                WEIGHT DIFF. (KG)</th>
                            <th
                                style="width: 12%; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 6px 4px;">
                                ACID CONTENT (%)</th>
                            <th style="border-bottom: 0.5px solid #000; padding: 6px 4px; width: 30%;">REMARKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($acidRows as $j => $row)
                            <tr>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 5px;">
                                    {{ $j + 1 }}</td>
                                <td
                                    style="font-weight: 600; border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 5px;">
                                    {{ $row->pallet_no }}</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 5px;">
                                    {{ number_format((float) ($row->initial_weight ?? 0), 2) }}</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 5px;">
                                    {{ number_format((float) ($row->drained_weight ?? 0), 2) }}</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 5px;">
                                    {{ number_format((float) ($row->weight_difference ?? max(0, ($row->initial_weight ?? 0) - ($row->drained_weight ?? 0))), 2) }}
                                </td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000; padding: 5px;">
                                    {{ number_format((float) ($row->avg_acid_pct ?? 0), 2) }}</td>
                                <td class="rmk" style="border-bottom: 0.5px solid #000;">{{ $row->remarks ?? '' }}</td>
                            </tr>
                        @endforeach

                        @for($b = $acidRows->count(); $b < $minRows; $b++)
                            <tr>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">{{ $b + 1 }}
                                </td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">&nbsp;</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">&nbsp;</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">&nbsp;</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">&nbsp;</td>
                                <td style="border-right: 0.5px solid #000; border-bottom: 0.5px solid #000;">&nbsp;</td>
                                <td style="border-bottom: 0.5px solid #000;">&nbsp;</td>
                            </tr>
                        @endfor

                        <tr style="background: #efefef;">
                            <td colspan="2"
                                style="font-weight: 700; text-transform: uppercase; border-right: 0.5px solid #000; padding: 6px;">
                                TOTAL (KG.)</td>
                            <td style="font-weight: 700; border-right: 0.5px solid #000; padding: 6px;">
                                {{ number_format($totalInitial, 2) }}</td>
                            <td style="font-weight: 700; border-right: 0.5px solid #000; padding: 6px;">
                                {{ number_format($totalDrained, 2) }}</td>
                            <td style="font-weight: 700; border-right: 0.5px solid #000; padding: 6px;">
                                {{ number_format($totalWtDiff, 2) }}</td>
                            <td
                                style="font-weight: 700; font-size: 12px; border-right: 0.5px solid #000; padding: 6px;">
                                {{ number_format($netAvgAcid, 2) }}</td>
                            <td style="padding: 6px;">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        {{-- ── ROW 7 : Signatures ── --}}
        <tr>
            <td colspan="3" style="padding: 0;">
                <table class="inn" style="width: 100%;">
                    <tr>
                        <td class="lbl"
                            style="width: 33%; height: 75px; vertical-align: top; padding: 8px; border-right: 0.5px solid #000;">
                            RECORDED BY:<br>
                            <span
                                style="font-size: 10px; font-weight: 400; text-transform: none; margin-top: 15px; display: block;">
                                {!! $recordedBy !!}
                            </span>
                        </td>
                        <td class="lbl"
                            style="width: 34%; height: 75px; vertical-align: top; padding: 8px; border-right: 0.5px solid #000;">
                            VERIFIED BY:<br>
                            <span
                                style="font-size: 10px; font-weight: 400; text-transform: none; margin-top: 15px; display: block;">
                                {!! $verifiedBy !!}
                            </span>
                        </td>
                        <td class="lbl" style="width: 33%; height: 75px; vertical-align: top; padding: 8px;">
                            SUPPLIER
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
</body>

</html>