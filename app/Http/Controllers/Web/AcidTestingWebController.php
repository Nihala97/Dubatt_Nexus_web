<?php
// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Web/ReceivingWebController.php
// Only serves Blade views. All data comes from the API via JS.
// ─────────────────────────────────────────────────────────────────

// namespace App\Http\Controllers\Web;

// use App\Http\Controllers\Controller;

// class AcidTestingWebController extends Controller
// {
//     public function index()
//     {
//         return view('admin.mes.acidTesting.index');
//     }

//     public function create()
//     {
//         return view('admin.mes.acidTesting.form');
//     }

//     public function edit($id)
//     {
//         // Pass id to view just in case, but form loads data via JS apiFetch
//         return view('admin.mes.acidTesting.form', ['item_id' => $id]);
//     }
// }

// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Web/AcidTestingWebController.php
// Only serves Blade views. All data comes from the API via JS.
// ─────────────────────────────────────────────────────────────────

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AcidTesting;
use App\Models\Company;
use Illuminate\Http\Request;

class AcidTestingWebController extends Controller
{
    public function index()
    {
        return view('admin.mes.acidTesting.index');
    }

    public function create()
    {
        return view('admin.mes.acidTesting.form');
    }

    public function edit($id)
    {
        // Pass id to view — form loads all data via JS apiFetch
        return view('admin.mes.acidTesting.form', ['item_id' => $id]);
    }

    // Named printView (not print) to avoid PHP reserved-word conflicts
    public function printView($id)
    {
        $test = AcidTesting::where('is_active', 1)
            ->with([
                'supplier' => function ($query) {
                    $query->where('is_active', 1);
                },
                'details' => function ($query) {
                    $query->where('is_active', 1);
                },
                'createdBy' // usually no filter needed for users
            ])
            ->findOrFail($id);

        if ((int) $test->status < 1) {
            return redirect()
                ->to(url('/admin/mes/acidTesting/' . $id . '/edit'))
                ->with('error', 'Only submitted records can be printed.');
        }

        $company = Company::first();

        return view('admin.mes.acidTesting.print', compact('test', 'company'));
    }

    // Actual deletion is handled via DELETE /api/acid-testings/{id} from JS
    public function destroy($id)
    {
        return redirect()->route('admin.mes.acidTesting.index');
    }

