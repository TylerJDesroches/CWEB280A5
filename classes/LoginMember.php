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

    /**
     * Ensures that the email / alias field is not blank. Can't really validate on anything else since it can take either and they
     * each have their own totally different requirements. Since it is just a log in box, this should be okay.
     * @return boolean
     */
    public function validate_aliasEmail()
    {
        return $this->checkProperty('aliasEmail', !empty(trim($this->aliasEmail)), '%s is required');
    }

    /**
     * validates that the password is not blank and is between 8 and 16 characters
     * @return boolean
     */
    public function validate_password()
    {
        return $this->checkProperty('password', !empty(trim($this->password)), '%s is required')
            && $this->checkProperty('password', strlen($this->password) >= 8 && strlen($this->password) <= 16, '%s must be between 8 and 16 characters long');
    }
}