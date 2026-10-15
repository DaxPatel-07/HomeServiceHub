<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\MaintenanceReminder;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller {
    public function index() {
        $reminders = MaintenanceReminder::where('customer_id', Auth::id())->with('category')->orderBy('next_due_date')->get();
        $categories = ServiceCategory::where('is_active',true)->get();
        return view('customer.reminders', compact('reminders','categories'));
    }
    public function store(Request $request) {
        $request->validate(['title'=>'required','category_id'=>'required|exists:service_categories,id','next_due_date'=>'required|date','repeat_months'=>'required|integer|min:1']);
        MaintenanceReminder::create(array_merge($request->only('title','category_id','next_due_date','repeat_months','notes'), ['customer_id' => Auth::id()]));
        return back()->with('success', 'Reminder set!');
    }
    public function destroy(MaintenanceReminder $reminder) {
        abort_if($reminder->customer_id !== Auth::id(), 403);
        $reminder->delete();
        return back()->with('success', 'Reminder deleted.');
    }
}
