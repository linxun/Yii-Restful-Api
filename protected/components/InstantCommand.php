<?php
/**
 * Desc: Send long-lasting tasks into a background process
 * Author: linxun
 * Date: 2014-05-19
 * Usage: InstantCommand::my_background_exec('Aclass::amethod', array('hello', 'jack'), 3600, 'require "protected/models/feeds/Aclass.php";');
 */
$GLOBALS['CONF']=require './protected/config/pro/main.php';    //get redis/mongo password
define('PHP_CLI', $GLOBALS['CONF']['params']['php_cli_path']);

class InstantCommand
{
    static function my_background_exec($function_name, $params, $timeout=600, $str_requires='require_once("../yii/yii.php");YiiBase::createWebApplication("./protected/config/pro/main.php");')
         {$map=array('"'=>'\"', '$'=>'\$', '`'=>'\`', '\\'=>'\\\\', '!'=>'\!');
          $str_requires=strtr($str_requires, $map);    #the $str_requires maybe only the file that includes autoload function
          $my_target_exec=PHP_CLI." -r \"{$str_requires}\\\$params=json_decode(file_get_contents('php://stdin'),true);call_user_func_array('{$function_name}', \\\$params);\"";
          $my_target_exec=strtr(strtr($my_target_exec, $map), $map);
          $my_background_exec="(".PHP_CLI." -r \"require '".__FILE__."';InstantCommand::my_timeout_exec(\\\"{$my_target_exec}\\\", file_get_contents('php://stdin'), {$timeout});\" <&3 &) 3<&0";//php by default use "sh", and "sh" don't support "<&0"
          InstantCommand::my_timeout_exec($my_background_exec, json_encode($params), 2);
         }

    static function my_timeout_exec($cmd, $stdin='', $timeout)
         {$start=time();
          $stdout='';
          $stderr='';
          file_put_contents('protected/runtime/instant_command_debug.txt', time().":cmd: echo '{$stdin}'|".$cmd."\n", FILE_APPEND);

          $process=proc_open($cmd, [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes);
          if (!is_resource($process))
             {return array('return'=>'1', 'stdout'=>$stdout, 'stderr'=>$stderr);
             }
          $status=proc_get_status($process);
          posix_setpgid($status['pid'], $status['pid']);    //seperate pgid(process group id) from parent's pgid

          stream_set_blocking($pipes[0], 0);
          stream_set_blocking($pipes[1], 0);
          stream_set_blocking($pipes[2], 0);
          fwrite($pipes[0], $stdin);
          fclose($pipes[0]);

          while (1)
                {$stdout.=stream_get_contents($pipes[1]);
                 $stderr.=stream_get_contents($pipes[2]);

                 if (time()-$start>$timeout)
                    {//proc_terminate($process, 9);    //only terminate subprocess, won't terminate sub-subprocess
                     posix_kill(-$status['pid'], 9);    //sends SIGKILL to all processes inside group(negative means GPID, all subprocesses share the top process group, except nested my_timeout_exec)
                     //file_put_contents('debug.txt', time().":kill group {$status['pid']}\n", FILE_APPEND);
                     return array('return'=>'1', 'stdout'=>$stdout, 'stderr'=>$stderr);
                    }

                 $status=proc_get_status($process);
                 //file_put_contents('debug.txt', time().':status:'.var_export($status, true)."\n";
                 if (!$status['running'])
                    {fclose($pipes[1]);
                     fclose($pipes[2]);
                     proc_close($process);
                     return $status['exitcode'];
                    }

                 usleep(100000); 
                }
         }

    static function log($message, $level, $category)
           {$logger=new CFileLogRoute;
            $logger->init();
            $method_processLogs=new ReflectionMethod(get_class($logger), 'processLogs');
            $method_processLogs->setAccessible(true);
            $method_processLogs->invoke($logger, array(array($message, $level, $category, time())));
           }
}