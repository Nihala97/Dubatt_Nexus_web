<?php
$path = 'resources/views/admin/reports/material_inward.blade.php';
$content = file_get_contents($path);

// 1. Add Driver Name filter field after Lot No field
$lotFilterTarget = <<<'HTML'
                <div class="field">
                    <label>Lot No</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="f_lot_no" placeholder="Search lot…" oninput="onFilterChange()">
                    </div>
                </div>
HTML;

$driverFilterAdd = <<<'HTML'
                <div class="field">
                    <label>Driver Name</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <input type="text" id="f_driver_name" placeholder="Search driver…" oninput="onFilterChange()">
                    </div>
                </div>
HTML;

if (strpos($content, $lotFilterTarget) === false) {
    // try CRLF or different ellipsis
    $lotFilterTarget = str_replace("\n", "\r\n", $lotFilterTarget);
    $driverFilterAdd = str_replace("\n", "\r\n", $driverFilterAdd);
}

if (strpos($content, $lotFilterTarget) === false) {
    $patternLot = '/<div class="field">\s*<label>Lot No<\/label>[\s\S]*?id="f_lot_no"[\s\S]*?<\/div>\s*<\/div>/u';
    if (preg_match($patternLot, $content, $m)) {
        $content = preg_replace($patternLot, $m[0] . "\n" . $driverFilterAdd, $content, 1);
    } else {
        die("Lot filter field not found\n");
    }
} else {
    $content = str_replace($lotFilterTarget, $lotFilterTarget . "\n" . $driverFilterAdd, $content);
}

// 2. Add Driver Name column header to #reportTable
$headerTarget = <<<'HTML'
                        <th class="sortable" data-col="lot_no" onclick="sortBy('lot_no')">Lot No <span class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg><svg class="ico-desc" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span></th>
                        <th class="sortable" data-col="supplier_name" onclick="sortBy('supplier_name')">Supplier <span class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg><svg class="ico-desc" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span></th>
HTML;

$driverHeaderAdd = <<<'HTML'
                        <th class="sortable" data-col="lot_no" onclick="sortBy('lot_no')">Lot No <span class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg><svg class="ico-desc" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span></th>
                        <th class="sortable" data-col="driver_name" onclick="sortBy('driver_name')">Driver Name <span class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg><svg class="ico-desc" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span></th>
                        <th class="sortable" data-col="supplier_name" onclick="sortBy('supplier_name')">Supplier <span class="sort-ico"><svg class="ico-asc" viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg><svg class="ico-desc" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span></th>
HTML;

if (strpos($content, $headerTarget) === false) {
    $headerTarget = str_replace("\n", "\r\n", $headerTarget);
    $driverHeaderAdd = str_replace("\n", "\r\n", $driverHeaderAdd);
}

if (strpos($content, $headerTarget) === false) {
    die("Header target not found\n");
}
$content = str_replace($headerTarget, $driverHeaderAdd, $content);

// 3. Update loading / error / empty colspans from 9 to 10, and tfoot colspan from 4 to 5
$content = str_replace('<td colspan="9">', '<td colspan="10">', $content);
$content = str_replace('colspan="4" style="text-align:right', 'colspan="5" style="text-align:right', $content);

// 4. Update buildParams to include driver_name
$oldBuildParams = "lot_no:      document.getElementById('f_lot_no').value,";
$newBuildParams = "lot_no:      document.getElementById('f_lot_no').value,\n        driver_name: document.getElementById('f_driver_name')?.value ?? '',";
if (strpos($content, $oldBuildParams) === false) {
    $patternBuild = "/lot_no:\s*document\.getElementById\('f_lot_no'\)\.value,/u";
    $content = preg_replace($patternBuild, "lot_no:      document.getElementById('f_lot_no').value,\n        driver_name: document.getElementById('f_driver_name')?.value ?? '',", $content, 1);
} else {
    $content = str_replace($oldBuildParams, $newBuildParams, $content);
}

// 5. Update renderTable row HTML to include driver_name cell
$oldRenderRow = <<<'JS'
        return `<tr>
            <td>${escHtml(r.receipt_date)}</td>
            <td style="font-weight:600">${escHtml(r.lot_no)}</td>
            <td>${escHtml(r.supplier_name)}</td>
JS;

$newRenderRow = <<<'JS'
        return `<tr>
            <td>${escHtml(r.receipt_date)}</td>
            <td style="font-weight:600">${escHtml(r.lot_no)}</td>
            <td>${escHtml(r.driver_name)}</td>
            <td>${escHtml(r.supplier_name)}</td>
JS;

if (strpos($content, $oldRenderRow) === false) {
    $oldRenderRow = str_replace("\n", "\r\n", $oldRenderRow);
    $newRenderRow = str_replace("\n", "\r\n", $newRenderRow);
}
if (strpos($content, $oldRenderRow) === false) {
    die("oldRenderRow not found\n");
}
$content = str_replace($oldRenderRow, $newRenderRow, $content);

// 6. Update resetFilters to clear f_driver_name
$content = str_replace(
    "['f_date_from','f_date_to','f_lot_no'].forEach",
    "['f_date_from','f_date_to','f_lot_no','f_driver_name'].forEach",
    $content
);

// 7. Update exportExcel: header, row mapping, and col widths
$oldExportWsData = <<<'JS'
    const wsData = [
        ['Date','Lot No','Supplier','Material','Received Qty','Invoice Qty','Unit','Category','Status'],
        ...exportRows.map(r=>[r.receipt_date,r.lot_no,r.supplier_name,r.material_name,r.received_qty,r.invoice_qty,r.unit,r.category,r.status>=1?'Submitted':'Draft'])
    ];
    const wb=XLSX.utils.book_new(), ws=XLSX.utils.aoa_to_sheet(wsData);
    ws['!cols']=[{wch:12},{wch:14},{wch:26},{wch:28},{wch:14},{wch:12},{wch:8},{wch:20},{wch:10}];
JS;

$newExportWsData = <<<'JS'
    const wsData = [
        ['Date','Lot No','Driver Name','Supplier','Material','Received Qty','Invoice Qty','Unit','Category','Status'],
        ...exportRows.map(r=>[r.receipt_date,r.lot_no,r.driver_name,r.supplier_name,r.material_name,r.received_qty,r.invoice_qty,r.unit,r.category,r.status>=1?'Submitted':'Draft'])
    ];
    const wb=XLSX.utils.book_new(), ws=XLSX.utils.aoa_to_sheet(wsData);
    ws['!cols']=[{wch:12},{wch:14},{wch:22},{wch:26},{wch:28},{wch:14},{wch:12},{wch:8},{wch:20},{wch:10}];
JS;

if (strpos($content, $oldExportWsData) === false) {
    $oldExportWsData = str_replace("\n", "\r\n", $oldExportWsData);
    $newExportWsData = str_replace("\n", "\r\n", $newExportWsData);
}
if (strpos($content, $oldExportWsData) === false) {
    die("oldExportWsData not found\n");
}
$content = str_replace($oldExportWsData, $newExportWsData, $content);

// 8. Update fetchAllForExport params
$oldFetchParams = "lot_no:document.getElementById('f_lot_no').value,";
$newFetchParams = "lot_no:document.getElementById('f_lot_no').value, driver_name:document.getElementById('f_driver_name')?.value??'',";
if (strpos($content, $oldFetchParams) === false) {
    die("oldFetchParams not found\n");
}
$content = str_replace($oldFetchParams, $newFetchParams, $content);

file_put_contents($path, $content);
echo "BLADE PATCH SUCCESS\n";

