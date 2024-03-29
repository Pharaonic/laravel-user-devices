<?php

namespace Pharaonic\Laravel\Devices\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken;
use Pharaonic\Laravel\Agents\Models\Agent;

/**
 * @property integer $id
 * @property integer $agent_id
 * @property string $signature
 * @property string $ip
 * @property string $data
 * @property boolean $is_primary
 * @property boolean $logged_out
 * @property string|null $fcm_token
 * @property integer|null $token_id
 * @property Carbon $last_action_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Agent $agent
 * @property \Illuminate\Database\Eloquent\Relations\MorphTo $user
 *
 * @author Omar Pero (omar) <omarpero85@gmail.com>
 * @author Moamen Eltouny (Raggi) <support@raggitech.com>
 */
class UserDevice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agent_id',
        'signature',
        'fcm_token',
        'token_id',
        'ip',
        'user_id',
        'user_type',
        'data',
        'is_primary',
        'logged_out',
        'last_action_at'
    ];

    /**
     * {@inheritDoc}
     */
    protected $casts = [
        'last_action_at' => 'datetime',
        'data' => 'array',
        'is_primary' => 'boolean',
        'logged_out' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Getting User Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function user()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function token()
    {
        return $this->belongsTo(PersonalAccessToken::class, 'token_id');
    }

    /**
     * Refresh LAST-ACTION-AT
     *
     * @return boolean
     */
    public function refresh()
    {
        $data = ['last_action_at' => now()];

        if ($this->ip == request()->ip()) {
            $data['ip'] = request()->ip();
        }

        return $this->update($data);
    }

    /**
     * Logout The Device
     *
     * @return boolean
     */
    public function logout()
    {
        if ($this->token_id) {
            $this->token()->delete();
        }
        
        return $this->update(['logged_out' => true]);
    }

    /**
     * Mark As A Primary Device
     *
     * @return boolean
     * 
     */
    public function markAsPrimary()
    {
        return $this->update(['is_primary' => true]);
    }
}
