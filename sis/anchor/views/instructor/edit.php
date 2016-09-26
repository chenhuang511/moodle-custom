<?php echo $header; ?>

<hgroup class="wrap">
	<h1><?php echo __('instructor.editing_user'); ?></h1>
</hgroup>

<section class="wrap">
	<?php echo $messages; ?>

	<?php if(Auth::admin()) : ?>
	<form method="post" action="<?php echo Uri::to('admin/instructor/edit/' . $instructor->id); ?>" novalidate autocomplete="off" enctype="multipart/form-data">

		<input name="token" type="hidden" value="<?php echo $token; ?>">
		
		<fieldset class="half split">
		
			<p>
				<label for="label-firstname"><?php echo __('instructor.first_name'); ?>:</label>
				<?php echo Form::text('firstname', Input::previous('firstname', $instructor->firstname), array('id' => 'label-firstname')); ?>
			</p>
			<p>
				<label for="label-lastname"><?php echo __('instructor.last_name'); ?>:</label>
				<?php echo Form::text('lastname', Input::previous('lastname', $instructor->lastname), array('id' => 'label-lastname')); ?>
			</p>
			<p>
				<label for="label-email"><?php echo __('instructor.email'); ?>:</label>
				<?php echo Form::text('email', Input::previous('email', $instructor->email), array('id' => 'label-email')); ?>
			</p>
			<p>
				<label for="label-birthday"><?php echo __('instructor.birthday'); ?>:</label>
				<?php echo Form::text('birthday', Input::previous('birthday', $instructor->birthday), array('id' => 'label-birthday')); ?>
			</p>
			<p>
				<label for="label-subject"><?php echo __('instructor.subject'); ?>:</label>
				<?php echo Form::text('subject', Input::previous('subject', $instructor->subject), array('id' => 'label-subject')); ?>
			</p>
		
		</fieldset>

		<fieldset class="half split">
		<?php
			$mysqlconn = new mysqli("localhost", "root", "", "anchor");
        	$sql_contract = "SELECT * FROM anchor_instructor_contract WHERE anchor_instructor_contract.instructor_id=".$instructor->id;
        	$result_contract = $mysqlconn->query($sql_contract);
			while($row = $result_contract->fetch_assoc())
			{
		?>
		<div style="border:1px solid;border-color:blue">
			<p>
				<label for="label-school"><?php echo __('instructor.school'); ?>:</label>
				<?php echo Form::text('school', $row['school_name'], array('id' => 'label-school')); ?>
			</p>
			<p>
				<label for="label-start_date"><?php echo __('instructor.start_date'); ?>:</label>
				<?php echo Form::text('start_date', $row['start_date'], array('id' => 'label-start_date')); ?>
			</p>
			<p>
				<label for="label-end_date"><?php echo __('instructor.end_date'); ?>:</label>
				<?php echo Form::text('end_date', $row['end_date'], array('id' => 'label-end_date')); ?>
			</p>
			<p>
				<label for="label-salary"><?php echo __('instructor.salary'); ?>:</label>
				<?php echo Form::text('salary', $row['salary'], array('id' => 'label-salary')); ?>
			</p>
			<p>
				<label for="label-rules"><?php echo __('instructor.rules'); ?>:</label>
				<?php echo Form::text('rules', $row['rules'], array('id' => 'label-rules')); ?>
			</p>
			<p>
				<label for="label-state"><?php echo __('instructor.state'); ?>:</label>
				<?php echo Form::text('state', $row['state'], array('id' => 'label-state')); ?>
			</p>
		</div></br>
		<?php } ?>
		</fieldset>
		<aside class="buttons">
			<?php echo Form::button(__('global.update'), array(
				'class' => 'btn',
				'type' => 'submit'
			)); ?>

			<?php echo Html::link('admin/instructor' , __('global.cancel'), array('class' => 'btn cancel blue')); ?>

			<?php echo Html::link('admin/instructor/delete/' . $instructor->id, __('global.delete'), array('class' => 'btn delete red')); ?>
		</aside>
	</form>
	<?php else : ?>
		<p>You do not have the required privileges to modify this instructor information, you must be an Administrator. Please contact the Administrator of the site if you are supposed to have these privileges.</p>
		<br><a class="btn" href="<?php echo Uri::to('admin/instructor'); ?>">Go back</a>
	<?php endif; ?>
</section>

<script src="<?php echo asset('anchor/views/assets/js/upload-fields.js'); ?>"></script>

<?php echo $footer; ?>