    /**
     * Serve / view / download the attachment file for a given acid test record.
     * Works regardless of record status.
     * Supports specific $attachmentId or serves the latest attachment.
     * View inline by default (for PDF/images), or download with ?download=1.
     */
    public function serveAttachment(Request $request, $id, $attachmentId = null)
    {
        $test = AcidTesting::where('is_active', 1)->findOrFail($id);

        $targetAttachment = null;
        $targetId = $attachmentId ?: $request->attachment_id;

        if ($targetId) {
            $targetAttachment = \App\Models\AcidTestAttachment::where('acid_test_id', $id)
                ->find($targetId);
        }

        if (!$targetAttachment) {
            $targetAttachment = \App\Models\AcidTestAttachment::where('acid_test_id', $id)
                ->orderBy('id', 'desc')
                ->first();
        }

        if ($targetAttachment && \Illuminate\Support\Facades\Storage::exists($targetAttachment->file_path)) {
            $filePath = $targetAttachment->file_path;
            $originalName = $targetAttachment->file_name;
        } elseif ($test->attachment_path && \Illuminate\Support\Facades\Storage::exists($test->attachment_path)) {
            $filePath = $test->attachment_path;
            $originalName = $test->attachment_name ?? basename($test->attachment_path);
        } else {
            abort(404, 'Attachment not found.');
        }

        $fullPath = \Illuminate\Support\Facades\Storage::path($filePath);
        $mime = \Illuminate\Support\Facades\Storage::mimeType($filePath) ?: 'application/octet-stream';

        if ($request->has('download')) {
            return response()->download($fullPath, $originalName, ['Content-Type' => $mime]);
        }

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($originalName) . '"',
        ]);
    }
    public function downloadPackage($id)
    {
        $test = AcidTesting::where('is_active', 1)
            ->with([
                'supplier' => function ($query) {
                    $query->where('is_active', 1);
                },
                'details' => function ($query) {
                    $query->where('is_active', 1);
                },
                'createdBy',
                'updatedBy',
            ])
            ->findOrFail($id);

        if ((int) $test->status < 1) {
            return redirect()
                ->route('admin.mes.acidTesting.index')
                ->with('error', 'Only submitted records can be downloaded.');
        }

        $company = Company::first();

        $html = view('admin.mes.acidTesting.print', compact('test', 'company'))->render();
        // remove the on-screen Back / Print buttons (they are not part of the report)
        $html = preg_replace('/<div class="no-print">.*?<\/div>/s', '', $html);

        // Render with headless Chrome = same output as the browser's Print / Save PDF
           $shot = \Spatie\Browsershot\Browsershot::html($html)
    ->setNodeBinary('/usr/bin/node')
    ->setNpmBinary('/usr/bin/npm')
    ->setNodeModulePath('/opt/browsershot-runtime/node_modules')
    ->setChromePath('/opt/browsershot-runtime/.cache/chrome/linux-155.0.8059.79/chrome-linux64/chrome')
    ->setEnvironmentOptions([
        'HOME' => '/tmp',
        'XDG_CONFIG_HOME' => '/tmp',
        'XDG_CACHE_HOME' => '/tmp',
    ])
    ->format('A4')
    ->margins(8, 10, 8, 10)
    ->showBackground()
    ->waitUntilNetworkIdle()
    ->noSandbox()
    ->timeout(90);  

        if (env('CHROME_PATH')) {        // optional: use an installed Chrome instead of Puppeteer's
            $shot->setChromePath(env('CHROME_PATH'));
        }

        $reportPdf = $shot->pdf();

        // ── 2) Collect attachments (new table first, legacy columns as fallback) ──
        $files = [];
        $atts = \App\Models\AcidTestAttachment::where('acid_test_id', $id)->orderBy('id')->get();
        foreach ($atts as $att) {
            if ($att->file_path && \Illuminate\Support\Facades\Storage::exists($att->file_path)) {
                $files[] = [
                    'path' => \Illuminate\Support\Facades\Storage::path($att->file_path),
                    'name' => $att->file_name ?: basename($att->file_path),
                ];
            }
        }
        if (
            empty($files) && $test->attachment_path
            && \Illuminate\Support\Facades\Storage::exists($test->attachment_path)
        ) {
            $files[] = [
                'path' => \Illuminate\Support\Facades\Storage::path($test->attachment_path),
                'name' => $test->attachment_name ?: basename($test->attachment_path),
            ];
        }

        // ── 3) Merge ──────────────────────────────────────────────────
        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->SetAutoPageBreak(false);
        $tempFiles = [];

        $addPdfSource = function ($source) use ($pdf) {
            $count = $pdf->setSourceFile($source);
            for ($i = 1; $i <= $count; $i++) {
                $tpl = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);
            }
        };

        // Ghostscript downgrade (only used if a PDF is too new for FPDI's free parser)
        $downgradePdf = function (string $src) use (&$tempFiles) {
            $out = tempnam(sys_get_temp_dir(), 'gs_') . '.pdf';
            $tempFiles[] = $out;
            @exec(
                'gs -q -dNOPAUSE -dBATCH -dSAFER -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 '
                . '-sOutputFile=' . escapeshellarg($out) . ' ' . escapeshellarg($src) . ' 2>&1',
                $o,
                $code
            );
            return ($code === 0 && is_file($out) && filesize($out) > 0) ? $out : null;
        };

        // Placeholder page so a failed file is never silently missing
        $addNotice = function (string $name) use ($pdf) {
            $pdf->AddPage('P', 'A4');
            $pdf->SetFont('Helvetica', 'B', 13);
            $pdf->SetXY(15, 30);
            $pdf->MultiCell(180, 8, 'Attachment could not be merged:');
            $pdf->SetFont('Helvetica', '', 12);
            $pdf->SetX(15);
            $pdf->MultiCell(180, 7, $name);
            $pdf->SetX(15);
            $pdf->MultiCell(180, 7, 'Please download this file separately from the attachment list.');
        };

        // 3a) Report first
        $addPdfSource(\setasign\Fpdi\PdfParser\StreamReader::createByString($reportPdf));

        // 3b) Then each attachment
        foreach ($files as $f) {
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));

            try {
                if ($ext === 'pdf') {
                    try {
                        $addPdfSource($f['path']);
                    } catch (\Throwable $e) {
                        // newer PDF format -> try to convert with Ghostscript, then retry
                        $converted = $downgradePdf($f['path']);
                        if (!$converted) {
                            throw $e;
                        }
                        $addPdfSource($converted);
                    }
                } else {
                    // Image: jpg is used directly, everything else is converted to jpg via GD
                    $imgPath = $f['path'];
                    if (!in_array($ext, ['jpg', 'jpeg'])) {
                        $src = @imagecreatefromstring(file_get_contents($f['path']));
                        if (!$src) {
                            throw new \Exception('Unsupported or unreadable image');
                        }
                        $w = imagesx($src);
                        $h = imagesy($src);
                        $canvas = imagecreatetruecolor($w, $h);
                        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
                        imagecopy($canvas, $src, 0, 0, 0, 0, $w, $h);
                        $imgPath = tempnam(sys_get_temp_dir(), 'img_') . '.jpg';
                        $tempFiles[] = $imgPath;
                        imagejpeg($canvas, $imgPath, 90);
                        imagedestroy($src);
                        imagedestroy($canvas);
                    }

                    $info = @getimagesize($imgPath);
                    if (!$info) {
                        throw new \Exception('Unreadable image');
                    }
                    $pageW = 210;
                    $pageH = 297;
                    $margin = 10;
                    $ratio = min(($pageW - 2 * $margin) / $info[0], ($pageH - 2 * $margin) / $info[1]);
                    $w = $info[0] * $ratio;
                    $h = $info[1] * $ratio;

                    $pdf->AddPage('P', 'A4');
                    $pdf->Image($imgPath, ($pageW - $w) / 2, ($pageH - $h) / 2, $w, $h, 'JPG');
                }
            } catch (\Throwable $e) {
                \Log::warning("AcidTesting #{$test->id}: could not merge '{$f['name']}': " . $e->getMessage());
                $addNotice($f['name']);
            }
        }

        $mergedPdf = $pdf->Output('S');

        foreach ($tempFiles as $t) {
            @unlink($t);
        }

        $baseName = 'AcidTest_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $test->lot_number);

        return response($mergedPdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $baseName . '.pdf"',
        ]);
    }
}
