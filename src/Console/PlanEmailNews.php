<?php

namespace Gwaps4nlp\NewsManager\Console;

use Illuminate\Console\Command;
use Gwaps4nlp\NewsManager\Models\News;
use Gwaps4nlp\NewsManager\Models\ScheduledEmailNews;
use Gwaps4nlp\Core\Models\User;
use DB,App;

class PlanEmailNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:plan-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Plan emails of the news';

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
        $news = News::where('send_by_email',1)
            ->whereRaw('scheduled_at <= NOW()')
            ->whereNull('sent_at')
            ->first();
        if($news){

            $news->sent_at = DB::raw('now()');
            $news->save();

                if(App::environment('local')){
                    $users = User::getAdmins();
                } else {
                    $users = User::where('email_frequency_id','!=',1)
                        ->where('email','!=','')->get();
                }

                foreach($users as $user){
                    try {
                        if($user->email!='')
                            ScheduledEmailNews::create(['scheduled_at'=>$news->scheduled_at,'user_id'=>$user->id,'news_id'=>$news->id]);
                    } catch (Exception $Ex){

                    }
                }

        }

    }
}
