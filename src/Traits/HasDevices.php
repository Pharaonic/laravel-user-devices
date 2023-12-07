<?php

namespace Pharaonic\Laravel\Devices\Traits;

use Illuminate\Support\Str;
use Pharaonic\Laravel\Devices\Models\UserDevice;


trait HasDevices
{
    /**
     * Collection Of User Devices
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     *
     */
    public function devices()
    {
        return $this->morphMany(UserDevice::class, 'user');
    }

    /**
     * Collection Of User Devices With Agents
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     *
     */
    public function devicesWithAgents()
    {
        return $this->devices()->with(['agent.browser', 'agent.device', 'agent.operationSystem']);
    }

    /**
     * List of FCM token.
     *
     * @return array|null
     */
    public function getFcmListArrtibute()
    {
        $list = $this->devices()->whereNotNull('fcm_token')->get();
        if ($list->isEmpty())
            return null;

        return $list->pluck('fcm_token')->toArray();
    }

    /**
     * Check if device detected
     *
     * @param string|null $signature
     * @return boolean
     */
    public function hasDevice(string $signature = null)
    {
        if (!$signature && !($signature = getCurrentDeviceSignature()))
            return false;

        return $this->devices()->whereSignature($signature)->exists();
    }

    /**
     * Add Current Agent To Current User
     *
     * @param string|null $fcm
     * @param bool $is_primary
     * @param array|null $data
     * @param mixed|null $token_id
     * @return UserDevice
     */
    public function addDevice(string $fcm = null, bool $is_primary = false, array $data = null, mixed $token_id = null)
    {
        if (!($signature = getCurrentDeviceSignature())) {
            $signature = Str::uuid() . '-' . Str::random();

            if (session()->isStarted()) {
                session()->put('X-DEVICE-SIGNATURE', $signature);
            } else {
                request()->headers->set('X-DEVICE-SIGNATURE', $signature);
            }
        }

        return $this->devices()->updateOrCreate([
            'signature' => $signature
        ], [
            'agent_id' => agent()->id,
            'fcm_token' => $fcm,
            'token_id' => $token_id,
            'is_primary' => $is_primary,
            'data' => $data,
            'ip' => agent()->ip,
            'last_action_at' => now()
        ]);
    }

    /**
     * Remove Device by signature
     *
     * @param string $signature
     * @return bool
     */
    public function removeDevice(string $signature)
    {
        if ($this->devices()->where('signature', $signature)->delete() == 0)
            return false;

        if (getCurrentDeviceSignature() == $signature && session()->isStarted()) {
            session()->forget('X-DEVICE-SIGNATURE');
        }

        return true;
    }

    /**
     * Remove All Device.
     *
     * @return bool
     */
    public function removeAllDevices()
    {
        if (session()->isStarted())
            session()->forget('X-DEVICE-SIGNATURE');

        return $this->devices()->delete() > 0;
    }

    /**
     * Getting current device
     *
     * @return UserDevice|null
     */
    public function getCurrentDeviceAttribute()
    {
        if (!($signature = getCurrentDeviceSignature()))
            return null;

        return $this->devices()->where('signature', $signature)->first();
    }

    /**
     * Getting current device with agent
     *
     * @return UserDevice|null
     */
    public function getCurrentDeviceWithAgentAttribute()
    {
        if (!($device = $this->CurrentDevice()))
            return null;

        $device->load(['agent.operationSystem', 'agent.browser', 'agent.device']);

        return $device;
    }
}
