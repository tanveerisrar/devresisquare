<?php

namespace App\Models;

use App\Models\DocumentType;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'documentable_id',
        'documentable_type',
        'upload_id',
        'document_type_id',
    ];

    /**
     * The parent model (user, product, complianceRecord, etc.)
     */
    public function documentable()
    {
        return $this->morphTo();
    }

    /**
     * The actual Upload record holding file info.
     */
    public function upload()
    {
        return $this->belongsTo(Upload::class);
    }

    /**
     * Optional type/category of this document.
     */
    public function type()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
}
