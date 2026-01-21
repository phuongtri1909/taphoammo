<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureFileUpload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasFile('image') || $request->hasFile('avatar') || $request->hasFile('file') || $request->hasFile('evidence_files') || $request->hasFile('qr_code')) {
            $files = $request->allFiles();
            
            $isEvidenceFile = $request->hasFile('evidence_files');
            
            foreach ($files as $fileKey => $file) {
                if (is_array($file)) {
                    foreach ($file as $singleFile) {
                        if (!$this->isFileSafe($singleFile, $isEvidenceFile || $fileKey === 'evidence_files')) {
                            $extension = strtolower($singleFile->getClientOriginalExtension());
                            $mimeType = $singleFile->getMimeType();
                            $errorMessage = $this->getErrorMessage($isEvidenceFile || $fileKey === 'evidence_files');
                            
                            if ($request->ajax() || $request->wantsJson()) {
                                return response()->json([
                                    'success' => false,
                                    'error' => 'File không được phép upload.',
                                    'message' => $errorMessage
                                ], 422);
                            } else {
                                return redirect()->back()->withErrors(['file' => $errorMessage])->withInput();
                            }
                        }
                    }
                } else {
                    if (!$this->isFileSafe($file, $isEvidenceFile || $fileKey === 'evidence_files')) {
                        $extension = strtolower($file->getClientOriginalExtension());
                        $mimeType = $file->getMimeType();
                        $errorMessage = $this->getErrorMessage($isEvidenceFile || $fileKey === 'evidence_files');
                        
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'error' => 'File không được phép upload.',
                                'message' => $errorMessage
                            ], 422);
                        } else {
                            return redirect()->back()->withErrors(['file' => $errorMessage])->withInput();
                        }
                    }
                }
            }
        }

        return $next($request);
    }

    private function isFileSafe($file, bool $allowDocuments = false): bool
    {
        if (!$file || !$file->isValid()) {
            return false;
        }

        $dangerousExtensions = [
            'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar',
            'asp', 'aspx', 'ashx', 'asmx',
            'jsp', 'jspx',
            'pl', 'py', 'rb', 'sh', 'bash',
            'exe', 'bat', 'cmd', 'com',
            'js', 'vbs', 'wsf',
            'htaccess', 'htpasswd',
            'ini', 'log', 'sql',
            'dll', 'so', 'dylib',
            'html', 'htm', 'xhtml'
        ];

        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $dangerousExtensions)) {
            return false;
        }

        $dangerousMimes = [
            'text/x-php',
            'application/x-php',
            'application/x-executable',
            'application/x-dosexec',
            'application/x-msdownload',
            'application/x-msi',
            'application/x-msdos-program',
            'application/x-executable',
            'application/x-shockwave-flash',
            'application/x-javascript',
            'text/javascript',
            'application/javascript',
            'text/html',
            'application/xhtml+xml'
        ];

        $mimeType = $file->getMimeType();
        if (in_array($mimeType, $dangerousMimes)) {
            return false;
        }

        $allowedImageMimes = [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml'
        ];

        $allowedDocumentMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/rtf'
        ];

        if (in_array($mimeType, $allowedImageMimes)) {
            return true;
        }

        if ($allowDocuments && in_array($mimeType, $allowedDocumentMimes)) {
            $content = file_get_contents($file->getRealPath(), false, null, 0, 512);
            if ($this->containsPhpCode($content)) {
                return false;
            }
            return true;
        }

        return false;
    }

    private function getErrorMessage(bool $allowDocuments = false): string
    {
        if ($allowDocuments) {
            return "Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, WEBP) hoặc tài liệu (PDF, DOC, DOCX, TXT, RTF).";
        }
        return "Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP, SVG).";
    }

    private function containsPhpCode($content): bool
    {
        $phpPatterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<\?/i',
            '/phpinfo\s*\(/i',
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/system\s*\(/i',
            '/shell_exec\s*\(/i',
            '/passthru\s*\(/i',
            '/base64_decode\s*\(/i',
            '/gzinflate\s*\(/i',
            '/str_rot13\s*\(/i',
            '/file_get_contents\s*\(/i',
            '/file_put_contents\s*\(/i',
            '/fopen\s*\(/i',
            '/fwrite\s*\(/i',
            '/include\s*\(/i',
            '/require\s*\(/i',
            '/include_once\s*\(/i',
            '/require_once\s*\(/i'
        ];

        foreach ($phpPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }
} 