@extends('admin.layouts.app')

@section('title', 'Acid Testing')

@section('breadcrumb')
  <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
  <span style="margin:0 6px;color:var(--border);">/</span>
  <a href="{{ route('admin.mes.acidTesting.index') }}" style="color:var(--text-muted);text-decoration:none;">Acid
    Testing</a>
  <span style="margin:0 6px;color:var(--border);">/</span>
  <strong id="breadcrumbTitle">Loading…</strong>
@endsection

@push('styles')
  <style>
    /* ── Design tokens ──────────────────────────────────────────── */
    :root {
      --g: #1a7a3a;
      --gd: #145f2d;
      --gl: #e8f5ed;
      --gxl: #f2faf5;
      --white: #fff;
      --bg: #f4f7f5;
      --bdr: #dde8e2;
      --txt: #1e2d26;
      --txtm: #3d5449;
      --txtmu: #6b8a78;
      --err: #dc2626;
      --warn: #d97706;
      --sh: 0 1px 6px rgba(26, 122, 58, .07), 0 4px 18px rgba(26, 122, 58, .05);
      --r: 12px;
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box
    }

    body {
      font-family: 'Outfit', sans-serif;
      background: var(--bg);
      color: var(--txt)
    }

    @keyframes spin {
      to {
        transform: rotate(360deg)
      }
    }

    @keyframes flashFill {
      0% {
        background: #d1fae5
      }

      100% {
        background: #eef6f1
      }
    }

    @keyframes sddIn {
      from {
        opacity: 0;
        transform: translateY(-4px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    @keyframes pulse {

      0%,
      100% {
        opacity: 1
      }

      50% {
        opacity: .4
      }
    }

    /* ── Page header ────────────────────────────────────────────── */
    .ph {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 10px
    }

    .ph h2 {
      font-size: clamp(17px, 2.3vw, 22px);
      font-weight: 800;
      color: var(--txt);
      letter-spacing: -.3px
    }

    .ph p {
      font-size: 12.5px;
      color: var(--txtmu);
      margin-top: 2px
    }

    /* ── Buttons ────────────────────────────────────────────────── */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 9px 17px;
      border-radius: 9px;
      font-family: 'Outfit', sans-serif;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      border: none;
      transition: all .2s;
      white-space: nowrap
    }

    .btn svg {
      width: 14px;
      height: 14px;
      stroke: currentColor;
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round
    }

    .btn-primary {
      background: var(--g);
      color: #fff
    }

    .btn-primary:hover {
      background: var(--gd);
      box-shadow: 0 4px 14px rgba(26, 122, 58, .28);
      transform: translateY(-1px)
    }

    .btn-outline {
      background: var(--white);
      color: var(--txtm);
      border: 1.5px solid var(--bdr)
    }

    .btn-outline:hover {
      border-color: var(--g);
      color: var(--g);
      background: var(--gxl)
    }

    .btn-sm {
      padding: 7px 13px;
      font-size: 12.5px
    }

    .btn-add {
      background: var(--g);
      color: #fff;
      padding: 8px 14px;
      border-radius: 7px;
      font-size: 12.5px;
      font-weight: 700;
      border: none;
      cursor: pointer;
      font-family: 'Outfit', sans-serif;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: all .2s
    }

    .btn-add:hover {
      background: var(--gd);
      transform: translateY(-1px)
    }

    .btn-add svg {
      width: 13px;
      height: 13px;
      stroke: #fff;
      fill: none;
      stroke-width: 2.5;
      stroke-linecap: round;
      stroke-linejoin: round
    }

    .del-btn {
      width: 26px;
      height: 26px;
      background: #fee2e2;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all .18s;
      margin: auto
    }

    .del-btn:hover {
      background: #fca5a5
    }

    .del-btn svg {
      width: 12px;
      height: 12px;
      stroke: #dc2626;
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round
    }

    /* ── Cards ──────────────────────────────────────────────────── */
    .card {
      background: var(--white);
      border: 1px solid var(--bdr);
      border-radius: var(--r);
      box-shadow: var(--sh);
      margin-bottom: 18px;
      overflow: hidden
    }

    .card-head {
      padding: 11px 20px;
      background: var(--gl);
      border-bottom: 1px solid var(--bdr);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px
    }

    .card-head-left {
      display: flex;
      align-items: center;
      gap: 8px
    }

    .card-head-left svg {
      width: 14px;
      height: 14px;
      stroke: var(--g);
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round
    }

    .card-head span {
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: 1.4px;
      text-transform: uppercase;
      color: var(--g)
    }

    .card-body {
      padding: 20px
    }

    /* ── Fields ─────────────────────────────────────────────────── */
    .fg2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px 24px
    }

    .fg4 {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px 20px
    }

    .field {
      display: flex;
      flex-direction: column;
      gap: 5px
    }

    .field label {
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: .7px;
      text-transform: uppercase;
      color: var(--txtm);
      display: flex;
      align-items: center;
      gap: 5px
    }

    .field label .req {
      color: var(--err)
    }

    .badge-auto {
      font-size: 9px;
      font-weight: 700;
      letter-spacing: .7px;
      padding: 1px 7px;
      border-radius: 10px;
      background: #dbeafe;
      color: #1d4ed8;
      text-transform: uppercase
    }

    .badge-calc {
      font-size: 9px;
      font-weight: 700;
      letter-spacing: .7px;
      padding: 1px 7px;
      border-radius: 10px;
      background: var(--gl);
      color: var(--gd);
      text-transform: uppercase
    }

    .iw {
      position: relative
    }

    .iw svg.ico {
      position: absolute;
      left: 11px;
      top: 50%;
      transform: translateY(-50%);
      width: 13px;
      height: 13px;
      stroke: var(--txtmu);
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      pointer-events: none;
      z-index: 1
    }

    input[type=text],
    input[type=number],
    input[type=date],
    select {
      width: 100%;
      padding: 9px 12px 9px 34px;
      border: 1.5px solid var(--bdr);
      border-radius: 8px;
      background: var(--gxl);
      font-family: 'Outfit', sans-serif;
      font-size: 13px;
      color: var(--txt);
      outline: none;
      appearance: none;
      transition: border-color .18s, box-shadow .18s, background .18s
    }

    input:focus,
    select:focus {
      border-color: var(--g);
      background: var(--white);
      box-shadow: 0 0 0 3px rgba(26, 122, 58, .09)
    }

    input[readonly].ro,
    input.ro {
      background: #eef6f1;
      color: var(--g);
      cursor: default;
      border-color: #c8dfd1;
      font-weight: 600
    }

    input[readonly].ro:focus {
      box-shadow: none;
      border-color: #c8dfd1
    }

    input.autofilled {
      background: #eef6f1;
      color: var(--txtm);
      cursor: default;
      border-color: #c8dfd1
    }

    input.autofilled:focus {
      box-shadow: none
    }

    input::placeholder {
      color: var(--txtmu);
      font-size: 12px
    }

    .autofilled.flash {
      animation: flashFill .7s ease forwards
    }

    .err-msg {
      font-size: 11.5px;
      color: var(--err);
      min-height: 14px
    }

    /* ── Table inputs ───────────────────────────────────────────── */
    .ri {
      width: 100%;
      padding: 6px 10px;
      border: 1.5px solid var(--bdr);
      border-radius: 6px;
      background: var(--gxl);
      font-family: 'Outfit', sans-serif;
      font-size: 12.5px;
      color: var(--txt);
      outline: none;
      transition: border-color .18s, background .18s
    }

    .ri:focus {
      border-color: var(--g);
      background: var(--white);
      box-shadow: 0 0 0 3px rgba(26, 122, 58, .08)
    }

    .ri[readonly] {
      background: #eef6f1;
      color: var(--g);
      font-weight: 600;
      cursor: default;
      border-color: #c8dfd1
    }

    .ri[readonly]:focus {
      box-shadow: none;
      border-color: #c8dfd1
    }

    .rsel {
      width: 100%;
      padding: 6px 28px 6px 10px;
      border: 1.5px solid var(--bdr);
      border-radius: 6px;
      background: var(--gxl);
      font-family: 'Outfit', sans-serif;
      font-size: 12.5px;
      color: var(--txt);
      outline: none;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b8a78' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 8px center;
      transition: border-color .18s, background .18s
    }

    .rsel:focus {
      border-color: var(--g);
      background: var(--white);
      box-shadow: 0 0 0 3px rgba(26, 122, 58, .08)
    }

    /* ── Data table ─────────────────────────────────────────────── */
    .tbl-wrap {
      overflow-x: auto;
      border-radius: 8px;
      border: 1px solid var(--bdr)
    }

    .dt {
      width: 100%;
      border-collapse: collapse
    }

    .dt thead th {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .9px;
      text-transform: uppercase;
      color: var(--g);
      background: var(--gl);
      padding: 9px 10px;
      border-bottom: 2px solid var(--bdr);
      text-align: left;
      white-space: nowrap
    }

    .dt thead th.tc {
      text-align: center
    }

    .dt thead th.disabled-col {
      color: #9ca3af;
      background: #f9fafb
    }

    .dt tbody td {
      padding: 6px 6px;
      border-bottom: 1px solid #edf2ef;
      vertical-align: middle
    }

    .dt tbody tr:last-child td {
      border-bottom: none
    }

    .dt tbody tr:hover td {
      background: #f7fbf8
    }

    .dt tfoot td {
      background: var(--gl);
      font-weight: 700;
      font-size: 12.5px;
      color: var(--g);
      padding: 8px 10px;
      border-top: 2px solid var(--bdr)
    }

    .sr-n {
      text-align: center;
      font-size: 12.5px;
      font-weight: 700;
      color: var(--g)
    }

    /* disabled cell visual */
    .cell-disabled input,
    .cell-disabled select {
      background: #f3f4f6 !important;
      color: #9ca3af !important;
      border-color: #e5e7eb !important;
      cursor: not-allowed !important;
      pointer-events: none
    }

    /* ── Net Avg Acid % banner ──────────────────────────────────── */
    .result-banner {
      margin-top: 16px;
      border-radius: var(--r);
      padding: 16px 22px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
      border: 2px solid var(--bdr);
      background: linear-gradient(135deg, var(--gxl) 0%, var(--white) 100%);
      transition: border-color .3s
    }

    .result-banner.cat-high {
      border-color: #f59e0b;
      background: linear-gradient(135deg, #fffbeb, #fff)
    }

    .result-banner.cat-normal {
      border-color: var(--g);
      background: linear-gradient(135deg, #e8f5ed, #fff)
    }

    .result-banner.cat-low {
      border-color: #3b82f6;
      background: linear-gradient(135deg, #eff6ff, #fff)
    }

    .result-banner.cat-dry {
      border-color: #6b7280;
      background: linear-gradient(135deg, #f3f4f6, #fff)
    }

    .rb-val {
      font-size: 32px;
      font-weight: 800;
      color: var(--g);
      letter-spacing: -1px;
      line-height: 1
    }

    .rb-label {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      color: var(--txtmu);
      margin-bottom: 3px
    }

    .rb-sub {
      font-size: 11.5px;
      color: var(--txtmu);
      margin-top: 4px
    }

    .cat-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: .5px;
      text-transform: uppercase;
      transition: all .3s
    }

    .cat-pill.cat-high {
      background: #fef3c7;
      color: #b45309;
      border: 1px solid #f59e0b
    }

    .cat-pill.cat-normal {
      background: var(--gl);
      color: var(--gd);
      border: 1px solid var(--g)
    }

    .cat-pill.cat-low {
      background: #eff6ff;
      color: #1d4ed8;
      border: 1px solid #3b82f6
    }

    .cat-pill.cat-dry {
      background: #f3f4f6;
      color: #374151;
      border: 1px solid #9ca3af
    }

    .cat-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      flex-shrink: 0
    }

    .cat-high .cat-dot {
      background: #f59e0b
    }

    .cat-normal .cat-dot {
      background: var(--g)
    }

    .cat-low .cat-dot {
      background: #3b82f6
    }

    .cat-dry .cat-dot {
      background: #9ca3af
    }

    .cat-rules {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 8px;
      margin-top: 14px
    }

    .crule {
      border-radius: 8px;
      padding: 9px 13px;
      border: 1.5px solid transparent;
      font-size: 10.5px
    }

    .crule-title {
      font-weight: 800;
      font-size: 11px;
      margin-bottom: 2px
    }

    .crule.ch {
      background: #fffbeb;
      border-color: #fde68a;
      color: #92400e
    }

    .crule.cn {
      background: var(--gxl);
      border-color: var(--bdr);
      color: var(--gd)
    }

    .crule.cl {
      background: #eff6ff;
      border-color: #bfdbfe;
      color: #1e3a8a
    }

    .crule.cd {
      background: #f9fafb;
      border-color: #e5e7eb;
      color: #374151
    }

    /* ── Alerts & Actions ───────────────────────────────────────── */
    .form-alert {
      display: none;
      padding: 11px 15px;
      border-radius: 9px;
      font-size: 12.5px;
      font-weight: 500;
      margin-bottom: 16px
    }

    .form-alert.error {
      background: #fee2e2;
      border: 1px solid #fca5a5;
      color: #991b1b;
      display: block
    }

    .form-alert.success {
      background: #d1fae5;
      border: 1px solid #6ee7b7;
      color: #065f46;
      display: block
    }

    .form-actions {
      position: sticky;
      bottom: 0;
      background: var(--white);
      border-top: 1px solid var(--bdr);
      padding: 13px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      z-index: 20;
      box-shadow: 0 -4px 16px rgba(0, 0, 0, .06)
    }

    .badge-status {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700
    }

    .badge-draft {
      background: #e0e7ff;
      color: #3730a3
    }

    .badge-submitted {
      background: #d1fae5;
      color: #065f46
    }

    .readonly-notice {
      background: #fef3c7;
      border: 1px solid #fde68a;
      border-radius: 9px;
      padding: 10px 15px;
      font-size: 12.5px;
      color: #92400e;
      font-weight: 600;
      margin-bottom: 16px;
      display: none
    }

    .as-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 4px
    }

    .as-dot.saving {
      background: var(--warn);
      animation: pulse .8s infinite
    }

    .as-dot.saved {
      background: var(--g)
    }

    /* ── SDD (ERPNext-style searchable dropdown) ────────────────── */
    .sdd {
      display: block;
      width: 100%
    }

    .sdd-trigger {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 9px 12px 9px 34px;
      border: 1.5px solid var(--bdr);
      border-radius: 8px;
      background: var(--gxl);
      font-family: 'Outfit', sans-serif;
      font-size: 13px;
      color: var(--txt);
      cursor: pointer;
      user-select: none;
      gap: 6px;
      transition: border-color .18s, background .18s;
      position: relative
    }

    .sdd-trigger-ico {
      position: absolute;
      left: 11px;
      top: 50%;
      transform: translateY(-50%);
      width: 13px;
      height: 13px;
      stroke: var(--txtmu);
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      pointer-events: none
    }

    .sdd-trigger:hover,
    .sdd.open>.sdd-trigger {
      border-color: var(--g);
      background: var(--white)
    }

    .sdd-trigger-text {
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      text-align: left
    }

    .sdd-trigger-text.placeholder {
      color: var(--txtmu)
    }

    .sdd-trigger-chevron {
      width: 12px;
      height: 12px;
      stroke: var(--txtmu);
      fill: none;
      stroke-width: 2.5;
      stroke-linecap: round;
      stroke-linejoin: round;
      flex-shrink: 0;
      transition: transform .18s
    }

    .sdd.open>.sdd-trigger .sdd-trigger-chevron {
      transform: rotate(180deg);
      stroke: var(--g)
    }

    .sdd-portal {
      position: fixed;
      z-index: 9999;
      background: #fff;
      border: 1.5px solid var(--g);
      border-radius: 10px;
      box-shadow: 0 6px 24px rgba(0, 0, 0, .16);
      min-width: 260px;
      overflow: hidden;
      display: none;
      animation: sddIn .12s ease
    }

    .sdd-portal.visible {
      display: block
    }

    .sdd-search-wrap {
      padding: 8px 10px;
      border-bottom: 1px solid var(--bdr);
      position: relative
    }

    .sdd-search-ico {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      width: 13px;
      height: 13px;
      stroke: var(--txtmu);
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      pointer-events: none
    }

    .sdd-search {
      width: 100%;
      padding: 7px 10px 7px 32px;
      border: 1.5px solid var(--bdr);
      border-radius: 7px;
      background: var(--gxl);
      font-family: 'Outfit', sans-serif;
      font-size: 12.5px;
      color: var(--txt);
      outline: none;
      transition: border-color .18s;
      box-sizing: border-box
    }

    .sdd-search:focus {
      border-color: var(--g);
      background: #fff
    }

    .sdd-search::placeholder {
      color: var(--txtmu)
    }

    .sdd-list {
      max-height: 240px;
      overflow-y: auto;
      padding: 4px 0
    }

    .sdd-item {
      padding: 9px 14px;
      font-size: 13px;
      cursor: pointer;
      transition: background .1s;
      display: flex;
      flex-direction: column;
      gap: 2px
    }

    .sdd-item:hover {
      background: #f0f9f4
    }

    .sdd-item.selected {
      background: #e8f5ed;
      font-weight: 600
    }

    .sdd-item-main {
      color: var(--txt);
      font-weight: 600
    }

    .sdd-item-sub {
      font-size: 11px;
      color: var(--txtmu)
    }

    .sdd-empty {
      padding: 18px 14px;
      font-size: 12.5px;
      color: var(--txtmu);
      text-align: center
    }

    .sdd-loading {
      padding: 18px 14px;
      font-size: 12.5px;
      color: var(--txtmu);
      text-align: center
    }

    .sdd-clear {
      display: none;
      width: 13px;
      height: 13px;
      stroke: var(--txtmu);
      fill: none;
      stroke-width: 2.5;
      stroke-linecap: round;
      stroke-linejoin: round;
      flex-shrink: 0;
      cursor: pointer;
      transition: stroke .15s
    }

    .sdd-clear:hover {
      stroke: var(--err)
    }

    .sdd.has-value .sdd-clear {
      display: block
    }

    /* lot error tag */
    .lot-err-tag {
      display: none;
      margin-top: 6px;
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fca5a5
    }

    .lot-err-tag.show {
      display: block
    }

    @media(max-width:900px) {
      .fg4 {
        grid-template-columns: 1fr 1fr
      }

      .cat-rules {
        grid-template-columns: 1fr 1fr
      }
    }

    @media(max-width:600px) {

      .fg2,
      .fg4 {
        grid-template-columns: 1fr
      }

      .form-actions {
        flex-direction: column;
        align-items: stretch
      }
    }
  </style>
@endpush

@section('content')

  {{-- SDD shared portal --}}
  <div class="sdd-portal" id="sddPortal">
    <div class="sdd-search-wrap">
      <svg class="sdd-search-ico" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8" />
        <line x1="21" y1="21" x2="16.65" y2="16.65" />
      </svg>
      <input class="sdd-search" id="sddPortalSearch" placeholder="Search lots…" oninput="sddPortalFilter(this.value)"
        onkeydown="sddPortalKeydown(event)">
    </div>
    <div class="sdd-list" id="sddPortalList"></div>
  </div>

  {{-- Page header --}}
  <div class="ph">
    <div>
      <h2 id="pageTitle">Loading…</h2>
      <p id="pageSubtitle"></p>
      <div id="statusBadge" style="margin-top:6px"></div>
    </div>
    <div style="display:flex;gap:8px" id="headerActions">
      <a href="{{ route('admin.mes.acidTesting.index') }}" class="btn btn-outline btn-sm">
        <svg viewBox="0 0 24 24">
          <polyline points="15 18 9 12 15 6" />
        </svg>Back
      </a>
    </div>
  </div>

  <div id="readonlyNotice" class="readonly-notice">🔒 This record has been submitted and is locked from editing.</div>
  <div id="formAlert" class="form-alert"></div>

  {{-- ══════════════════════════════════════════════════════════
  CARD 1 — Primary Details
  ══════════════════════════════════════════════════════════ --}}
  <div class="card">
    <div class="card-head">
      <div class="card-head-left">
        <svg viewBox="0 0 24 24">
          <rect x="3" y="4" width="18" height="18" rx="2" />
          <line x1="16" y1="2" x2="16" y2="6" />
          <line x1="8" y1="2" x2="8" y2="6" />
          <line x1="3" y1="10" x2="21" y2="10" />
        </svg>
        <span>Primary Details</span>
      </div>
      <span id="autosaveStatus" style="font-size:11.5px;color:var(--txtmu);display:none">
        <span class="as-dot" id="asDot"></span><span id="asText"></span>
      </span>
    </div>
    <div class="card-body">
      <div class="fg4" style="margin-bottom:0">

        {{-- DATE --}}
        <div class="field">
          <label>Date <span class="req">*</span></label>
          <div class="iw">
            <svg class="ico" viewBox="0 0 24 24">
              <rect x="3" y="4" width="18" height="18" rx="2" />
              <line x1="16" y1="2" x2="16" y2="6" />
              <line x1="8" y1="2" x2="8" y2="6" />
              <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            <input type="date" id="date" required>
          </div>
        </div>

        {{-- LOT NO — SDD searchable --}}
        <div class="field">
          <label>Lot No <span class="req">*</span></label>
          <div class="sdd" id="sdd_lot_no">
            <div class="sdd-trigger" onclick="toggleSdd('lot_no')" style="min-width:0">
              <svg class="sdd-trigger-ico" viewBox="0 0 24 24">
                <polygon points="12 2 2 7 12 12 22 7 12 2" />
                <polyline points="2 17 12 22 22 17" />
                <polyline points="2 12 12 17 22 12" />
              </svg>
              <span class="sdd-trigger-text placeholder" id="sdd_lot_no_label" data-placeholder="Select a lot…">Select a
                lot…</span>
              <svg class="sdd-clear" onclick="clearSdd('lot_no',event)" viewBox="0 0 24 24">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
              <svg class="sdd-trigger-chevron" viewBox="0 0 24 24">
                <polyline points="6 9 12 15 18 9" />
              </svg>
            </div>
            <input type="hidden" id="lot_no" onchange="onLotSelected()">
          </div>
          <div class="lot-err-tag" id="lotErrTag"></div>
          <div class="err-msg" id="err_lot_no"></div>
        </div>

        {{-- VEHICLE --}}
        <div class="field">
          <label>Vehicle <span class="badge-auto">Auto</span></label>
          <div class="iw">
            <svg class="ico" viewBox="0 0 24 24">
              <rect x="1" y="3" width="15" height="13" />
              <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
              <circle cx="5.5" cy="18.5" r="2.5" />
              <circle cx="18.5" cy="18.5" r="2.5" />
            </svg>
            <input type="text" id="vehicle" class="autofilled" readonly placeholder="Select a lot first…">
          </div>
        </div>

        {{-- SUPPLIER --}}
        <div class="field">
          <label>Supplier <span class="badge-auto">Auto</span></label>
          <div class="iw">
            <svg class="ico" viewBox="0 0 24 24">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
            </svg>
            <input type="text" id="supplier_name" class="autofilled" readonly placeholder="Select a lot first…">
          </div>
        </div>

        {{-- AVG PALLET WEIGHT --}}
        <div class="field">
          <label>Avg Pallet Weight (KG) <span class="req">*</span></label>
          <div class="iw">
            <svg class="ico" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            <input type="number" id="avg_pallet_weight" step="0.01" placeholder="0.00" oninput="calcAvgPalletForeign()">
          </div>
        </div>

        {{-- IN HOUSE WEIGH BRIDGE --}}
        <div class="field">
          <label>InHouse Weigh Bridge(KG) <span class="badge-auto">Auto</span></label>
          <div class="iw">
            <svg class="ico" viewBox="0 0 24 24">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
            <input type="number" id="inhouse_weight" class="autofilled" readonly placeholder="Select a lot first…">
          </div>
        </div>

        {{-- FOREIGN MATERIAL --}}
        <div class="field">
          <label>Foreign Material Weight (KG) <span class="req">*</span></label>
          <div class="iw">
            <svg class="ico" viewBox="0 0 24 24">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
            </svg>
            <input type="number" id="foreign_material_weight" step="0.01" placeholder="0.00"
              oninput="calcAvgPalletForeign()">
          </div>
        </div>

        {{-- AVG PALLET & FOREIGN (auto) --}}
        <div class="field">
          <label>Avg Pallet &amp; Foreign (KG) <span class="badge-calc">Calc</span></label>
          <div class="iw">
            <svg class="ico" viewBox="0 0 24 24">
              <line x1="12" y1="1" x2="12" y2="23" />
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
            </svg>
            <input type="number" id="avg_pallet_foreign_weight" readonly class="ro" placeholder="Auto-calc">
          </div>
          <div style="font-size:10px;color:var(--txtmu);margin-top:1px">= (Avg×Pallets + Foreign) ÷ Pallets</div>
        </div>

      </div>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════════
  CARD 2 — Pallet Weight Records (Section 1)
  Columns: SR | Pallet No | ULAB Type | Gross Wt | Avg P&F | Net Wt | [acid cols if acid present]
  ══════════════════════════════════════════════════════════ --}}
  <div class="card">
    <div class="card-head">
      <div class="card-head-left">
        <svg viewBox="0 0 24 24">
          <path
            d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
        </svg>
        <span>Pallet Weight Records</span>
      </div>
      <button class="btn-add" onclick="addRow()" id="btnAddRow">
        <svg viewBox="0 0 24 24">
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add Row
      </button>
    </div>
    <div style="padding:0">
      <div class="tbl-wrap" style="border-radius:0;border:none;border-bottom:1px solid var(--bdr)">
        <table class="dt" id="palletTable">
          <thead>
            <tr>
              <th class="tc" style="width:40px">#</th>
              <th style="min-width:90px">Pallet No</th>
              <th style="min-width:170px">ULAB Type</th>
              <th style="min-width:110px">Gross Wt (KG)</th>
              <th style="min-width:110px">Avg P&amp;F (KG) <span style="font-size:9px;font-weight:400">Auto</span></th>
              <th style="min-width:110px">Net Wt (KG) <span style="font-size:9px;font-weight:400">Auto</span></th>
              <th class="acid-col" style="min-width:100px">Initial Wt (KG)</th>
              <th class="acid-col" style="min-width:100px">Drained Wt (KG)</th>
              <th class="acid-col" style="min-width:90px">Wt Diff (KG) <span
                  style="font-size:9px;font-weight:400">Auto</span></th>
              <th class="acid-col" style="min-width:90px">Acid % <span style="font-size:9px;font-weight:400">Auto</span>
              </th>
              <th style="width:36px"></th>
            </tr>
          </thead>
          <tbody id="palletBody"></tbody>
          <tfoot>
            <tr>
              <td colspan="3" style="text-align:right;font-size:10.5px;letter-spacing:.7px;color:var(--txtmu)">TOTAL (KG)
              </td>
              <td><input type="text" id="totalGross" readonly class="ri" placeholder="0.000"
                  style="font-weight:800;color:var(--g);background:var(--gl)"></td>
              <td></td>
              <td><input type="text" id="totalNet" readonly class="ri" placeholder="0.000"
                  style="font-weight:800;color:var(--g);background:var(--gl)"></td>
              <td><input type="text" id="totalInitial" readonly class="ri acid-col" placeholder="0.000"
                  style="font-weight:800;color:var(--g);background:var(--gl)"></td>
              <td><input type="text" id="totalDrained" readonly class="ri acid-col" placeholder="0.000"
                  style="font-weight:800;color:var(--g);background:var(--gl)"></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>

      {{-- Category reference + Net Avg Acid % banner --}}
      <div style="padding:16px 20px 20px">
        <div class="cat-rules">
          <div class="crule ch">
            <div class="crule-title">⚡ High Acid</div>
            <div style="font-size:10px;opacity:.8">Avg Acid % &gt; 30%</div>
          </div>
          <div class="crule cn">
            <div class="crule-title">✓ Normal</div>
            <div style="font-size:10px;opacity:.8">15% – 30%</div>
          </div>
          <div class="crule cl">
            <div class="crule-title">↓ Low Acid</div>
            <div style="font-size:10px;opacity:.8">5% – 15%</div>
          </div>
          <div class="crule cd">
            <div class="crule-title">○ Dry / Empty</div>
            <div style="font-size:10px;opacity:.8">&lt; 5%</div>
          </div>
        </div>

        <div class="result-banner" id="resultBanner">
          <div>
            <div class="rb-label">Net Average Acid %</div>
            <div class="rb-val" id="netAvgPct">—</div>
            <div class="rb-sub">= Total Drained ÷ Total Initial × 100</div>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px">
            <div class="cat-pill" id="catPill" style="display:none">
              <span class="cat-dot"></span>
              <span id="catLabel">—</span>
            </div>
            <div style="font-size:11px;color:var(--txtmu)" id="catRule"></div>
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- Hidden fields --}}
  <input type="hidden" id="supplier_id">
  <input type="hidden" id="invoice_qty_hidden">

  {{-- Sticky footer --}}
  <div class="form-actions" id="formActions">
    <a href="{{ route('admin.mes.acidTesting.index') }}" class="btn btn-outline btn-sm">Cancel</a>
    <div style="display:flex;gap:10px;align-items:center">
      <button type="button" class="btn btn-primary btn-sm" id="btnSave" onclick="saveForm()">
        <svg viewBox="0 0 24 24">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z" />
          <polyline points="17 21 17 13 7 13 7 21" />
          <polyline points="7 3 7 8 15 8" />
        </svg>
        <span id="btnSaveLabel">Create Record</span>
      </button>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    // ── State ─────────────────────────────────────────────────────────
    const PATH_PARTS = window.location.pathname.split('/').filter(Boolean);
    const isCreate = PATH_PARTS[PATH_PARTS.length - 1] === 'create';
    const recordId = isCreate ? null : PATH_PARTS[PATH_PARTS.length - 2];
    let isSubmitted = false;
    let autosaveTimer;
    let rowCount = 0;
    let currentLotData = null;

    // SDD state
    let sddRegistry = {};
    let sddActiveField = null;
    let lotItems = [];  // [{lot_no, supplier_name, vehicle_number, ...}]

    const ACID_PRESENT = 'ACID PRESENT';
    const ULAB_OPTIONS = [
      { id: 1000024, name: 'USED GEL BATTERY/ABS' },
      { id: 1000025, name: 'USED TRACTION BATTERY' },
      { id: 1000026, name: 'ULAB - MC BATTERY (DRY)' },
      { id: 1000028, name: 'ULAB - INDUSTRIAL' },
      { id: 5, name: 'ACID_PRESENT' }
    ];

    // ════════════════════════════════════════════════════════════════
    // INIT
    // ════════════════════════════════════════════════════════════════
    async function init() {
      document.getElementById('date').value = new Date().toISOString().slice(0, 10);
      await loadAvailableLots();

      if (isCreate) {
        document.getElementById('pageTitle').textContent = 'Create Acid Test';
        document.getElementById('pageSubtitle').textContent = 'Record new acid testing log';
        document.getElementById('breadcrumbTitle').textContent = 'Create';
        document.getElementById('btnSaveLabel').textContent = 'Create Record';
        addRow();
      } else {
        await loadRecord();
      }
    }
    init();

    // ════════════════════════════════════════════════════════════════
    // SDD ENGINE (ERPNext portal style — exact copy from refining)
    // ════════════════════════════════════════════════════════════════
    function sddRegister(fieldId, items, selectedValue = null) {
      sddRegistry[fieldId] = { items, selected: null };
      if (selectedValue !== null && selectedValue !== '') {
        const item = items.find(i => String(i.value) === String(selectedValue));
        if (item) sddRegistry[fieldId].selected = item;
      }
      sddUpdateTrigger(fieldId);
      const hidden = document.getElementById(fieldId);
      if (hidden) hidden.value = sddRegistry[fieldId]?.selected?.value ?? '';
    }

    function sddUpdateTrigger(fieldId) {
      const reg = sddRegistry[fieldId];
      if (!reg) return;
      const wrap = document.getElementById(`sdd_${fieldId}`);
      const label = document.getElementById(`sdd_${fieldId}_label`);
      if (!label || !wrap) return;
      if (reg.selected) {
        label.textContent = reg.selected.label;
        label.classList.remove('placeholder');
        wrap.classList.add('has-value');
      } else {
        label.textContent = label.dataset.placeholder || 'Select…';
        label.classList.add('placeholder');
        wrap.classList.remove('has-value');
      }
      const hidden = document.getElementById(fieldId);
      if (hidden) hidden.value = reg?.selected?.value ?? '';
    }

    function sddSelect(fieldId, value, triggerChange = true) {
      if (!sddRegistry[fieldId]) return;
      const item = value ? sddRegistry[fieldId].items.find(i => String(i.value) === String(value)) : null;
      sddRegistry[fieldId].selected = item || null;
      sddUpdateTrigger(fieldId);
      const hidden = document.getElementById(fieldId);
      if (hidden && triggerChange) hidden.dispatchEvent(new Event('change'));
      sddClosePortal();
    }

    function clearSdd(fieldId, e) {
      if (e) { e.stopPropagation(); e.preventDefault(); }
      sddSelect(fieldId, '', false);
      const hidden = document.getElementById(fieldId);
      if (hidden) hidden.dispatchEvent(new Event('change'));
    }

    function toggleSdd(fieldId) {
      if (sddActiveField === fieldId) { sddClosePortal(); return; }
      sddOpenPortal(fieldId);
    }

    function sddOpenPortal(fieldId) {
      const trigger = document.querySelector(`#sdd_${fieldId} .sdd-trigger`);
      if (!trigger || !sddRegistry[fieldId]) return;

      sddActiveField = fieldId;
      document.querySelectorAll('.sdd.open').forEach(el => el.classList.remove('open'));
      document.getElementById(`sdd_${fieldId}`)?.classList.add('open');

      const portal = document.getElementById('sddPortal');
      const rect = trigger.getBoundingClientRect();
      const viewW = window.innerWidth;
      const viewH = window.innerHeight;

      portal.style.top = portal.style.bottom = portal.style.left = portal.style.right = '';
      portal.style.width = Math.max(rect.width, 280) + 'px';

      let left = rect.left;
      const portalW = Math.max(rect.width, 280);
      if (left + portalW > viewW - 8) left = Math.max(8, viewW - portalW - 8);
      portal.style.left = left + 'px';

      const spaceBelow = viewH - rect.bottom;
      if (spaceBelow >= 200 || spaceBelow >= rect.top) {
        portal.style.top = (rect.bottom + 4) + 'px';
      } else {
        portal.style.bottom = (viewH - rect.top + 4) + 'px';
      }

      sddPortalRender('');
      portal.classList.add('visible');
      const search = document.getElementById('sddPortalSearch');
      if (search) { search.value = ''; setTimeout(() => search.focus(), 40); }
    }

    function sddClosePortal() {
      document.getElementById('sddPortal')?.classList.remove('visible');
      document.querySelectorAll('.sdd.open').forEach(el => el.classList.remove('open'));
      sddActiveField = null;
    }

    function sddPortalRender(query) {
      if (!sddActiveField || !sddRegistry[sddActiveField]) return;
      const q = query.toLowerCase().trim();
      const items = sddRegistry[sddActiveField].items;
      const filtered = q ? items.filter(i =>
        i.label.toLowerCase().includes(q) || (i.sub ?? '').toLowerCase().includes(q)
      ) : items;
      const current = sddRegistry[sddActiveField].selected?.value ?? '';
      const list = document.getElementById('sddPortalList');
      if (!list) return;

      if (!filtered.length) {
        list.innerHTML = '<div class="sdd-empty">No lots available</div>';
        return;
      }
      list.innerHTML = filtered.map(item => {
        const sel = String(item.value) === String(current);
        return `<div class="sdd-item${sel ? ' selected' : ''}" onclick="sddSelect('${sddActiveField}','${item.value}')">
                    <div class="sdd-item-main">${item.label}</div>
                    ${item.sub ? `<div class="sdd-item-sub">${item.sub}</div>` : ''}
                  </div>`;
      }).join('');
    }

    function sddPortalFilter(q) { sddPortalRender(q); }
    function sddPortalKeydown(e) { if (e.key === 'Escape') sddClosePortal(); }

    document.addEventListener('click', e => {
      if (!e.target.closest('.sdd') && !e.target.closest('#sddPortal')) sddClosePortal();
    });
    document.addEventListener('scroll', () => {
      if (sddActiveField) {
        const trigger = document.querySelector(`#sdd_${sddActiveField} .sdd-trigger`);
        if (trigger) {
          const rect = trigger.getBoundingClientRect();
          const portal = document.getElementById('sddPortal');
          portal.style.top = (rect.bottom + 4) + 'px';
          portal.style.left = rect.left + 'px';
        }
      }
    }, true);

    // ════════════════════════════════════════════════════════════════
    // LOAD AVAILABLE LOTS → populate SDD
    // ════════════════════════════════════════════════════════════════
    async function loadAvailableLots() {
      const list = document.getElementById('sddPortalList');
      if (list) list.innerHTML = '<div class="sdd-loading">Loading lots…</div>';

      const res = await apiFetch('/acid-testings/available-lots');
      if (!res?.ok) {
        sddRegister('lot_no', [], null);
        return;
      }
      const json = await res.json();
      lotItems = json.data ?? [];

      const items = lotItems.map(l => ({
        value: l.lot_no,
        label: l.lot_no,
        sub: `${l.supplier_name} · ${l.receipt_date ?? ''} · Rcvd: ${l.received_qty ?? ''} KG`,
      }));

      sddRegister('lot_no', items, null);
    }

    // When lot selected from SDD dropdown
    function onLotSelected() {
      const lotNo = document.getElementById('lot_no').value;
      const tag = document.getElementById('lotErrTag');
      tag.className = 'lot-err-tag';

      if (!lotNo) {
        clearLotAutofill();
        currentLotData = null;
        return;
      }

      const lot = lotItems.find(l => l.lot_no === lotNo);
      if (!lot) { clearLotAutofill(); return; }

      currentLotData = lot;

      // Autofill fields
      const fields = {
        vehicle: lot.vehicle_number ?? '',
        supplier_name: lot.supplier_name ?? '',
        inhouse_weight: lot.received_qty ?? '',
        supplier_id: lot.supplier_id ?? '',
        invoice_qty_hidden: lot.invoice_qty ?? '',
      };
      ['vehicle', 'supplier_name', 'inhouse_weight'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.value = fields[id];
        el.classList.remove('flash');
        void el.offsetWidth;
        el.classList.add('flash');
      });
      document.getElementById('supplier_id').value = lot.supplier_id ?? '';
      document.getElementById('invoice_qty_hidden').value = lot.invoice_qty ?? '';

      calcAvgPalletForeign();
      triggerAutosave();
    }

    function clearLotAutofill() {
      ['vehicle', 'supplier_name', 'inhouse_weight'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.value = ''; el.placeholder = 'Select a lot first…'; }
      });
      document.getElementById('supplier_id').value = '';
      document.getElementById('invoice_qty_hidden').value = '';
    }

    // ════════════════════════════════════════════════════════════════
    // LOAD RECORD (edit mode)
    // ════════════════════════════════════════════════════════════════
    async function loadRecord() {
      const res = await apiFetch(`/acid-testings/${recordId}`);
      if (!res?.ok) { showAlert('Failed to load record.'); return; }

      const { data } = await res.json();
      isSubmitted = (int => int >= 1)(parseInt(data.status ?? 0));

      document.getElementById('date').value = data.test_date?.slice(0, 10) ?? '';
      document.getElementById('supplier_name').value = data.supplier?.supplier_name ?? '';
      document.getElementById('supplier_id').value = data.supplier_id ?? '';
      document.getElementById('vehicle').value = data.vehicle_number ?? '';
      document.getElementById('avg_pallet_weight').value = data.avg_pallet_weight ?? '';
      document.getElementById('inhouse_weight').value = data.received_qty ?? '';
      document.getElementById('foreign_material_weight').value = data.foreign_material_weight ?? '';
      document.getElementById('avg_pallet_foreign_weight').value = data.avg_pallet_and_foreign_weight ?? '';
      document.getElementById('invoice_qty_hidden').value = data.invoice_qty ?? '';

      // Lot SDD — add to items if not present, then select
      if (data.lot_number) {
        if (!lotItems.find(l => l.lot_no === data.lot_number)) {
          const extra = {
            lot_no: data.lot_number,
            supplier_name: data.supplier?.supplier_name ?? '',
            vehicle_number: data.vehicle_number ?? '',
            received_qty: data.received_qty,
            invoice_qty: data.invoice_qty,
            supplier_id: data.supplier_id,
          };
          lotItems.push(extra);
        }
        const items = lotItems.map(l => ({
          value: l.lot_no,
          label: l.lot_no,
          sub: `${l.supplier_name} · Rcvd: ${l.received_qty ?? ''} KG`,
        }));
        sddRegister('lot_no', items, data.lot_number);
      }

      document.getElementById('palletBody').innerHTML = '';
      rowCount = 0;

      (data.details ?? []).forEach(row => {
        addRow({
          pallet_no: row.pallet_no,
          ulab_type: row.ulab_type,
          gross_weight: row.gross_weight,
          initial_weight: row.initial_weight,
          drained_weight: row.drained_weight,
        });
      });
      if (!data.details?.length) addRow();

      recalcTotals();

      document.getElementById('pageTitle').textContent = 'Edit Acid Test';
      document.getElementById('pageSubtitle').textContent = `Lot: ${data.lot_number}`;
      document.getElementById('breadcrumbTitle').textContent = 'Edit';
      document.getElementById('btnSaveLabel').textContent = 'Save Changes';

      const badge = document.getElementById('statusBadge');
      if (isSubmitted) {
        badge.innerHTML = '<span class="badge-status badge-submitted">● Submitted</span>';
        setReadonly(true);
        // Show print button
        const actDiv = document.getElementById('headerActions');
        const pb = document.createElement('a');
        pb.className = 'btn btn-outline btn-sm';
        pb.target = '_blank';
        pb.href = `{{ url('/admin/mes/acidTesting') }}/${recordId}/print`;
        pb.innerHTML = `<svg viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print Report`;
        actDiv.prepend(pb);
      } else {
        badge.innerHTML = '<span class="badge-status badge-draft">Draft</span>';
        const actDiv = document.getElementById('headerActions');
        const sb = document.createElement('button');
        sb.className = 'btn btn-outline btn-sm';
        sb.innerHTML = `<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Submit`;
        sb.onclick = submitRecord;
        actDiv.prepend(sb);
        setupAutosave();
      }
    }

    // ════════════════════════════════════════════════════════════════
    // CALCULATIONS
    // ════════════════════════════════════════════════════════════════
    function calcAvgPalletForeign() {
      const avg = parseFloat(document.getElementById('avg_pallet_weight').value) || 0;
      const foreign = parseFloat(document.getElementById('foreign_material_weight').value) || 0;
      const pallets = Math.max(document.querySelectorAll('#palletBody tr').length, 1);
      const result = avg + (foreign / pallets);
      document.getElementById('avg_pallet_foreign_weight').value = result > 0 ? result.toFixed(3) : '';
      document.querySelectorAll('#palletBody tr').forEach(tr => calcRow(tr.dataset.idx));
      recalcTotals();
    }

    function calcRow(idx) {
      const gross = parseFloat(document.getElementById(`gross_${idx}`)?.value) || 0;
      const avgPF = parseFloat(document.getElementById('avg_pallet_foreign_weight')?.value) || 0;
      const net = gross > 0 ? Math.max(0, gross - avgPF) : 0;
      const netEl = document.getElementById(`net_${idx}`);
      const avgPFEl = document.getElementById(`avgpf_${idx}`);
      if (netEl) netEl.value = gross > 0 ? net.toFixed(3) : '';
      if (avgPFEl) avgPFEl.value = avgPF.toFixed(3);

      // Acid columns
      const initial = parseFloat(document.getElementById(`initial_${idx}`)?.value) || 0;
      const drained = parseFloat(document.getElementById(`drained_${idx}`)?.value) || 0;
      const diff = initial > 0 ? Math.max(0, initial - drained) : 0;
      const pct = initial > 0 ? (diff / initial) * 100 : 0;
      const diffEl = document.getElementById(`diff_${idx}`);
      const pctEl = document.getElementById(`acid_pct_${idx}`);
      if (diffEl) diffEl.value = initial > 0 ? diff.toFixed(3) : '';
      if (pctEl) pctEl.value = initial > 0 ? pct.toFixed(2) : '';

      recalcTotals();
      triggerAutosave();
    }

    function recalcTotals() {
      let tGross = 0, tNet = 0, tInit = 0, tDrained = 0;
      document.querySelectorAll('#palletBody tr').forEach(tr => {
        const idx = tr.dataset.idx;
        tGross += parseFloat(document.getElementById(`gross_${idx}`)?.value) || 0;
        tNet += parseFloat(document.getElementById(`net_${idx}`)?.value) || 0;
        tInit += parseFloat(document.getElementById(`initial_${idx}`)?.value) || 0;
        tDrained += parseFloat(document.getElementById(`drained_${idx}`)?.value) || 0;
      });
      document.getElementById('totalGross').value = tGross > 0 ? tGross.toFixed(3) : '';
      document.getElementById('totalNet').value = tNet > 0 ? tNet.toFixed(3) : '';
      document.getElementById('totalInitial').value = tInit > 0 ? tInit.toFixed(3) : '';
      document.getElementById('totalDrained').value = tDrained > 0 ? tDrained.toFixed(3) : '';

      const netPct = tInit > 0 ? (tDrained / tInit) * 100 : null;
      updateResultBanner(netPct);
    }

    function updateResultBanner(pct) {
      const banner = document.getElementById('resultBanner');
      const valEl = document.getElementById('netAvgPct');
      const pill = document.getElementById('catPill');
      const label = document.getElementById('catLabel');
      const ruleEl = document.getElementById('catRule');

      if (pct === null) {
        valEl.textContent = '—'; pill.style.display = 'none';
        banner.className = 'result-banner'; ruleEl.textContent = '';
        return;
      }

      valEl.textContent = pct.toFixed(2) + '%';
      pill.style.display = 'inline-flex';

      let cat, catText, rule;
      if (pct > 30) { cat = 'cat-high'; catText = 'High Acid'; rule = 'Avg Acid % > 30%'; }
      else if (pct >= 15) { cat = 'cat-normal'; catText = 'Normal'; rule = '15% ≤ Avg Acid % ≤ 30%'; }
      else if (pct >= 5) { cat = 'cat-low'; catText = 'Low Acid'; rule = '5% ≤ Avg Acid % < 15%'; }
      else { cat = 'cat-dry'; catText = 'Dry / Empty'; rule = 'Avg Acid % < 5%'; }

      banner.className = `result-banner ${cat}`;
      pill.className = `cat-pill ${cat}`;
      label.textContent = catText;
      ruleEl.textContent = rule;
    }

    // ════════════════════════════════════════════════════════════════
    // ROWS
    // ════════════════════════════════════════════════════════════════
    function addRow(data = {}) {
      rowCount++;
      const idx = rowCount;
      const tbody = document.getElementById('palletBody');
      const tr = document.createElement('tr');
      tr.id = `prow-${idx}`;
      tr.dataset.idx = idx;

      const ulabOpts = ULAB_OPTIONS.map(u =>
        `<option value="${u.id}" ${(data.ulab_type ?? '') == u.id ? 'selected' : ''}>${u.name}</option>`
      ).join('');
      console.log("Acid", data.ulab_types);
      const isAcid = (data.ulab_type ?? '') == 5;
      const cellDis = isAcid ? '' : 'cell-disabled';
      const avgPFVal = parseFloat(document.getElementById('avg_pallet_foreign_weight').value || 0).toFixed(3);

      tr.innerHTML = `
                <td class="sr-n">${idx}</td>
                <td><input type="text" class="ri" id="pallet_no_${idx}" value="${data.pallet_no ?? ''}" placeholder="WP-${String(idx).padStart(2, '0')}"></td>
                <td>
                  <select class="rsel" id="ulab_${idx}" onchange="onUlabChange(${idx})">
                    ${ulabOpts}
                  </select>
                </td>
                <td><input type="number" class="ri" id="gross_${idx}" step="0.001" placeholder="0.000" value="${data.gross_weight ?? ''}" oninput="calcRow(${idx})"></td>
                <td><input type="number" class="ri" id="avgpf_${idx}" readonly value="${avgPFVal}"></td>
                <td><input type="number" class="ri" id="net_${idx}" readonly></td>
                <td class="${cellDis}" id="cell_initial_${idx}">
                  <input type="number" class="ri" id="initial_${idx}" step="0.001" placeholder="0.000"
                    value="${data.initial_weight ?? ''}"
                    ${isAcid ? '' : 'readonly tabindex="-1"'}
                    oninput="calcRow(${idx})">
                </td>
                <td class="${cellDis}" id="cell_drained_${idx}">
                  <input type="number" class="ri" id="drained_${idx}" step="0.001" placeholder="0.000"
                    value="${data.drained_weight ?? ''}"
                    ${isAcid ? '' : 'readonly tabindex="-1"'}
                    oninput="calcRow(${idx})">
                </td>
                <td class="${cellDis}" id="cell_diff_${idx}">
                  <input type="number" class="ri" id="diff_${idx}" readonly>
                </td>
                <td class="${cellDis}" id="cell_pct_${idx}">
                  <input type="number" class="ri" id="acid_pct_${idx}" readonly>
                </td>
                <td style="text-align:center">
                  ${idx > 1 ? `<button class="del-btn" onclick="removeRow(${idx})" title="Remove">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                  </button>` : ''}
                </td>`;

      tbody.appendChild(tr);
      tr.style.opacity = '0'; tr.style.transform = 'translateY(-4px)';
      requestAnimationFrame(() => {
        tr.style.transition = 'opacity .22s,transform .22s';
        tr.style.opacity = '1'; tr.style.transform = 'translateY(0)';
      });

      calcRow(idx);
      calcAvgPalletForeign();
    }

    function onUlabChange(idx) {
      const ulab = document.getElementById(`ulab_${idx}`).value;
      const isAcid = ulab == 5;

      ['initial', 'drained', 'diff', 'pct'].forEach(key => {
        const cell = document.getElementById(`cell_${key === 'pct' ? 'pct' : key}_${idx}`);
        const inp = document.getElementById(key === 'pct' ? `acid_pct_${idx}` : `${key}_${idx}`);
        if (!cell || !inp) return;
        if (isAcid) {
          cell.classList.remove('cell-disabled');
          if (key === 'initial' || key === 'drained') {
            inp.removeAttribute('readonly');
            inp.removeAttribute('tabindex');
          }
        } else {
          cell.classList.add('cell-disabled');
          if (key === 'initial' || key === 'drained') {
            inp.setAttribute('readonly', '');
            inp.setAttribute('tabindex', '-1');
          }
          inp.value = '';
        }
      });

      calcRow(idx);
      triggerAutosave();
    }

    function removeRow(idx) {
      const tr = document.getElementById(`prow-${idx}`);
      if (!tr) return;
      tr.style.transition = 'opacity .18s'; tr.style.opacity = '0';
      setTimeout(() => {
        tr.remove();
        renumberRows();
        calcAvgPalletForeign();
        recalcTotals();
      }, 200);
    }

    function renumberRows() {
      document.querySelectorAll('#palletBody tr').forEach((tr, i) => {
        tr.querySelector('.sr-n').textContent = i + 1;
      });
    }

    // ════════════════════════════════════════════════════════════════
    // BUILD PAYLOAD
    // ════════════════════════════════════════════════════════════════
    function buildPayload() {
      const lotNo = document.getElementById('lot_no').value;
      if (!lotNo) {
        showAlert('Please select a Lot No before saving.');
        document.getElementById('sdd_lot_no')?.querySelector('.sdd-trigger')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return null;
      }

      const details = [];
      let valid = true;
      const errs = [];

      document.querySelectorAll('#palletBody tr').forEach((tr, i) => {
        const idx = tr.dataset.idx;
        const palletNo = document.getElementById(`pallet_no_${idx}`)?.value?.trim();
        const ulab = document.getElementById(`ulab_${idx}`)?.value;
        const gross = parseFloat(document.getElementById(`gross_${idx}`)?.value) || 0;
        const net = parseFloat(document.getElementById(`net_${idx}`)?.value) || 0;
        const isAcid = ulab === ACID_PRESENT;
        const initial = document.getElementById(`initial_${idx}`).value;
        const drained = document.getElementById(`drained_${idx}`).value;

        if (!palletNo) { errs.push(`Row ${i + 1}: Pallet No required.`); valid = false; }
        if (!ulab) { errs.push(`Row ${i + 1}: ULAB Type required.`); valid = false; }
        if (gross <= 0) { errs.push(`Row ${i + 1}: Gross Weight must be > 0.`); valid = false; }

        details.push({
          pallet_no: palletNo || (i + 1),
          ulab_type: ulab,
          gross_weight: gross,
          net_weight: net,
          initial_weight: initial,
          drained_weight: drained,
          remarks: ulab,
        });
      });

      if (!valid) { showAlert(errs.join('\n')); return null; }

      return {
        test_date: document.getElementById('date').value,
        lot_number: lotNo,
        supplier_id: document.getElementById('supplier_id').value,
        vehicle_number: document.getElementById('vehicle').value,
        avg_pallet_weight: parseFloat(document.getElementById('avg_pallet_weight').value) || 0,
        foreign_material_weight: parseFloat(document.getElementById('foreign_material_weight').value) || 0,
        invoice_qty: parseFloat(document.getElementById('invoice_qty_hidden').value) || 0,
        received_qty: parseFloat(document.getElementById('inhouse_weight').value) || 0,
        avg_pallet_and_foreign_weight: parseFloat(document.getElementById('avg_pallet_foreign_weight').value) || 0,
        details,
      };
    }

    // ════════════════════════════════════════════════════════════════
    // SAVE / SUBMIT
    // ════════════════════════════════════════════════════════════════
    async function saveForm(silent = false) {
      const payload = buildPayload();
      console.log("Payload", payload);
      if (!payload) return;

      const btn = document.getElementById('btnSave');
      if (!silent) {
        btn.disabled = true;
        btn.innerHTML = `<svg viewBox="0 0 24 24" style="animation:spin .7s linear infinite;width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Saving…`;
      }

      const method = isCreate ? 'POST' : 'PUT';
      const endpoint = isCreate ? '/acid-testings' : `/acid-testings/${recordId}`;

      const res = await apiFetch(endpoint, { method, body: JSON.stringify(payload) });
      if (!silent) {
        btn.disabled = false;
        btn.innerHTML = `<svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> <span id="btnSaveLabel">${isCreate ? 'Create Record' : 'Save Changes'}</span>`;
      }

      if (!res) return;
      const data = await res.json();

      if (res.ok && data.status === 'ok') {
        if (!silent) {
          if (isCreate) {
            window.location.href = `{{ url('/admin/mes/acidTesting') }}/${data.data.id}/edit`;
          } else {
            showAlert('Saved successfully.', 'success');
          }
        } else {
          setDot('saved', 'Autosaved ' + new Date().toLocaleTimeString());
          setTimeout(() => document.getElementById('autosaveStatus').style.display = 'none', 4000);
        }
      } else if (res.status === 422) {
        if (!silent) showAlert(data.message ?? 'Fix the errors below.');
      } else {
        if (!silent) showAlert(data.message ?? 'Something went wrong.');
      }
    }

    async function submitRecord() {
      if (!confirm('Submit this record? It will be locked from further edits.')) return;
      await saveForm(true);
      const res = await apiFetch(`/acid-testings/${recordId}/status`, {
        method: 'PATCH', body: JSON.stringify({ status: 1 }),
      });
      if (res?.ok) {
        showAlert('Submitted successfully.', 'success');
        setTimeout(() => window.location.href = '{{ route("admin.mes.acidTesting.index") }}', 1500);
      } else {
        const d = await res?.json();
        showAlert(d?.message ?? 'Submit failed.');
      }
    }

    // ════════════════════════════════════════════════════════════════
    // AUTOSAVE
    // ════════════════════════════════════════════════════════════════
    function setupAutosave() {
      ['date', 'avg_pallet_weight', 'foreign_material_weight'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', triggerAutosave);
      });
    }

    function triggerAutosave() {
      if (isCreate || isSubmitted) return;
      setDot('saving', 'Saving…');
      document.getElementById('autosaveStatus').style.display = 'inline';
      clearTimeout(autosaveTimer);
      autosaveTimer = setTimeout(() => saveForm(true), 2200);
    }

    function setDot(state, text) {
      const dot = document.getElementById('asDot');
      const txt = document.getElementById('asText');
      if (dot) dot.className = `as-dot ${state}`;
      if (txt) txt.textContent = text;
    }

    // ════════════════════════════════════════════════════════════════
    // UTILITY
    // ════════════════════════════════════════════════════════════════
    function setReadonly(on) {
      document.querySelectorAll('#palletBody input,#palletBody select').forEach(el => {
        if (on) el.setAttribute('disabled', '');
        else el.removeAttribute('disabled');
      });
      ['date', 'avg_pallet_weight', 'foreign_material_weight'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (on) el.setAttribute('disabled', ''); else el.removeAttribute('disabled');
      });
      document.querySelectorAll('.sdd-trigger,.sdd-search').forEach(el => {
        if (on) { el.style.pointerEvents = 'none'; el.style.opacity = '.6'; }
        else { el.style.pointerEvents = ''; el.style.opacity = ''; }
      });
      document.getElementById('formActions').style.display = on ? 'none' : 'flex';
      const addBtn = document.getElementById('btnAddRow');
      if (addBtn) addBtn.style.display = on ? 'none' : '';
      if (on) document.getElementById('readonlyNotice').style.display = 'block';
    }

    function showAlert(msg, type = 'error') {
      const el = document.getElementById('formAlert');
      el.className = `form-alert ${type}`;
      el.textContent = msg;
      window.scrollTo({ top: 0, behavior: 'smooth' });
      if (type === 'success') setTimeout(() => { el.className = 'form-alert'; el.textContent = ''; }, 4000);
    }
  </script>
@endpush