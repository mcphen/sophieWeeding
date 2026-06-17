<?php

namespace App\Exports;

use App\Models\TrainingSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SessionRegistrationsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    public function __construct(private TrainingSession $session) {}

    public function collection()
    {
        return $this->session->registrations()->orderBy('created_at')->get();
    }

    public function headings(): array
    {
        return ['#', 'Nom', 'Email', 'Téléphone', 'Message', 'Statut', 'Date d\'inscription'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->email,
            $row->phone ?? '—',
            $row->message ?? '—',
            $row->is_confirmed ? 'Confirmé' : 'En attente',
            $row->created_at->format('d/m/Y H:i'),
        ];
    }

    public function title(): string
    {
        return 'Inscriptions';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
