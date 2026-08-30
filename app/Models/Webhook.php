<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $fillable = [
        'name',
        'url',
        'secret',
        'status',
    ];
    
public function logs()
{
    return $this->hasMany(WebhookLog::class);
}
}