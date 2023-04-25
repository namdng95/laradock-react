<?php

namespace App\Jobs;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $idUser = authId();
        $title  = "【jinjer請求書】請求書番号{$idUser}の申請が否認されました";
        $body   = $this->parseBodyMail($idUser);

        Mail::to($this->user->email)->send(new WelcomeEmail($this->user));
    }

    private function parseBodyMail($idUser)
    {
        $url = config('custom.web_url') . '/invoices/' . $idUser . '/detail';
        $applied_at = now()->format('Y年m月d日H時i分');

        $body = "※ このメールは「jinjer請求書」からの自動配信メールとなっております。
            ご返信はお受けできかねますのでご了承ください。

            {$this->user->name}様
        ";

        $body .= "\n請求書番号{$invoiceCode}の申請が否認されました。\n";

        $body .= "
            ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
            請求書番号：{$invoiceCode}
            帳票種別：請求書
            申請日時：{$applied_at}
            ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝

            申請内容はこちらになります。クリックすると内容が別ウィンドウで表示されます。
        ";
        $body .= "<a target='_blank' href='{$url}'>{$url}</a>";

        return $body;
    }
}
