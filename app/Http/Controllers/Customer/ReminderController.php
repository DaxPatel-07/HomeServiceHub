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
        $title = $request->input('title') ?: $request->input('service_type');
        $nextDueDate = $request->input('next_due_date') ?: $request->input('due_date');
        $categoryId = $request->input('category_id');

        if (!$categoryId && $request->filled('service_type')) {
            $cat = ServiceCategory::where('name', 'like', '%'.$request->input('service_type').'%')->first() 
                ?? ServiceCategory::first();
            $categoryId = $cat?->id;
        }

        $request->merge([
            'title' => $title,
            'next_due_date' => $nextDueDate,
            'category_id' => $categoryId,
            'repeat_months' => $request->input('repeat_months', 6),
        ]);

        $request->validate([
            'title' => 'required|string|max:150',
            'category_id' => 'required|exists:service_categories,id',
            'next_due_date' => 'required|date',
            'repeat_months' => 'required|integer|min:1|max:120',
            'notes' => 'nullable|string|max:1000',
        ]);

        MaintenanceReminder::create([
            'customer_id'   => Auth::id(),
            'title'         => $request->title,
            'category_id'   => $request->category_id,
            'next_due_date' => $request->next_due_date,
            'repeat_months' => $request->repeat_months,
            'notes'         => $request->notes,
        ]);

        return back()->with('success', 'Reminder set!');
    }
    public function destroy(MaintenanceReminder $reminder) {
        abort_if($reminder->customer_id !== Auth::id(), 403);
        $reminder->delete();
        return back()->with('success', 'Reminder deleted.');
    }
}
