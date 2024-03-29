<?php echo $header; ?>
<ol class="breadcrumb">
    <li><a href="<?php echo Uri::to('admin'); ?>">Trang chủ</a></li>
    <li><a href="<?php echo Uri::to('admin/courses'); ?>">Quản lý khóa học</a></li>
    <li class="active">Lịch giảng</li>
</ol>
<section class="wrap">
    <?php echo $messages; ?>
    <?php if ($pages->count): ?>
        <p class="text-right">
            <?php if ($pages->results[0]->status == PENDING) : ?>
                <a href="#" class="btn btn-primary" id="approve-course">
                    Xét duyệt khóa học
                </a>
            <?php elseif ($pages->results[0]->status == APPROVED) : ?>
                <a href="#" class="btn btn-primary" id="add-remote-course">
                    Đồng bộ khóa học
                </a>
            <?php else : ?>
                <span class="btn btn-primary"> Khóa học đã được đồng bộ </span>
            <?php endif; ?>
        </p>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Thời gian</th>
                <th>Tên chuyên đề</th>
                <th>Giảng viên thực hiện</th>
                <th>Phòng học</th>
                <th>Ghi chú</th>
                <th>Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($pages->results as $item): $display_pages = array($item); ?>
                <?php foreach ($display_pages as $page) : ?>
                    <tr>
                        <td><?php echo $page->topicday ?></td>
                        <td>
                                <span class="bhxh-course">
                                    <?php if ($page->topictime !== NULL):
                                        if ($page->topictime == 1):
                                            echo '<strong> Sáng </strong>' . ' ' . $page->topicname;
                                        elseif ($page->topictime == 2):
                                            echo '<strong> Chiều </strong>' . ' ' . $page->topicname;
                                        endif;
                                    else:
                                        echo $page->topicname;
                                    endif;
                                    ?>
                                </span>
                        </td>
                        <td><?php
                            echo $page->teacher_name;
                            ?></td>
                        <td><?php
                            echo $page->room_name;
                            ?></td>
                        <td>
                            <?php
                            echo $page->note;
                            ?>
                        </td>
                        <td>
                            <?php if ($page->status == 1): ?>
                                <a href="<?php echo Uri::to('admin/curriculum/edit/topic/' . $page->id); ?>"
                                >Sửa <i class="fa fa-pencil" aria-hidden="true"></i></a> |
                                <a href="<?php echo Uri::to('admin/curriculum/topic/delete/' . $page->id); ?>"
                                   onclick="return confirm('Bạn chắc chắn muốn xóa thông tin này');">Xóa <i
                                        class="fa fa-times"
                                        aria-hidden="true"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>

        <aside class="paging"><?php echo $pages->links(); ?></aside>

    <?php else: ?>
        <aside class="empty pages">
            <span class="icon"></span>
            <?php echo __('pages.nopages_desc'); ?><br>
        </aside>
    <?php endif; ?>
    <input id="menuSelected" type="hidden" value="<?php if (isset($tab)): echo $tab; endif; ?>">
</section>

<script type="text/javascript">
    var addRemoteCourse = (function () {
        var i = 0;
        var callAjax = function (url, token, courseid, loop) {
            $.ajax({
                method: "POST",
                url: url,
                data: {token: token, courseid: courseid, loop: loop},
                dataType: "text",
                success: function (result) {
                    if (result && result.status == false && i < 500) {
                        callAjax(url, token, courseid, 1);
                        i++;
                    } else {
                        $('#load').removeClass();
                        if (result && result.status == false) {
                            $('#load').addClass('fa fa-exclamation-triangle');
                        }
                        $('#load').addClass('fa fa-check');
                    }
                }
            });
        }
        var init = function (url, token, courseid) {
            $('#add-remote-course').click(function () {
                // call ajax
                $('#load').remove();
                $(this).append('<i id="load" class="fa fa-spinner fa-pulse fa fa-fw"></i>');
                callAjax(url, token, courseid, 0);
            });
        }
        return {
            init: init
        }
    }());

    $(document).ready(function () {
        $('#approve-course').click(function () {
            // call ajax
            $('#load').remove();
            $(this).append('<i id="load" class="fa fa-spinner fa-pulse fa fa-fw"></i>');
            $.ajax({
                method: "POST",
                url: '/admin/approve/course',
                data: {token: '<?php echo Csrf::token(); ?>', courseid: '<?php echo $courseid; ?>'},
                dataType: "json",
                success: function (result) {
                    $('#load').removeClass();
                    if (result && result.status == true) {
                        $('#load').addClass('fa fa-check');
                    } else {
                        $('#load').addClass('fa fa-exclamation-triangle');
                    }
                }
            });
        });

        addRemoteCourse.init('<?php echo base_url('admin/curriculum/add/remote/course') ?>', '<?php echo Csrf::token(); ?>', '<?php echo $courseid; ?>');
    });

</script>
<?php echo $footer; ?>
