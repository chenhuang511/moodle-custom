<?php

Route::collection(array('before' => 'auth,csrf,install_exists'), function() {

	/*
		List users
	*/
	Route::get(array('admin/instructor', 'admin/instructor/(:num)'), function($page = 1) {
		$vars['messages'] = Notify::read();
		$vars['instructors'] = Instructor::paginate($page, Config::get('admin.posts_per_page'));
		$vars['official_instructors'] = Instructor::get_official_instructor($page, Config::get('admin.posts_per_page'));
		$vars['tab'] = 'sys';

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
		list($total_official_inst, $pages_official_inst) = Instructor::search_official_instructor($key, $page, $perpage);
        $url = Uri::to('admin/contract/search');

        $pagination = new Paginator($pages, $total, $page, $perpage, $url, $whatSearch);
		$pagination_official_inst = new Paginator($pages_official_inst, $total_official_inst, $page, $perpage, $url, $whatSearch);

        $vars['instructors'] = $pagination;
		$vars['official_instructors'] = $pagination_official_inst;
        $vars['tab'] = 'sys';

        return View::create('instructor/search', $vars)
            ->partial('header', 'partials/header')
            ->partial('footer', 'partials/footer');
    });


	Route::get('admin/instructor/edit/(:num)', function($id) {
		$vars['messages'] = Notify::read();
		$vars['token'] = Csrf::token();
		$vars['instructor'] = Instructor::find($id);
		$vars['contracts'] = Contract::search_by_instructor_id($id);
		
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

        $vars['tab'] = 'sys';
		
		return View::create('instructor/edit', $vars)
			->partial('header', 'partials/header')
			->partial('footer', 'partials/footer');
	});


	Route::post('admin/instructor/edit/(:num)', function($id) {
		$arr_input = array('fullname', 'birthday', 'email');
		$contracts = Contract::search_by_instructor_id($id);
		$count_contract = Query::table(Base::table('instructor_contract'))->where('instructor_id', '=', $id)->count();
		if($count_contract > 0){
			foreach($contracts as $c){
				array_push($arr_input, 'name_contract' . $c->id);
				array_push($arr_input, 'type' . $c->id);
				array_push($arr_input, 'name_partner' . $c->id);
				array_push($arr_input, 'name_head' . $c->id);
				array_push($arr_input, 'tax_code' . $c->id);
				array_push($arr_input, 'start_date' . $c->id);
				array_push($arr_input, 'end_date' . $c->id);
				array_push($arr_input, 'salary' . $c->id);
				array_push($arr_input, 'state' . $c->id);
				array_push($arr_input, 'rules' . $c->id);
			}
		}
		
		$input = Input::get($arr_input);
		$validator = new Validator($input);

		$validator->add('valid', function($email) use($id) {
			return Query::table(Base::table('instructors'))->where('id', '!=', $id)->where('email', '=', $email)->count() == 0;
		});
		
		$validator->check('fullname')
		 	->is_max(2, __('instructor.fullname_missing', 2));

		$validator->check('email')
			->is_email(__('instructor.email_missing'))
			->is_valid(__('instructor.email_was_found'));;

		$validator->check('birthday')
		 	->is_regex('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', __('instructor.birthday_missing'));

		if($count_contract > 0){
			foreach($contracts as $c){
			$validator->check('name_contract' . $c->id)
		 		->is_max(2, __('contract.name_contract_missing', 2));
				
			if($input['type' . $c->id] == 'organization'){
				$validator->check('name_partner'. $c->id)
		 			->is_max(2, __('contract.name_partner_missing', 2));

				$validator->check('name_head'. $c->id)
		 			->is_max(2, __('contract.name_head_missing', 2));
			 
				$validator->check('tax_code'. $c->id)
		 			->is_max(2, __('contract.tax_code_missing', 2));
			}
			$validator->check('start_date'. $c->id)
		 		->is_regex('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', __('contract.start_date_missing'));

			$validator->check('end_date'. $c->id)	
		 		->is_regex('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', __('contract.end_date_missing'));

			$validator->check('salary'. $c->id)
		 		->is_max(2, __('contract.salary_missing', 2));

			$validator->check('rules'. $c->id)
		 		->is_max(2, __('contract.rules_missing', 2));
			}
		}

		if($errors = $validator->errors()) {
			Input::flash();

			Notify::error($errors);

			return Response::redirect('admin/instructor/edit/' . $id);
		}
		$input_instructor = Input::get(array('fullname', 'birthday', 'email'));
		Instructor::update($id, $input_instructor);

		if($count_contract > 0){
			foreach($contracts as $c){
			if($input['type' . $c->id] == "organization"){
				$input_contract = array(
				'name_contract'=> $input['name_contract' . $c->id],
				'type'=> $input['type' . $c->id],
				'name_partner'=> $input['name_partner' . $c->id],
				'name_head'=> $input['name_head' . $c->id],
				'tax_code'=> $input['tax_code' . $c->id],
				'start_date'=> $input['start_date' . $c->id],
				'end_date'=> $input['end_date' . $c->id],
				'salary'=> $input['salary' . $c->id],
				'state'=> $input['state' . $c->id],
				'rules'=> $input['rules' . $c->id]
				);
				Contract::update($c->id, $input_contract);
			}

			else{
				$input_contract = array(
				'name_contract'=> $input['name_contract' . $c->id],
				'type'=> $input['type' . $c->id],
				'name_partner'=> null,
				'name_head'=> null,
				'tax_code'=> null,
				'start_date'=> $input['start_date' . $c->id],
				'end_date'=> $input['end_date' . $c->id],
				'salary'=> $input['salary' . $c->id],
				'state'=> $input['state' . $c->id],
				'rules'=> $input['rules' . $c->id]
				);
				Contract::update($c->id, $input_contract);
			}
			}
		}
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

		$vars['type_instructor'] = array(
			'contract' => __('instructor.contract'),
			'official' => __('instructor.official')
		);

		$vars['type'] = array(
			'personal' => __('contract.personal'),
			'organization' => __('contract.organization')
		);

		$vars['state'] = array(
			'paid' => __('contract.paid'),
			'unpaid' => __('contract.unpaid'),
		);

        $vars['tab'] = 'sys';
		
		return View::create('instructor/view', $vars)
			->partial('header', 'partials/header')
			->partial('footer', 'partials/footer');
	});


	Route::get(array('admin/instructor/curriculum/(:num)', 'admin/instructor/curriculum/(:num)/(:num)'), function($id, $page = 1) {
		$vars['messages'] = Notify::read();
		$vars['token'] = Csrf::token();
		// extended fields
		$vars['fields'] = Extend::fields('curriculum', $id);
		list($total, $pages) = Curriculum::getByTeacherId($id, $page, $perpage= Config::get('admin.posts_per_page'));

        $url = Uri::to('admin/instructor/curriculum/' .$id);

        $pagination = new Paginator($pages, $total, $page, $perpage, $url);

        $vars['curriculums'] = $pagination;
        $vars['tab'] = 'sys';

		return View::create('instructor/curriculum', $vars)
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
		$vars['fields'] = Extend::fields('contract');

		$vars['type'] = array(
			'personal' => __('contract.personal'),
			'organization' => __('contract.organization')
		);

        $vars['tab'] = 'sys';

		$instructor = Instructor::get_name_instructor();
		$inst = array('0' => 'Tạo Mới');

		foreach($instructor as $in)
		{
			$inst[$in->id] = $in->fullname;
		}	

		$vars['instructor_id'] = $inst;
		return View::create('instructor/add', $vars)
			->partial('header', 'partials/header')
			->partial('footer', 'partials/footer');
	});


	Route::post('admin/instructor/add', function() {
		$input = Input::get(array('fullname', 'birthday', 'email', 'type_instructor', 'name_contract', 'instructor_id', 'type', 'name_partner', 'start_date', 'name_head', 'tax_code', 'number_phone', 'address', 'end_date', 'salary', 'state', 'rules'));
		$ins_id = $input['instructor_id'];
		
		$validator = new Validator($input);
		
		$validator->check('name_contract')
		 	->is_max(2, __('contract.name_contract_missing', 2));

		$validator->check('start_date')
		 	->is_regex('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', __('contract.start_date_missing'));

		$validator->check('end_date')	
		 	->is_regex('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', __('contract.end_date_missing'));

		$validator->check('salary')
		 	->is_max(2, __('contract.salary_missing', 2));

		$validator->check('rules')
		 	->is_max(2, __('contract.rules_missing', 2));

		if($input['type'] == "organization"){

			$validator->check('name_partner')
		 		->is_max(2, __('contract.name_partner_missing', 2));
			
			$validator->check('name_head')
		 		->is_max(2, __('contract.name_head_missing', 2));
			 
			$validator->check('tax_code')
		 		->is_max(2, __('contract.tax_code_missing', 2));

			$validator->check('number_phone')
		 		->is_max(2, __('contract.number_phone_missing', 2));
			 
			$validator->check('address')
		 		->is_max(2, __('contract.address_missing', 2));
		}
	
		if($ins_id == 0){
			$input_instructor =  array(
				'fullname'=>$input['fullname'],
				'birthday'=>$input['birthday'],
				'email'=>$input['email'],
				'type_instructor'=> 'contract',
			);

			$validator->add('valid', function($email) {
				return Query::table(Base::table('instructors'))->where('email', '=', $email)->count() == 0;
			});

			$validator->check('fullname')
		 		->is_max(2, __('contract.fullname_missing'));

			$validator->check('birthday')	
		 		->is_regex('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', __('contract.birthday_missing'));

			$validator->check('email')
				->is_email(__('contract.email_missing'))
				->is_valid(__('contract.email_was_found'));

			if($errors = $validator->errors()) {
				Input::flash();
				Notify::error($errors);
				return Response::redirect('admin/instructor/add');
			}
			$instructor = Instructor::create($input_instructor);
			Extend::process('Instructor', $instructor->id);
			$input_contract = array(
				'name_contract'=> $input['name_contract'],
				'instructor_id'=> $instructor->id,
				'type'=> $input['type'],
				'name_partner'=> $input['name_partner'],
				'name_head'=> $input['name_head'],
				'tax_code'=> $input['tax_code'],
				'number_phone'=> $input['number_phone'],
				'address'=> $input['address'],
				'start_date'=> $input['start_date'],
				'end_date'=> $input['end_date'],
				'salary'=> $input['salary'],
				'state'=> 'unpaid',
				'rules'=> $input['rules']
			);
			$contract = Contract::create($input_contract);
			Extend::process('Contract', $contract->id);

		}

		else{
			if($errors = $validator->errors()) {
				Input::flash();
				Notify::error($errors);
				return Response::redirect('admin/instructor/add');
			}
			$input_contract = array(
				'name_contract'=> $input['name_contract'],
				'instructor_id'=> $input['instructor_id'],
				'type'=> $input['type'],
				'name_partner'=> $input['name_partner'],
				'name_head'=> $input['name_head'],
				'tax_code'=> $input['tax_code'],
				'number_phone'=> $input['number_phone'],
				'address'=> $input['address'],
				'start_date'=> $input['start_date'],
				'end_date'=> $input['end_date'],
				'salary'=> $input['salary'],
				'state'=> 'unpaid',
				'rules'=> $input['rules']
			);
			$contract = Contract::create($input_contract);
			Extend::process('Contract', $contract->id);
			
		}
		
		Notify::success(__('contract.created'));

		return Response::redirect('admin/instructor');
	});


	/*
		Delete user
	*/
});
