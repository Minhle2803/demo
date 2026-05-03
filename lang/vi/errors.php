<?php

/**
 * lang/vi/errors.php
 *
 * Vietnamese translation file for all client auth error and success codes.
 * Keys map 1-to-1 with App\Support\ErrorCodes constants.
 */

return [
    // Thành công
    'SUCCESS' => 'Thao tác thành công.',
    'REGISTER_SUCCESS' => 'Đăng ký thành công. Vui lòng xác minh email và số điện thoại của bạn.',
    'LOGIN_SUCCESS' => 'Đăng nhập thành công.',
    'LOGOUT_SUCCESS' => 'Đăng xuất thành công.',
    'EMAIL_VERIFICATION_SENT' => 'Email xác minh đã được gửi.',
    'EMAIL_VERIFIED_SUCCESS' => 'Xác minh email thành công.',
    'PHONE_OTP_SENT' => 'Mã OTP đã được gửi đến số điện thoại của bạn.',
    'PHONE_VERIFIED_SUCCESS' => 'Xác minh số điện thoại thành công.',
    'PASSWORD_RESET_LINK_SENT' => 'Đường dẫn đặt lại mật khẩu đã được gửi đến email của bạn.',
    'PASSWORD_RESET_SUCCESS' => 'Đặt lại mật khẩu thành công.',

    // Lỗi xác thực
    'AUTH_VALIDATION_ERROR' => 'Dữ liệu không hợp lệ.',
    'AUTH_INVALID_CREDENTIALS' => 'Email/số điện thoại hoặc mật khẩu không đúng.',
    'AUTH_ACCOUNT_NOT_FOUND' => 'Không tìm thấy tài khoản.',
    'AUTH_ACCOUNT_ALREADY_EXISTS' => 'Tài khoản với thông tin này đã tồn tại.',
    'AUTH_EMAIL_ALREADY_USED' => 'Địa chỉ email này đã được đăng ký.',
    'AUTH_PHONE_ALREADY_USED' => 'Số điện thoại này đã được đăng ký.',
    'AUTH_NICKNAME_ALREADY_USED' => 'Tên người dùng này đã được sử dụng.',
    'AUTH_UNVERIFIED_ACCOUNT' => 'Tài khoản của bạn chưa được xác minh.',
    'AUTH_EMAIL_VERIFICATION_REQUIRED' => 'Yêu cầu xác minh email.',
    'AUTH_PHONE_VERIFICATION_REQUIRED' => 'Yêu cầu xác minh số điện thoại.',
    'AUTH_UNAUTHORIZED' => 'Chưa xác thực. Vui lòng đăng nhập.',
    'AUTH_FORBIDDEN' => 'Bạn không có quyền thực hiện hành động này.',

    // Lỗi OTP
    'AUTH_INVALID_OTP' => 'Mã OTP không hợp lệ.',
    'AUTH_OTP_EXPIRED' => 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.',
    'AUTH_OTP_TOO_MANY_REQUESTS' => 'Quá nhiều yêu cầu OTP. Vui lòng đợi trước khi thử lại.',

    // Lỗi đặt lại mật khẩu
    'AUTH_INVALID_RESET_TOKEN' => 'Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
    'AUTH_RESET_PASSWORD_FAILED' => 'Đặt lại mật khẩu thất bại. Vui lòng thử lại.',

    // Mã giới thiệu
    'AUTH_REFERRAL_CODE_INVALID' => 'Mã giới thiệu không hợp lệ.',

    // Hệ thống
    'SYSTEM_INTERNAL_ERROR' => 'Đã xảy ra lỗi không mong muốn. Vui lòng thử lại sau.',

    // Trade
    'TRADE_SESSION_NOT_FOUND' => 'Không tìm thấy phiên giao dịch.',
    'TRADE_SESSION_NOT_OPEN' => 'Phiên giao dịch chưa mở.',
    'TRADE_SESSION_LOCKED' => 'Phiên giao dịch đã bị khóa.',
    'TRADE_ALREADY_PLACED' => 'Bạn đã đặt lệnh trong phiên này.',
    'TRADE_INSUFFICIENT_BALANCE' => 'Số dư không đủ.',
    'TRADE_INVALID_AMOUNT' => 'Số tiền không hợp lệ.',
    'TRADE_PLACE_SUCCESS' => 'Đặt lệnh thành công.',
    'TRADE_RESULT_FETCHED' => 'Đã lấy kết quả phiên.',
    'TRADE_SESSION_FETCHED' => 'Đã lấy thông tin phiên.',
    'TRADE_CANDLE_NOT_FOUND' => 'Không tìm thấy dữ liệu nến cho phiên này.',
    'USER_NOT_FULLY_VERIFIED' => 'Tài khoản chưa được xác minh đầy đủ. Vui lòng hoàn tất KYC.',

    // Profile / KYC
    'PROFILE_UPDATED' => 'Cap nhat thong tin thanh cong.',
    'PASSWORD_UPDATED' => 'Doi mat khau thanh cong.',
    'CURRENT_PASSWORD_INVALID' => 'Mat khau hien tai khong dung.',

    // Deposit
    'DEPOSIT_AMOUNT_REQUIRED' => 'Vui long nhap so tien nap.',
    'DEPOSIT_QR_GENERATED' => 'Da tao ma QR nap tien.',
    'DEPOSIT_CONFIRMED' => 'Xac nhan nap tien thanh cong. So du da duoc cap nhat.',

    // Withdraw
    'WITHDRAW_REQUESTED' => 'Yeu cau rut tien da duoc gui. Vui long doi admin duyet.',
    'WITHDRAW_PROCESSED' => 'Yeu cau rut tien da duoc xu ly.',
    'WITHDRAW_INSUFFICIENT_BALANCE' => 'So du khong du de rut tien.',
    'WITHDRAW_BANK_INFO_MISSING' => 'Vui long them thong tin tai khoan ngan hang truoc khi rut tien.',

    // KYC
    'KYC_UPLOAD_REQUIRED' => 'Vui long tai len ca mat truoc va mat sau CCCD.',
    'KYC_QR_SCAN_FAILED' => 'Khong the quet ma QR tu anh CCCD. Vui long dam bao anh ro net.',
    'KYC_DATA_MISMATCH' => 'Thong tin CCCD khong khop voi thong tin tai khoan.',
    'KYC_VERIFIED_SUCCESS' => 'Xac minh KYC thanh cong.',
    'KYC_ALREADY_VERIFIED' => 'Tai khoan da duoc xac minh KYC.',

];
