<?php

namespace Modules\Install\Helpers;

class RequirementsCheck
{
    const MIN_PHP_VERSION = '7.0.0';

    public function requirementArray()
    {
        return  [
            'php' => [
                'intl',
                'openssl',
                'pdo',
                'mbstring',
                'tokenizer',
                'JSON',
                'cURL'
            ],
            'apache' => [
                'mod_rewrite',
            ],
        ];
    }

    /**
     * Permission array
     */
    public function permissionFolderCheckArray()
    {
        return [
            'storage/framework/'     => '775',
            'storage/logs/'          => '775',
            'bootstrap/cache/'       => '775'
        ];
    }

    public function checkExtension()
    {
        $requirements = $this->requirementArray();
        $rs = [];
        foreach ($requirements as $type => $requirement) {
            switch ($type) {
                // check php requirements
                case 'php':
                    foreach ($requirements[$type] as $ext) {
                        $rs['requirements'][$type][$ext] = true;
                        if (!extension_loaded($ext)) {
                            $rs['requirements'][$type][$ext] = false;
                            $rs['errors'] = true;
                        }
                    }
                    break;
                // check apache requirements
                case 'apache':
                    foreach ($requirements[$type] as $mod) {
                        // if function doesn't exist we can't check apache modules
                        if (function_exists('apache_get_modules')) {
                            $rs['requirements'][$type][$mod] = true;
                            if (!in_array($mod, apache_get_modules())) {
                                $rs['requirements'][$type][$mod] = false;
                                $rs['errors'] = true;
                            }
                        }
                    }
                    break;
            }
        }

        return $rs;
    }

    /**
     * Get current Php version information
     */
    private static function getPhpVersionInfo()
    {
        $currentVersionFull = PHP_VERSION;
        preg_match("#^\d+(\.\d+)*#", $currentVersionFull, $filtered);
        $currentVersion = $filtered[0];
        return [
            'full' => $currentVersionFull,
            'version' => $currentVersion
        ];
    }

    /**
     * Check php version
     */
    public function checkPhpVersion(string $minPhpVersion = null)
    {
        $currentPhpVersion = $this->getPhpVersionInfo();
        $supported = false;
        $minVersionPhp = $minPhpVersion;
        if ($minPhpVersion == null) {
            $minVersionPhp = self::MIN_PHP_VERSION;
        }
        if (version_compare($currentPhpVersion['version'], $minVersionPhp) >= 0) {
            $supported = true;
        }
        $phpStatus = [
            'full' => $currentPhpVersion['full'],
            'current' => $currentPhpVersion['version'],
            'minimum' => $minVersionPhp,
            'supported' => $supported
        ];
        return $phpStatus;
    }



    /**
     * Get a folder permission.
     */
    private function getPermission($folder)
    {
        return substr(sprintf('%o', fileperms(base_path($folder))), -4);
    }

    /**
     * Check permission
     */
    public function checkPermission()
    {
        $folders = $this->permissionFolderCheckArray();
        $rs = [];
        foreach ($folders as $folder => $permission) {
            if (!($this->getPermission($folder) >= $permission)) {
                $data = [
                    'folder' => $folder,
                    'permission' => $permission,
                    'isSet' => true
                ];
                $rs['permissions'][] = $data;
                $rs['errors'] = true;
            } else {
                $data = [
                    'folder' => $folder,
                    'permission' => $permission,
                    'isSet' => true
                ];
                $rs['permissions'][] = $data;
            }
        }
        return $rs;
    }

}