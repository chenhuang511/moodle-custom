<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'error', language 'vi', branch 'MOODLE_31_STABLE'
 *
 * @package   error
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['alreadyloggedin'] = 'Bạn đã đăng nhập với tên {$a}, cần đăng xuất trước khi đăng nhập làm người dùng khác.';
$string['blockcannotconfig'] = 'Khối này không hỗ trợ cấu hình tổng thể';
$string['blockcannotinistantiate'] = 'Vấn đề trong khởi tạo đối tượng khối';
$string['blockdoesnotexist'] = 'Khối không tồn tại';
$string['blockdoesnotexistonpage'] = 'Khối này (id={$a->instanceid}) không tồn tại trên trang này ({$a->url}).';
$string['blocknameconflict'] = 'Xung đột tên: khối {$a->name} có cùng tiêu đề với khối hiện tại: {$a->conflict}!';
$string['callbackrejectcomment'] = 'Bộ chuyển hoàn bình luận đã từ chối bình luận này.';
$string['cannotaddcoursemodule'] = 'Không thể thêm mô-đun khóa học mới';
$string['cannotaddcoursemoduletosection'] = 'Không thể thêm mô đun khóa học mới vào phiên này';
$string['cannotaddnewmodule'] = 'Không thể thêm mô-đun {$a} mới';
$string['cannotaddthisblocktype'] = 'Bạn không thể thêm khối {$a} vào trang này.';
$string['cannotassignrole'] = 'Không thể phân quyền trong khóa học';
$string['cannotassignrolehere'] = 'Bạn không được phép phân quyền này (id = {$a->roleid}) trong bối cảnh này ({$a->context})';
$string['cannotassignselfasparent'] = 'Không thể tự phân thành cha!';
$string['cannotcallscript'] = 'Bạn không thể gọi mã nguồn này theo cách đó';
$string['cannotcallusgetselecteduser'] = 'Bạn không thể gọi user_selector::get_selected_user nếu đa lựa chọn được chọn.';
$string['cannotcreatecategory'] = 'Chuyên mục không được chèn';
$string['cannotcreatedboninstall'] = '<p>Không thể tạo CSDL.</p>
<p>CSDL được chỉ định không tồn tại và người dùng không được phép tạo CSDL.</p>
<p>Quản trị trang nên xác minh thiết lập CSDL.</p>';
$string['cannotcreatelangdir'] = 'Không thể tạo thư mục lang';
$string['cannotcreateorfindstructs'] = 'Lỗi tìm kiếm hay tạo các cấu trúc phân mục cho khóa học này';
$string['cannotcreatetempdir'] = 'Không thể tạo thư mục tạm thời';
$string['cannotcustomisefiltersblockuser'] = 'Bạn không thể tùy chỉnh các thiết lập lọc trong bối cảnh người dùng hay khối.';
$string['cannotdeletecategorycourse'] = 'Khóa học \'{$a}\' không xóa được.';
$string['cannotdeletecategoryquestions'] = 'Không thể xóa các câu hỏi từ chuyên mục \'{$a}\'';
$string['cannotdeletecourse'] = 'Bạn không được phép xóa khóa học này';
$string['cannotdeletecustomfield'] = 'Lỗi xóa dữ liệu mục tùy chỉnh';
$string['cannotdeletefile'] = 'Không thể xóa tập tin này';
$string['cannotdeleterolewithid'] = 'Không thể xóa quyền với ID {$a}';
$string['cannotdeletethisrole'] = 'Bạn không thể xóa quyền này bởi vì nó được dùng bởi hệ thống, hoặc bởi vì nó là quyền cuối cùng có quyền quản trị viên.';
$string['cannotdisableformat'] = 'Bạn không thể vô hiệu hóa định dạng mặc định';
$string['cannotdownloadcomponents'] = 'Không thể tải component';
$string['cannotdownloadlanguageupdatelist'] = 'Không thể tải danh sách cập nhật ngôn ngữ từ download.moodle.org';
$string['cannotdownloadzipfile'] = 'Không thể tải tập tin ZIP';
$string['cannoteditsiteform'] = 'Bạn không thể chỉnh sửa trang khóa học sử dụng mẫu đơn này';
$string['cannotedityourprofile'] = 'Xin lỗi, bạn không thể chỉnh sửa hồ sơ';
$string['cannotexecduringupgrade'] = 'Không thể được thực thi trong quá trình nâng cấp';
$string['cannotfindcategory'] = 'Không thể tìm bản ghi chuyên mục từ CSDL theo ID - {$a}';
$string['cannotfindcomponent'] = 'Không thấy component';
$string['cannotfindcourse'] = 'Không thấy khóa học';
$string['cannotfindgradeitem'] = 'Không thể tìm grade_item';
$string['cannotfindteacher'] = 'Không thấy giáo viên';
$string['cannotfinduser'] = 'Không thể tìm người dùng tên "{$a}"';
$string['cannotgeoplugin'] = 'Không thể kết nối máy chủ geoPlugin tại http://www.geoplugin.com, hãy kiểm tra thiết lập proxy hoặc tốt hơn là cài đặt tệp dữ liệu MaxMind GeoLite City';
$string['cannotgradeuser'] = 'Không thể cho điểm ngươi dùng này';
$string['cannothaveparentcate'] = 'Phụ mục khóa học không thể có phụ mục lớp ngoài (parent)';
$string['cannotimport'] = 'Lỗi import';
$string['cannotimportformat'] = 'Xin lỗi, nhập định dạng này chưa được hiện thực!';
$string['cannotimportgrade'] = 'Lỗi nhập điểm';
$string['cannotinsertgrade'] = 'Không thể chèn mục điểm mà không có id khóa học!';
$string['cannotmailconfirm'] = 'Lỗi khi gửi thư điện báo sự thay đổi mật khẩu';
$string['cannotmanualctrack'] = 'Hoạt động không cung cấp theo dõi hoàn thành thủ công';
$string['cannotmapfield'] = 'Phát hiện xung đột móc nối - hai mục nối cùng một mục điểm {$a}';
$string['cannotmovecategory'] = 'Không thể chuyển chuyên mục';
$string['cannotmovecourses'] = 'Không thể chuyển các khóa học trong chuyên mục vốn có sang một cái khác.';
$string['cannotmoverolewithid'] = 'Không thể chuyển quyền với ID {$a}';
$string['cannotopencsv'] = 'Không thể mở tập tin CSV';
$string['cannotopenfile'] = 'Không thể mở tập tin ({$a})';
$string['cannotopenzip'] = 'Không thể mở tệp zip, có lẽ là lỗi trình mở rộng zip trên hệ điều hành 64bit';
$string['cannotoverridebaserole'] = 'Không thể thay thế các quyền hạn vai trò cơ bản';
$string['cannotoverriderolehere'] = 'Bạn không được phép thay thế vai trò này (id = {$a->roleid}) trong bối cảnh này ({$a->context})';
$string['cannotreadfile'] = 'Không thể đọc tập tin ({$a})';
$string['cannotreadtmpfile'] = 'Lỗi đọc tập tin tạm thời';
$string['cannotreaduploadfile'] = 'Không thể đọc tệp được đăng tải';
$string['cannotresetguestpwd'] = 'Bạn không thể đặt lại mật khẩu khách';
$string['cannotresetmail'] = 'Lỗi đặt lại mật khẩu và gửi thư cho bạn';
$string['cannotrestore'] = 'Một lỗi đã xảy ra và quá trình phục hồi không thể hoàn tất!';
$string['cannotsavemd5file'] = 'Không thể lưu tệp md5';
$string['cannotsavezipfile'] = 'Không thể lưu tập tin ZIP';
$string['cannotservefile'] = 'Không thể phục vụ tập tin - vấn đề thiết lập máy chủ.';
$string['cannotsetparentforcatoritem'] = 'Không thể đặt làm cha cho chuyên mục hay mục khóa học này!';
$string['cannotsetpassword'] = 'Không thể đặt mật khẩu cho người dùng';
$string['cannotunassigncap'] = 'Không thể rút phân công quyền hạn lỗi thời {$a->cap} từ vai trò {$a->role}';
$string['cannotunzipfile'] = 'Không thể giải nén tập tin';
$string['cannotupdatemod'] = 'Không thể cập nhật {$a}';
$string['cannotupdatepasswordonextauth'] = 'Cập nhật mật khẩu bất thành trên xác thực ngoài: {$a}. Xem nhật kí máy chủ để biết thêm chi tiết.';
$string['cannotupdateprofile'] = 'Lỗi cập nhật bản ghi người dùng';
$string['cannotupdateusermsgpref'] = 'Không thể cập nhật tùy chỉnh tin nhắn';
$string['cannotupdateuseronexauth'] = 'Cập nhật dữ liệu người dùng bất thành trên xác thực ngoài: {$a}. Xem nhật kí máy chủ để biết thêm chi tiết.';
$string['cannotuploadfile'] = 'Lỗi xử lí đăng tải tập tin';
$string['cannotusepage2'] = 'Xin lỗi, bạn không thể sử dụng trang này';
$string['cannotviewprofile'] = 'Bạn không thể xem hồ sơ người dùng này';
$string['cannotviewreport'] = 'Bạn không thể xem báo cáo này';
$string['categoryerror'] = 'Lỗi chuyên mục';
$string['categoryidnumbertaken'] = 'Số ID đã được sử dụng cho chuyên mục khác';
$string['categorynamerequired'] = 'Tên danh mục là bắt buộc';
$string['categorytoolong'] = 'Tên khóa học quá dài';
$string['componentisuptodate'] = 'Component đang ở tình trạng mới nhất';
$string['confirmsesskeybad'] = 'Xin lỗi, nhưng khóa phiên hoạt động của bạn không thể xác nhận được để thực hiện hoạt động này. Tính năng bảo mật này ngăn việc thực thi các hàm quan trọng một cách bất ngờ hay rủi ro bằng danh tính của bạn. Hãy chắc rằng bạn thật sự muốn thực thi hàm này.';
$string['coursedoesnotbelongtocategory'] = 'Khóa học không phụ thuộc chuyên mục này';
$string['courseformatnotfound'] = 'Định dạng khóa học \'$a\' không tồn tại hoặc không nhận diện được';
$string['courseidnotfound'] = 'ID khóa học không tồn tại';
$string['courseidnumbertaken'] = 'Số ID đã được sử dụng cho khóa học khác ({$a})';
$string['coursemisconf'] = 'Khóa học không được cấu hình đúng';
$string['courserequestdisabled'] = 'Xin lỗi, nhưng yêu cầu khóa học đã bị vô hiệu hóa bởi quản trị viên.';
$string['csvcolumnduplicates'] = 'Phát hiện chồng chéo cột';
$string['csvemptyfile'] = 'Tập tin CSV rỗng';
$string['csvfewcolumns'] = 'Không đủ cột, hãy xác minh thiết lập phân cách';
$string['csvinvalidcols'] = '<b>Tệp CSV không hợp lệ:</b>Dòng đầu tiên phải bao gồm "Mục đầu đề" và tệp phải thuộc loại <br />"Mục mở rộng/ngăn cách bởi dấu phẩy"<br />hay<br /> "Mục mở rộng với CAVV sinh mã/ngăn cách bởi dấu phẩy"';
$string['csvinvalidcolsnum'] = 'Tệp CSV không hợp lệ - mỗi dòng phải bao gồm 49 hoặc 70 mục';
$string['csvloaderror'] = 'Một lỗi đã xảy ra trong khi tải tệp CSV: {$a}';
$string['csvweirdcolumns'] = 'Định dang tệp CSV không hợp lệ - số cột không đồng nhất!';
$string['dbconnectionfailed'] = '<p>Lỗi: Kết nối CSDL bất thành</p>
<p>Có thể CSDL bị quá tải hay vận hành không phù hợp.<p>
<p>Quản trị trang cũng nên kiểm tra chi tiết CSDL đã được chỉ định phù hợp trong config.php</p>';
$string['dbdriverproblem'] = '<p>Lỗi: phát hiện vấn đề trình điều phối CSDL</p>
<p>Quản trị trang nên xác minh thiết lập máy chủ</p>
<p>{$a}</p>';
$string['dbsessionhandlerproblem'] = 'Cài đặt phiên hoạt động CSDL bất thành. Hãy thông báo quản trị máy chủ.';
$string['dbsessionmysqlpacketsize'] = 'Phát hiện lỗi phiên hoạt động nghiêm trọng. Hãy thông báo cho quản trị trang. Vấn đề có lẽ được gây ra bởi giá trị nhỏ trong thiết lập MySQL max_allowed_packet.';
$string['dbupdatefailed'] = 'Cập nhật CSDL thất bại';
$string['ddldependencyerror'] = '{$a->targettype} "{$a->targetname}" không thể chỉnh sửa được. Tìm thấy phần phụ thuộc với {$a->offendingtype} "{$a->offendingname}"';
$string['ddlexecuteerror'] = 'Lỗi thực thi DDL sql';
$string['ddlfieldalreadyexists'] = 'Mục "{$a} đã tồn tại';
$string['ddlfieldnotexist'] = 'Mục "{$a->fieldname}" không tồn tại trong bảng "{$a->tablename}"';
$string['ddltablealreadyexists'] = 'Bảng "{$a}" đã tồn tại';
$string['ddltablenotexist'] = 'Bảng "{$a}" không tồn tại';
$string['ddlunknownerror'] = 'Lỗi thư viện DDL không rõ';
$string['ddlxmlfileerror'] = 'Tìm thấy lỗi tệp CSDL XML';
$string['ddsequenceerror'] = 'Định nghĩa bảng "{$a}" sai; chỉ có thể có một cột tự động và nó phải được định nghĩa là khóa.';
$string['detectedbrokenplugin'] = 'Trình bổ trợ "{$a}" khiếm khuyết hoặc lỗi thời, không thể tiếp tục, xin lỗi.';
$string['dmlexceptiononinstall'] = '<p>Lỗi CSDL xảy ra [{$a->errorcode}].<br />{$a->debuginfo}</p>';
$string['dmlreadexception'] = 'Không đọc được CSDL';
$string['dmltransactionexception'] = 'Lỗi giao tác CSDL';
$string['dmlwriteexception'] = 'Không viết được vào CSDL';
$string['downgradedcore'] = 'LỖI!!! Mã nguồn bạn đang sử dụng CŨ hơn phiên bản làm ra các CSDL này!';
$string['downloadedfilecheckfailed'] = 'Kiểm tra tệp tải về bất thành';
$string['duplicatefieldname'] = 'Phát hiện trùng lặp tên mục "{$a}"';
$string['duplicatenosupport'] = 'Hoạt động \'{$a->modname}\' không thể lặp lại bởi mô-đun {$a->modtype} không hỗ trợ sao lưu và phục hồi.';
$string['duplicateparaminsql'] = 'LỖI: trùng lặp tên tham số trong truy vấn';
$string['duplicateusername'] = 'Trùng lặp tài khoản - bỏ qua bản ghi';
$string['error'] = 'Lỗi đã xảy ra';
$string['errorfetchingrssfeed'] = 'Lỗi lấy dòng tin RSS.';
$string['errorparsingxml'] = 'Lỗi phân tích XML: {$a->errorstring} ở dòng {$a->errorline}, kí tự {$a->errorchar}';
$string['errorprocessingarchive'] = 'Lỗi xử lí tệp lưu trữ';
$string['errorsettinguserpref'] = 'Lỗi thiết lập tùy chỉnh người dùng';
$string['expiredkey'] = 'Khóa đã hết hạn';
$string['externalauthpassworderror'] = 'Mật khẩu không trống đối với xác thực ngoài';
$string['externalfilenolocation'] = 'Tệp ngoài không có đường dẫn';
$string['fieldrequired'] = '"{$a}" là mục bắt buộc';
$string['fileexists'] = 'Tập tin tồn tại';
$string['filenotfound'] = 'Xin lỗi, không thể tìm thấy tập tin được yêu cầu';
$string['filenotreadable'] = 'Tệp không đọc được';
$string['filterdoesnothavelocalconfig'] = 'Bộ lọc {$a} không cho phép thiết lập cục bộ.';
$string['filternotenabled'] = 'Bộ lọc chưa được kích hoạt!';
$string['filternotinstalled'] = 'Bộ lọc {$a} hiện chưa được cài đặt';
$string['forumblockingtoomanyposts'] = 'Bạn đã vượt quá ngưỡng gửi bài được đặt cho diễn đàn này';
$string['generalexceptionmessage'] = 'Ngoại lệ - {$a}';
$string['gradecantregrade'] = 'Lỗi xảy ra trong quá trình tính điểm: {$a}';
$string['gradepubdisable'] = 'Công bố điểm bị vô hiệu hóa';
$string['groupexistforcourse'] = 'Nhóm "{$a}" đã tồn tại với khóa học này';
$string['groupexistforcoursewithidnumber'] = '{$a->problemgroup}: Nhóm "{$a->name}" có số id "{$a->idnumber}" đã tồn tại với khóa học này';
$string['grouphasidnumber'] = 'Nhóm "{$a}" có số id và có thể đã được tạo tự động bởi một hệ thống ngoài. Bạn không được phép xóa nhóm này.';
$string['groupinghasidnumber'] = 'Tổ "{$a}" có số id và có thể đã được tạo tự động bởi một hệ thống ngoài. Bạn không được phép xóa tổ này.';
$string['groupingnotaddederror'] = 'Tổ "{$a}" chưa được thêm';
$string['groupnotaddederror'] = 'Nhóm "{$a}" chưa được thêm';
$string['groupnotaddedtogroupingerror'] = 'Nhóm "{$a->groupname}" chưa được thêm vào tổ "{$a->groupingname}"';
$string['groupunknown'] = 'Nhóm {$a} không kết hợp với khóa học được chỉ định';
$string['groupusernotmember'] = 'Người dùng không phải thành viên của nhóm này.';
$string['guestcantaccessprofiles'] = 'Khách không thể truy cập hồ sơ người dùng. Đăng nhập với tài khoản người dùng hoàn chỉnh để tiếp tục.';
$string['guestnoeditprofile'] = 'Khách không thể chỉnh sửa thông tin cá nhân.';
$string['guestnoeditprofileother'] = 'Không thể thay đổi thông tin cá nhân của khách.';
$string['guestsarenotallowed'] = 'Khách không được phép thao tác này.';
$string['hashpoolproblem'] = 'Nội dung tệp pool không đúng sai {$a}.';
$string['headersent'] = 'Headers already sent';
$string['idnumbertaken'] = 'Số ID này đã được sử dụng';
$string['idnumbertoolong'] = 'Số ID quá dài';
$string['installproblem'] = 'Thường thì không thể phục hồi khỏi các lỗi phát sinh trong quá trình cài đặt, bạn có thể tạo CSDL mới hoặc sử dụng tiền tố CSDL khác nếu muốn thử cài đặt lại.';
$string['internalauthpassworderror'] = 'Thiếu mật khẩu hoặc chính sách mật khẩu không hợp lệ đối với xác thực nội';
$string['invalidaccess'] = 'Trang này không truy cập được đúng cách';
$string['invalidaccessparameter'] = 'Tham số truy cập không hợp lệ';
$string['invalidaction'] = 'Tham số hành động không hợp lệ';
$string['invalidadminsettingname'] = 'Thiết lập quản trị ({$a}) không hợp lệ';
$string['invalidargorconf'] = 'Không có tham số hợp lệ được cung cấp hoặc thiết lập máy chủ sai';
$string['invalidarguments'] = 'Không có tham số hợp lệ được cung cấp';
$string['invalidbulkenrolop'] = 'Thao tác ghi danh hàng loạt không hợp lệ được yêu cầu.';
$string['invalidcategory'] = 'Sai phụ mục';
$string['invalidcategoryid'] = 'Sai ID của phụ mục';
$string['invalidcomment'] = 'Lời bình không đúng';
$string['invalidcommentarea'] = 'Vùng bình luận không hợp lệ';
$string['invalidcommentid'] = 'Id bình luận không hợp lệ';
$string['invalidcommentitemid'] = 'Id mục bình luận không hợp lệ';
$string['invalidcommentparam'] = 'Tham số bình luận không hợp lệ';
$string['invalidcomponent'] = 'Tên thành phần không hợp lệ';
$string['invalidconfirmdata'] = 'Dữ liệu xác nhận không hợp lệ';
$string['invalidcontext'] = 'Bối cảnh không phù hợp';
$string['invalidcourse'] = 'Khóa học không hợp lệ';
$string['invalidcourseid'] = 'Bạn đang cố sử dụng một ID khóa học không hợp lệ';
$string['invalidcourselevel'] = 'Cấp độ bối cảnh sai';
$string['invalidcoursemodule'] = 'ID mô-đun khóa học không hợp lệ';
$string['invalidcoursenameshort'] = 'Tên ngắn khóa học không hợp lệ';
$string['invaliddata'] = 'Dữ liệu được gửi không hợp lệ';
$string['invaliddatarootpermissions'] = 'Phát hiện các cấp phép không hợp lệ trong thư mục $CFG->dataroot, quản trị viên phải sửa cấp phép.';
$string['invaliddevicetype'] = 'Loại thiết bị không hợp lệ';
$string['invalidelementid'] = 'Id nhân tố sai';
$string['invalidentry'] = 'Đây không phải mục hợp lệ!';
$string['invalidfieldname'] = '"{$a}" không phải tên mục hợp lệ';
$string['invalidfiletype'] = '"{$a}" không phải loại tệp hợp lệ';
$string['invalidformdata'] = 'Dữ liệu mẫu đơn sai';
$string['invalidfunction'] = 'Hàm sai';
$string['invalidgradeitemid'] = 'Id mục điểm sai';
$string['invalidgroupid'] = 'Id nhóm được chỉ định sai';
$string['invalidipformat'] = 'Định dạng địa chỉ IP không hợp lệ';
$string['invaliditemid'] = 'Id mục sai';
$string['invalidkey'] = 'Khóa sai';
$string['invalidlegacy'] = 'Định nghĩa vai trò thừa kế không đúng đối với loại: {$a}';
$string['invalidmd5'] = 'Biến kiểm tra sai - thử lại';
$string['invalidmode'] = 'Chế độ không hợp lệ ({$a})';
$string['invalidnum'] = 'Giá trị số không hợp lệ';
$string['invalidnumkey'] = 'Mảng $conditions không thể chứa các khóa số, hãy sửa mã nguồn!';
$string['invalidoutcome'] = 'Id đầu ra sai';
$string['invalidpagesize'] = 'Kích thước trang không hợp lệ';
$string['invalidpasswordpolicy'] = 'Chính sách mật khẩu không hợp lệ';
$string['invalidpaymentmethod'] = 'Phương thức thanh toán không hợp lệ: {$a}';
$string['invalidqueryparam'] = 'LỖI: Số tham số truy vấn sai. Mong là {$a->expected}, nhưng lại được {$a->actual}).';
$string['invalidratingarea'] = 'Khu đánh giá không hợp lệ';
$string['invalidrecord'] = 'Không thể tìm thấy bản ghi dữ liệu trong bảng CSDL {$a}.';
$string['invalidrecordunknown'] = 'Không thể tìm thấy bản ghi dữ liệu trong CSDL.';
$string['invalidrequest'] = 'Yêu cầu không hợp lệ';
$string['invalidrole'] = 'Vai trò không hợp lệ';
$string['invalidroleid'] = 'ID vai trò không hợp lệ';
$string['invalidscaleid'] = 'Id scale sai';
$string['invalidsection'] = 'Bản ghi mô-đun khóa học chứa phân mục không hợp lệ';
$string['invalidsesskey'] = 'Sesskey gửi đi sai, mẫu đơn không được chấp nhận!';
$string['invalidshortname'] = 'Đó là tên ngắn khóa học không hợp lệ';
$string['invalidsourcefield'] = 'Mục nguồn của tập tin nháp không hợp lệ';
$string['invalidstatedetected'] = 'Thứ gì đó sai lệch: {$a}>. Bình thường nó không bao giờ xảy ra.';
$string['invalidurl'] = 'URL không hợp lệ';
$string['invaliduser'] = 'Người dùng không hợp lệ';
$string['invaliduserfield'] = 'Mục người dùng không hợp lệ: {$a}';
$string['invaliduserid'] = 'Id người dùng không hợp lệ';
$string['invalidusername'] = 'Tài khoản được cho chứa các kí tự không hợp lệ';
$string['iplookupfailed'] = 'Không thể tìm thấy thông tin địa lí về địa chỉ IP {$a} này';
$string['iplookupprivate'] = 'Không thể hiển thị tìm kiếm địa chỉ IP riêng';
$string['ipmismatch'] = 'Địa chỉ IP khách không khớp';
$string['listcantmovedown'] = 'Chuyển mục xuống bất thành, vì nó là cái cuối trong hàng';
$string['listcantmoveleft'] = 'Chuyển mục sang trái bất thành, vì nó không có cha';
$string['listcantmoveright'] = 'Chuyển mục sang phải bất thành, vì không có hàng để làm con. Chuyển nó xuống dưới hàng khác và rồi bạn có thể chuyển sang bên phải.';
$string['listcantmoveup'] = 'Chuyển mục lên trên bất thành, vì nó là cái đầu tiên của hàng';
$string['listnoitem'] = 'Không tìm thấy mục';
$string['logfilenotavailable'] = 'Nhật kí không hiện hữu';
$string['loginasnoenrol'] = 'Bạn không thể sử dụng ghi danh và rút tên khi ở trong phiên hoạt động khóa học "Đăng nhập với tên"';
$string['loginasonecourse'] = 'Bạn không thể vào khóa học này.<br /> Bạn phải ngắt phiên hoạt động "Đăng nhập với tên" trước khi vào khóa học khác.';
$string['maxareabytes'] = 'Tập tin lớn hơn dung lượng còn lại trong vùng.';
$string['maxbytes'] = 'Tập tin lớn hơn kích thước tối đa được cho phép.';
$string['messagingdisable'] = 'Tin nhắn bị vô hiệu hóa trên trang này';
$string['mimetexisnotexist'] = 'Hệ thống của bạn không được thiết lập để chạy mimeTeX. Bạn cần tải bản thực thi phù hợp cho thể thức PHP_OS từ <a href="http://moodle.org/download/mimetex/">http://moodle.org/download/mimetex/</a>, hoặc lấy mã nguồn C từ <a href="http://www.forkosh.com/mimetex.zip"> http://www.forkosh.com/mimetex.zip</a>, biên dịch nó và đặt bản thực thi vào thư mục moodle/filter/tex/ của bạn.';
$string['mimetexnotexecutable'] = 'mimetex tuỳ chỉnh không thể thực thi!';
$string['missingfield'] = 'Thiếu mục "{$a}"';
$string['missingkeyinsql'] = 'LỖI: thiếu tham số "{$a}" trong truy vấn';
$string['missing_moodle_backup_xml_file'] = 'Bản sao lưu thiếu tệp XML: {$a}';
$string['missingparam'] = 'Thiếu tham số bắt buộc ({$a})';
$string['missingparameter'] = 'Thiếu tham số';
$string['missingrequiredfield'] = 'Thiếu một số mục bắt buộc';
$string['missinguseranditemid'] = 'Thiếu userid và itemid';
$string['mixedtypesqlparam'] = 'LỖI: Trộn lẫn các loại tham số truy vấn sql!!';
$string['mnetdisable'] = 'MNET bị vô hiệu hoá';
$string['mnetlocal'] = 'Người dùng MNET từ xa không thể đăng nhập cục bộ';
$string['moduledisable'] = 'Mô-đun ({$a}) này đã bị vô hiệu hoá đối với khoá học cụ thể này';
$string['moduledoesnotexist'] = 'Mô-đun này không tồn tại';
$string['modulemissingcode'] = 'Mô-đun {$a} đang thiếu mã nguồn để thực hiện hàm này';
$string['movecatcontentstoroot'] = 'Không cho phép chuyển nội dung chuyên mục vào gốc. Bạn phải chuyển nội dung vào một chuyên mục tồn tại!';
$string['movecategorynotpossible'] = 'Bạn không thể chuyển chuyên mục \'{$a}\' vào chuyên mục được chọn.';
$string['movecategoryownparent'] = 'Bạn không thể làm chuyên mục \'{$a} thành cha của chính nó.';
$string['movecategoryparentconflict'] = 'Bạn không thể làm chuyên mục \'{$a}\' thành một tiểu mục trong các tiểu mục của chính nó.';
$string['multiplerecordsfound'] = 'Tìm thấy nhiều bản ghi, dầu chỉ mong một bản ghi.';
$string['mustbeloggedin'] = 'Bạn phải đăng nhập để làm điều này';
$string['myisamproblem'] = 'Các bảng CSDL đang sử dụng cơ chế CSDL MyISAM, khuyến nghị sử dụng cơ chế tương thích ACID có hỗ trợ giao tác đầy đủ như InnoDB.';
$string['needcopy'] = 'Bạn cần chép một số thứ trước tiên!';
$string['needcoursecategroyid'] = 'Id khóa học hoặc chuyên mục phải được chỉ định';
$string['needphpext'] = 'Bạn cần thêm hỗ trợ {$a} vào quá trình cài đặt PHP của mình';
$string['noadmins'] = 'Không quản trị viên!';
$string['noblocks'] = 'Không tìm thấy các khối!';
$string['nocapabilitytousethisservice'] = 'Người dùng không đủ khả năng yêu cầu để sử dụng dịch vụ này';
$string['nocontext'] = 'Xin lỗi, nhưng khóa học đó là một bối cảnh không phù hợp';
$string['nodata'] = 'Không dữ liệu';
$string['nofile'] = 'Tập tin chưa được chỉ định';
$string['nofiltersenabled'] = 'Không có bộ lọc được kích hoạt.';
$string['noformdesc'] = 'Không tìm thấy tệp mô tả mẫu đơn formslib cho hoạt động này.';
$string['noguest'] = 'Không có khách ở đây!';
$string['noinstances'] = 'Không có các thực thể của {$a} trong khóa học này!';
$string['nologinas'] = 'Bạn không được phép đăng nhập làm người dùng đó';
$string['noparticipants'] = 'Không tìm thấy người tham gia khóa học này';
$string['nopermissions'] = 'Xin lỗi, nhưng hiện tại bạn không được phép làm điều đó ({$a})';
$string['nopermissiontocomment'] = 'Bạn không thể thêm bình luận';
$string['nopermissiontodelentry'] = 'Bản không thể xóa các mục của người khác!';
$string['nopermissiontohide'] = 'Không có phép để ẩn!';
$string['nopermissiontoimportact'] = 'Bạn không đủ phép yêu cầu để nhập các hoạt động vào khóa học này';
$string['nopermissiontolock'] = 'Không có phép để khóa!';
$string['nopermissiontoshow'] = 'Không có phép để xem nó!';
$string['nopermissiontounlock'] = 'Không có phép để mở khóa!';
$string['nopermissiontoupdatecalendar'] = 'Xin lỗi, nhưng hiện tại bạn không được phép cập nhật sự kiện lịch';
$string['nopermissiontoviewgrades'] = 'Không thể xem điểm.';
$string['nopermissiontoviewletergrade'] = 'Thiếu phép để xem điểm theo kí tự';
$string['nosite'] = 'Không thể tìm thấy khóa học đầu cấp';
$string['nostatstodisplay'] = 'Xin lỗi, không có dữ liệu để hiển thị';
$string['notallowedtoupdateprefremotely'] = 'Bạn không được phép cập nhật tùy chỉnh người dùng này từ xa';
$string['notavailable'] = 'Hiện tại không hiện hữu';
$string['notlocalisederrormessage'] = '{$a}';
$string['notmemberofgroup'] = 'Bạn không là thành viên của nhóm khóa học này';
$string['notownerofkey'] = 'Bạn không phải chủ của khóa này';
$string['nousers'] = 'Không có người dùng như vậy!';
$string['onlyadmins'] = 'Chỉ quản trị viên có thể làm điều đó';
$string['orderidnotfound'] = 'Không tìm thấy Order ID {$a}';
$string['pagenotexist'] = 'Một lỗi bất thường đã xảy ra (cố tới một trang không tồn tại)';
$string['pathdoesnotstartslash'] = 'Không có tham số hợp lệ được cung cấp, đường dẫn không bắt đầu với dấu xoẹt!';
$string['pleasereport'] = 'Nếu bạn có thời gian, hãy cho chúng tôi biết bạn đã thử làm gì khi lỗi xảy ra:';
$string['pluginrequirementsnotmet'] = 'Trình bổ trợ "{$a->pluginname}" ({$a->pluginversion}) không thể cài đặt được. Nó yêu cầu một phiên bản mới hơn của Moodle (hiện tại bạn đang sử dụng {$a->currentmoodle}, bạn cần {$a->requiremoodle}).';
$string['prefixcannotbeempty'] = '<p>Lỗi: tiền tố bảng CSDL không thể trống ({$a})</p>
<p>Quản trị trang phải khắc phục vấn đề này.</p>';
$string['prefixtoolong'] = '<p>Lỗi: tiền tố bảng CSDL quá dài ({$a->dbfamily})</p>
<p>Quản trị trang phải khắc phục vấn đề này. Độ dài tối đa cho các tiền tố bảng trong {$a->dbfamily} là {$a->maxlength} kí tự.</p>';
$string['protected_cc_not_supported'] = 'Không hỗ trợ lốc mực in có bảo vệ.';
$string['querystringcannotbeempty'] = 'Chuỗi truy vấn không thể trống.';
$string['redirecterrordetected'] = 'Phát hiện chuyển hướng không hỗ trợ, việc thực thi mã nguồn bị ngắt';
$string['refoundto'] = 'Có thể được chuyển hoàn về {$a}';
$string['refoundtoorigi'] = 'Chuyển hoàn về khoản ban đầu: {$a}';
$string['remotedownloaderror'] = '<p>Tải về thành phần máy chủ của bạn bất thành. Hãy xác minh các thiết lập proxy; bộ mở rộng PHP cURL được khuyến nghị.<p>
<p>Bạn phải tải tệp <a href="{$a->url}">{$a->url}</a> thủ công, chép nó vào "{$a->dest}" trong máy chủ của mình và giải nén nó.</p>';
$string['reportnotavailable'] = 'Loại báo cáo này chỉ hiện hữu với trang khóa học';
$string['requirecorrectaccess'] = 'Url hoặc cổng không hợp lệ.';
$string['requireloginerror'] = 'Khóa học hay hoạt động không truy cập được.';
$string['restore_path_element_missingmethod'] = 'Phục hồi phương thức {$a} đang thiếu. Nó phải được xác định bởi nhà phát triển.';
$string['restore_path_element_noobject'] = 'Phục hồi đối tượng {$a} không phải là một đối tượng.';
$string['restrictedcontextexception'] = 'Xin lỗi, việc thực thi hàm ngoài vi phạm giới hạn bối cảnh.';
$string['restricteduser'] = 'Xin lỗi, nhưng tài khoản hiện tại "{$a}" bị giới hạn làm điều đó';
$string['reverseproxyabused'] = 'Proxy nghịch đảo được kích hoạt, máy chủ không thể truy cập trực tiếp, xin lỗi.<br />Hãy liên hệ quản trị máy chủ.';
$string['rpcerror'] = 'Ối! GIao tiếp MNET hỏng! Đây là thông báo lỗi để chuyển cho quản trị viên của bạn: {$a}';
$string['scheduledbackupsdisabled'] = 'Sao lưu theo kế hoạch đã bị vô hiệu hóa bởi quản trị máy chủ';
$string['secretalreadyused'] = 'Liên kết xác nhận đổi mật khẩu đã được sử dụng, mật khẩu không thay đổi';
$string['sectionnotexist'] = 'Phiên này không tồn tại';
$string['sendmessage'] = 'Gửi thông điệp';
$string['serverconnection'] = 'Lỗi kết nối máy chủ';
$string['sessioncookiesdisable'] = 'Sử dụng sai require_key_login() - các cookie phiên hoạt động phải được vô hiệu hóa!';
$string['sessiondiskfull'] = 'Phân vùng phiên hoạt động đầy. Không thể đăng nhập vào lúc này. Hãy thông báo quản trị máy chủ.';
$string['sessionerroruser'] = 'Phiên hoạt động của bạn đã hết. Hãy đăng nhập lại.';
$string['sessionhandlerproblem'] = 'Bộ kiểm soát phiên hoạt động được thiết lập không phù hợp';
$string['sessionipnomatch2'] = '<p>Xin lỗi, nhưng số IP của bạn dường như thay đổi từ khi bạn đăng nhập lần đầu tiên. Tính năng bảo mật này ngăn chặn những người bẻ khóa trộm danh tính của bạn khi đăng nhập vào trang này. Bạn có thể thấy lỗi này nếu sử dụng mạng không dây hoặc bạn đang chuyển vùng giữa các mạng khác nhau. Hãy hỏi quản trị trang để thêm sự trợ giúp.</p>
<p>Nếu bạn muốn tiếp tục hãy nhấn phím F5 để tải lại trang.</p>';
$string['sessionwaiterr'] = 'Hết giờ trong khi đang đợi khóa phiên hoạt động.<br />Đợi yêu cầu của bạn hoàn thành và thử lại sau.';
$string['shortnametaken'] = 'Tên ngắn đã được dùng cho một khóa học khác ({$a})';
$string['socksnotsupported'] = 'Proxy SOCKS5 không được hỗ trợ trong PHP4';
$string['sslonlyaccess'] = 'Vì lí do bảo mật chỉ các kết nối https được cho phép, xin lỗi.';
$string['statscatchupmode'] = 'Thống kê hiện tại ở chế độ cuốn chiếu. Đến nay {$a->daysdone} ngày đã được xử lí và {$a->dayspending} đang chờ. Kiểm tra lại sau!';
$string['statsdisable'] = 'Thống kê không được kích hoạt';
$string['statsnodata'] = 'Không có dữ liệu cho việc kết hợp khóa học và chu kì thời gian';
$string['storedfilecannotcreatefile'] = 'Không thể tạo tệp pool cục bộ, hãy xác minh cấp phép trong dataroot và dung lượng đĩa còn có.';
$string['storedfilecannotcreatefiledirs'] = 'Không thể tạo các thư mục tệp pool, hãy xác minh cấp phép trong dataroot.';
$string['storedfilecannotread'] = 'Không thể đọc tệp, hoặc tệp không tồn tại hoặc có vấn đề về cấp phép';
$string['storedfilenotcreated'] = 'Không thể tạo tệp "{$a->contextid}/{$a->component}/{$a->filearea}/{$a->itemid}{$a->filepath}{$a->filename}"';
$string['storedfileproblem'] = 'Lỗi không rõ liên quan đến các tập tin cục bộ ({$a})';
$string['tagdisabled'] = 'Thẻ bị vô hiệu hóa!';
$string['targetdatabasenotempty'] = 'CSDL mục tiêu không trống. Chuyển đổi bị ngưng vì lí do an toàn.';
$string['textconditionsnotallowed'] = 'So sánh của các điều kiện cột kí tự không cho phép. Hãy dùng sql_compare_text() trong truy vấn của bạn.';
$string['TODO'] = 'CẦN LÀM';
$string['tokengenerationfailed'] = 'Không thể tạo token mới';
$string['transactionvoid'] = 'Giao tác không thể mất hiệu lực vì nó đã bị mất hiệu lực';
$string['unknowaction'] = 'Hành động không rõ!';
$string['unknowcategory'] = 'Chuyên mục không rõ!';
$string['unknowcontext'] = 'Đây là một bối cảnh không rõ ({$a}) trong get_child_contexts!';
$string['unknowformat'] = 'Định dạng không rõ  ({$a})';
$string['unknownbackupexporterror'] = 'Lỗi không rõ khi đang chuẩn bị thông tin để nhập vào';
$string['unknownblockregion'] = 'Vùng khối \'{$a}\' không được nhận diện trên trang này.';
$string['unknowncontext'] = 'Đây là một bối cảnh không rõ.';
$string['unknowncourse'] = 'Khóa học không rõ được đặt tên "{$a}"';
$string['unknowncourseidnumber'] = 'ID khóa học "{$a}" không rõ';
$string['unknowncourserequest'] = 'Yêu cầu khóa học không rõ';
$string['unknowncoursesection'] = 'Phân mục khóa học không rõ trong khóa học "{$a}"';
$string['unknownevent'] = 'Sự kiện không rõ';
$string['unknowngroup'] = 'Nhóm "{$a}" không rõ';
$string['unknownmodulename'] = 'Tên mô-đun không rõ đối với mẫu đơn';
$string['unknownrole'] = 'Vai trò "{$a}" không rõ';
$string['unknownsortcolumn'] = 'Cột sắp xếp {$a} không rõ';
$string['unknownuseraction'] = 'Xin lỗi, tôi không hiểu hành động người dùng này';
$string['unknownuserselector'] = 'Bộ lựa chọn người dùng không rõ';
$string['unknoworder'] = 'Thứ tự không rõ';
$string['unknowparamtype'] = 'Loại tham số không rõ: {$a}';
$string['unknowuploadaction'] = 'Lỗi: hành động đăng tải không rõ ({$a})';
$string['unspecifycourseid'] = 'Phải chỉ định id khóa học, tên ngắn hoặc số id';
$string['unsupportedstate'] = 'Trạng thái hoàn thành không hỗ trợ';
$string['unsupportedwebserver'] = 'Phần mềm máy chủ ({$a}) không được hỗ trợ, xin lỗi.';
$string['upgraderequires19'] = 'Lỗi: Phiên bản Moodle mới đã được cài đặt trên máy chủ, không may là việc nâng cấp từ phiên bản trước không được hỗ trợ.<br />Hãy nâng cấp trước tiên lên bản 1.9.x. Bản cũng có thể trở về phiên bản trước bằng cách cài đặt lại các tập tin ban đầu.';
$string['upgraderunning'] = 'Trang đang được nâng cấp, hãy thử lại sau.';
$string['useradmineditadmin'] = 'Chỉ các quản trị viên được phép chỉnh sửa các tài khoản quản trị viên khác';
$string['useradminodelete'] = 'Các tài khoản quản trị viên không thể xóa được';
$string['userautherror'] = 'Trình bỗ trợ xác thực không rõ';
$string['userauthunsupported'] = 'Trình bỗ trợ xác thực không được hỗ trợ ở đây';
$string['useremailduplicate'] = 'Địa chỉ trùng lặp';
$string['usermustbemnet'] = 'Các người dùng trong danh sách kiểm soát truy cập MNET phải là người dùng MNET từ xa';
$string['usernotaddederror'] = 'Người dùng không được thêm - lỗi';
$string['usernotaddedregistered'] = 'Người dùng không được thêm - đã đăng kí';
$string['usernotavailable'] = 'Chi tiết của người dùng này không hiện hữu với bạn';
$string['usernotdeletedadmin'] = 'Người dùng không được xóa - không thể xóa các  tài khoản quản trị viên';
$string['usernotdeletederror'] = 'Người dùng không được xóa - lỗi';
$string['usernotdeletedmissing'] = 'Người dùng không được xóa - không thể tìm tài khoản';
$string['usernotdeletedoff'] = 'Người dùng không được xóa - không cho phép xóa';
$string['usernotincourse'] = 'Người dùng này không tham gia khóa học này!';
$string['usernotrenamedadmin'] = 'Không thể đặt tên các tài khoản quản trị viên';
$string['usernotrenamedexists'] = 'Người dùng không được đặt tên - tên tài khoản mới đã được sử dụng';
$string['usernotrenamedmissing'] = 'Người dùng không được đặt tên - không thể tìm tài khoản cũ';
$string['usernotrenamedoff'] = 'Người dùng không được đặt tên - không cho phép đặt tên';
$string['usernotupdatedadmin'] = 'Không thể cập nhật các tài khoản quản trị viên';
$string['usernotupdatederror'] = 'Người dùng không được cập nhật - lỗi';
$string['usernotupdatednotexists'] = 'Người dùng không được cập nhật - không tồn tại';
$string['userquotalimit'] = 'Bạn đã đạt giới hạn định mức tập tin.';
$string['userselectortoomany'] = 'user_selector có nhiều hơn một người dùng được chọn, mặc dù đa lựa chọn là sai.';
$string['wrongcall'] = 'Mã nguồn này được gói một cách sai lệch';
$string['wrongdestpath'] = 'Đường dẫn sai';
$string['wrongsourcebase'] = 'Nền URL nguồn sai';
$string['wrongusernamepassword'] = 'Người dùng/mật khẩu sai';
$string['wrongzipfilename'] = 'Tên tệp ZIP sai';
$string['wwwrootmismatch'] = 'Phát hiện truy cập sai, máy chủ này chỉ truy cập được qua địa chỉ "{$a}", xin lỗi.<br />Hãy thông báo cho quản trị máy chủ.';
$string['wwwrootslash'] = 'Phát hiện $CFG->wwwroot sai trong config.php, Nó không được chứa dấu xoẹt cuối câu.<br />Hãy thông báo quản trị máy chủ';
$string['xmldberror'] = 'Lỗi XMLDB!';
$string['youcannotdeletecategory'] = 'Bạn không thể xóa chuyên mục \'{$a}\' bởi vì bạn không thể xóa nội dung, hoặc chuyển chúng đi đâu.';
