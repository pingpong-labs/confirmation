<?php

namespace Pingpong\Confirmation;

trait Confirmable
{
    public function save(array $options)
    {
        // We throw any new email address in the unconfirmed_emails table.
        $confirmation = [
            'user_id' => $this->id,
            'confirmation_token' => str_random(16),
            'email' => $this->attributes['email']
        ];

        \DB::table('unconfirmed_emails')->insert($confirmation);
        $this->attributes['email'] = null;

        parent::save($options);

    }

    /**
     * Accessor for 'confirmation_url' attribute.
     *
     * @return string
     */
    public function getConfirmationUrlAttribute()
    {
        $token = \DB::table('unconfirmed_emails')->where('user_id', $this->id)->value('confirmation_token');
        return route('confirmation.confirm', $token);
    }

    /**
     * Accessor for 'email' attribute.
     *
     * @return string
     */
    public function getEmailAttribute()
    {
        if ( $this->attributes['email'] !== null) {
            return  $this->attributes['email'];
        }

        return $unconfirmed_mail = \DB::table('unconfirmed_emails')->where('user_id', $this->id)->value('email');

    }


    /**
     * Confirm current user.
     *
     * @param $token
     * @return string
     */
    public function confirm($token)
    {
        // Now we transfer the email value to the actual table. (unconfirmed_emails => )
        $email = \DB::table('unconfirmed_emails')->where('confirmation_token', $token)->value('email');

        if ($email) {

            \DB::table('unconfirmed_emails')->where('confirmation_token', $token)->delete();

            // inject the confirmed email address in users table.
            return \DB::table($this->getTable())->where('id', $this->id)->update(['email' => $email]);
        }
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
        $user_ids = \DB::table('unconfirmed_emails')->pluck('id')->toArray();
        return $query->whereIn('id', $user_ids);
    }

    /**
     * Determine whether the current user is confirmed.
     *
     * @return bool
     */
    public function confirmed()
    {
        return !is_null $this->attributes['email']);
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
