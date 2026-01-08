<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomEncryptable;

class SMTPSetting extends Model
{
    use CustomEncryptable;

    protected $table = 'smtp_settings';

    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'admin_email',
    ];

    protected $encryptable = [
        'password',
    ];
}
