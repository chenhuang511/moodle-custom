<?php

class User extends Base
{

    public static $table = 'users';

    public static function search($params = array())
    {
        $query = static::where('status', '=', 'active');

        foreach ($params as $key => $value) {
            $query->where($key, '=', $value);
        }

        return $query->fetch();
    }

    public static function searchuser($key, $page = 1, $per_page = 10)
    {

        $query = static::where('real_name', 'LIKE', '%' . $key . '%');

        $total = $query->count();

        // get posts
        $posts = $query->take($per_page)
            ->skip(--$page * $per_page)
            ->get();

        return array($total, $posts);
    }

    public static function paginate($page = 1, $perpage = 10, $url = 'admin/users')
    {
        $query = Query::table(static::table());

        $count = $query->count();

        $results = $query->take($perpage)->skip(($page - 1) * $perpage)->sort('id', 'asc')->get();

        return new Paginator($results, $count, $page, $perpage, Uri::to($url));
    }

    public static function get_id($id = 1)
    {
        return static::get('id', $id)[0]->username;
    }

    public static function get_list_author($auth = 1, $params = array())
    {
        $query = static::where('role_id', '=', $auth);
        foreach ($query->sort('real_name')->get() as $item) {
            $items[$item->id] = $item->real_name;
        }
        return $items;
    }

    public static function get_list_advance_by_name($name, $perpage, $page = 1, $params = array())
    {
        $query = static::join(Base::table('advance'), Base::table('users.id'), '=', Base::table('advance.applicant_id'))
            ->join(Base::table('courses'), Base::table('courses.id'), '=', Base::table('advance.course_id'))
            ->like(Base::table('users.real_name'), 'LIKE', $name);
        $total = $query->count();
        $advance = $query->sort('anchor_advance.id', 'DESC')->take($perpage)->skip(($page - 1) * $perpage)
            ->get(array(Base::table('advance.*'),
                Base::table('courses.shortname as course_name'),
                Base::table('users.real_name as user')));
        return array($total, $advance);
    }

    public static function dropdown($roleid = null)
    {
        $items = array();
        $query = Query::table(static::table());
        if ($roleid !== null) {
            $query->where('roleid', '=', $roleid);
        }

        foreach ($query->sort('id')->get() as $item) {
            $items[$item->id] = $item->real_name;
        }

        return $items;
    }

    public static function getRealName($id)
    {
        $user = self::getUserById($id);
        if ($user)
            return $user->real_name;
        return '';
    }

    public static function getUserById($id = 1)
    {
        return static::find($id);
    }

    public static function getUsersByCourse($courseid) {
        $query = static::join(Base::table('user_course'), Base::table('user_course.userid'), '=', Base::table('users.id'))
            ->where(Base::table('user_course.courseid'), '=', $courseid)
            ->get(array( Base::table('users.real_name as fullname'),
                Base::table('users.email'),
                Base::table('user_course.userid')
            ));

        return $query;

    }
}
