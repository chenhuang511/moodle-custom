<?php
Route::collection(array('before' => 'auth,csrf'), function() {

    Route::get(array('admin/grade', 'admin/grade/(:num)'), function($page = 1) {

        $userid = Auth::get_userid();
        list($total, $pages) = Course::getCoursesBy($userid, $page, $perpage = Config::get('admin.posts_per_page'));

        $url = Uri::to('admin/grade');

        $pagination = new Paginator($pages, $total, $page, $perpage, $url);

        $vars['messages'] = Notify::read();
        $vars['pages'] = $pagination;
        //need process here
        return View::create('course/index', $vars)
            ->partial('header', 'partials/header')
            ->partial('footer', 'partials/footer');
    });

    Route::get(array( 'admin/grade/course/(:num)', 'admin/grade/course/(:num)/(:num)'), function($courseid, $page = 1) {

        // get public listings
        list($total, $pages) = Course::get_grade_by_course($courseid, $page, $perpage = Config::get('admin.posts_per_page'));

        $url = Uri::to('admin/grade/course/' . $courseid);

        $pagination = new Paginator($pages, $total, $page, $perpage, $url);

        $vars['messages'] = Notify::read();
        $vars['pages'] = $pagination;
        $vars['courseid'] = $courseid;
        //need process here
        return View::create('grades/grades', $vars)
            ->partial('header', 'partials/header')
            ->partial('footer', 'partials/footer');
    });

    Route::get(array('admin/grade/course/search/(:num)', 'admin/grade/course/search/(:num)/(:num)'), function($courseid, $page = 1) {
        $input = Input::get(array(
            'gradeMin',
            'gradeMax',
            'key'
        ));
        if(!$input['gradeMin'] && !$input['gradeMax'] && !$input['key']) {
            return Response::redirect('admin/grade/course/' . $courseid);
        }
        foreach($input as $key => &$value) {
            $value = eq($value);
        }

        $whatSearch = '?gradeMin=' . $input['gradeMin'] . '&gradeMax=' . $input['gradeMax'] . '&key=' . $input['key'];
        //Session::put($whatSearch, $whatSearch);
        list($total, $pages) = Course::get_grade_by_course($courseid, $page, $perpage = Config::get('admin.posts_per_page'), $input['key'], $input['gradeMin'], $input['gradeMax']);
        // get public listings

        $url = Uri::to('admin/grade/course/search/' . $courseid);

        $pagination = new Paginator($pages, $total, $page, $perpage, $url, $whatSearch);

        $vars['messages'] = Notify::read();
        $vars['pages'] = $pagination;
        $vars['courseid'] = $courseid;
        //need process here
        return View::create('grades/grades', $vars)
            ->partial('header', 'partials/header')
            ->partial('footer', 'partials/footer');
    });

    Route::get(array('admin/grade/course/info/(:num)', 'admin/grade/course/info/(:num)/(:num)'), function($courseid, $studentid) {

        // get public listings
        $pages = Course::getDetailGrade($courseid, $studentid);

        $vars['messages'] = Notify::read();
        $vars['pages'] = $pages;
        //need process here
        return View::create('grades/grades', $vars)
            ->partial('header', 'partials/header')
            ->partial('footer', 'partials/footer');
    });
});
