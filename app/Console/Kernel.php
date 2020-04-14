<?php

namespace App\Console;

use Illuminate\Support\Facades\Log;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {   
        $schedule->call(function(){
           for ($i=0; $i <12 ; $i++) { 
                $url = 'http://astro.click108.com.tw/daily_'.$i.'.php?iAstro='.$i;
                $lines_array = file($url);
                $arr = array();
                foreach ($lines_array as $data) {
                    if(strstr($data,'今日') and strstr($data,'解析')){
                        array_push($arr,mb_substr(trim(strip_tags($data)),2,3,"utf-8"));
                    }elseif(strstr($data,'整體運勢')){
                        array_push($arr,trim(strip_tags($data)));
                    }elseif(strstr($data,'愛情運勢')){
                        array_push($arr,trim(strip_tags($data)));
                    }elseif(strstr($data,'事業運勢')){
                        array_push($arr,trim(strip_tags($data)));
                    }elseif(strstr($data,'財運運勢')){
                        array_push($arr,trim(strip_tags($data)));
                    }
                }
                array_push($arr,now());
                DB::insert('INSERT INTO constellation (name,overall,love,cause,fortune,created_at)VALUES
                                                      (?,?,?,?,?,?)', $arr);
            }
        })->hourly();

        // $schedule->call(function () {
        //     Log::info('success');
        // })->everyMinute();
        
        // $schedule->call(function(){
        //     DB::insert('insert into test (data,datetime) values (?,?)', array('1min',now()));
        // })->everyMinute();

        // $schedule->call(function(){
        //     DB::insert('insert into test (data,datetime) values (?,?)', array('5min',now()));
        // })->everyFiveMinutes();

        // $schedule->command('inspire')
        //          ->hourly();
       
        // $schedule->command('test:LOG')->everyMinute();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
