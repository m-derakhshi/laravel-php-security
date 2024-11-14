<?php

namespace MDerakhshi\SecurityCheck;

class LaravelPHPSecurityCheck
{
    protected array $settingsToFix;

    public function checkExtension($extensionName): void
    {
        if (extension_loaded($extensionName)) {
            echo "<div class='col'><div class='p-3 border bg-light'><p class='text-success'>✔️ $extensionName: Enabled</p></div></div>";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light'><p class='text-danger'>❌ $extensionName: Disabled</p></div></div>";
        }
    }

    public function checkFunction($functionName): void
    {
        if (function_exists($functionName)) {
            echo "<div class='col'><div class='p-3 border bg-light'><p class='text-success'>✔️ $functionName: Supported</p></div></div>";
        } else {
            echo "<div class='col'><div class='p-3 border bg-light'><p class='text-danger'>❌ $functionName: Not supported</p></div></div>";
        }
    }

    public function checkSetting($settingName, $recommended, $comparison = '=='): bool
    {
        $currentValue = ini_get($settingName);
        $isCorrect = true;

        switch ($comparison) {
            case '==':
                $isCorrect = ($currentValue == $recommended);
                break;
            case '>':
                $isCorrect = ($currentValue > $recommended);
                break;
            case '<':
                $isCorrect = ($currentValue < $recommended);
                break;
        }

        $status = $isCorrect ? 'success' : 'danger';
        $message = $isCorrect
            ? "<div class='col'><div class='p-3 border bg-light text-$status'>✔️ $settingName: $currentValue</div></div>"
            : "<div class='col'><div class='p-3 border bg-light text-$status'>❌ $settingName: $currentValue (Recommended: $recommended)</div></div>";

        echo $message;

        if (! $isCorrect) {
            $this->settingsToFix[] = "$settingName = $recommended";
        }

        return $isCorrect;
    }

    public function checkSettings()
    {
        $this->settingsToFix = [];
        ob_start();

        $this->settingsToFix = [];

        // Check extensions
        $this->checkExtension('gd');
        $this->checkExtension('intl');
        $this->checkExtension('mbstring');
        $this->checkExtension('curl');
        $this->checkExtension('mysqli');
        $this->checkExtension('pdo_mysql');
        $this->checkExtension('openssl');
        $this->checkExtension('zip');

        // Check functions related to WebP
        $this->checkFunction('imagecreatefromwebp');
        $this->checkFunction('imagewebp');

        // PHP version check
        echo "<div class='col'><div class='p-3 border bg-light'><p>Current PHP Version: ".phpversion().'</p></div></div>';

        // PHP settings check
        echo "<div class='col'><div class='p-3 border bg-light'><p>Upload max file size: ".ini_get('upload_max_filesize').'</p></div></div>';
        echo "<div class='col'><div class='p-3 border bg-light'><p>Post max size: ".ini_get('post_max_size').'</p></div></div>';
        echo "<div class='col'><div class='p-3 border bg-light'><p>Max execution time: ".ini_get('max_execution_time').' seconds</p></div></div>';
        echo "<div class='col'><div class='p-3 border bg-light'><p>Memory limit: ".ini_get('memory_limit').'</p></div></div>';

        // Security settings check
        if (ini_get('register_argc_argv')) {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ register_argc_argv is enabled. It is recommended to disable this setting.</div></div>";
            $this->settingsToFix[] = 'register_argc_argv = Off';
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ register_argc_argv is disabled.</div></div>";
        }

        $this->checkSetting('display_errors', '0', '==');
        $this->checkSetting('expose_php', '0', '==');
        $this->checkSetting('allow_url_fopen', '0', '==');
        $this->checkSetting('session.use_strict_mode', '1', '==');
        $this->checkSetting('session.cookie_httponly', '1', '==');

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $this->checkSetting('session.cookie_secure', '1', '==');
        }

        // Check disable_functions
        $disabledFunctions = ini_get('disable_functions');
        if (empty($disabledFunctions) || strpos($disabledFunctions, 'exec') === false) {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ No functions are disabled. It is recommended to disable dangerous functions like exec, shell_exec, system, etc.</div></div>";
            $this->settingsToFix[] = 'disable_functions = exec, shell_exec, system, passthru, popen, proc_open, curl_exec, curl_multi_exec, parse_ini_file, show_source';
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ Disabled functions: $disabledFunctions</div></div>";
        }

        $this->checkSetting('file_uploads', '0', '==');

        if (! ini_get('open_basedir')) {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ open_basedir is not set. It is recommended to restrict file access to specific directories.</div></div>";
            $this->settingsToFix[] = 'open_basedir = /path/to/your/project:/tmp';
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ open_basedir is set.</div></div>";
        }

        $this->checkSetting('session.use_trans_sid', '0', '==');

        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ magic_quotes_gpc is enabled. It is recommended to disable this setting as it is deprecated.</div></div>";
            $this->settingsToFix[] = 'magic_quotes_gpc = Off';
        } else {
            echo "<div class='col'><div class='p-3 border bg-light text-success'>✔️ magic_quotes_gpc is disabled.</div></div>";
        }

        // Error reporting level
        $errorReporting = ini_get('error_reporting');
        $displayErrors = ini_get('display_errors');

        echo "<div class='col'><div class='p-3 border bg-light'><p>Current error reporting level: $errorReporting</p></div></div>";

        // Check if display_errors is enabled in a production environment
        if ($displayErrors && strtolower($displayErrors) !== 'off') {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ display_errors is enabled. It is recommended to disable it in production to prevent exposing sensitive information.</div></div>";
            $this->settingsToFix[] = 'display_errors = Off';
        }

        // Recommend logging errors instead of displaying them in production
        if ($errorReporting != (E_ERROR | E_WARNING | E_PARSE)) {
            echo "<div class='col'><div class='p-3 border bg-light text-warning'>⚠️ Recommended error reporting level for production is <code>E_ERROR | E_WARNING | E_PARSE</code> to log only critical errors.</div></div>";
            $this->settingsToFix[] = 'error_reporting = E_ERROR | E_WARNING | E_PARSE';
        }

        // Ensure log_errors is enabled to store errors in a log file
        $logErrors = ini_get('log_errors');
        if (! $logErrors || strtolower($logErrors) === 'off') {
            echo "<div class='col'><div class='p-3 border bg-light text-danger'>❌ log_errors is disabled. It is recommended to enable it in production to log errors to a file.</div></div>";
            $this->settingsToFix[] = 'log_errors = On';
        }

        $output = ob_get_clean();

        return [
            'output' => $output,
            'settingsToFix' => $this->settingsToFix,
        ];
    }
}
