<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailListEntry extends Model
{
    protected $fillable = ['email_list_id', 'email', 'name', 'phone'];

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(EmailList::class);
    }
}
