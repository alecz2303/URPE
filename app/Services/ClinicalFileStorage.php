<?php

namespace App\Services;

use App\Models\ClinicalFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ClinicalFileStorage
{
    public function __construct(private readonly AuditTrail $auditTrail)
    {
    }

    public function store(
        UploadedFile $file,
        User $actor,
        ?Model $subject = null,
        array $metadata = [],
    ): ClinicalFile {
        if (! $file->isValid()) {
            throw new RuntimeException('El archivo clínico no es válido.');
        }

        $uuid = (string) Str::uuid();
        $extension = strtolower($file->getClientOriginalExtension() ?: '');
        $storedName = $uuid.($extension !== '' ? '.'.$extension : '');
        $directory = 'documents/'.now()->format('Y/m');
        $path = $directory.'/'.$storedName;
        $disk = 'clinical';
        $sha256 = hash_file('sha256', $file->getRealPath());

        $storedPath = Storage::disk($disk)->putFileAs($directory, $file, $storedName);

        if ($storedPath === false) {
            throw new RuntimeException('No fue posible almacenar el archivo clínico.');
        }

        try {
            return DB::transaction(function () use ($actor, $disk, $extension, $file, $metadata, $path, $sha256, $subject, $uuid): ClinicalFile {
                $clinicalFile = ClinicalFile::query()->create([
                    'uuid' => $uuid,
                    'disk' => $disk,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $extension !== '' ? $extension : null,
                    'size_bytes' => $file->getSize(),
                    'sha256' => $sha256,
                    'uploaded_by' => $actor->getKey(),
                    'subject_type' => $subject?->getMorphClass(),
                    'subject_id' => $subject ? (string) $subject->getKey() : null,
                    'metadata' => $metadata,
                ]);

                $this->auditTrail->record('clinical_file.stored', $clinicalFile, [
                    'original_name' => $clinicalFile->original_name,
                    'mime_type' => $clinicalFile->mime_type,
                    'size_bytes' => $clinicalFile->size_bytes,
                    'sha256' => $clinicalFile->sha256,
                    'subject_type' => $clinicalFile->subject_type,
                    'subject_id' => $clinicalFile->subject_id,
                ], $actor);

                return $clinicalFile;
            });
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($path);
            throw $exception;
        }
    }

    public function retire(ClinicalFile $clinicalFile, User $actor, ?string $reason = null): void
    {
        DB::transaction(function () use ($actor, $clinicalFile, $reason): void {
            $clinicalFile->delete();

            $this->auditTrail->record('clinical_file.retired', $clinicalFile, [
                'reason' => $reason,
                'physical_file_retained' => true,
            ], $actor);
        });
    }
}
