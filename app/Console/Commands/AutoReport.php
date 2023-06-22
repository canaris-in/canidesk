<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\SLASetting;
use App\Conversation;
use Dompdf\Dompdf;
use Dompdf\Options;
use \PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use Carbon\Carbon;

class AutoReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'canidesk:auto-reporting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command is used to send email automatically';

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
    public function handle(Request $request)
    {
        // $this->info(Carbon::now()->format('l'));
        $today = Carbon::today();
        $fourDaysAgo = Carbon::today()->subDays(4);
        $sevenDaysAgo = Carbon::today()->subDays(7);
        $thirtyDaysAgo = Carbon::today()->subDays(30);
        $prev = false;
        $date_field = 'conversations.created_at';
        $date_field_to = '';

        $settings=SLASetting::orderBy('id', 'desc')->first();
        if($settings->auto_data == 1){ // Start generating email report if auto reporting is turned on 
            $emails=explode(',', $settings->to_email);
            $tickets = Conversation::query();
            $tickets = $tickets->with('user', 'conversationCustomField.custom_field', 'conversationCategory','conversationPriority');
           
            if(!empty($settings->frequency) && $settings->frequency === 'Monthly' && $settings->schedule === Carbon::now()->format('d') && $settings->time === Carbon::now()->format('H:i:00') ){
                $tickets = $tickets->whereBetween('created_at',[Carbon::now()->subDays(30)->startOfMonth()->startOfDay(),Carbon::now()->subDays(30)->endOfMonth()->endOfDay()]);

            }
            else if(!empty($settings->frequency) && $settings->frequency === 'Weekly'&& $settings->schedule === Carbon::now()->format('l') && $settings->time === Carbon::now()->format('H:i:00')){
                $tickets = $tickets->whereBetween('created_at',[Carbon::now()->subDays(7)->startOfWeek()->startOfDay(),Carbon::now()->subDays(7)->endOfWeek()->endOfDay()]);
                
            }
            else if(!empty($settings->frequency) && $settings->frequency === 'Daily' && $settings->time === Carbon::now()->format('H:i:00')){
                $tickets = $tickets->whereBetween('created_at',[Carbon::now()->subDays(1)->startOfDay(),Carbon::now()->subDays(1)->endOfDay()]);

            }
            
            $tickets = $tickets->get();
            $dompdf = new Dompdf();
            $options = new Options();
            $options->set('defaultFont', 'Arial'); 
            $dompdf->setOptions($options);
            $html = view('sla.report-email', compact('tickets'));
            $dompdf->load_html($html);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents('storage/slaReport/report.pdf', $output);
            $data = array('name'=>"Example");

            if($settings->frequency === 'Daily' && $settings->time === Carbon::now()->format('H:i:00') || $settings->frequency === 'Weekly'&& $settings->schedule === Carbon::now()->format('l') && $settings->time === Carbon::now()->format('H:i:00') || $settings->frequency === 'Monthly' && $settings->schedule === Carbon::now()->format('d') && $settings->time === Carbon::now()->format('H:i:00') ){
                foreach($emails as $email){
                $mail = Mail::send('mail', $data, function($message) use ($email, $settings){
                $message->to($email, 'Canidesk User')->subject
                    ($settings->frequency.' Report')->attach('storage/slaReport/report.pdf');
                $message->from('rajeshcanaris@gmail.com','[ Canidesk Report ]');
                
                });
            }
            }
        }
       
    }
}
