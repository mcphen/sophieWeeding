<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

$registration = new \App\Models\TrainingRegistration([
    'id' => 99,
    'name' => 'Aminata Diallo',
    'email' => 'aminata@example.com',
    'is_confirmed' => true,
]);
$registration->created_at = now();

$session = new \App\Models\TrainingSession([
    'start_date' => '2026-06-20 09:00:00',
    'end_date'   => '2026-06-20 17:00:00',
    'location_type' => 'presentiel',
    'adresse' => 'Ngor Almadies, Dakar',
]);

$masterclass = new \App\Models\Masterclass([
    'title'  => 'Les Fondamentaux du Wedding Planning',
    'niveau' => 'Débutant',
]);

$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.attestation', compact('registration', 'session', 'masterclass'));
$pdf->setPaper('A3', 'landscape');
$path = storage_path('app/test-attestation-v5.pdf');
$pdf->save($path);
echo "OK - " . $path . PHP_EOL;
