<?php

/**
 * lang/vi/errors.php
 *
 * Vietnamese translation file for all client auth error and success codes.
 * Keys map 1-to-1 with App\Support\ErrorCodes constants.
 */

return [
    // Thành công
    'SUCCESS'                          => 'Thao tác thành công.',
    'REGISTER_SUCCESS'                 => 'Đăng ký thành công. Vui lòng xác minh email và số điện thoại của bạn.',
    'LOGIN_SUCCESS'                    => 'Đăng nhập thành công.',
    'LOGOUT_SUCCESS'                   => 'Đăng xuất thành công.',
    'EMAIL_VERIFICATION_SENT'          => 'Email xác minh đã được gửi.',
    'EMAIL_VERIFIED_SUCCESS'           => 'Xác minh email thành công.',
    'PHONE_OTP_SENT'                   => 'Mã OTP đã được gửi đến số điện thoại của bạn.',
    'PHONE_VERIFIED_SUCCESS'           => 'Xác minh số điện thoại thành công.',
    'PASSWORD_RESET_LINK_SENT'         => 'Đường dẫn đặt lại mật khẩu đã được gửi đến email của bạn.',
    'PASSWORD_RESET_SUCCESS'           => 'Đặt lại mật khẩu thành công.',

    // Lỗi xác thực
    'AUTH_VALIDATION_ERROR'            => 'Dữ liệu không hợp lệ.',
    'AUTH_INVALID_CREDENTIALS'         => 'Email/số điện thoại hoặc mật khẩu không đúng.',
    'AUTH_ACCOUNT_NOT_FOUND'           => 'Không tìm thấy tài khoản.',
    'AUTH_ACCOUNT_ALREADY_EXISTS'      => 'Tài khoản với thông tin này đã tồn tại.',
    'AUTH_EMAIL_ALREADY_USED'          => 'Địa chỉ email này đã được đăng ký.',
    'AUTH_PHONE_ALREADY_USED'          => 'Số điện thoại này đã được đăng ký.',
    'AUTH_NICKNAME_ALREADY_USED'       => 'Tên người dùng này đã được sử dụng.',
    'AUTH_UNVERIFIED_ACCOUNT'          => 'Tài khoản của bạn chưa được xác minh.',
    'AUTH_EMAIL_VERIFICATION_REQUIRED' => 'Yêu cầu xác minh email.',
    'AUTH_PHONE_VERIFICATION_REQUIRED' => 'Yêu cầu xác minh số điện thoại.',
    'AUTH_UNAUTHORIZED'                => 'Chưa xác thực. Vui lòng đăng nhập.',
    'AUTH_FORBIDDEN'                   => 'Bạn không có quyền thực hiện hành động này.',

    // Lỗi OTP
    'AUTH_INVALID_OTP'                 => 'Mã OTP không hợp lệ.',
    'AUTH_OTP_EXPIRED'                 => 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.',
    'AUTH_OTP_TOO_MANY_REQUESTS'       => 'Quá nhiều yêu cầu OTP. Vui lòng đợi trước khi thử lại.',

    // Lỗi đặt lại mật khẩu
    'AUTH_INVALID_RESET_TOKEN'         => 'Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
    'AUTH_RESET_PASSWORD_FAILED'       => 'Đặt lại mật khẩu thất bại. Vui lòng thử lại.',

    // Mã giới thiệu
    'AUTH_REFERRAL_CODE_INVALID'       => 'Mã giới thiệu không hợp lệ.',

    // Hệ thống
    'SYSTEM_INTERNAL_ERROR'            => 'Đã xảy ra lỗi không mong muốn. Vui lòng thử lại sau.',
];
