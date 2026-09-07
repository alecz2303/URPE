<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CenterConfigurationController;
use App\Http\Controllers\ClinicalFileController;
use App\Http\Controllers\ClinicalRecordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TherapistController;
use App\Http\Controllers\TherapyController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
    Route::get('/usuarios/crear', [UserController::class, 'create'])->name('users.create');
    Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
    Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/usuarios/{user}/estado', [UserController::class, 'toggleActive'])->name('users.toggle-active');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}/editar', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');

    Route::get('/configuracion/centro', [CenterConfigurationController::class, 'edit'])->name('center.edit');
    Route::put('/configuracion/centro', [CenterConfigurationController::class, 'update'])->name('center.update');

    Route::get('/terapeutas', [TherapistController::class, 'index'])->name('therapists.index');
    Route::get('/terapeutas/crear', [TherapistController::class, 'create'])->name('therapists.create');
    Route::post('/terapeutas', [TherapistController::class, 'store'])->name('therapists.store');
    Route::get('/terapeutas/{therapist}/editar', [TherapistController::class, 'edit'])->name('therapists.edit');
    Route::put('/terapeutas/{therapist}', [TherapistController::class, 'update'])->name('therapists.update');
    Route::post('/terapeutas/{therapist}/bloqueos', [TherapistController::class, 'storeBlock'])->name('therapists.blocks.store');

    Route::get('/terapias', [TherapyController::class, 'index'])->name('therapies.index');
    Route::get('/terapias/crear', [TherapyController::class, 'create'])->name('therapies.create');
    Route::post('/terapias', [TherapyController::class, 'store'])->name('therapies.store');
    Route::get('/terapias/{therapy}/editar', [TherapyController::class, 'edit'])->name('therapies.edit');
    Route::put('/terapias/{therapy}', [TherapyController::class, 'update'])->name('therapies.update');
    Route::patch('/terapias/{therapy}/estado', [TherapyController::class, 'toggleActive'])->name('therapies.toggle-active');

    Route::get('/pacientes', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/pacientes/crear', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/pacientes', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/pacientes/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::get('/pacientes/{patient}/editar', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/pacientes/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::patch('/pacientes/{patient}/estado', [PatientController::class, 'toggleActive'])->name('patients.toggle-active');
    Route::post('/pacientes/{patient}/responsables', [PatientController::class, 'storeGuardian'])->name('patients.guardians.store');
    Route::put('/pacientes/{patient}/responsables/{guardian}', [PatientController::class, 'updateGuardian'])->name('patients.guardians.update');
    Route::patch('/pacientes/{patient}/responsables/{guardian}/principal', [PatientController::class, 'setPrimaryGuardian'])->name('patients.guardians.primary');

    Route::get('/pacientes/{patient}/expediente-clinico', [ClinicalRecordController::class, 'show'])
        ->name('clinical-records.show');
    Route::put('/pacientes/{patient}/expediente-clinico', [ClinicalRecordController::class, 'update'])
        ->name('clinical-records.update');

    Route::get('/archivos-clinicos/{clinicalFile}/descargar', [ClinicalFileController::class, 'download'])
        ->name('clinical-files.download');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
