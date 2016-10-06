<?php

Route::collection(array('before' => 'auth,csrf,install_exists'), function() {

	/*
		List users
	*/
	Route::get(array('admin/instructor', 'admin/instructor/(:num)'), function($page = 1) {
		$vars['messages'] = Notify::read();
		$vars['instructor'] = Instructor::paginate($page, Config::get('admin.posts_per_page'));

		return View::create('instructor/index', $vars)
			->partial('header', 'partials/header')
			->partial('footer', 'partials/footer');
	});

	Route::get(array('admin/instructor/search', 'admin/instructor/search/(:num)'), function($page = 1) {
        $vars['messages'] = Notify::read();
        $vars['token'] = Csrf::token();
        $key = $_GET['text-search'];

        $whatSearch = '?text-search=' . $key;
        $perpage = Config::get('admin.posts_per_page');
        list($total, $pages) = Instructor::search($key, $page, $perpage);

        $url = Uri::to('admin/contract/search');

        $pagination = new Paginator($pages, $total, $page, $perpage, $url, $whatSearch);

        $vars['instructor'] = $pagination;

        return View::create('instructor/search', $vars)
            ->partial('header', 'partials/header')
            ->partial('footer', 'partials/footer');
    });

	Route::get('admin/instructor/edit/(:num)', function($id) {
		$vars['messages'] = Notify::read();
		$vars['token'] = Csrf::token();
		$vars['instructor'] = Instructor::find($id);
		$vars['contract'] = Contract::search_by_instructor_id($id);
		
		// extended fields
		$vars['fields'] = Extend::fields('instructor', $id);

		$vars['type'] = array(
			'personal' => __('contract.personal'),
			'organization' => __('contract.organization')
		);

		$vars['state'] = array(
			'paid' => __('contract.paid'),
			'unpaid' => __('contract.unpaid'),
		);
		
		return View::create('instructor/edit', $vars)
			->partial('header', 'partials/header')
			->partial('footer', 'partials/footer');
	});

	Route::post('admin/instructor/edit/(:num)', function($id) {
		$input = Input::get(array('lastname', 'firstname', 'birthday', 'email', 'subject', 'name_contract', 'instructor_id', 'type', 'name_partner', 'start_date', 'end_date', 'salary', 'state', 'rules'));
		$validator = new Validator($input);

		$validator->add('valid', function($email) use($id) {
			return Query::table(Base::table('instructors'))->where('id', '!=', $id)->where('email', '=', $email)->count() == 0;
		});

		$validator->check('firstname')
		 	->is_max(2, __('instructor.firstname_missing', 2));

		$validator->check('lastname')
		 	->is_max(2, __('instructor.lastname_missing', 2));

		$validator->check('email')
			->is_email(__('instructor.email_missing'))
			->is_valid(__('instructor.email_was_found'));;

		$validator->check('birthday')
		 	->is_regex('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', __('instructor.birthday_missing'));

		$validator->check('subject')
		 	->is_max(2, __('instructor.subject_missing', 2));

		$validator->check('name_contract')
		 	->is_max(2, __('contract.name_contract_missing', 2));

		$validator->check('name_partner')
		 	->is_max(2, __('contract.name_partner_missing', 2));
	
		$validator->check('start_date')
		 	->is_regex('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', __('contract.start_date_missing'));

		$validator->check('end_date')	
		 	->is_regex('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', __('contract.end_date_missing'));

		$validator->check('salary')
		 	->is_max(2, __('contract.salary_missing', 2));

		$validator->check('rules')
		 	->is_max(2, __('contract.rules_missing', 2));

		if($errors = $validator->errors()) {
			Input::flash();

			Notify::error($errors);

			return Response::redirect('admin/instructor/edit/' . $id);
		}
		$input_instructor = Input::get(array('lastname', 'firstname', 'birthday', 'email', 'subject'));
		$input_contract = Input::get(array('name_contract', 'type', 'name_partner', 'start_date', 'end_date', 'salary', 'state', 'rules'));
		Instructor::update($id, $input_instructor);
		Query::table(Base::table('instructor_contract'))->where('instructor_id', '=', $id)->update($input_contract);

		Extend::process('instructor', $id);

		Notify::success(__('instructor.updated'));

		return Response::redirect('admin/instructor/edit/' . $id);
	});

	Route::get('admin/instructor/view/(:num)', function($id) {
		$vars['messages'] = Notify::read();
		$vars['token'] = Csrf::token();
		$vars['instructor'] = Instructor::find($id);
		$vars['contract'] = Contract::search_by_instructor_id($id);
		
		// extended fields
		$vars['fields'] = Extend::fields('instructor', $id);

		$vars['type'] = array(
			'personal' => __('contract.personal'),
			'organization' => __('contract.organization')
		);

		$vars['state'] = array(
			'paid' => __('contract.paid'),
			'unpaid' => __('contract.unpaid'),
		);
		
		return View::create('instructor/view', $vars)
			->partial('header', 'partials/header')
			->partial('footer', 'partials/footer');
	});

	/*
		Add user
	*/
	Route::get('admin/instructor/add', function() {
		$vars['messages'] = Notify::read();
		$vars['token'] = Csrf::token();

		// extended fields
		$vars['fields'] = Extend::fields('user');

		return View::create('instructor/add', $vars)
			->partial('header', 'partials/header')
			->partial('footer', 'partials/footer');
	});

	Route::post('admin/instructor/add', function() {
		$input = Input::get(array('firstname', 'lastname', 'email', 'birthday', 'subject'));
		$validator = new Validator($input);

		$validator->add('valid', function($email) {
			return Query::table(Base::table('instructors'))->where('email', '=', $email)->count() == 0;
		});

		$validator->check('firstname')
		 	->is_max(2, __('instructor.firstname_missing', 2));

		$validator->check('lastname')
		 	->is_max(2, __('instructor.lastname_missing', 2));

		$validator->check('email')
			->is_email(__('instructor.email_missing'))
			->is_valid(__('instructor.email_was_found'));;

		$validator->check('birthday')
		 	->is_regex('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', __('instructor.birthday_missing'));

		$validator->check('subject')
		 	->is_max(2, __('instructor.subject_missing', 2));
		if($errors = $validator->errors()) {
			Input::flash();

			Notify::error($errors);

			return Response::redirect('admin/instructor/add');
		}
		
		$inst = Instructor::create($input);

		Extend::process('Instructor', $inst->id);

		Notify::success(__('instructor.created'));

		return Response::redirect('admin/instructor');
	});

	/*
		Delete user
	*/
	Route::get('admin/instructor/delete/(:num)', function($id) {
		Instructor::where('id', '=', $id)->delete();
		Query::table(Base::table('instructor_contract'))->where('instructor_id', '=', $id)->delete();
		Notify::success(__('instructor.deleted'));
		return Response::redirect('admin/instructor');
		
	});

});
