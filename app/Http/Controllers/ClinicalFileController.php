<?php

namespace App\Http\Controllers;

use App\Models\ClinicalFile;
use App\Services\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClinicalFileController extends Controller
{
    public function download(Request $request, ClinicalFile $clinicalFile, AuditTrail $auditTrail): StreamedResponse
    {
        $this->authorize('clinical_files.download');

        abort_unless(Storage::disk($clinicalFile->disk)->exists($clinicalFile->path), 404);

        $auditTrail->record('clinical_file.downloaded', $clinicalFile, [
            'original_name' => $clinicalFile->original_name,
            'sha256' => $clinicalFile->sha256,
        ], $request->user(), $request);

        return Storage::disk($clinicalFile->disk)->download(
            $clinicalFile->path,
            $clinicalFile->original_name,
            array_filter(['Content-Type' => $clinicalFile->mime_type])
        );
    }
}
