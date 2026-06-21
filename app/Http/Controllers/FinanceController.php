<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\HouseholdMember;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

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
            'incomes' => Income::with(['member', 'category'])
                ->where('month', $month)
                ->latest()
                ->get(),
            'members' => HouseholdMember::where('is_active', true)
                ->orderBy('name')
                ->get(),
            'incomeCategories' => IncomeCategory::where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function expenses(): Response
    {
        $month = request('month', now()->format('Y-m'));

        return Inertia::render('Finance/Expenses', [
            'month' => $month,
            'expenses' => Expense::where('month', $month)
                ->latest()
                ->get(),
            'categories' => ExpenseCategory::where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function summary(): Response
    {
        $month = request('month', now()->format('Y-m'));

        return Inertia::render('Finance/Summary', [
            'month' => $month,
            'incomes' => Income::with(['member', 'category'])
                ->where('month', $month)
                ->latest()
                ->get(),
            'expenses' => Expense::where('month', $month)
                ->latest()
                ->get(),
            'totalIncome' => Income::where('month', $month)->sum('amount'),
            'totalExpense' => Expense::where('month', $month)->sum('amount'),
        ]);
    }

    public function settings(): Response
    {
        return Inertia::render('Finance/Settings', [
            'members' => HouseholdMember::where('is_active', true)
                ->orderBy('name')
                ->get(),
            'categories' => ExpenseCategory::where('is_active', true)
                ->orderBy('name')
                ->get(),
            'incomeCategories' => IncomeCategory::where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeIncome(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'household_member_id' => ['required', 'exists:household_members,id'],
            'income_category_id' => ['required', 'exists:income_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'month' => ['required', 'date_format:Y-m'],
            'description' => ['nullable', 'string'],
        ]);

        $category = IncomeCategory::findOrFail($validated['income_category_id']);

        if (! $category->allow_multiple_per_month) {
            $exists = Income::where('household_member_id', $validated['household_member_id'])
                ->where('income_category_id', $validated['income_category_id'])
                ->where('month', $validated['month'])
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'income_category_id' => 'Este participante ya tiene un ingreso de esta categoría en este mes.',
                ]);
            }
        }

        $member = HouseholdMember::findOrFail($validated['household_member_id']);

        Income::create([
            'household_member_id' => $validated['household_member_id'],
            'income_category_id' => $validated['income_category_id'],
            'person_name' => $member->name,
            'amount' => $validated['amount'],
            'month' => $validated['month'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('finances.incomes', [
            'month' => $validated['month'],
        ]);
    }

    public function updateIncome(Request $request, Income $income): RedirectResponse
    {
        $validated = $request->validate([
            'household_member_id' => ['required', 'exists:household_members,id'],
            'income_category_id' => ['required', 'exists:income_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'month' => ['required', 'date_format:Y-m'],
            'description' => ['nullable', 'string'],
        ]);

        $category = IncomeCategory::findOrFail($validated['income_category_id']);

        if (! $category->allow_multiple_per_month) {
            $exists = Income::where('household_member_id', $validated['household_member_id'])
                ->where('income_category_id', $validated['income_category_id'])
                ->where('month', $validated['month'])
                ->where('id', '!=', $income->id)
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'income_category_id' => 'Este participante ya tiene un ingreso de esta categoría en este mes.',
                ]);
            }
        }

        $member = HouseholdMember::findOrFail($validated['household_member_id']);

        $income->update([
            'household_member_id' => $validated['household_member_id'],
            'income_category_id' => $validated['income_category_id'],
            'person_name' => $member->name,
            'amount' => $validated['amount'],
            'month' => $validated['month'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->back();
    }

    public function destroyIncome(Income $income): RedirectResponse
    {
        $income->delete();

        return redirect()->back();
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string', 'max:255'],
            'month' => ['required', 'date_format:Y-m'],
            'description' => ['nullable', 'string'],
        ]);

        Expense::create($validated);

        return redirect()->route('finances.expenses', [
            'month' => $validated['month'],
        ]);
    }

    public function updateExpense(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string', 'max:255'],
            'month' => ['required', 'date_format:Y-m'],
            'description' => ['nullable', 'string'],
        ]);

        $expense->update($validated);

        return redirect()->back();
    }

    public function destroyExpense(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->back();
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
        $category->update([
            'is_active' => false,
        ]);

        return redirect()->back();
    }

    public function storeIncomeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:income_categories,name'],
            'allow_multiple_per_month' => ['boolean'],
        ]);

        IncomeCategory::create([
            'name' => $validated['name'],
            'allow_multiple_per_month' => $request->boolean('allow_multiple_per_month'),
            'is_active' => true,
        ]);

        return redirect()->back();
    }

    public function destroyIncomeCategory(IncomeCategory $category): RedirectResponse
    {
        $category->update([
            'is_active' => false,
        ]);

        return redirect()->back();
    }

    public function participants(): Response
    {
        return Inertia::render('Finance/Participants', [
            'members' => HouseholdMember::where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeParticipant(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:household_members,name'],
        ]);

        HouseholdMember::create([
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return redirect()->back();
    }

    public function destroyParticipant(HouseholdMember $member): RedirectResponse
    {
        $member->update([
            'is_active' => false,
        ]);

        return redirect()->back();
    }
}