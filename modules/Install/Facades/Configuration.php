<?php

namespace Modules\Install\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Backend\Core\System;
use Modules\Backend\Models\BackendUser;
use Modules\Backend\Models\Role;
use Symfony\Component\Console\Output\BufferedOutput;

class Configuration extends Model
{
    const SAVE_SAMPLE_DATA = 0;
    const NOT_SAVE_SAMPLE_DATA = 1;

    /**
     * set environment key
     */
    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$envKey}=");
        $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
        $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
        $str = substr($str, 0, -1);
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }

    /**
     * Migrate database
     * notice : run composer dump-autoload if file seeder not exists
     */
    public static function migrateAndSeed($outputLog, $data)
    {
        $installDataSample = $data['install_db_data_sample'];
        if ($installDataSample == Configuration::SAVE_SAMPLE_DATA) {
            $command = ['--class'=>'SampleSeeder'];
        } else {
            $command = ['--class'=>'NotSampleSeeder'];
        }
        Artisan::call('migrate', ["--force"=>true], $outputLog);
        Artisan::call('db:seed', $command, $outputLog);
    }

    /**
     * Save backend user
     */
    public static function saveBackendUser($data)
    {
        $backenUser = BackendUser::where('email', $data['email'])->first();
        if (!empty($backenUser)) {
            return ['rs'=>System::FAIL, 'msg'=>[trans('Install.Lang::lang.msg.email_exists')]];
        }
        $model = new BackendUser();
        $model->name = $data['name'];
        $model->email = $data['email'];
        if (!empty($data['password'])) {
            $model->password = Hash::make($data['password']);
        }
        $model->status = System::YES;
        $admin = Role::select('permission')->where('id', 1)->first();//administrator
        if (!empty($admin)) {
            $model->role_id = 1;
            $model->permission = $admin->permission;
        }
        $model->save();
    }

    /**
     * Create installed file
     */
    public static function createInstalledFile()
    {
        $installedLogFile = storage_path('installed');
        $dateStamp = date("Y/m/d h:i:s");
        if (!file_exists($installedLogFile)) {
            $message = trans('Install.Lang::lang.installed.success_log_message') . $dateStamp . "\n";
            file_put_contents($installedLogFile, $message);
        }
    }

    /**
     * Save config :
     * save db info to file .env
     * migrate and seed
     * generate key
     * save admin user
     */
    public static function doInstall($data)
    {
        try {
            //save file .env
            $arrayDb = ['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'ADMIN_URL'];
            foreach ($arrayDb as $row) {
                self::setEnvironmentValue($row, $data[strtolower($row)]);
            }

            Config::set("database.connections.mysql", [
                'driver' => 'mysql',
                'host' => $data['db_host'],
                'port' => $data['db_port'],
                'database' => $data['db_database'],
                'username' => $data['db_username'],
                'password' => $data['db_password'],
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => 'ids_',
                'strict' => true,
                'engine' => null,
            ]);

            //migrate and seeder
            $outputLog = new BufferedOutput;
            self::migrateAndSeed($outputLog, $data);
            //generate key
            Artisan::call('key:generate', ["--force"=>true], $outputLog);
            //save admin
            self::saveBackendUser($data);
            return ['rs'=>System::SUCCESS, 'msg'=>''];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
    }


    /**
     * Validate configuration
     */
    public static function validateAndDoInstall($data)
    {
        $msgValidate = [];
        $rule = [
            'db_host' => 'required',
            'db_port' => 'required',
            'db_username' => 'required',
            'db_password' => 'required',
            'db_database' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:5',
            'admin_url' => 'required',
        ];
        $validator = Validator::make($data, $rule, $msgValidate);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $msg = $messages->all();
            return ['rs'=>System::FAIL, 'msg'=>[$msg[0]]];
        } else {
            $rs = self::doInstall($data);
            return $rs;
        }
    }
}

