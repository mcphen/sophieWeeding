<?php

namespace App\Exports;

use App\Models\Newsletter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NewsletterExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Newsletter::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Email',
            'Statut',
            'Source',
            'Date d\'inscription',
            'Date de désinscription',
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->email,
            $row->is_active ? 'Actif' : 'Inactif',
            $row->source,
            $row->subscribed_at ? $row->subscribed_at->format('d/m/Y H:i') : '',
            $row->unsubscribed_at ? $row->unsubscribed_at->format('d/m/Y H:i') : '',
        ];
    }
}
