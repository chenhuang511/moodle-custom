<?php

/**
 * Created by PhpStorm.
 * User: VietNH
 * Date: 5/10/2016
 * Time: 4:05 PM
 */
class AuthController extends RESTController
{
    /**
     * Initializes the controller
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Login API
     * @param username
     * @param password
     */
    public function loginAction()
    {
        $username = $this->request->get("username"); // get username from request
        $password = $this->request->get("password"); // get password from request
        $userobject = User::findFirst(array(
            "columns"=>"id,firstname,lastname,username,address,email",
            "conditions"=>"username = :username: and password=:password:",
            "bind"=>array("username"=>$username,"password"=>Helper::encryptpassword($password)),
            "cache"=>array("key"=>$username.$password)
        )); // select user from cache, and DB
        if($userobject->id<=0){ // if user not available
            $this->session->destroy(); // remove session
            $this->datarespone = array("status"=>0,"mss"=>"Cannot find user","data"=>new stdClass());
        }
        else{ // if user available
            $dtr['status'] = 1;
            $dtr['mss'] = "Successfully";
            $tokenkey = $this->session->getId(); // get session id and set to tokenkey
            $userobject = $userobject->toArray(); // convert user object to array
            $userobject['tokenkey'] = $tokenkey; // set return data with tokenkey
            $this->session->set("uinfo",$userobject);
            $this->datarespone = array("status"=>1,"mss"=>"Successfully","data"=>$userobject);
        }
        $this->setPayload($this->datarespone);
        $this->render();
    }

    /**
     * Login Facebook API by FacebookID
     * @param fbid
     */
    public function loginbyfacebookidAction()
    {
        $fbid = $this->request->get("fbid"); // get username from request
        $userobject = User::findFirst(array(
            "columns"=>"id,firstname,lastname,username,address,email",
            "conditions"=>"fbid = :fbid:",
            "bind"=>array("fbid"=>$fbid)
        )); // select user from cache, and DB
        if($userobject->id<=0){ // if user not available
            $this->session->destroy(); // remove session
            $this->datarespone = array("status"=>0,"mss"=>"Cannot find user","data"=>new stdClass());
        }
        else{ // if user available
            $dtr['status'] = 1;
            $dtr['mss'] = "Successfully";
            $tokenkey = $this->session->getId(); // get session id and set to tokenkey
            $userobject = $userobject->toArray(); // convert user object to array
            $userobject['tokenkey'] = $tokenkey; // set return data with tokenkey
            $this->session->set("uinfo",$userobject);
            $this->datarespone = array("status"=>1,"mss"=>"Successfully","data"=>$userobject);
        }
        $this->setPayload($this->datarespone);
        $this->render();
    }
}