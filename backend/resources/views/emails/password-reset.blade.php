<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - Q-Potek Dashboard</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Design System Colors - Based on IndexPage.vue */
        :root {
            --primary-blue: #2563eb;
            --primary-blue-50: #eff6ff;
            --primary-blue-100: #dbeafe;
            --primary-blue-600: #2563eb;
            --primary-blue-700: #1d4ed8;
            
            --secondary-teal: #0d9488;
            --secondary-teal-50: #f0fdfa;
            --secondary-teal-100: #ccfbf1;
            
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-300: #d1d5db;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            --success-green: #059669;
            --warning-orange: #d97706;
            --error-red: #dc2626;
        }

        /* Typography - Inter font family */
        body {
            font-family: 'Inter', 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: var(--gray-800);
            background-color: var(--gray-50);
            margin: 0;
            padding: 0;
        }

        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            overflow: hidden;
        }

        /* Header with gradient background */
        .email-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-700) 100%);
            padding: 40px 32px;
            text-align: center;
            position: relative;
        }

        .email-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .logo-container {
            position: relative;
            z-index: 1;
        }

        .logo {
            width: 64px;
            height: 64px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .logo svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        .email-title {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .email-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 400;
            margin: 8px 0 0;
            position: relative;
            z-index: 1;
        }

        /* Content */
        .email-content {
            padding: 40px 32px;
        }

        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 24px;
        }

        .message {
            font-size: 16px;
            color: var(--gray-700);
            margin-bottom: 24px;
            line-height: 1.6;
        }

        /* Password display */
        .password-container {
            background: linear-gradient(135deg, var(--primary-blue-50) 0%, var(--secondary-teal-50) 100%);
            border: 2px solid var(--primary-blue-100);
            border-radius: 12px;
            padding: 24px;
            margin: 32px 0;
            text-align: center;
            position: relative;
        }

        .password-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .password-value {
            font-family: 'JetBrains Mono', 'Fira Code', 'Monaco', 'Consolas', monospace;
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-blue);
            background-color: white;
            padding: 16px 24px;
            border-radius: 8px;
            border: 1px solid var(--primary-blue-100);
            letter-spacing: 2px;
            word-break: break-all;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Action button */
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-700) 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 24px 0;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(37, 99, 235, 0.4);
        }

        /* Security notice */
        .security-notice {
            background-color: var(--warning-orange);
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }

        .security-notice-icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 8px;
            vertical-align: middle;
        }

        .security-notice-text {
            color: #92400e;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
            vertical-align: middle;
        }

        /* Footer */
        .email-footer {
            background-color: var(--gray-50);
            padding: 32px;
            text-align: center;
            border-top: 1px solid var(--gray-100);
        }

        .footer-text {
            color: var(--gray-500);
            font-size: 14px;
            margin-bottom: 16px;
        }

        .footer-brand {
            color: var(--primary-blue);
            font-weight: 600;
            font-size: 16px;
        }

        .footer-tagline {
            color: var(--gray-500);
            font-size: 12px;
            margin-top: 8px;
        }

        /* Responsive design */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            .email-header,
            .email-content,
            .email-footer {
                padding: 24px 20px;
            }
            
            .password-value {
                font-size: 18px;
                padding: 12px 16px;
            }
            
            .action-button {
                display: block;
                width: 100%;
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo-container">
                <div class="logo">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <h1 class="email-title">Password Reset</h1>
                <p class="email-subtitle">Q-Potek Dashboard System</p>
            </div>
        </div>

        <!-- Content -->
        <div class="email-content">
            <div class="greeting">Halo {{ $user->name }}!</div>
            
            <div class="message">
                Password Anda telah direset oleh administrator sistem. Untuk keamanan akun Anda, silakan gunakan password sementara di bawah ini untuk login dan segera ganti dengan password baru.
            </div>

            <div class="password-container">
                <div class="password-label">Password Sementara</div>
                <div class="password-value">{{ $newPassword }}</div>
            </div>

            <div class="security-notice">
                <svg class="security-notice-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="security-notice-text">
                    Penting: Segera ganti password ini setelah login untuk menjaga keamanan akun Anda.
                </span>
            </div>

            <div style="text-align: center;">
                <a href="{{ config('app.url') }}" class="action-button">
                    Login ke Dashboard
                </a>
            </div>

            <div class="message">
                Jika Anda tidak meminta reset password ini, segera hubungi administrator sistem untuk tindakan lebih lanjut.
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                Email ini dikirim secara otomatis oleh sistem Q-Potek Dashboard.
            </div>
            <div class="footer-brand">Q-Potek Team</div>
            <div class="footer-tagline">Sistem Manajemen Apotek Terpadu</div>
        </div>
    </div>
</body>
</html>