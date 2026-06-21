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
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function incomes(): Response
    {
        $userId = Auth::id();
        $month = request('month', now()->format('Y-m'));

        return Inertia::render('Finance/Incomes', [
            'month' => $month,
            'incomes' => Income::with(['member', 'category'])
                ->where('user_id', $userId)
                ->where('month', $month)
                ->latest()
                ->get(),
            'members' => HouseholdMember::where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'incomeCategories' => IncomeCategory::where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function expenses(): Response
    {
        $userId = Auth::id();
        $month = request('month', now()->format('Y-m'));

        return Inertia::render('Finance/Expenses', [
            'month' => $month,
            'expenses' => Expense::where('user_id', $userId)
                ->where('month', $month)
                ->latest()
                ->get(),
            'categories' => ExpenseCategory::where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    
    public function summary(): Response
    {
        $userId = Auth::id();
        $month = request('month', now()->format('Y-m'));
        $year = substr($month, 0, 4);
        $yearExpenses = Expense::where('user_id', $userId)
        ->where('month', 'like', $year . '-%')
        ->get();
        return Inertia::render('Finance/Summary', [
            'yearExpenses' => $yearExpenses,
            'year' => $year,
            'month' => $month,
            'incomes' => Income::with(['member', 'category'])
                ->where('user_id', $userId)
                ->where('month', $month)
                ->latest()
                ->get(),
            'expenses' => Expense::where('user_id', $userId)
                ->where('month', $month)
                ->latest()
                ->get(),
            'totalIncome' => Income::where('user_id', $userId)
                ->where('month', $month)
                ->sum('amount'),
            'totalExpense' => Expense::where('user_id', $userId)
                ->where('month', $month)
                ->sum('amount'),
        ]);
    }

    public function settings(): Response
    {
        $userId = Auth::id();

        return Inertia::render('Finance/Settings', [
            'members' => HouseholdMember::where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'categories' => ExpenseCategory::where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'incomeCategories' => IncomeCategory::where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeIncome(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'household_member_id' => ['required', 'exists:household_members,id'],
            'income_category_id' => ['required', 'exists:income_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'month' => ['required', 'date_format:Y-m'],
            'description' => ['nullable', 'string'],
        ]);

        $member = HouseholdMember::where('user_id', $userId)
            ->findOrFail($validated['household_member_id']);

        $category = IncomeCategory::where('user_id', $userId)
            ->findOrFail($validated['income_category_id']);

        if (! $category->allow_multiple_per_month) {
            $exists = Income::where('user_id', $userId)
                ->where('household_member_id', $member->id)
                ->where('income_category_id', $category->id)
                ->where('month', $validated['month'])
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'income_category_id' => 'Este participante ya tiene un ingreso de esta categoría en este mes.',
                ]);
            }
        }

        Income::create([
            'user_id' => $userId,
            'household_member_id' => $member->id,
            'income_category_id' => $category->id,
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
        $userId = Auth::id();

        abort_if($income->user_id !== $userId, 403);

        $validated = $request->validate([
            'household_member_id' => ['required', 'exists:household_members,id'],
            'income_category_id' => ['required', 'exists:income_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'month' => ['required', 'date_format:Y-m'],
            'description' => ['nullable', 'string'],
        ]);

        $member = HouseholdMember::where('user_id', $userId)
            ->findOrFail($validated['household_member_id']);

        $category = IncomeCategory::where('user_id', $userId)
            ->findOrFail($validated['income_category_id']);

        if (! $category->allow_multiple_per_month) {
            $exists = Income::where('user_id', $userId)
                ->where('household_member_id', $member->id)
                ->where('income_category_id', $category->id)
                ->where('month', $validated['month'])
                ->where('id', '!=', $income->id)
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'income_category_id' => 'Este participante ya tiene un ingreso de esta categoría en este mes.',
                ]);
            }
        }

        $income->update([
            'household_member_id' => $member->id,
            'income_category_id' => $category->id,
            'person_name' => $member->name,
            'amount' => $validated['amount'],
            'month' => $validated['month'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->back();
    }

    public function destroyIncome(Income $income): RedirectResponse
    {
        abort_if($income->user_id !== Auth::id(), 403);

        $income->delete();

        return redirect()->back();
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string', 'max:255'],
            'month' => ['required', 'date_format:Y-m'],
            'description' => ['nullable', 'string'],
        ]);

        Expense::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'month' => $validated['month'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('finances.expenses', [
            'month' => $validated['month'],
        ]);
    }

    public function updateExpense(Request $request, Expense $expense): RedirectResponse
    {
        abort_if($expense->user_id !== Auth::id(), 403);

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
        abort_if($expense->user_id !== Auth::id(), 403);

        $expense->delete();

        return redirect()->back();
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        ExpenseCategory::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return redirect()->back();
    }

    public function destroyCategory(ExpenseCategory $category): RedirectResponse
    {
        abort_if($category->user_id !== Auth::id(), 403);

        $category->update([
            'is_active' => false,
        ]);

        return redirect()->back();
    }

    public function storeIncomeCategory(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'allow_multiple_per_month' => ['boolean'],
        ]);

        IncomeCategory::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'allow_multiple_per_month' => $request->boolean('allow_multiple_per_month'),
            'is_active' => true,
        ]);

        return redirect()->back();
    }

    public function destroyIncomeCategory(IncomeCategory $category): RedirectResponse
    {
        abort_if($category->user_id !== Auth::id(), 403);

        $category->update([
            'is_active' => false,
        ]);

        return redirect()->back();
    }

    public function participants(): Response
    {
        $userId = Auth::id();

        return Inertia::render('Finance/Participants', [
            'members' => HouseholdMember::where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeParticipant(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        HouseholdMember::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return redirect()->back();
    }

    public function destroyParticipant(HouseholdMember $member): RedirectResponse
    {
        abort_if($member->user_id !== Auth::id(), 403);

        $member->update([
            'is_active' => false,
        ]);

        return redirect()->back();
    }
}