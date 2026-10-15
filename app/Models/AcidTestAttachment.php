<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcidTestAttachment extends Model
{
    protected $table = 'acid_test_attachments';

    protected $fillable = [
        'acid_test_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'created_by',
    ];

    public function acidTest()
    {
        return $this->belongsTo(AcidTesting::class, 'acid_test_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
