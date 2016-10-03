<?php echo $header; ?>

<hgroup class="wrap">
    <h1><?php  echo __('Kết quả tìm kiếm'); ?></h1>
    <nav style="margin-top: 20px;">
        <form method="get" action="<?php echo Uri::to('admin/schools/search'); ?>" novalidate>

            <?php echo Form::text('text-search', Input::get('text-search'), array('id' => 'text-search')); ?>
            <?php echo Form::button('Tìm kiếm', array(
                'class' => 'btn search blue',
                'type' => 'submit'
            )); ?>

        </form>
    </nav>
</hgroup>

<section class="wrap">
    <?php echo $messages;
    ?>

    <ul class="list">
        <?php
            if ($school->results == null)
            {
                echo 'Not found any school with keyword "'. $keysearch . '"';
            }
            else foreach($school->results as $sch): ?>
            <li>
                <a href="<?php echo Uri::to('admin/schools/edit/' . $sch->id); ?>">
                    <strong><?php echo $sch->id; ?></strong>
                    <span><?php echo __('schools.name'); ?>: <?php echo $sch->name; ?></span>
                    <em class="highlight"><?php echo __($sch->id); ?></em>
                </a>
            </li>
        <?php endforeach;  ?>
    </ul>

    <aside class="paging"><?php echo $school->links(); ?></aside>

    <aside class="buttons">
        <?php echo Html::link('admin/schools' , __('global.cancel'), array('class' => 'btn cancel blue')); ?>
    </aside>

</section>

<?php echo $footer; ?>
