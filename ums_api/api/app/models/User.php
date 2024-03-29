<?php
//use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
class User extends BaseModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $firstname;

    /**
     *
     * @var string
     */
    public $lastname;

    /**
     *
     * @var integer
     */
    public $type;

    /**
     *
     * @var string
     */
    public $avatar;

    /**
     *
     * @var integer
     */
    public $dob;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $address;

    /**
     *
     * @var string
     */
    public $phone;

    /**
     *
     * @var integer
     */
    public $datecreate;

    /**
     *
     * @var integer
     */
    public $usercreate;

    /**
     *
     * @var integer
     */
    public $gender;

    /**
     *
     * @var string
     */
    public $private_permission;

    /**
     *
     * @var string
     */
    public $flags;

    /**
     *
     * @var integer
     */
    public $classid;

    /**
     *
     * @var string
     */
    public $activekey;

    /**
     *
     * @var string
     */
    public $fbid;

    /**
     *
     * @var string
     */
    public $fbemail;

    /**
     *
     * @var string
     */
    public $status;

    /**
     *
     * @var string
     */
    public $active_register;

    /**
     *
     * @var string
     */
    public $phone2;

    /**
     *
     * @var string
     */
    public $father_name;

    /**
     *
     * @var string
     */
    public $mother_name;

    /**
     *
     * @var string
     */
    public $captions;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
       /* if(strlen($this->email)>0){
            $this->validate(new EmailValidator(array(
                'field' => 'email'
            )));
        }


        if ($this->validationHasFailed() == true) {
            return false;
        }*/

        return true;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'User', 'usercreate', array('alias' => 'User'));
        $this->belongsTo('usercreate', 'User', 'id', array('alias' => 'User'));
        $this->belongsTo('classid', 'Classobj', 'id', array('alias' => 'Classobj'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return User[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return User
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
