<?php echo $header; ?>

<ol class="breadcrumb">
	<li><a href="<?php echo Uri::to('admin'); ?>">Trang chủ</a></li>
	<li><a href="<?php echo Uri::to('admin/instructor'); ?>">Quản lý giảng viên</a></li>
	<li class="active">Thêm mới hợp đồng</li>
</ol>

<hgroup class="wrap">
	<h1 style="margin: 0"><?php echo 'Thêm mới hợp đồng' ?></h1>
</hgroup>

<section class="wrap">
	<?php echo $messages; ?>
	<?php if(Auth::admin()) : ?>


	<form method="post" action="<?php echo Uri::to('admin/instructor/add'); ?>" novalidate autocomplete="on" enctype="multipart/form-data">

		<input name="token" type="hidden" value="<?php echo $token; ?>">

		<fieldset class="half split">
			<p>
				<label for="label-instructor_id"><?php echo __('contract.instructor_selected'); ?>:</label>
				<?php echo Form::select('instructor_id', $instructor_id, Input::previous('instructor_id'), array('id' => 'label-instructor_id')); ?>
			</p>
			<p>
				<label for="label-fullname"><?php echo __('instructor.fullname'); ?>:</label>
				<?php echo Form::text('fullname', Input::previous('fullname'), array('id' => 'label-fullname')); ?>
			</p>
			<p>
				<label for="label-birthday"><?php echo __('instructor.birthday'); ?>:</label>
				<?php echo Form::date('birthday', Input::previous('birthday'), array('id' => 'label-birthday')); ?>
			</p>
			<p>
				<label for="label-email"><?php echo __('instructor.email'); ?>:</label>
				<?php echo Form::text('email', Input::previous('email'), array('id' => 'label-email')); ?>
			</p>
			<p style="height: 39px"></p>
			<aside class="buttons" style="padding-left: 5px">
				<?php echo Form::button(__('Tạo mới'), array('class' => 'btn btn-primary', 'type' => 'submit')); ?>

				<?php echo Html::link('admin/instructor' , __('global.cancel'), array('class' => 'btn btn-primary')); ?>
			</aside>
		</fieldset>

		<fieldset class="half split">
			<p>
				<label for="label-name_contract"><?php echo __('contract.name_contract'); ?>:</label>
				<?php echo Form::text('name_contract', Input::previous('name_contract'), array('id' => 'label-name_contract')); ?>
			</p>
			<p>
				<label for="label-type"><?php echo __('contract.type'); ?>:</label>
				<?php echo Form::select('type', $type, Input::previous('type'), array('id' => 'label-type')); ?>
			</p>
			<div id="organization_register" style="display:none">
				<p>
					<label for="label-name_partner"><?php echo __('contract.name_partner'); ?>:</label>
					<?php echo Form::text('name_partner', Input::previous('name_partner'), array('id' => 'label-name_partner')); ?>
				</p>
				<p>
					<label for="label-name_head"><?php echo __('contract.name_head'); ?>:</label>
					<?php echo Form::text('name_head', Input::previous('name_head'), array('id' => 'label-name_head')); ?>
				</p>
				<p>
					<label for="label-tax_code"><?php echo __('contract.tax_code'); ?>:</label>
					<?php echo Form::text('tax_code', Input::previous('tax_code'), array('id' => 'label-tax_code')); ?>
				</p>
				<p>
					<label for="label-number_phone"><?php echo __('contract.number_phone'); ?>:</label>
					<?php echo Form::text('number_phone', Input::previous('number_phone'), array('id' => 'label-number_phone')); ?>
				</p>
				<p>
					<label for="label-address"><?php echo __('contract.address'); ?>:</label>
					<?php echo Form::text('address', Input::previous('address'), array('id' => 'label-address')); ?>
				</p>
			</div>
			<p>
				<label for="label-start_date"><?php echo __('contract.start_date'); ?>:</label>
				<?php echo Form::date('start_date', Input::previous('start_date'), array('id' => 'label-start_date')); ?>
			</p>
			<p>
				<label for="label-end_date"><?php echo __('contract.end_date'); ?>:</label>
				<?php echo Form::date('end_date', Input::previous('end_date'), array('id' => 'label-end_date')); ?>
			</p>
			<p>
				<label for="label-salary"><?php echo __('contract.salary'); ?>:</label>
				<?php echo Form::text('salary', Input::previous('salary'), array('id' => 'label-salary')); ?>
			</p>
			<p>
				<label for="label-rules"><?php echo __('contract.rules'); ?>:</label>
				<?php echo Form::textarea('rules', Input::previous('rules'), array('cols' => 20 ,'id' => 'label-rules')); ?>
			</p>
		</fieldset>

	</form>
	<?php else : ?>
		<p>You do not have the required privileges to add instructor, you must be an Administrator. Please contact the Administrator of the site if you are supposed to have these privileges.</p>
		<br><a class="btn" href="<?php echo Uri::to('admin/contract'); ?>">Go back</a>
	<?php endif; ?>
	<input id="menuSelected" type="hidden" value="<?php if (isset($tab)): echo $tab; endif; ?>">
</section>
<script>
	$(document).ready( function () {
		$('#label-instructor_id').on('change', function(){
			var $this = $(this);
			var $value = $this.val();
			if($value != 0){
				document.getElementById("label-fullname").value = "";
				document.getElementById("label-birthday").value = "";
				document.getElementById("label-email").value = "";
				document.getElementById("label-fullname").disabled = true;
				document.getElementById("label-birthday").disabled = true;
				document.getElementById("label-email").disabled = true;
				
			}
			else{
				document.getElementById("label-fullname").value = "";
				document.getElementById("label-birthday").value = "";
				document.getElementById("label-email").value = "";
				document.getElementById("label-fullname").disabled = false;
				document.getElementById("label-birthday").disabled = false;
				document.getElementById("label-email").disabled = false;
			}
		});
		$('#label-type').on('change', function(){
			var $this = $(this);
			var $value = $this.val();
			if($value == "organization")
				document.getElementById("organization_register").style.display = "inline";
			else 
				document.getElementById("organization_register").style.display = "none";
		});
	});
</script>
<script src="<?php echo asset('anchor/views/assets/js/upload-fields.js'); ?>"></script>

<?php echo $footer; ?>
