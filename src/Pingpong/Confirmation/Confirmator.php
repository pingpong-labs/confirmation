<?php

namespace Pingpong\Confirmation;

use App\User;
use Illuminate\Contracts\Mail\Mailer;
use Pingpong\Confirmation\EmailAlreadyConfirmedException;

class Confirmator implements Contracts\Confirmator
{
    /**
     * The instance of Laravel Mailer class.
     * 
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mailer;
    protected $user;

    /**
     * Create new instance of confirmator class.
     * 
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->user = app(config('auth.providers.users.model'));
        $this->mailer = $mailer;
    }

    /**
     * Get user by given confirmation code.
     * 
     * @param string $token
     *
     * @return \App\User
     */
    public function getUserByCode($token)
    {
        $user_id = \DB::table('unconfirmed_emails')
            ->whereConfirmationToken($token)
            ->value('user_id');
        
        return $this->user->findOrFail($user_id);
    }

    /**
     * Confirm specific user by given confirmation code.
     * 
     * @param string $code
     *
     * @return \App\User
     */
    public function confirm($code)
    {
        $user = app(config('auth.providers.users.model'));

        return $user->confirm($code);
    }

    /**
     * Get user instance by email address.
     * 
     * @param string $email
     *
     * @return \App\User
     */
    public function getUserByEmail($email)
    {
        $user_id = \DB::table('unconfirmed_emails')
            ->whereEmail($email)
            ->value('user_id');

        return $this->user->find($user_id);

    }

    /**
     * Send confirmation email.
     * 
     * @param  string email
     *
     * @return mixed
     */
    public function send($email)
    {
        $verifiedEmail = (bool)$this->user->where('email',$email)->first(['email']);
        if ($verifiedEmail) {
            throw new EmailAlreadyConfirmedException("This email has already confirmed.");
        }

        $user = $this->getUserByEmail($email);

        return $this->mailer->send($this->getView(), compact('user'),
            function ($m) use ($email) {
            $m->from($this->getSenderEmail(), $this->getSenderName())
              ->subject($this->getSubject())
              ->to($email)
            ;
        });
    }

    /**
     * Get view name.
     * 
     * @return string
     */
    public function getView()
    {
        return 'confirmation::email';
    }

    /**
     * Get email subject.
     * 
     * @return string
     */
    protected function getSubject()
    {
        return config('confirmation.subject', 'Confirm Your Email Address');
    }

    /**
     * Get sender email.
     * 
     * @return string
     */
    protected function getSenderEmail()
    {
        return config('mail.from.address');
    }

    /**
     * Get sender name.
     * 
     * @return string
     */
    protected function getSenderName()
    {
        return config('mail.from.name');
    }

    /**
     * Get random code that used as confirmation code.
     * 
     * @return string
     */
    public function getCode()
    {
        return str_random(16);
    }
}
