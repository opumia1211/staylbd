<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    use GlobalStatus;

    protected $hidden = [
        'gateway_parameters','extra'
    ];
    
    protected $casts = [
        'code' => 'string',
        'extra' => 'object',
        'input_form'=> 'object',
        'supported_currencies'=>'object'
    ];

    public function currencies()
    {
        return $this->hasMany(GatewayCurrency::class, 'method_code', 'code');
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function singleCurrency()
    {
        return $this->hasOne(GatewayCurrency::class, 'method_code', 'code')->orderBy('id','desc');
    }

    public function scopeCrypto()
    {
        return $this->crypto == Status::ENABLE ? 'crypto' : 'fiat';
    }

    public function scopeAutomatic($query)
    {
        return $query->where('code', '<', 1000);
    }

    public function scopeManual($query)
    {
        return $query->where('code', '>=', 1000)->where('code', '<', 2000);
    }

    /** Autopay: redirect to external payment website (code 2000-2999) */
    public function scopeAutopayExternal($query)
    {
        return $query->where('code', '>=', 2000)->where('code', '<', 3000);
    }

    /** Autopay: confirm via app/SMS message (code 3000+) */
    public function scopeAutopayMessage($query)
    {
        return $query->where('code', '>=', 3000);
    }
}
