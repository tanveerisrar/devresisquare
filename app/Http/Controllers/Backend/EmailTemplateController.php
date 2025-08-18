<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\EmailManager;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailTemplateController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:manage_email_templates'])->only('index', 'edit', 'update');
    }

    public function index(Request $request, $emailReceiver)
    {
        $email_template_sort_search = (isset($request->email_template_sort_search) && $request->email_template_sort_search) ? $request->email_template_sort_search : null;
        $emailTemplates = EmailTemplate::where('receiver', $emailReceiver);

        if ($email_template_sort_search != null){
            $notificationTypes = $emailTemplates->where('email_type', 'like', '%' . $email_template_sort_search . '%');
        }
        $emailTemplates = $emailTemplates->paginate(10);
        return view('backend.setup_configurations.email_templates.index', compact('emailTemplates', 'email_template_sort_search', 'emailReceiver'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $emailTemplate  = EmailTemplate::findOrFail($id);
        return view('backend.setup_configurations.email_templates.edit', compact('emailTemplate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);
        $emailTemplate->subject = $request->subject;
        $emailTemplate->default_text = $request->default_text;
        $emailTemplate->save();

        flash('Email Template has been updated successfully')->success();
        return back();
    }

    public function updateStatus(Request $request) {
        $emailTemplate = EmailTemplate::findOrFail($request->id);
        $emailTemplate->status = $request->status;
        $emailTemplate->save();
        return 1;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function testEmail(Request $request){
        $array['view'] = 'emails.newsletter';
        $array['subject'] = "SMTP Test";
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = "This is a test email.";

        try {
            Mail::to($request->email)->queue(new EmailManager($array));
        } catch (\Exception $e) {
            // dd($e);
            Log::error('SMTP Test Email Failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Email could not be sent. Please check logs.');
        }

        flash('An email has been sent.')->success();
        return back();
    }
}
