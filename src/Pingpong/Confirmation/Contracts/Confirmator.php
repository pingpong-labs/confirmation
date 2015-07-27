<?php

namespace Pingpong\Confirmation\Contracts;

interface Confirmator {

    /**
     * Get user by given confirmation code.
     * 
     * @param  string $code
     * @return \App\User
     */
    public function getUserByCode($code);

    /**
     * Confirm specific user by given confirmation code.
     * 
     * @param  string $code
     * @return \App\User
     */
    public function confirm($code);

    /**
     * Get user instance by email address.
     * 
     * @param  string $email
     * @return \App\User
     */
    public function getUserByEmail($email);

    /**
     * Send confirmation email.
     * 
     * @param  string email
     * @return mixed
     */
    public function send($email);

    /**
     * Get random code that used as confirmation code.
     * 
     * @return string
     */
    public function getCode();

}