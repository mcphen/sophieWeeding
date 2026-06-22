<?php

namespace App\Imports;

use App\Models\EmailList;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class EmailListImport implements ToCollection, WithHeadingRow
{
    public int $added = 0;
    public int $skipped = 0;

    public function __construct(private EmailList $emailList) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $email = strtolower(trim($row['email'] ?? ''));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->skipped++;
                continue;
            }

            $name  = trim($row['nom'] ?? '') ?: null;
            $phone = trim($row['telephone'] ?? '') ?: null;

            $entry = $this->emailList->entries()->firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'phone' => $phone]
            );

            if ($entry->wasRecentlyCreated) {
                $this->added++;
            } else {
                $this->skipped++;
            }
        }
    }
}
