<?php

class Advance extends Base {

    public static $table = 'advance';

    public static function read($params = array()) {
        $query = static::left_join(Base::table('staff'), Base::table('staff.id'), '=', Base::table('advance.applicant_id')) ;

        return $query->get();
    }


}
