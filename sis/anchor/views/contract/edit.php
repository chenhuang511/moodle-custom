<?php echo $header; ?>

<hgroup class="wrap">
	<h1><?php echo __('contract.editing_contract'); ?></h1>
</hgroup>

<section class="wrap">
	<?php echo $messages; ?>

	<?php if(Auth::admin()) : ?>
	<form method="post" action="<?php echo Uri::to('admin/contract/edit/' . $contract->id); ?>" novalidate autocomplete="off" enctype="multipart/form-data">

		<input name="token" type="hidden" value="<?php echo $token; ?>">
		
		<fieldset class="half split">
			<p>
				<label for="label-name_contract"><?php echo __('contract.name_contract'); ?>:</label>
				<?php echo Form::text('name_contract', Input::previous('name_contract', $contract->name_partner), array('id' => 'label-name_contract')); ?>
			</p>
			<p>
				<label for="label-instructor_id"><?php echo __('contract.instructor'); ?>:</label>
				<?php echo Form::select('instructor_id', $instructor_id, Input::previous('instructor_id', $contract->instructor_id), array('id' => 'label-instructor_id')); ?>
			</p>
			<p>
				<label for="label-type"><?php echo __('contract.type'); ?>:</label>
				<?php echo Form::select('type', $type, Input::previous('type', $contract->type), array('id' => 'label-type')); ?>
			</p>
			<p>
				<label for="label-name_partner"><?php echo __('contract.name_partner'); ?>:</label>
				<?php echo Form::text('name_partner', Input::previous('name_partner', $contract->name_partner), array('id' => 'label-name_partner')); ?>
			</p>
		
		</fieldset>

		<fieldset class="half split">
			<p>
				<label for="label-start_date"><?php echo __('contract.start_date'); ?>:</label>
				<?php echo Form::date('start_date', Input::previous('start_date', $contract->start_date), array('id' => 'label-start_date')); ?>
			</p>
			<p>
				<label for="label-end_date"><?php echo __('contract.end_date'); ?>:</label>
				<?php echo Form::date('end_date', Input::previous('end_date', $contract->end_date), array('id' => 'label-end_date')); ?>
			</p>
			<p>
				<label for="label-salary"><?php echo __('contract.salary'); ?>:</label>
				<?php echo Form::text('salary', Input::previous('salary', $contract->salary), array('id' => 'label-salary')); ?>
			</p>
			<p>
				<label for="label-state"><?php echo __('contract.state'); ?>:</label>
				<?php echo Form::select('state', $state, Input::previous('state', $contract->state), array('id' => 'label-state')); ?>
			</p>
			<p>
				<label for="label-rules"><?php echo __('contract.rules'); ?>:</label>
				<?php echo Form::textarea('rules', Input::previous('rules', $contract->rules), array('cols' => 20 ,'id' => 'label-rules')); ?>
			</p>
		</fieldset>
		<aside class="buttons">
			<?php echo Form::button(__('global.update'), array(
				'class' => 'btn',
				'type' => 'submit'
			)); ?>

			<?php echo Html::link('admin/contract' , __('global.cancel'), array('class' => 'btn cancel blue')); ?>

		</aside>
	</form>
	<?php else : ?>
		<p>You do not have the required privileges to modify this instructor information, you must be an Administrator. Please contact the Administrator of the site if you are supposed to have these privileges.</p>
		<br><a class="btn" href="<?php echo Uri::to('admin/contract'); ?>">Go back</a>
	<?php endif; ?>
	<input id="menuSelected" type="hidden" value="<?php if (isset($tab)): echo $tab; endif; ?>">
</section>

<script src="<?php echo asset('anchor/views/assets/js/upload-fields.js'); ?>"></script>

<?php echo $footer; ?>
