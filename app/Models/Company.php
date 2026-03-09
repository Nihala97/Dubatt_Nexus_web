<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name', 'legal_name', 'vat_number', 'trn_number',
        'currency', 'document_prefix', 'address', 'city', 'country',
        'plot_number', 'zone', 'contact_email', 'contact_phone',
        'website', 'logo_path', 'status', 'created_by', 'updated_by',
    ];
}