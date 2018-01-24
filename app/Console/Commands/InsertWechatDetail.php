<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InsertWechatDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertWechatDetail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每隔五分钟自动读取微信机器人的拼车数据';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $text = file_get_contents('/root/wechat_rb/wechat.log');
        if (!$text) {
            return;
        }
        $array = explode('INFO:root:||', $text);

        foreach ($array as $k => $value) {
            // 去掉多余日志
            $detail = is_numeric(strpos($value, 'INFO:itchat:'))
                ? substr_replace($value, '', strpos($value, 'INFO:itchat:'))
                : $value;
            // 去掉itchat 错误日志
            $detail = is_numeric(strpos($detail, 'ERROR:itchat:'))
                ? substr_replace($detail, '', strpos($detail, 'ERROR:itchat:'))
                : $detail;
            if (!$detail) {
                continue;
            }

            // 检测是否在当天库里面  内容一致的不入库
            $reg='[\d{11}]';//匹配数字的正则表达式
            preg_match($reg, $detail, $mobile);
            if (!$mobile) {
                continue;
            }
            $num = \App\InsertWechatDetail::where('mobile', $mobile[0])->where('date', Carbon::today()->toDateString())->count();
            if ($num >= 3) {
                continue;
            }

            if (\App\InsertWechatDetail::where('mobile', $mobile[0])
                ->where('detail', $detail)
                ->where('date', Carbon::today()->toDateString())->count()) {
                continue;
            }
            $wechat = new \App\InsertWechatDetail();
            $wechat->detail = $detail;
            $wechat->mobile = $mobile[0];
            $wechat->date = Carbon::today();
            if (!$wechat->save()) {
                Log::info('InsertWechatDetail save error|'.$value.'|'.$detail.'|'.$mobile);
            }
        }
    }
}
