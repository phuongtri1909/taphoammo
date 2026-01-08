<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomEncryptable;

class GoogleSetting extends Model
{
    use CustomEncryptable;

    protected $table = 'google_settings';

    protected $fillable = [
        'google_client_id',
        'google_client_secret',
        'google_redirect',
    ];

    protected $encryptable = [
        'google_client_secret',
    ];
}
