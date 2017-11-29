<?php
use DB3\Model;

/**
 * Is basically a dummy "Member" used for logging in, since having the alias OR email address to log in on would create validation issues
 *
 * @version 1.0
 * @author cst243
 */
class LoginMember extends Model
{
    public $aliasEmail; // holds either the alias, or the email depending on what the user tried to sign in with.
    public $password;

    public function __construct()
    {
        // Note, this will NEVER be stored in the DB, so don't define columns
        // Do define labels though
        $this->setLabel('aliasEmail', 'Alias / Email');
        $this->setLabel('password', 'Password');
    }

    // NOTE ADD VALIDATION HERE
}