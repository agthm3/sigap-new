<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi OTP - SIGAP BRIDA</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f5f7;
            color: #333333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f5f7;
            padding: 40px 0;
        }
        .email-container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px border-gray-200;
        }
        .email-header {
            background-color: #7a2222; /* Maroon SIGAP BRIDA */
            padding: 24px 32px;
            text-align: left;
        }
        .email-header .brand {
            display: inline-block;
            background-color: #ffffff;
            color: #7a2222;
            font-weight: 800;
            font-size: 16px;
            padding: 6px 12px;
            border-radius: 6px;
            vertical-align: middle;
            margin-right: 10px;
        }
        .email-header .title {
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            display: inline-block;
            vertical-align: middle;
        }
        .email-body {
            padding: 32px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 12px;
        }
        .message {
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 24px;
        }
        .otp-box {
            background-color: #fdf7f7;
            border: 2px dashed #a64040;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .otp-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #7a2222;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 10px;
            color: #7a2222;
            font-family: 'Courier New', Courier, monospace;
        }
        .otp-expiry {
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
        }
        .warning-box {
            background-color: #fffbe8;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 12px;
            color: #92400e;
            margin-top: 24px;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 20px 32px;
            text-align: center;
            border-top: 1px solid #f3f4f6;
            font-size: 12px;
            color: #9ca3af;
        }
        .email-footer a {
            color: #7a2222;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <span class="brand">SB</span>
                <span class="title">SIGAP BRIDA</span>
            </div>

            <!-- Body -->
            <div class="email-body">
                <div class="greeting">Halo, {{ $user->name }}!</div>
                <p class="message">
                    Terima kasih telah mendaftar di sistem <strong>SIGAP BRIDA Kota Makassar</strong>. Untuk menyelesaikan verifikasi akun Anda, gunakan kode One-Time Password (OTP) berikut:
                </p>

                <!-- OTP Display Box -->
                <div class="otp-box">
                    <div class="otp-label">Kode Verifikasi Anda</div>
                    <div class="otp-code">{{ $otp }}</div>
                    <div class="otp-expiry">⏳ Berlaku selama 10 menit</div>
                </div>

                <p class="message" style="font-size: 13px;">
                    Masukkan kode ini pada halaman verifikasi di browser Anda. Jangan berikan kode verifikasi ini kepada siapapun, termasuk pihak yang mengaku dari BRIDA Kota Makassar.
                </p>

                <div class="warning-box">
                    <strong>Perhatian:</strong> Jika Anda tidak merasa melakukan pendaftaran akun di SIGAP BRIDA, silakan abaikan email ini.
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>© {{ date('Y') }} SIGAP BRIDA • Badan Riset dan Inovasi Daerah Kota Makassar</p>
                <p>Email ini dikirimkan secara otomatis oleh sistem, mohon tidak membalas email ini.</p>
            </div>
        </div>
    </div>
</body>
</html>