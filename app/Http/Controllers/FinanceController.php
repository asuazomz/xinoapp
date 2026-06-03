<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class FinanceController extends Controller
{
    public function index(): Response
    {
        $month = request('month', now()->format('Y-m'));

        return Inertia::render('Finance/Index', [
            'month' => $month,
            'incomes' => Income::where('month', $month)->latest()->get(),
            'expenses' => Expense::where('month', $month)->latest()->get(),
            'totalIncome' => Income::where('month', $month)->sum('amount'),
            'totalExpense' => Expense::where('month', $month)->sum('amount'),
        ]);
    }

    public function storeIncome(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'person_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'month' => ['required', 'date_format:Y-m'],
            'description' => ['nullable', 'string'],
        ]);

        Income::create($validated);

        return redirect()->back();
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category' => ['nullable', 'string', 'max:255'],
            'month' => ['required', 'date_format:Y-m'],
            'description' => ['nullable', 'string'],
        ]);

        Expense::create($validated);

        return redirect()->back();
    }
}