<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\ExpenseCategory;


class FinanceController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Finance/Home');
    }

    public function incomes(): Response
    {
        $month = request('month', now()->format('Y-m'));

        return Inertia::render('Finance/Incomes', [
            'month' => $month,
            'incomes' => Income::where('month', $month)->latest()->get(),
        ]);
    }

    public function expenses(): Response
{
    $month = request('month', now()->format('Y-m'));

    return Inertia::render('Finance/Expenses', [
        'month' => $month,
        'expenses' => Expense::where('month', $month)->latest()->get(),
        'categories' => ExpenseCategory::where('is_active', true)->orderBy('name')->get(),
    ]);
}

    public function summary(): Response
    {
        $month = request('month', now()->format('Y-m'));

        return Inertia::render('Finance/Summary', [
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

        return redirect()->route('finances.incomes');
    }

    public function settings(): Response
    {
    return Inertia::render('Finance/Settings', [
        'categories' => ExpenseCategory::orderBy('name')->get(),
    ]);
    }

    
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:expense_categories,name'],
        ]);

        ExpenseCategory::create([
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return redirect()->back();
    }

    public function destroyCategory(ExpenseCategory $category): RedirectResponse
    {
        $category->delete();

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

        return redirect()->route('finances.expenses');
    }
}