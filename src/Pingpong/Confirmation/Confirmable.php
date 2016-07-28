<?php

namespace Pingpong\Confirmation;

trait Confirmable
{
    /**
     * Accessor for 'confirmation_url' attribute.
     * 
     * @return string
     */
    public function getConfirmationUrlAttribute()
    {
        return route('confirmation.confirm', $this->confirmation_code);
    }

    /**
     * Confirm current user.
     * 
     * @return string
     */
    public function confirm()
    {
        $this->confirmation_code = null;
        $this->confirmed = 1;
        $this->save();
    }

    /**
     * 'confirmed' query scope.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyConfirmed($query)
    {
        return $query->whereConfirmed(true)->whereNull('confirmation_code');
    }

    /**
     * 'unconfirmed' query scope.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyUnconfirmed($query)
    {
        return $query->whereConfirmed(false)->whereNotNull('confirmation_code');
    }

    /**
     * Determine whether the current user is confirmed.
     * 
     * @return bool
     */
    public function confirmed()
    {
        return is_null($this->confirmation_code) && intval($this->confirmed) == 1;
    }

    /**
     * Determine whether the current user is not confirmed.
     * 
     * @return bool
     */
    public function unconfirmed()
    {
        return !$this->confirmed();
    }
}
