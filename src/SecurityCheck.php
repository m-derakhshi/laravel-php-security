<?php

namespace MDerakhshi\SecurityCheck;

class SecurityCheck
{
    public function checkSettings()
    {
        $settingsToFix = [];
        ob_start();

        echo "<div class='container'><h2>Security Settings Check</h2><div class='row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3'>";

        // Check register_argc_argv
        if (ini_get('register_argc_argv')) {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ register_argc_argv is enabled. Disable it for security.</div></div>";
            $settingsToFix[] = "register_argc_argv = Off";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ register_argc_argv is disabled.</div></div>";
        }

        // Check display_errors
        if (ini_get('display_errors') != '0') {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ display_errors is enabled. Disable it in production to prevent exposing sensitive information.</div></div>";
            $settingsToFix[] = "display_errors = Off";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ display_errors is disabled.</div></div>";
        }

        // Check expose_php
        if (ini_get('expose_php') != '0') {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ expose_php is enabled. Disable it to prevent disclosing PHP version in headers.</div></div>";
            $settingsToFix[] = "expose_php = Off";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ expose_php is disabled.</div></div>";
        }

        // Check allow_url_fopen
        if (ini_get('allow_url_fopen') != '0') {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ allow_url_fopen is enabled. Disable it to prevent remote file inclusion vulnerabilities.</div></div>";
            $settingsToFix[] = "allow_url_fopen = Off";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ allow_url_fopen is disabled.</div></div>";
        }

        // Check session.use_strict_mode
        if (ini_get('session.use_strict_mode') != '1') {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ session.use_strict_mode is disabled. Enable it for added session security.</div></div>";
            $settingsToFix[] = "session.use_strict_mode = 1";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ session.use_strict_mode is enabled.</div></div>";
        }

        // Check session.cookie_httponly
        if (ini_get('session.cookie_httponly') != '1') {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ session.cookie_httponly is disabled. Enable it to protect session cookies.</div></div>";
            $settingsToFix[] = "session.cookie_httponly = 1";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ session.cookie_httponly is enabled.</div></div>";
        }

        // Check session.cookie_secure (only if SSL is enabled)
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' && ini_get('session.cookie_secure') != '1') {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ session.cookie_secure is disabled. Enable it for HTTPS to secure session cookies.</div></div>";
            $settingsToFix[] = "session.cookie_secure = 1";
        } else if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
            echo "<div class='col'><div class='p-3 border bg-light text-warning'>⚠️ session.cookie_secure is only effective when HTTPS is enabled.</div></div>";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ session.cookie_secure is enabled.</div></div>";
        }

        // Check disable_functions
        $disabledFunctions = ini_get('disable_functions');
        if (empty($disabledFunctions) || strpos($disabledFunctions, 'exec') === false) {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ No dangerous functions are disabled. It is recommended to disable functions like exec, shell_exec, and system.</div></div>";
            $settingsToFix[] = "disable_functions = exec, shell_exec, system, passthru, popen, proc_open, curl_exec, curl_multi_exec, parse_ini_file, show_source";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ Dangerous functions are disabled: $disabledFunctions</div></div>";
        }

        // Check file_uploads
        if (ini_get('file_uploads') != '0') {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ file_uploads is enabled. Disable it if file uploads are not needed for security reasons.</div></div>";
            $settingsToFix[] = "file_uploads = Off";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ file_uploads is disabled.</div></div>";
        }

        // Check open_basedir
        if (!ini_get('open_basedir')) {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ open_basedir is not set. It is recommended to restrict access to specific directories.</div></div>";
            $settingsToFix[] = "open_basedir = /path/to/your/project:/tmp";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ open_basedir is set.</div></div>";
        }

        // Check session.use_trans_sid
        if (ini_get('session.use_trans_sid') != '0') {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ session.use_trans_sid is enabled. Disable it to prevent session ID leakage.</div></div>";
            $settingsToFix[] = "session.use_trans_sid = 0";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ session.use_trans_sid is disabled.</div></div>";
        }

        // Check magic_quotes_gpc (if available)
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ magic_quotes_gpc is enabled. Disable it as it is deprecated.</div></div>";
            $settingsToFix[] = "magic_quotes_gpc = Off";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ magic_quotes_gpc is disabled.</div></div>";
        }

        // Error reporting level
        $errorReporting = ini_get('error_reporting');
        echo "<div class='col'><div class='p-3 border bg-light'><p>Current error reporting level: $errorReporting</p></div></div>";
        if (ini_get('display_errors') && $errorReporting) {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ display_errors is enabled and errors are shown. Log errors instead in production.</div></div>";
            $settingsToFix[] = "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT";
        }

        echo "</div></div>"; // Close row and container

        $output = ob_get_clean();
        return [
            'output' => $output,
            'settingsToFix' => $settingsToFix,
        ];
    }
}
