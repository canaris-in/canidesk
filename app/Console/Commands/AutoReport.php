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
        $settings = SLASetting::orderBy('id', 'desc')->first();
        $todate = \Carbon\Carbon::parse($settings->created_at)->format('Y-m-d');
        if ($settings->frequency === 'Daily') {
            $fromdate = \Carbon\Carbon::parse($todate)->subDays(1)->format('Y-m-d');
        } elseif ($settings->frequency === 'Weekly') {
            $fromdate = \Carbon\Carbon::parse($todate)->subDays(7)->format('Y-m-d');
        } elseif ($settings->frequency === 'Monthly') {
            $fromdate = \Carbon\Carbon::parse($todate)->subDays(30)->format('Y-m-d');
        }


        #the below code is for the comparision on real time with time and date with date
        if ($settings->frequency === 'Daily') {
            $nowTime = Carbon::now()->format('h:i');
            $compTime = $settings->time;
            $compTime = Carbon::createFromFormat('H:i:s', $compTime);
            $emails = explode(',', $settings->to_email);
            // Format the time without seconds
            $compTime = $compTime->format('H:i');

            if ($nowTime === $compTime) {
                if ($settings->auto_data == 1) {
                    $tickets = Conversation::query();
                    $tickets = $tickets->with('user', 'conversationCustomField.custom_field', 'conversationCategory', 'conversationPriority');
                    $tickets = $tickets->whereBetween('created_at', [$fromdate, $todate]);
                    $tickets = $tickets->get();
                    $dompdf = new Dompdf();
                    $options = new Options();
                    $options->set('defaultFont', 'Arial');
                    $dompdf->setOptions($options);
                    $html = view('sla.report-email', compact('tickets'));
                    $dompdf->load_html($html);
                    $dompdf->render();
                    $output = $dompdf->output();
                    file_put_contents("storage/slaReport/Report_{{$settings->frequency}}_{{$fromdate}}_{{$todate}}.pdf", $output);
                    $filePath = "storage/slaReport/Report_{{$settings->frequency}}_{{$fromdate}}_{{$todate}}.pdf";


                    // $toEmail = 'rajesh@canaris.in';
                    $subject = 'Hi';
                    $message = 'Hello Rajesh, this is a test email from Canidesk.';
                    $fromEmail = 'support@canaris.in';
                    $fromName = '[ Canidesk Report ]';
                    foreach ($emails as $email) {
                        Mail::raw($message, function ($mail) use ($email, $subject, $fromEmail, $fromName, $filePath) {
                            $mail->to($email)
                                ->subject($subject)
                                ->from($fromEmail, $fromName)
                                ->attach($filePath);
                        });
                    }
                }
            }
        } elseif ($settings->frequency === 'Weekly') {
            $nowDate = Carbon::now()->format('l');
            $nowTime = Carbon::now()->format('h:i');
            $compTime = $settings->time;
            $compTime = Carbon::createFromFormat('H:i:s', $compTime);
            $emails = explode(',', $settings->to_email);
            // Format the time without seconds
            $compTime = $compTime->format('H:i');
            $compDate = $settings->schedule;

            if ($nowTime === $compTime && $nowDate === $compDate) {
                if ($settings->auto_data == 1) {
                    $tickets = Conversation::query();
                    $tickets = $tickets->with('user', 'conversationCustomField.custom_field', 'conversationCategory', 'conversationPriority');
                    $tickets = $tickets->whereBetween('created_at', [$fromdate, $todate]);
                    $tickets = $tickets->get();
                    $dompdf = new Dompdf();
                    $options = new Options();
                    $options->set('defaultFont', 'Arial');
                    $dompdf->setOptions($options);
                    $html = view('sla.report-email', compact('tickets'));
                    $dompdf->load_html($html);
                    $dompdf->render();
                    $output = $dompdf->output();
                    file_put_contents("storage/slaReport/Report_{{$settings->frequency}}_{{$fromdate}}_{{$todate}}.pdf", $output);
                    $filePath = "storage/slaReport/Report_{{$settings->frequency}}_{{$fromdate}}_{{$todate}}.pdf";


                    // $toEmail = 'rajesh@canaris.in';
                    $subject = 'Hi';
                    $message = 'Hello Rajesh, this is a test email from Canidesk.';
                    $fromEmail = 'support@canaris.in';
                    $fromName = '[ Canidesk Report ]';
                    foreach ($emails as $email) {
                        Mail::raw($message, function ($mail) use ($email, $subject, $fromEmail, $fromName, $filePath) {
                            $mail->to($email)
                                ->subject($subject)
                                ->from($fromEmail, $fromName)
                                ->attach($filePath);
                        });
                    }
                }
            }
        } elseif ($settings->frequency === 'Monthly') {
            $nowDate = Carbon::now()->format('d');
            $nowTime = Carbon::now()->format('h:i');
            $compTime = $settings->time;
            $compTime = Carbon::createFromFormat('H:i:s', $compTime);
            $emails = explode(',', $settings->to_email);
            // Format the time without seconds
            $compTime = $compTime->format('H:i');
            $compDate = $settings->schedule;

            if ($nowTime === $compTime && $nowDate === $compDate) {
                if ($settings->auto_data == 1) {
                    $tickets = Conversation::query();
                    $tickets = $tickets->with('user', 'conversationCustomField.custom_field', 'conversationCategory', 'conversationPriority');
                    $tickets = $tickets->whereBetween('created_at', [$fromdate, $todate]);
                    $tickets = $tickets->get();
                    $dompdf = new Dompdf();
                    $options = new Options();
                    $options->set('defaultFont', 'Arial');
                    $dompdf->setOptions($options);
                    $html = view('sla.report-email', compact('tickets'));
                    $dompdf->load_html($html);
                    $dompdf->render();
                    $output = $dompdf->output();
                    file_put_contents("storage/slaReport/Report_{{$settings->frequency}}_{{$fromdate}}_{{$todate}}.pdf", $output);
                    $filePath = "storage/slaReport/Report_{{$settings->frequency}}_{{$fromdate}}_{{$todate}}.pdf";


                    // $toEmail = 'rajesh@canaris.in';
                    $subject = 'Hi';
                    $message = 'Hello Rajesh, this is a test email from Canidesk.';
                    $fromEmail = 'support@canaris.in';
                    $fromName = '[ Canidesk Report ]';
                    foreach ($emails as $email) {
                        Mail::raw($message, function ($mail) use ($email, $subject, $fromEmail, $fromName, $filePath) {
                            $mail->to($email)
                                ->subject($subject)
                                ->from($fromEmail, $fromName)
                                ->attach($filePath);
                        });
                    }
                }
            }
        }
    }
}
