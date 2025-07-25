taskkill /F /im php-cgi.exe
taskkill /F /im nginx.exe
cd C:\nginx-rtmp-win32-dev\nginx-rtmp-win32-dev
nginx.exe
cd C:\nginx-rtmp-win32-dev\nginx-rtmp-win32-dev\php-8.2.19-nts-Win32-vs16-x64
php-cgi.exe -b 127.0.0.1:9999

