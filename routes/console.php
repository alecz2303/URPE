<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('urpe:status', function (): void {
    $this->info('URPE Gestión Clínica — foundation ready.');
})->purpose('Show the URPE foundation status');
