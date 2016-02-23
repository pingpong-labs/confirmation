<?php

namespace Pingpong\Confirmation;

use App\User;
use Illuminate\Contracts\Mail\Mailer;

class Confirmator implements Contracts\Confirmator
{
    /**
     * The instance of Laravel Mailer class.
     * 
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mailer;

    /**
     * Create new instance of confirmator class.
     * 
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Get user by given confirmation code.
     * 
     * @param string $code
     *
     * @return \App\User
     */
    public function getUserByCode($code)
    {
        return User::whereConfirmationCode($code)
            ->whereConfirmed(false)
            ->firstOrFail();
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
        $user = $this->getUserByCode($code);

        $user->confirm();

        return $user;
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
        return User::whereEmail($email)->firstOrFail();
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
        $user = $this->getUserByEmail($email);

        $user->confirmation_code = $this->getCode();
        $user->save();

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
        return sha1(time());
    }
}
