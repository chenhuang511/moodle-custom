<?php echo $header; ?>
<ol class="breadcrumb">
    <li><a href="<?php echo Uri::to('admin'); ?>">Trang chủ</a></li>
    <li><a href="<?php echo Uri::to('admin/courses'); ?>">Quản lý khóa học</a></li>
    <li class="active">Cập nhật thông tin khóa học</li>
</ol>
<h4 class="step-heading">Cập nhật thông tin khóa học</h4>
<form method="post" class="form-horizontal"
      action="<?php echo Uri::to('admin/curriculum/update/course/' . $course->id); ?>"
      enctype="multipart/form-data" novalidate>
    <input name="token" type="hidden" value="<?php echo $token; ?>">
    <div class="form-group notification">
        <?php
        if (count($errors) == 0) {
            echo $messages;
        }
        ?>
    </div>
    <div class="form-group <?php if (isset($errors['fullname'])) {
        echo 'has-error';
    } else {
        echo '';
    } ?>">
        <label for="fullname" class="col-sm-2 control-label"><?php echo __('courses.fullname') ?> <span
                class="text-danger">*</span></label>
        <div class="col-sm-10">
            <?php echo Form::text('fullname', Input::previous('fullname', $course->fullname), array(
                'placeholder' => __('courses.fullname'),
                'autocomplete' => 'off',
                'autofocus' => 'true',
                'class' => 'form-control'
            )); ?>
            <?php if (isset($errors['fullname'])) { ?>
                <p class="help-block"><?php echo $errors['fullname'][0] ?></p>
            <?php } ?>
        </div>
    </div>
    <div class="form-group <?php if (isset($errors['shortname'])) {
        echo 'has-error';
    } else {
        echo '';
    } ?>">
        <label for="shortname" class="col-sm-2 control-label"><?php echo __('courses.shortname') ?> <span
                class="text-danger">*</span></label>
        <div class="col-sm-4">
            <?php echo Form::text('shortname', Input::previous('shortname', $course->shortname), array(
                'placeholder' => __('courses.shortname'),
                'autocomplete' => 'off',
                'autofocus' => 'true',
                'class' => 'form-control'
            )); ?>
            <?php if (isset($errors['shortname'])) { ?>
                <p class="help-block"><?php echo $errors['shortname'][0] ?></p>
            <?php } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group <?php if (isset($errors['startdate'])) {
                echo 'has-error';
            } else {
                echo '';
            } ?>">
                <label for="startdate"
                       class="col-sm-4 control-label"><?php echo __('courses.startdate') ?> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-8">
                    <div class='input-group date' id='datetimepicker_startdate'>
                        <input id="startdate" name="startdate" type='text' class="form-control"  value="<?php echo  $course->startdate;?>" readonly/>
                        <span class="input-group-addon">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                        </span>
                    </div>
                    <?php if (isset($errors['startdate'])) { ?>
                        <p class="help-block"><?php echo $errors['startdate'][0] ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group <?php if (isset($errors['enddate'])) {
                echo 'has-error';
            } else {
                echo '';
            } ?>">
                <label for="datetimepicker_enddate"
                       class="col-sm-4 control-label"><?php echo __('courses.enddate') ?> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-8">
                    <div class='input-group date' id='datetimepicker_enddate'>
                        <input id="enddate" name="enddate" type='text' class="form-control"  value="<?php echo  $course->enddate;?>" readonly/>
                        <span class="input-group-addon">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                        </span>
                    </div>
                    <?php if (isset($errors['enddate'])) { ?>
                        <p class="help-block"><?php echo $errors['enddate'][0] ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group <?php if (isset($errors['summary'])) {
        echo 'has-error';
    } else {
        echo '';
    } ?>">
        <label for="summary" class="col-sm-2 control-label"><?php echo __('courses.summary') ?></label>
        <div class="col-sm-10">
            <?php echo Form::textarea('summary', Input::previous('summary', $course->summary), array('id' => 'summary', 'class' => 'form-control')); ?>
            <em><?php echo __('courses.summary_explain'); ?></em>
            <?php if (isset($errors['summary'])) { ?>
                <p class="help-block"><?php echo $errors['summary'][0] ?></p>
            <?php } ?>
        </div>
    </div>
    <div class="form-group text-right">
        <aside class="buttons">
            <?php echo Form::button(__('global.continue'), array(
                'type' => 'submit',
                'class' => 'btn btn-primary btn-continue',
                'data-loading' => __('global.saving') ,
                'id' => 'submit'
            )); ?>
            <?php echo Html::link('admin/courses', __('global.cancel'), array(
                'class' => 'btn btn-danger btn-cancel'
            )); ?>
        </aside>
    </div>
    <input id="menuSelected" type="hidden" value="<?php if (isset($tab)): echo $tab; endif; ?>">
</form>
<script src="<?php echo asset('anchor/views/assets/js/bootstrap-datetimepicker.js'); ?>"></script>
<script src="<?php echo asset('anchor/views/assets/js/autosave.js'); ?>"></script>
<script src="<?php echo asset('anchor/views/assets/ckeditor/ckeditor.js'); ?>"></script>
<script src="<?php echo asset('anchor/views/assets/ckeditor/ckeditor.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var editor = CKEDITOR.replace( 'summary');

        $('#datetimepicker_startdate').datetimepicker({
            language: 'fr',
            startDate: new Date(),
            startView: 2,
            minView: 2,
            format: 'yyyy-mm-dd',
            pickTime: false,
        });
        $('#datetimepicker_enddate').datetimepicker({
            language: 'fr',
            pickTime: false,
            startView: 2,
            minView: 2,
            format: 'yyyy-mm-dd'
        });
        $("#datetimepicker_startdate").on("changeDate", function (e) {
            console.log(e.date);
            $('#datetimepicker_enddate').datetimepicker('setStartDate', e.date);
        });
        $("#datetimepicker_enddate").on("changeDate", function (e) {
            $('#datetimepicker_startdate').datetimepicker('setEndDate', e.date);
        });
    });
</script>
<?php echo $footer; ?>
